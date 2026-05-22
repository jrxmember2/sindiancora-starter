<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CondominiumLinkDecisionRequest;
use App\Models\CondominiumLinkRequest;
use App\Notifications\CondominiumLinkWorkflowNotification;
use App\Services\Condominiums\CondominiumGovernanceManager;
use App\Services\Tenancy\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CondominiumLinkRequestController extends Controller
{
    public function __construct(
        protected CondominiumGovernanceManager $governanceManager,
        protected TenantResolver $tenantResolver,
    ) {
    }

    public function index(Request $request): Response
    {
        $company = app('currentCompany');
        $membership = $this->tenantResolver->currentCompanyUser($request->user(), $company);

        abort_unless($membership?->isPrimaryAdmin(), 403);

        $request->user()
            ->unreadNotifications()
            ->where('type', CondominiumLinkWorkflowNotification::class)
            ->update(['read_at' => now()]);

        return Inertia::render('Tenant/CondominiumLinks/Index', [
            'incoming' => CondominiumLinkRequest::query()
                ->with(['condominium:id,name,document,city,state', 'requestingCompany:id,name', 'requestedBy:id,name'])
                ->where('current_primary_company_id', $company->id)
                ->latest()
                ->get()
                ->map(fn (CondominiumLinkRequest $linkRequest) => $this->present($linkRequest)),
            'outgoing' => CondominiumLinkRequest::query()
                ->with(['condominium:id,name,document,city,state', 'currentPrimaryCompany:id,name', 'requestedBy:id,name'])
                ->where('requesting_company_id', $company->id)
                ->latest()
                ->get()
                ->map(fn (CondominiumLinkRequest $linkRequest) => $this->present($linkRequest)),
        ]);
    }

    public function decide(CondominiumLinkDecisionRequest $request, CondominiumLinkRequest $condominiumLinkRequest): RedirectResponse
    {
        $company = app('currentCompany');
        $membership = $this->tenantResolver->currentCompanyUser($request->user(), $company);

        abort_unless($membership?->isPrimaryAdmin(), 403);
        abort_unless((int) $condominiumLinkRequest->current_primary_company_id === (int) $company->id, 404);

        $decision = $request->string('decision')->toString();
        $notes = $request->input('decision_notes');

        match ($decision) {
            'share' => $this->governanceManager->share($condominiumLinkRequest, $request->user(), $notes),
            'transfer' => $this->governanceManager->transfer($condominiumLinkRequest, $request->user(), $notes),
            'reject' => $this->governanceManager->reject($condominiumLinkRequest, $request->user(), $notes),
        };

        return back()->with('success', 'Solicitação de condomínio processada com sucesso.');
    }

    protected function present(CondominiumLinkRequest $request): array
    {
        return [
            'id' => $request->id,
            'status' => $request->status,
            'decision_type' => $request->decision_type,
            'request_notes' => $request->request_notes,
            'decision_notes' => $request->decision_notes,
            'resolved_at' => $request->resolved_at?->format('d/m/Y H:i'),
            'created_at' => $request->created_at?->format('d/m/Y H:i'),
            'condominium' => [
                'id' => $request->condominium?->id,
                'name' => $request->condominium?->name,
                'document' => $request->condominium?->document,
                'city' => $request->condominium?->city,
                'state' => $request->condominium?->state,
            ],
            'requesting_company' => $request->requestingCompany ? [
                'id' => $request->requestingCompany->id,
                'name' => $request->requestingCompany->name,
            ] : null,
            'current_primary_company' => $request->currentPrimaryCompany ? [
                'id' => $request->currentPrimaryCompany->id,
                'name' => $request->currentPrimaryCompany->name,
            ] : null,
            'requested_by' => $request->requestedBy?->name,
        ];
    }
}
