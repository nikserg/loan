<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "LoanApplicationRequest",
    title: "Loan Application Request",
    description: "Request for applying for a loan",
    required: ["name", "amount", "rate"]
)]
class LoanApplicationRequest extends FormRequest
{
    #[OA\Property(
        property: "name",
        description: "Loan name/type",
        type: "string",
        example: "Personal Loan"
    )]
    public $name;

    #[OA\Property(
        property: "amount",
        description: "Loan amount in USD",
        type: "number",
        format: "float",
        example: 1000
    )]
    public $amount;

    #[OA\Property(
        property: "rate",
        description: "Interest rate percentage",
        type: "number",
        format: "float",
        example: 10
    )]
    public $rate;

    #[OA\Property(
        property: "start_date",
        description: "Loan start date (optional, defaults to current date)",
        type: "string",
        format: "date",
        example: "2024-01-01"
    )]
    public $start_date;

    #[OA\Property(
        property: "end_date",
        description: "Loan end date (optional, defaults to one year after start date)",
        type: "string",
        format: "date",
        example: "2024-12-31"
    )]
    public $end_date;

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
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'rate' => 'required|numeric|min:0',
            'start_date' => 'sometimes|date|date_format:Y-m-d',
            'end_date' => 'sometimes|date|date_format:Y-m-d|after:start_date',
        ];
    }
}
