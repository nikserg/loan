<?php

namespace App\Contracts;

use App\Models\Client;

/**
 * Interface for loan eligibility rules
 */
interface LoanEligibilityRule
{
    /**
     * Check if the client is eligible based on this rule
     *
     * @param Client $client
     * @return bool
     */
    public function isEligible(Client $client): bool;
    
    /**
     * Get the message for when the rule fails
     *
     * @return string
     */
    public function getMessage(): string;
}