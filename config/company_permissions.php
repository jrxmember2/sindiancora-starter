<?php

return [
    'roles' => [
        'admin' => [
            'label' => 'Admin da empresa',
            'description' => 'Gerencia usuários internos, vínculos por condomínio, carteira de condomínios e configurações operacionais.',
            'abilities' => [
                'view_company_users',
                'create_company_users',
                'update_company_users',
                'deactivate_company_users',
                'assign_user_condominiums',
                'view_condominiums',
                'create_condominiums',
                'update_condominiums',
                'deactivate_condominiums',
            ],
        ],
        'gestor' => [
            'label' => 'Gestor',
            'description' => 'Opera os módulos liberados com visão ampla, sem gerenciar usuários internos.',
            'abilities' => [],
        ],
        'operacional' => [
            'label' => 'Operacional',
            'description' => 'Atua na operação diária com escopo funcional controlado.',
            'abilities' => [],
        ],
        'financeiro' => [
            'label' => 'Financeiro',
            'description' => 'Reservado para rotinas financeiras liberadas em fases futuras.',
            'abilities' => [],
        ],
    ],
];
