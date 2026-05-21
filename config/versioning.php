<?php

return [
    'current' => [
        'number' => env('APP_VERSION', '0.4.0'),
        'name' => env('APP_RELEASE_NAME', 'Contract Licensing'),
        'stage' => env('APP_RELEASE_STAGE', 'production'),
        'released_at' => env('APP_RELEASED_AT', '2026-05-21'),
        'build_sha' => env('APP_BUILD_SHA', env('GIT_SHA')),
        'visibility' => 'superadmin',
    ],

    'history' => [
        [
            'number' => '0.4.0',
            'name' => 'Contract Licensing',
            'stage' => 'production',
            'released_at' => '2026-05-21',
            'visibility' => 'superadmin',
            'highlights' => [
                'Historico de alteracoes de licenca e snapshots de uso passaram a ser persistidos em banco.',
                'LicenseGuard ganhou leitura de status, alertas, consumo e bloqueios para storage, IA e WhatsApp.',
                'Tela Minha licenca criada para a empresa acompanhar contrato, limites, modulos e alertas.',
                'Modo somente leitura e bloqueio contratual passaram a ser aplicados nas rotas operacionais.',
            ],
        ],
        [
            'number' => '0.3.0',
            'name' => 'Tenant Hardening',
            'stage' => 'production',
            'released_at' => '2026-05-21',
            'visibility' => 'superadmin',
            'highlights' => [
                'Resolucao segura da empresa ativa antes do route model binding para endurecer o tenancy.',
                'Troca de empresa limitada a vinculos ativos e empresas ativas para usuarios nao superadmin.',
                'Tabela user_condominiums criada para preparar o escopo por condominio dos usuarios internos.',
                'Chamados, documentos, dashboard e listagens tenant passaram a respeitar vinculos por condominio.',
            ],
        ],
        [
            'number' => '0.2.0',
            'name' => 'Web Foundation',
            'stage' => 'production',
            'released_at' => '2026-05-21',
            'visibility' => 'superadmin',
            'highlights' => [
                'Form Requests aplicados aos fluxos principais de autenticacao, superadmin e tenant.',
                'Componentes base do painel consolidados com DataTable, Drawer, Modal, ConfirmDialog e ToastRegion.',
                'Cadastros web de condominios, fornecedores, documentos e chamados padronizados com telas dedicadas.',
                'Dashboard, navegacao responsiva e feedback visual revisados para a primeira entrega comercial.',
            ],
        ],
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
