<?php

declare(strict_types=1);

return [
    'providers' => [
        'default_selection' => 'priority',
        'drivers' => [
            'fake' => App\Services\Providers\FakeServiceProvider::class,
            'sanaei' => App\Services\Providers\Sanaei\SanaeiProvider::class,
            // 'marzban' => App\Services\Providers\Marzban\MarzbanServiceProvider::class,
        ],
    ],
];
