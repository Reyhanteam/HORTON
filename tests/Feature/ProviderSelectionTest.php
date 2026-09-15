<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\ProviderSelectorContract;
use App\Enums\ServiceProviderOperation;
use App\Exceptions\ServiceProviderException;
use App\Models\Plan;
use App\Models\ProviderCapability;
use App\Models\ProviderPolicy;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderAccount;
use App\Models\TelegramAccount;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class ProviderSelectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();
        foreach (['services', 'provider_capabilities', 'provider_policies', 'service_provider_accounts', 'service_providers'] as $table) Schema::dropIfExists($table);
        Schema::create('service_providers', function (Blueprint $t): void {
            $t->id(); $t->string('name'); $t->string('slug'); $t->string('driver'); $t->string('status')->default('active');
            $t->string('region')->nullable(); $t->unsignedInteger('priority')->default(100); $t->unsignedInteger('max_users')->nullable();
            $t->unsignedInteger('max_services')->nullable(); $t->string('health_status')->default('unknown'); $t->timestamps();
        });
        Schema::create('service_provider_accounts', function (Blueprint $t): void {
            $t->id(); $t->unsignedBigInteger('service_provider_id'); $t->string('name'); $t->string('status')->default('active'); $t->unsignedInteger('priority')->default(100); $t->timestamps();
        });
        Schema::create('services', function (Blueprint $t): void { $t->id(); $t->unsignedBigInteger('service_provider_id'); $t->unsignedBigInteger('telegram_account_id'); $t->string('status')->default('active'); $t->timestamps(); });
        Schema::create('provider_capabilities', function (Blueprint $t): void { $t->id(); $t->unsignedBigInteger('service_provider_id'); $t->string('operation'); $t->boolean('supported')->default(true); $t->timestamps(); });
        Schema::create('provider_policies', function (Blueprint $t): void {
            $t->id(); $t->unsignedBigInteger('service_provider_id'); $t->boolean('allow_create')->default(true); $t->boolean('allow_renew')->default(true);
            $t->boolean('allow_extend')->default(true); $t->boolean('allow_add_capacity')->default(true); $t->boolean('allow_disable')->default(true); $t->boolean('allow_delete')->default(true);
            $t->unsignedInteger('max_services_per_user')->nullable(); $t->unsignedInteger('max_capacity_per_service')->nullable(); $t->unsignedInteger('max_duration_days')->nullable(); $t->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    protected function tearDown(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach (['services', 'provider_capabilities', 'provider_policies', 'service_provider_accounts', 'service_providers'] as $table) Schema::dropIfExists($table);
        Schema::enableForeignKeyConstraints(); parent::tearDown();
    }

    private function account(): TelegramAccount { return new TelegramAccount(['id' => 1]); }
    private function plan(): Plan { return new Plan(['id' => 2, 'duration_value' => 30, 'capacity_value' => 5]); }

    public function test_selects_lowest_priority_active_provider(): void
    { ServiceProvider::create(['name'=>'P2','slug'=>'p2','driver'=>'fake','priority'=>20]); $p1=ServiceProvider::create(['name'=>'P1','slug'=>'p1','driver'=>'fake','priority'=>10]); $this->assertSame($p1->id, app(ProviderSelectorContract::class)->select($this->account(),$this->plan())->provider->id); }

    public function test_full_provider_is_skipped(): void
    { $full=ServiceProvider::create(['name'=>'Full','slug'=>'full','driver'=>'fake','priority'=>1,'max_services'=>1]); $available=ServiceProvider::create(['name'=>'Available','slug'=>'available','driver'=>'fake','priority'=>2]); Service::create(['service_provider_id'=>$full->id,'telegram_account_id'=>99]); $this->assertSame($available->id, app(ProviderSelectorContract::class)->select($this->account(),$this->plan())->provider->id); }

    public function test_inactive_provider_is_skipped(): void
    { ServiceProvider::create(['name'=>'Disabled','slug'=>'disabled','driver'=>'fake','priority'=>1,'status'=>'disabled']); $active=ServiceProvider::create(['name'=>'Active','slug'=>'active','driver'=>'fake','priority'=>2]); $this->assertSame($active->id, app(ProviderSelectorContract::class)->select($this->account(),$this->plan())->provider->id); }

    public function test_region_filter_is_respected(): void
    { ServiceProvider::create(['name'=>'DE','slug'=>'de','driver'=>'fake','priority'=>1,'region'=>'de']); $us=ServiceProvider::create(['name'=>'US','slug'=>'us','driver'=>'fake','priority'=>2,'region'=>'us']); $this->assertSame($us->id, app(ProviderSelectorContract::class)->select($this->account(),$this->plan(),ServiceProviderOperation::CREATE,'us')->provider->id); }

    public function test_unsupported_operation_is_skipped(): void
    { $blocked=ServiceProvider::create(['name'=>'Blocked','slug'=>'blocked','driver'=>'fake','priority'=>1]); ProviderCapability::create(['service_provider_id'=>$blocked->id,'operation'=>'create','supported'=>false]); $allowed=ServiceProvider::create(['name'=>'Allowed','slug'=>'allowed','driver'=>'fake','priority'=>2]); $this->assertSame($allowed->id, app(ProviderSelectorContract::class)->select($this->account(),$this->plan())->provider->id); }

    public function test_disabled_create_policy_is_skipped(): void
    { $blocked=ServiceProvider::create(['name'=>'Blocked','slug'=>'blocked','driver'=>'fake','priority'=>1]); ProviderPolicy::create(['service_provider_id'=>$blocked->id,'allow_create'=>false]); $allowed=ServiceProvider::create(['name'=>'Allowed','slug'=>'allowed','driver'=>'fake','priority'=>2]); $this->assertSame($allowed->id, app(ProviderSelectorContract::class)->select($this->account(),$this->plan())->provider->id); }

    public function test_max_services_per_user_is_enforced(): void
    { $blocked=ServiceProvider::create(['name'=>'Blocked','slug'=>'blocked','driver'=>'fake','priority'=>1]); ProviderPolicy::create(['service_provider_id'=>$blocked->id,'max_services_per_user'=>1]); Service::create(['service_provider_id'=>$blocked->id,'telegram_account_id'=>1]); $allowed=ServiceProvider::create(['name'=>'Allowed','slug'=>'allowed','driver'=>'fake','priority'=>2]); $this->assertSame($allowed->id, app(ProviderSelectorContract::class)->select($this->account(),$this->plan())->provider->id); }

    public function test_max_capacity_policy_is_enforced(): void
    { $blocked=ServiceProvider::create(['name'=>'Blocked','slug'=>'blocked','driver'=>'fake','priority'=>1]); ProviderPolicy::create(['service_provider_id'=>$blocked->id,'max_capacity_per_service'=>2]); $allowed=ServiceProvider::create(['name'=>'Allowed','slug'=>'allowed','driver'=>'fake','priority'=>2]); $this->assertSame($allowed->id, app(ProviderSelectorContract::class)->select($this->account(),$this->plan())->provider->id); }

    public function test_account_priority_is_respected(): void
    { $provider=ServiceProvider::create(['name'=>'P','slug'=>'p','driver'=>'fake']); ServiceProviderAccount::create(['service_provider_id'=>$provider->id,'name'=>'A2','priority'=>20]); $a1=ServiceProviderAccount::create(['service_provider_id'=>$provider->id,'name'=>'A1','priority'=>10]); $this->assertSame($a1->id, app(ProviderSelectorContract::class)->select($this->account(),$this->plan())->account?->id); }

    public function test_no_eligible_provider_throws(): void
    { ServiceProvider::create(['name'=>'Disabled','slug'=>'disabled','driver'=>'fake','status'=>'disabled']); $this->expectException(ServiceProviderException::class); app(ProviderSelectorContract::class)->select($this->account(),$this->plan()); }
}
