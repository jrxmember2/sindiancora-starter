<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DocumentRequest;
use App\Models\Condominium;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tenant/Documents/Index', [
            'items' => Document::query()->with('condominium:id,name')->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/Documents/Form', [
            'item' => null,
            'condominiums' => Condominium::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(DocumentRequest $request): RedirectResponse
    {
        Document::create($request->validated() + ['created_by' => $request->user()->id]);

        return redirect()->route('documents.index')->with('success', 'Documento criado com sucesso.');
    }

    public function edit(Document $document): Response
    {
        return Inertia::render('Tenant/Documents/Form', [
            'item' => $document,
            'condominiums' => Condominium::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(DocumentRequest $request, Document $document): RedirectResponse
    {
        $document->update($request->validated());

        return redirect()->route('documents.index')->with('success', 'Documento atualizado.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $document->delete();

        return back()->with('success', 'Documento removido.');
    }
}
