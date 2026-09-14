<?php

namespace Tests\Unit\Models;

use App\Filament\Resources\BotSetings\BotSetingResource;
use App\Models\BotSetting;
use Tests\TestCase;

final class BotSettingTest extends TestCase
{
    public function test_boolean_setting_is_detected_and_cast_correctly(): void
    {
        $setting = new BotSetting([
            'key' => 'features.registration',
            'type' => 'boolean',
            'value' => 'true',
        ]);

        self::assertTrue($setting->isBoolean());
        self::assertFalse($setting->isInteger());
        self::assertFalse($setting->isFloat());
        self::assertFalse($setting->isString());
        self::assertFalse($setting->isText());
        self::assertFalse($setting->isJson());
        self::assertTrue($setting->typedValue());
    }

    public function test_false_boolean_setting_is_really_false(): void
    {
        $setting = new BotSetting([
            'key' => 'features.channel_membership',
            'type' => 'boolean',
            'value' => 'false',
        ]);

        self::assertTrue($setting->isBoolean());
        self::assertFalse($setting->typedValue());
    }

    public function test_integer_setting_is_cast_to_integer(): void
    {
        $setting = new BotSetting([
            'key' => 'notifications.service_expiry.24_hours.hours',
            'type' => 'integer',
            'value' => '24',
        ]);

        self::assertTrue($setting->isInteger());
        self::assertSame(24, $setting->typedValue());
        self::assertIsInt($setting->typedValue());
    }

    public function test_float_setting_is_cast_to_float(): void
    {
        $setting = new BotSetting([
            'key' => 'payment.tax',
            'type' => 'float',
            'value' => '12.50',
        ]);

        self::assertTrue($setting->isFloat());
        self::assertSame(12.5, $setting->typedValue());
        self::assertIsFloat($setting->typedValue());
    }

    public function test_string_setting_keeps_string_value(): void
    {
        $setting = new BotSetting([
            'key' => 'telegram.bot_username',
            'type' => 'string',
            'value' => 'horton_bot',
        ]);

        self::assertTrue($setting->isString());
        self::assertSame('horton_bot', $setting->typedValue());
        self::assertIsString($setting->typedValue());
    }

    public function test_text_setting_keeps_text_value(): void
    {
        $value = "سلام\nبه ربات هورتون خوش آمدید.";

        $setting = new BotSetting([
            'key' => 'messages.welcome',
            'type' => 'text',
            'value' => $value,
        ]);

        self::assertTrue($setting->isText());
        self::assertSame($value, $setting->typedValue());
        self::assertIsString($setting->typedValue());
    }

    public function test_json_setting_is_decoded_to_array(): void
    {
        $setting = new BotSetting([
            'key' => 'telegram.menu',
            'type' => 'json',
            'value' => json_encode([
                'buttons' => [
                    'shop',
                    'profile',
                ],
            ], JSON_THROW_ON_ERROR),
        ]);

        self::assertTrue($setting->isJson());
        self::assertSame([
            'buttons' => [
                'shop',
                'profile',
            ],
        ], $setting->typedValue());
    }

    public function test_unknown_type_keeps_raw_value(): void
    {
        $setting = new BotSetting([
            'key' => 'custom.setting',
            'type' => 'unknown',
            'value' => 'raw-value',
        ]);

        self::assertSame('raw-value', $setting->typedValue());
    }

    public function test_resource_does_not_allow_creating_settings(): void
    {
        self::assertFalse(BotSetingResource::canCreate());
    }

    public function test_resource_contains_only_index_and_edit_pages(): void
    {
        $pages = BotSetingResource::getPages();

        self::assertArrayHasKey('index', $pages);
        self::assertArrayHasKey('edit', $pages);
        self::assertArrayNotHasKey('create', $pages);
    }
}
