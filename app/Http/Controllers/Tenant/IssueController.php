<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\IssueRequest;
use App\Models\Condominium;
use App\Models\Issue;
use Illuminate\Http\RedirectResponse;
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

    public function store(IssueRequest $request): RedirectResponse
    {
        Issue::create($request->validated() + [
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

    public function update(IssueRequest $request, Issue $issue): RedirectResponse
    {
        $issue->update($request->validated() + ['updated_by' => $request->user()->id]);

        return redirect()->route('issues.index')->with('success', 'Chamado atualizado.');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $issue->update(['status' => 'cancelado']);

        return back()->with('success', 'Chamado cancelado.');
    }
}
