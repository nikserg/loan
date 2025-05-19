<?php

namespace App\Domain\Services;

use App\Domain\Contracts\LoanEligibilityRule;
use App\Domain\Contracts\LoanModifierRule;
use App\Models\Client;
use App\Models\Loan;

/**
 * Service to check loan eligibility using configured rules
 */
class LoanEligibilityService
{
    /**
     * Check if client is eligible for a loan
     *
     * @param Client $client
     * @param LoanEligibilityRule[] $rules
     * @return array
     */
    public function checkEligibility(Client $client, array $rules): array
    {
        $isEligible = true;
        $failedRules = [];
        $failMessages = [];

        // Check each rule
        foreach ($rules as $rule) {
            if (!$rule->isEligible($client)) {
                $isEligible = false;
                $failedRules[] = $rule::class;
                $failMessages[] = $rule->getMessage();
            }
        }

        return [
            'eligible' => $isEligible,
            'failed_rules' => $failedRules,
            'messages' => $failMessages
        ];
    }
}
