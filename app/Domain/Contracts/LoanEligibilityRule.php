<?php

namespace App\Domain\Contracts;

use App\Models\Client;

/**
 * Interface for loan eligibility rules
 */
interface LoanEligibilityRule
{
    /**
     * Check if the client is eligible based on this rule
     */
    public function isEligible(Client $client): bool;
    
    /**
     * Get the message for when the rule fails
     */
    public function getMessage(): string;
}