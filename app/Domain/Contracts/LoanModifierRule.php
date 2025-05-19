<?php

namespace App\Domain\Contracts;

use App\Models\Client;
use App\Models\Loan;

/**
 * Interface for rules that can modify loan terms
 */
interface LoanModifierRule
{
    /**
     * Apply modifications to the loan based on client information
     */
    public function applyModification(Client $client, Loan $loan): void;
}