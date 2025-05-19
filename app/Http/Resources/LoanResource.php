<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @property int $id
 * @property int $client_id
 * @property string $name
 * @property float $amount
 * @property float $rate
 * @property string $start_date
 * @property string $end_date
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
#[OA\Schema(
    schema: "Loan",
    title: "Loan",
    description: "Loan model",
    properties: [
        new OA\Property(property: "id", type: "integer", format: "int64", description: "Loan ID", example: 1),
        new OA\Property(property: "client_id", type: "integer", format: "int64", description: "Client ID", example: 1),
        new OA\Property(property: "name", type: "string", description: "Loan name/type", example: "Personal Loan"),
        new OA\Property(property: "amount", type: "number", format: "float", description: "Loan amount in USD", example: 1000),
        new OA\Property(property: "rate", type: "number", format: "float", description: "Interest rate percentage", example: 10),
        new OA\Property(property: "start_date", type: "string", format: "date", description: "Loan start date", example: "2024-01-01"),
        new OA\Property(property: "end_date", type: "string", format: "date", description: "Loan end date", example: "2024-12-31"),
        new OA\Property(property: "status", type: "string", enum: ["pending", "approved", "rejected"], description: "Loan status", example: "approved"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", description: "Creation timestamp"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", description: "Last update timestamp")
    ]
)]
class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->resource->client_id,
            'name' => $this->name,
            'amount' => $this->amount,
            'rate' => $this->rate,
            'start_date' => $this->resource->start_date,
            'end_date' => $this->resource->end_date,
            'status' => $this->status,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'client' => new ClientResource($this->whenLoaded('client')),
        ];
    }
}