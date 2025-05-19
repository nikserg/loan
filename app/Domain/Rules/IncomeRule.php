<?php

namespace App\Domain\Rules;

use App\Domain\Contracts\LoanEligibilityRule;
use App\Models\Client;

/**
 * Rule to check if client has sufficient income
 */
readonly class IncomeRule implements LoanEligibilityRule
{
    /**
     * IncomeRule constructor.
     */
    public function __construct(
        private float $minimumIncome
    ) {
    }

    /**
     * Check if the client has sufficient income
     */
    public function isEligible(Client $client): bool
    {
        return $client->income >= $this->minimumIncome;
    }

    /**
     * Get the message for when the rule fails
     */
    public function getMessage(): string
    {
        return "Monthly income must be at least \${$this->minimumIncome}.";
    }
}
