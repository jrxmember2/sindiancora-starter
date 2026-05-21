<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Condominium;
use App\Models\Issue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IssueController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tenant/Issues/Index', [
            'issues' => Issue::query()->with('condominium:id,name')->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/Issues/Form', [
            'issue' => null,
            'condominiums' => Condominium::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Issue::create($this->validated($request) + [
            'origin' => 'interno',
            'opened_at' => now(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('issues.index')->with('success', 'Chamado criado com sucesso.');
    }

    public function edit(Issue $issue): Response
    {
        return Inertia::render('Tenant/Issues/Form', [
            'issue' => $issue,
            'condominiums' => Condominium::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Issue $issue): RedirectResponse
    {
        $issue->update($this->validated($request) + ['updated_by' => $request->user()->id]);

        return redirect()->route('issues.index')->with('success', 'Chamado atualizado.');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $issue->update(['status' => 'cancelado']);

        return back()->with('success', 'Chamado cancelado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'condominium_id' => ['required', 'exists:condominiums,id'],
            'subject' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:pendente,em_andamento,aguardando_assembleia,finalizado,cancelado'],
            'priority' => ['required', 'in:baixa,media,alta,urgente'],
            'deadline_at' => ['nullable', 'date'],
            'shared_with_residents' => ['boolean'],
        ]);
    }
}
