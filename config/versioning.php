<?php

return [
    'current' => [
        'number' => env('APP_VERSION', '0.1.0'),
        'name' => env('APP_RELEASE_NAME', 'Foundation'),
        'stage' => env('APP_RELEASE_STAGE', 'foundation'),
        'released_at' => env('APP_RELEASED_AT', '2026-05-21'),
        'build_sha' => env('APP_BUILD_SHA', env('GIT_SHA')),
        'visibility' => 'superadmin',
    ],

    'history' => [
        [
            'number' => '0.1.0',
            'name' => 'Foundation',
            'stage' => 'production',
            'released_at' => '2026-05-21',
            'visibility' => 'superadmin',
            'highlights' => [
                'Base Laravel + Inertia + React publicada com autenticacao web.',
                'CRUDs iniciais de empresas, licencas, modulos, condominios, fornecedores, documentos e chamados.',
                'Correcao de proxy e HTTPS em producao para estabilizar o login no EasyPanel.',
                'Identidade visual inicial aplicada na autenticacao e no layout interno.',
            ],
        ],
    ],
];
