<?php

namespace App\Http\Controllers\Api;

use App\Domain\Contracts\ClientRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreClientRequest;
use App\Http\Requests\Api\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

#[OA\Tag(
    name: "Clients",
    description: "API Endpoints for client management"
)]
class ClientController extends Controller
{
    /**
     * ClientController constructor.
     */
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository
    ) {
    }

    /**
     * Display a listing of clients.
     */
    #[OA\Get(
        path: "/api/clients",
        operationId: "getClientsList",
        description: "Returns list of clients",
        summary: "Get list of clients",
        tags: ["Clients"]
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
                    items: new OA\Items(ref: "#/components/schemas/Client")
                )
            ],
            type: "object"
        )
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->get('per_page', 15);
        $clients = $this->clientRepository->getAll($perPage);

        return ClientResource::collection($clients);
    }

    /**
     * Store a newly created client.
     */
    #[OA\Post(
        path: "/api/clients",
        operationId: "storeClient",
        description: "Creates a new client and returns the client data",
        summary: "Store new client",
        tags: ["Clients"]
    )]
    #[OA\RequestBody(
        description: "Client data",
        required: true,
        content: new OA\JsonContent(ref: "#/components/schemas/StoreClientRequest")
    )]
    #[OA\Response(
        response: 201,
        description: "Successful operation",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "data",
                    ref: "#/components/schemas/Client"
                ),
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "Client created successfully"
                )
            ],
            type: "object"
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Validation error",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "The given data was invalid."
                ),
                new OA\Property(
                    property: "errors",
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function store(StoreClientRequest $request): JsonResponse|JsonResource
    {
        $client = $this->clientRepository->create($request->validated());

        return (new ClientResource($client))
            ->additional(['message' => 'Client created successfully'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified client.
     */
    #[OA\Get(
        path: "/api/clients/{id}",
        operationId: "getClientById",
        description: "Returns client data",
        summary: "Get client information",
        tags: ["Clients"]
    )]
    #[OA\Parameter(
        name: "id",
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
        description: "Successful operation",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "data",
                    ref: "#/components/schemas/Client"
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
    public function show(int $id): JsonResponse|JsonResource
    {
        $client = $this->clientRepository->findById($id);

        if (!$client) {
            return response()->json([
                'message' => 'Client not found',
            ], ResponseAlias::HTTP_NOT_FOUND);
        }

        return new ClientResource($client);
    }

    /**
     * Update the specified client.
     */
    #[OA\Put(
        path: "/api/clients/{id}",
        operationId: "updateClient",
        description: "Updates a client and returns the updated data",
        summary: "Update existing client",
        tags: ["Clients"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "Client id",
        in: "path",
        required: true,
        schema: new OA\Schema(
            type: "integer",
            format: "int64"
        )
    )]
    #[OA\RequestBody(
        description: "Client data",
        required: true,
        content: new OA\JsonContent(ref: "#/components/schemas/UpdateClientRequest")
    )]
    #[OA\Response(
        response: 200,
        description: "Successful operation",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "data",
                    ref: "#/components/schemas/Client"
                ),
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "Client updated successfully"
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
    #[OA\Response(
        response: 422,
        description: "Validation error",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "The given data was invalid."
                ),
                new OA\Property(
                    property: "errors",
                    type: "object"
                )
            ],
            type: "object"
        )
    )]
    public function update(UpdateClientRequest $request, int $id): JsonResponse|JsonResource
    {
        $client = $this->clientRepository->update($id, $request->validated());

        if (!$client) {
            return response()->json([
                'message' => 'Client not found',
            ], ResponseAlias::HTTP_NOT_FOUND);
        }

        return (new ClientResource($client))
            ->additional(['message' => 'Client updated successfully']);
    }

    /**
     * Remove the specified client.
     */
    #[OA\Delete(
        path: "/api/clients/{id}",
        operationId: "deleteClient",
        description: "Deletes a client and returns a confirmation message",
        summary: "Delete existing client",
        tags: ["Clients"]
    )]
    #[OA\Parameter(
        name: "id",
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
        description: "Successful operation",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "Client deleted successfully"
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
    public function destroy(int $id): JsonResponse
    {
        $result = $this->clientRepository->delete($id);

        if (!$result) {
            return response()->json([
                'message' => 'Client not found',
            ], ResponseAlias::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Client deleted successfully',
        ]);
    }
}
