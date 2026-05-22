<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\IssueRequest;
use App\Models\Issue;
use App\Services\Tenancy\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IssueController extends Controller
{
    public function index(): Response
    {
        $tenantResolver = app(TenantResolver::class);
        $company = app('currentCompany');
        $user = request()->user();
        $query = $tenantResolver->scopeByAccessibleCondominiums(Issue::query(), $user, $company);
        $query = $tenantResolver->scopeByIssueAssignments($query, $user, $company);

        return Inertia::render('Tenant/Issues/Index', [
            'issues' => $query
                ->with('condominium:id,name')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): Response
    {
        $tenantResolver = app(TenantResolver::class);
        $company = app('currentCompany');

        return Inertia::render('Tenant/Issues/Form', [
            'issue' => null,
            'condominiums' => $tenantResolver
                ->accessibleCondominiumsQuery(request()->user(), $company)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(IssueRequest $request): RedirectResponse
    {
        $companyUser = app(TenantResolver::class)->currentCompanyUser($request->user(), app('currentCompany'));

        Issue::create($request->validated() + [
            'origin' => 'interno',
            'opened_at' => now(),
            'created_by' => $request->user()->id,
            'responsible_user_id' => $companyUser?->only_responsible_issues ? $request->user()->id : null,
        ]);

        return redirect()->route('issues.index')->with('success', 'Chamado criado com sucesso.');
    }

    public function edit(Issue $issue): Response
    {
        abort_unless(
            app(TenantResolver::class)->canAccessIssue(request()->user(), app('currentCompany'), $issue),
            404
        );

        $tenantResolver = app(TenantResolver::class);
        $company = app('currentCompany');

        return Inertia::render('Tenant/Issues/Form', [
            'issue' => $issue,
            'condominiums' => $tenantResolver
                ->accessibleCondominiumsQuery(request()->user(), $company)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function update(IssueRequest $request, Issue $issue): RedirectResponse
    {
        abort_unless(
            app(TenantResolver::class)->canAccessIssue($request->user(), app('currentCompany'), $issue),
            404
        );

        $issue->update($request->validated() + ['updated_by' => $request->user()->id]);

        return redirect()->route('issues.index')->with('success', 'Chamado atualizado.');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        abort_unless(
            app(TenantResolver::class)->canAccessIssue(request()->user(), app('currentCompany'), $issue),
            404
        );

        $issue->update(['status' => 'cancelado']);

        return back()->with('success', 'Chamado cancelado.');
    }
}
