<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DocumentRequest;
use App\Models\Condominium;
use App\Models\Document;
use App\Services\Tenancy\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function index(): Response
    {
        $tenantResolver = app(TenantResolver::class);
        $company = app('currentCompany');

        return Inertia::render('Tenant/Documents/Index', [
            'items' => $tenantResolver
                ->scopeByAccessibleCondominiums(Document::query()->withoutGlobalScopes(), request()->user(), $company, includeNull: true)
                ->with('condominium:id,name')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): Response
    {
        $tenantResolver = app(TenantResolver::class);
        $company = app('currentCompany');

        return Inertia::render('Tenant/Documents/Form', [
            'item' => null,
            'condominiums' => $tenantResolver
                ->accessibleCondominiumsQuery(request()->user(), $company)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(DocumentRequest $request): RedirectResponse
    {
        Document::create($request->validated() + [
            'company_id' => app('currentCompany')->id,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('documents.index')->with('success', 'Documento criado com sucesso.');
    }

    public function edit(Document $document): Response
    {
        abort_unless(
            app(TenantResolver::class)->canAccessCondominium(request()->user(), app('currentCompany'), $document->condominium_id),
            404
        );

        $tenantResolver = app(TenantResolver::class);
        $company = app('currentCompany');

        return Inertia::render('Tenant/Documents/Form', [
            'item' => $document,
            'condominiums' => $tenantResolver
                ->accessibleCondominiumsQuery(request()->user(), $company)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function update(DocumentRequest $request, Document $document): RedirectResponse
    {
        abort_unless(
            app(TenantResolver::class)->canAccessCondominium($request->user(), app('currentCompany'), $document->condominium_id),
            404
        );

        $document->update($request->validated());

        return redirect()->route('documents.index')->with('success', 'Documento atualizado.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        abort_unless(
            app(TenantResolver::class)->canAccessCondominium(request()->user(), app('currentCompany'), $document->condominium_id),
            404
        );

        $document->delete();

        return back()->with('success', 'Documento removido.');
    }
}
