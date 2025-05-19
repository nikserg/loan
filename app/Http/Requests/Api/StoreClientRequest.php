<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreClientRequest",
    title: "Store Client Request",
    description: "Request for creating a new client",
    required: ["name", "age", "city", "region", "income", "score", "pin", "email", "phone"]
)]
class StoreClientRequest extends FormRequest
{
    #[OA\Property(
        property: "name",
        description: "Client name",
        type: "string",
        example: "Petr Pavel"
    )]
    public string $name;

    #[OA\Property(
        property: "age",
        description: "Client age",
        type: "integer",
        example: 35
    )]
    public int $age;

    #[OA\Property(
        property: "city",
        description: "Client city",
        type: "string",
        example: "Prague"
    )]
    public string $city;

    #[OA\Property(
        property: "region",
        description: "Client region code (2 characters)",
        type: "string",
        example: "PR"
    )]
    public string $region;

    #[OA\Property(
        property: "income",
        description: "Client monthly income in USD",
        type: "number",
        format: "float",
        example: 1500
    )]
    public float $income;

    #[OA\Property(
        property: "score",
        description: "Client credit score",
        type: "integer",
        example: 600
    )]
    public int $score;

    #[OA\Property(
        property: "pin",
        description: "Client personal identification number (must be unique)",
        type: "string",
        example: "123-45-6789"
    )]
    public string $pin;

    #[OA\Property(
        property: "email",
        description: "Client email address (must be unique)",
        type: "string",
        format: "email",
        example: "petr.pavel@example.com"
    )]
    public string $email;

    #[OA\Property(
        property: "phone",
        description: "Client phone number",
        type: "string",
        example: "+420123456789"
    )]
    public string $phone;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:255',
            'age'    => 'required|integer|min:0|max:120',
            'city'   => 'required|string|max:255',
            'region' => 'required|string|size:2',
            'income' => 'required|numeric|min:0',
            'score'  => 'required|integer|min:0',
            'pin'    => 'required|string|max:20|unique:clients',
            'email'  => 'required|email|max:255|unique:clients',
            'phone'  => 'required|string|max:20',
        ];
    }
}
