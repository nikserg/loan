<?php

namespace App\Domain\Rules;

use App\Domain\Contracts\LoanEligibilityRule;
use App\Models\Client;

/**
 * Rule to check if client has sufficient credit score
 */
readonly class ScoreRule implements LoanEligibilityRule
{
    /**
     * ScoreRule constructor.
     */
    public function __construct(
        private int $minimumScore
    ) {
    }

    /**
     * Check if the client has a sufficient credit score
     */
    public function isEligible(Client $client): bool
    {
        return $client->score > $this->minimumScore;
    }

    /**
     * Get the message for when the rule fails
     */
    public function getMessage(): string
    {
        return "Credit score must be greater than {$this->minimumScore}.";
    }
}
