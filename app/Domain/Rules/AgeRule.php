<?php

namespace App\Domain\Rules;

use App\Domain\Contracts\LoanEligibilityRule;
use App\Models\Client;

/**
 * Rule to check if client age is within acceptable range
 */
readonly class AgeRule implements LoanEligibilityRule
{
    /**
     * AgeRule constructor.
     */
    public function __construct(
        private int $minimumAge,
        private int $maximumAge
    ) {
    }

    /**
     * Check if the client's age is within acceptable range
     */
    public function isEligible(Client $client): bool
    {
        return $client->age >= $this->minimumAge && $client->age <= $this->maximumAge;
    }

    /**
     * Get the message for when the rule fails
     */
    public function getMessage(): string
    {
        return "Age must be between {$this->minimumAge} and {$this->maximumAge} years.";
    }
}
