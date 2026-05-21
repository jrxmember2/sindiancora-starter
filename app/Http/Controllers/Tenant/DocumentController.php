<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Condominium;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tenant/Generic/Index', [
            'title' => 'Documentos',
            'description' => 'Organize convenções, regimentos, atas, contratos e demais arquivos.',
            'items' => Document::query()->with('condominium:id,name')->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/Generic/Form', [
            'title' => 'Novo documento',
            'item' => null,
            'condominiums' => Condominium::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'fields' => ['title', 'document_type', 'status'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Document::create($this->validated($request) + ['created_by' => $request->user()->id]);

        return redirect()->route('documents.index')->with('success', 'Documento criado com sucesso.');
    }

    public function edit(Document $document): Response
    {
        return Inertia::render('Tenant/Generic/Form', [
            'title' => 'Editar documento',
            'item' => $document,
            'condominiums' => Condominium::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'fields' => ['title', 'document_type', 'status'],
        ]);
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $document->update($this->validated($request));

        return redirect()->route('documents.index')->with('success', 'Documento atualizado.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $document->delete();

        return back()->with('success', 'Documento removido.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'condominium_id' => ['nullable', 'integer', 'exists:condominiums,id'],
            'title' => ['required', 'string', 'max:180'],
            'document_type' => ['nullable', 'string', 'max:80'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'valid_until' => ['nullable', 'date'],
            'renewal_date' => ['nullable', 'date'],
            'status' => ['required', 'in:valido,vencido,proximo_vencimento,sem_vigencia'],
            'available_to_residents' => ['boolean'],
            'added_to_ai_assistant' => ['boolean'],
            'observation' => ['nullable', 'string'],
            'file_path' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
