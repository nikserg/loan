<?php

namespace App\Domain\Rules;

use App\Domain\Contracts\LoanEligibilityRule;
use App\Models\Client;

/**
 * Rule to check if client is from an eligible region
 */
readonly class RegionRule implements LoanEligibilityRule
{
    /**
     * RegionRule constructor.
     */
    public function __construct(
        private array $allowedRegions
    ) {
    }

    /**
     * Check if the client is from an eligible region
     */
    public function isEligible(Client $client): bool
    {
        return in_array($client->region, $this->allowedRegions);
    }

    /**
     * Get the message for when the rule fails
     */
    public function getMessage(): string
    {
        return "Client must be from one of these regions: " . implode(', ', $this->allowedRegions) . ".";
    }
}
