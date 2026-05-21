<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['dashboard', 'Dashboard', 'Indicadores e visão executiva', 'Base'],
            ['chamados', 'Chamados', 'Gestão de chamados e ocorrências', 'Operacional'],
            ['acompanhamentos', 'Acompanhamentos', 'Timeline dos chamados', 'Operacional'],
            ['documentos', 'Documentos', 'Gestão documental do condomínio', 'Operacional'],
            ['fornecedores', 'Fornecedores', 'Cadastro central de fornecedores', 'Cadastros'],
            ['relatorios', 'Relatórios', 'Relatórios operacionais e PDFs', 'Operacional'],
            ['cronograma', 'Cronograma', 'Calendário de prazos e eventos', 'Operacional'],
            ['manutencoes', 'Manutenções', 'Manutenções preventivas e corretivas', 'Operacional'],
            ['obras', 'Obras', 'Controle de obras e projetos', 'Operacional'],
            ['pagamentos', 'Pagamentos', 'Vencimentos e controle financeiro operacional', 'Financeiro'],
            ['orcamentos', 'Orçamentos', 'Solicitação, aprovação e histórico de orçamentos', 'Financeiro'],
            ['whatsapp', 'WhatsApp', 'Atendimento e automações via WhatsApp', 'Integrações'],
            ['ia', 'IA', 'Assistente e automações inteligentes', 'Inteligência'],
            ['app_condomino', 'App do Condômino', 'Recursos para moradores', 'Aplicativo'],
            ['reservas', 'Reservas', 'Reserva de áreas comuns', 'Aplicativo'],
            ['avisos', 'Avisos', 'Comunicados para condôminos', 'Aplicativo'],
            ['pets', 'Pets', 'Cadastro de pets', 'Aplicativo'],
            ['veiculos', 'Veículos', 'Cadastro de veículos', 'Aplicativo'],
            ['consumo', 'Consumo', 'Água, gás e energia', 'Aplicativo'],
            ['controle_ferias', 'Controle de férias', 'Controle de férias da equipe', 'RH'],
            ['configuracoes', 'Configurações', 'Empresas, condomínios, usuários e preferências', 'Base'],
        ];

        foreach ($modules as [$key, $name, $description, $category]) {
            Module::updateOrCreate(
                ['key' => $key],
                compact('name', 'description', 'category') + ['active' => true]
            );
        }
    }
}
