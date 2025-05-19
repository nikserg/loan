<?php

namespace App\Domain\Rules;

use App\Domain\Contracts\LoanModifierRule;
use App\Models\Client;
use App\Models\Loan;

/**
 * Rule to modify interest rate for clients from specific regions
 */
readonly class RegionalInterestRule implements LoanModifierRule
{
    /**
     * RegionalInterestRule constructor.
     */
    public function __construct(
        private string $targetRegion,
        private float $rateIncrease
    ) {
    }

    /**
     * Apply the interest rate increase to the loan if client is from the target region
     */
    public function applyModification(Client $client, Loan $loan): void
    {
        if ($client->region === $this->targetRegion) {
            $loan->rate += $this->rateIncrease;
        }
    }
}
