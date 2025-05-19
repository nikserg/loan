<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @property int $id
 * @property string $name
 * @property int $age
 * @property string $city
 * @property string $region
 * @property float $income
 * @property int $score
 * @property string $pin
 * @property string $email
 * @property string $phone
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
#[OA\Schema(
    schema: "Client",
    title: "Client",
    description: "Client model",
    properties: [
        new OA\Property(property: "id", type: "integer", format: "int64", description: "Client ID", example: 1),
        new OA\Property(property: "name", type: "string", description: "Client name", example: "Petr Pavel"),
        new OA\Property(property: "age", type: "integer", description: "Client age", example: 35),
        new OA\Property(property: "city", type: "string", description: "Client city", example: "Prague"),
        new OA\Property(property: "region", type: "string", description: "Client region code", example: "PR"),
        new OA\Property(property: "income", type: "number", format: "float", description: "Client monthly income in USD", example: 1500),
        new OA\Property(property: "score", type: "integer", description: "Client credit score", example: 600),
        new OA\Property(property: "pin", type: "string", description: "Client personal identification number", example: "123-45-6789"),
        new OA\Property(property: "email", type: "string", format: "email", description: "Client email address", example: "petr.pavel@example.com"),
        new OA\Property(property: "phone", type: "string", description: "Client phone number", example: "+420123456789"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", description: "Creation timestamp"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", description: "Last update timestamp")
    ]
)]
class ClientResource extends JsonResource
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
            'name' => $this->name,
            'age' => $this->age,
            'city' => $this->city,
            'region' => $this->region,
            'income' => $this->income,
            'score' => $this->score,
            'pin' => $this->pin,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'loans' => LoanResource::collection($this->whenLoaded('loans')),
        ];
    }
}