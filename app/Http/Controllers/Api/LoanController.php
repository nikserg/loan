<?php

namespace App\Http\Controllers\Api;

use App\Domain\Contracts\ClientRepositoryInterface;
use App\Domain\Contracts\LoanRepositoryInterface;
use App\Domain\Services\LoanApplicationService;
use App\Domain\Services\LoanEligibilityService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoanApplicationRequest;
use App\Http\Resources\LoanResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

#[OA\Tag(
    name: "Loans",
    description: "API Endpoints for loan management"
)]
class LoanController extends Controller
{
    /**
     * LoanController constructor.
     */
    public function __construct(
        private readonly LoanRepositoryInterface $loanRepository,
        private readonly LoanApplicationService $loanApplicationService,
        private readonly LoanEligibilityService $loanEligibilityService
    ) {
    }

    /**
     * Display a listing of loans.
     */
    #[OA\Get(
        path: "/api/loans",
        operationId: "getLoansList",
        description: "Returns list of all loans",
        summary: "Get list of loans",
        tags: ["Loans"]
    )]
    #[OA\Parameter(
        name: "per_page",
        description: "Number of items per page",
        in: "query",
        required: false,
        schema: new OA\Schema(
            type: "integer",
            default: 15
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Successful operation",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "data",
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Loan")
                )
            ],
            type: "object"
        )
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->get('per_page', 15);
        $loans = $this->loanRepository->getAll($perPage);

        return LoanResource::collection($loans);
    }

    /**
     * Display loans for a specific client.
     */
    #[OA\Get(
        path: "/api/clients/{clientId}/loans",
        operationId: "getClientLoans",
        description: "Returns list of loans for a specific client",
        summary: "Get client's loans",
        tags: ["Loans"]
    )]
    #[OA\Parameter(
        name: "clientId",
        description: "Client id",
        in: "path",
        required: true,
        schema: new OA\Schema(
            type: "integer",
            format: "int64"
        )
    )]
    #[OA\Parameter(
        name: "per_page",
        description: "Number of items per page",
        in: "query",
        required: false,
        schema: new OA\Schema(
            type: "integer",
            default: 15
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Successful operation",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "data",
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Loan")
                )
            ],
            type: "object"
        )
    )]
    public function clientLoans(Request $request, int $clientId): AnonymousResourceCollection
    {
        $perPage = $request->get('per_page', 15);
        $loans = $this->loanRepository->getByClientId($clientId, $perPage);

        return LoanResource::collection($loans);
    }

    /**
     * Process a loan application for a client.
     */
    #[OA\Post(
        path: "/api/clients/{clientId}/loans",
        operationId: "applyForLoan",
        description: "Process a loan application for a client",
        summary: "Apply for a loan",
        tags: ["Loans"]
    )]
    #[OA\Parameter(
        name: "clientId",
        description: "Client id",
        in: "path",
        required: true,
        schema: new OA\Schema(
            type: "integer",
            format: "int64"
        )
    )]
    #[OA\RequestBody(
        description: "Loan application data",
        required: true,
        content: new OA\JsonContent(ref: "#/components/schemas/LoanApplicationRequest")
    )]
    #[OA\Response(
        response: 201,
        description: "Loan approved",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "data",
                    ref: "#/components/schemas/Loan"
                ),
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "Loan application approved"
                )
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Loan application rejected",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "Loan application rejected"
                ),
                new OA\Property(
                    property: "reasons",
                    type: "array",
                    items: new OA\Items(
                        type: "string"
                    ),
                    example: ["Credit score must be greater than 500", "Income must be at least $1000"]
                )
            ],
            type: "object"
        )
    )]
    public function apply(LoanApplicationRequest $request, int $clientId): JsonResponse|JsonResource
    {
        $eligibilityRules = app('loan.eligibility_rules');
        $modifierRules = app('loan.modifier_rules');

        $result = $this->loanApplicationService->processApplication(
            $clientId,
            $request->validated(),
            $eligibilityRules,
            $modifierRules
        );

        if (!$result['success']) {
            return response()->json([
                'message' => $result['message'],
                'reasons' => $result['reasons'] ?? [],
            ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        return (new LoanResource($result['loan']))
            ->additional(['message' => $result['message']])
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    /**
     * Check if a client is eligible for a loan without creating one.
     */
    #[OA\Get(
        path: "/api/clients/{clientId}/eligibility",
        operationId: "checkLoanEligibility",
        description: "Check if a client is eligible for a loan without creating one",
        summary: "Check loan eligibility",
        tags: ["Loans"]
    )]
    #[OA\Parameter(
        name: "clientId",
        description: "Client id",
        in: "path",
        required: true,
        schema: new OA\Schema(
            type: "integer",
            format: "int64"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Eligibility check result",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "eligible",
                    type: "boolean",
                    example: true
                ),
                new OA\Property(
                    property: "messages",
                    type: "array",
                    items: new OA\Items(
                        type: "string"
                    ),
                    example: []
                )
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Client not found",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "Client not found"
                )
            ],
            type: "object"
        )
    )]
    public function checkEligibility(int $clientId): JsonResponse
    {
        $eligibilityRules = app('loan.eligibility_rules');
        $client = app(ClientRepositoryInterface::class)->findById($clientId);

        if (!$client) {
            return response()->json([
                'message' => 'Client not found',
            ], ResponseAlias::HTTP_NOT_FOUND);
        }

        $result = $this->loanEligibilityService->checkEligibility($client, $eligibilityRules);

        return response()->json([
            'eligible' => $result['eligible'],
            'messages' => $result['messages'],
        ]);
    }

    /**
     * Display the specified loan.
     */
    #[OA\Get(
        path: "/api/loans/{id}",
        operationId: "getLoanById",
        description: "Returns loan data",
        summary: "Get loan information",
        tags: ["Loans"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "Loan id",
        in: "path",
        required: true,
        schema: new OA\Schema(
            type: "integer",
            format: "int64"
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Successful operation",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "data",
                    ref: "#/components/schemas/Loan"
                )
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Loan not found",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "Loan not found"
                )
            ],
            type: "object"
        )
    )]
    public function show(int $id): JsonResponse|JsonResource
    {
        $loan = $this->loanRepository->findById($id);

        if (!$loan) {
            return response()->json([
                'message' => 'Loan not found',
            ], ResponseAlias::HTTP_NOT_FOUND);
        }

        return new LoanResource($loan);
    }
}
