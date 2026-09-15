<?php

declare(strict_types=1);

namespace App\Filament\Resources\SanaeiServers;

use App\Filament\Resources\SanaeiServers\Pages\CreateSanaeiServer;
use App\Filament\Resources\SanaeiServers\Pages\EditSanaeiServer;
use App\Filament\Resources\SanaeiServers\Pages\ListSanaeiServers;
use App\Models\ServiceProviderAccount;
use App\Services\Providers\Sanaei\SanaeiServerManager;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class SanaeiServerResource extends Resource
{
    protected static ?string $model = ServiceProviderAccount::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;
    protected static ?string $navigationLabel = 'سرورهای سنایی';
    protected static ?string $modelLabel = 'سرور سنایی';
    protected static ?string $pluralModelLabel = 'سرورهای سنایی';
    protected static string|\UnitEnum|null $navigationGroup = 'Providerها';
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('مشخصات سرور')->schema([
                TextInput::make('name')->label('نام')->required()->maxLength(120),
                TextInput::make('base_url')->label('API Base URL')->url()->required()->maxLength(500),
                TextInput::make('token')->label('API Token')->password()->revealable()->maxLength(1000)->helperText('توکن فقط هنگام ذخیره به‌صورت رمزنگاری‌شده نگهداری می‌شود.'),
                TextInput::make('priority')->label('اولویت')->numeric()->minValue(0)->default(100)->required(),
                TextInput::make('inbound_ids')->label('Inbound IDs')->helperText('شناسه‌ها را با کاما جدا کنید؛ مثال: 1,2,3'),
                TextInput::make('region')->label('منطقه')->maxLength(80),
                Textarea::make('notes')->label('یادداشت')->rows(3),
                Toggle::make('enabled')->label('فعال')->default(true),
                TextInput::make('timeout')->label('Timeout (ثانیه)')->numeric()->minValue(1)->default(15),
                TextInput::make('connect_timeout')->label('Connect Timeout (ثانیه)')->numeric()->minValue(1)->default(5),
                TextInput::make('retry_times')->label('Retry Attempts')->numeric()->minValue(0)->default(2),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('سرور')->searchable()->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('priority')->label('اولویت')->sortable(),
                TextColumn::make('metadata.health.status')->label('Health')->badge(),
                TextColumn::make('metadata.health.latency_ms')->label('Latency')->suffix(' ms'),
                TextColumn::make('metadata.health.checked_at')->label('آخرین بررسی')->dateTime(),
            ])
            ->actions([
                Action::make('connectionTest')->label('تست اتصال')->icon(Heroicon::OutlinedSignal)
                    ->authorize(fn (ServiceProviderAccount $record): bool => auth()->user()?->can('connectionTest', $record) ?? false)
                    ->action(function (ServiceProviderAccount $record): void {
                        $result = app(SanaeiServerManager::class)->connectionTest($record);
                        Notification::make()->title($result['healthy'] ? 'اتصال موفق بود' : 'اتصال ناموفق بود')->body($result['message'])->success($result['healthy'])->danger(! $result['healthy'])->send();
                    }),
                Action::make('healthCheck')->label('Health Check')->icon(Heroicon::OutlinedHeart)
                    ->authorize(fn (ServiceProviderAccount $record): bool => auth()->user()?->can('healthCheck', $record) ?? false)
                    ->action(function (ServiceProviderAccount $record): void {
                        $result = app(SanaeiServerManager::class)->healthCheck($record);
                        Notification::make()->title($result['healthy'] ? 'سرور سالم است' : 'سرور ناسالم است')->body($result['message'])->success($result['healthy'])->danger(! $result['healthy'])->send();
                    }),
                Action::make('disable')->label('غیرفعال')->icon(Heroicon::OutlinedPause)
                    ->visible(fn (ServiceProviderAccount $record): bool => $record->status === 'active')
                    ->authorize(fn (ServiceProviderAccount $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()->action(fn (ServiceProviderAccount $record) => app(SanaeiServerManager::class)->disable($record)),
                Action::make('activate')->label('فعال')->icon(Heroicon::OutlinedPlay)
                    ->visible(fn (ServiceProviderAccount $record): bool => $record->status !== 'active')
                    ->authorize(fn (ServiceProviderAccount $record): bool => auth()->user()?->can('update', $record) ?? false)
                    ->action(fn (ServiceProviderAccount $record) => app(SanaeiServerManager::class)->activate($record)),
            ])
            ->recordUrl(fn (ServiceProviderAccount $record): string => static::getUrl('edit', ['record' => $record]));
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->whereHas('provider', fn ($query) => $query->where('driver', 'sanaei'));
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create', ServiceProviderAccount::class) ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSanaeiServers::route('/'),
            'create' => CreateSanaeiServer::route('/create'),
            'edit' => EditSanaeiServer::route('/{record}/edit'),
        ];
    }
}
