<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\ForceCondominiumTransferRequest;
use App\Models\Company;
use App\Models\Condominium;
use App\Models\CondominiumLinkRequest;
use App\Services\Condominiums\CondominiumGovernanceManager;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CondominiumGovernanceController extends Controller
{
    public function __construct(protected CondominiumGovernanceManager $governanceManager)
    {
    }

    public function index(): Response
    {
        return Inertia::render('SuperAdmin/CondominiumGovernance/Index', [
            'requests' => CondominiumLinkRequest::query()
                ->with([
                    'condominium:id,name,document,city,state,company_id',
                    'requestingCompany:id,name',
                    'currentPrimaryCompany:id,name',
                    'requestedBy:id,name',
                    'respondedBy:id,name',
                ])
                ->latest()
                ->paginate(15)
                ->through(fn (CondominiumLinkRequest $request) => [
                    'id' => $request->id,
                    'status' => $request->status,
                    'decision_type' => $request->decision_type,
                    'created_at' => $request->created_at?->format('d/m/Y H:i'),
                    'resolved_at' => $request->resolved_at?->format('d/m/Y H:i'),
                    'request_notes' => $request->request_notes,
                    'decision_notes' => $request->decision_notes,
                    'condominium' => $request->condominium ? [
                        'id' => $request->condominium->id,
                        'name' => $request->condominium->name,
                        'document' => $request->condominium->document,
                        'city' => $request->condominium->city,
                        'state' => $request->condominium->state,
                    ] : null,
                    'requesting_company' => $request->requestingCompany?->only(['id', 'name']),
                    'current_primary_company' => $request->currentPrimaryCompany?->only(['id', 'name']),
                    'requested_by' => $request->requestedBy?->name,
                    'responded_by' => $request->respondedBy?->name,
                ]),
            'companies' => Company::query()->orderBy('name')->get(['id', 'name']),
            'condominiums' => Condominium::query()
                ->withoutGlobalScopes()
                ->orderBy('name')
                ->get(['id', 'name', 'document', 'company_id'])
                ->map(fn (Condominium $condominium) => [
                    'id' => $condominium->id,
                    'name' => $condominium->name,
                    'document' => $condominium->document,
                    'company_id' => $condominium->company_id,
                ]),
        ]);
    }

    public function forceTransfer(ForceCondominiumTransferRequest $request, Condominium $condominium): RedirectResponse
    {
        $targetCompany = Company::query()->findOrFail($request->integer('target_company_id'));

        $this->governanceManager->forceTransferToCompany(
            $condominium,
            $targetCompany,
            $request->user(),
            $request->input('decision_notes')
        );

        return back()->with('success', 'Transferência forçada aplicada pelo superadmin com sucesso.');
    }
}
