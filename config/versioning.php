<?php

return [
    'current' => [
        'number' => env('APP_VERSION', '0.5.0'),
        'name' => env('APP_RELEASE_NAME', 'User Access Control'),
        'stage' => env('APP_RELEASE_STAGE', 'production'),
        'released_at' => env('APP_RELEASED_AT', '2026-05-21'),
        'build_sha' => env('APP_BUILD_SHA', env('GIT_SHA')),
        'visibility' => 'superadmin',
    ],

    'history' => [
        [
            'number' => '0.5.0',
            'name' => 'User Access Control',
            'stage' => 'production',
            'released_at' => '2026-05-21',
            'visibility' => 'superadmin',
            'highlights' => [
                'Gestão de usuários internos entregue com CRUD, papéis por empresa e vínculo por condomínio.',
                'Policies, gates e abilities passaram a proteger a área de usuários no menu, na rota e no backend.',
                'Limites de usuários da licença passaram a bloquear ativações acima do contratado.',
                'Logs iniciais de criação, edição e inativação de usuários internos passaram a ser persistidos.',
            ],
        ],
        [
            'number' => '0.4.0',
            'name' => 'Contract Licensing',
            'stage' => 'production',
            'released_at' => '2026-05-21',
            'visibility' => 'superadmin',
            'highlights' => [
                'Histórico de alterações de licença e snapshots de uso passaram a ser persistidos em banco.',
                'LicenseGuard ganhou leitura de status, alertas, consumo e bloqueios para storage, IA e WhatsApp.',
                'Tela Minha licença criada para a empresa acompanhar contrato, limites, módulos e alertas.',
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
                'Resolução segura da empresa ativa antes do route model binding para endurecer o tenancy.',
                'Troca de empresa limitada a vínculos ativos e empresas ativas para usuários não superadmin.',
                'Tabela user_condominiums criada para preparar o escopo por condomínio dos usuários internos.',
                'Chamados, documentos, dashboard e listagens tenant passaram a respeitar vínculos por condomínio.',
            ],
        ],
        [
            'number' => '0.2.0',
            'name' => 'Web Foundation',
            'stage' => 'production',
            'released_at' => '2026-05-21',
            'visibility' => 'superadmin',
            'highlights' => [
                'Form Requests aplicados aos fluxos principais de autenticação, superadmin e tenant.',
                'Componentes base do painel consolidados com DataTable, Drawer, Modal, ConfirmDialog e ToastRegion.',
                'Cadastros web de condomínios, fornecedores, documentos e chamados padronizados com telas dedicadas.',
                'Dashboard, navegação responsiva e feedback visual revisados para a primeira entrega comercial.',
            ],
        ],
        [
            'number' => '0.1.0',
            'name' => 'Foundation',
            'stage' => 'production',
            'released_at' => '2026-05-21',
            'visibility' => 'superadmin',
            'highlights' => [
                'Base Laravel + Inertia + React publicada com autenticação web.',
                'CRUDs iniciais de empresas, licenças, módulos, condomínios, fornecedores, documentos e chamados.',
                'Correção de proxy e HTTPS em produção para estabilizar o login no EasyPanel.',
                'Identidade visual inicial aplicada na autenticação e no layout interno.',
            ],
        ],
    ],
];
