<?php

namespace App\Domain\Rules;

use App\Domain\Contracts\LoanEligibilityRule;
use App\Models\Client;

/**
 * Rule to randomly reject clients from a specific region
 */
readonly class RandomRejectRule implements LoanEligibilityRule
{
    /**
     * RandomRejectRule constructor.
     */
    public function __construct(
        private string $targetRegion,
        private int $rejectionChance
    ) {
    }

    /**
     * Check if the client passes random rejection
     */
    public function isEligible(Client $client): bool
    {
        // Only apply this rule to clients from the target region
        if ($client->region !== $this->targetRegion) {
            return true;
        }

        // Randomly reject based on rejection chance
        return mt_rand(1, 100) > $this->rejectionChance;
    }

    /**
     * Get the message for when the rule fails
     */
    public function getMessage(): string
    {
        return "Application was randomly rejected for clients from {$this->targetRegion} region.";
    }
}
