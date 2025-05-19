<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Loan Rules Configuration
    |--------------------------------------------------------------------------
    |
    | All loan rules with their parameters, grouped by rule type.
    | Each rule specifies its class, whether it's enabled, and its parameters.
    |
    */
    'rules' => [
        // Eligibility rules
        'eligibility' => [
            \App\Domain\Rules\ScoreRule::class => [
                'enabled' => true,
                'parameters' => [
                    'minimumScore' => 500
                ]
            ],
            \App\Domain\Rules\IncomeRule::class => [
                'enabled' => true,
                'parameters' => [
                    'minimumIncome' => 1000.00 // in dollars
                ]
            ],
            \App\Domain\Rules\AgeRule::class => [
                'enabled' => true,
                'parameters' => [
                    'minimumAge' => 18,
                    'maximumAge' => 60
                ]
            ],
            \App\Domain\Rules\RegionRule::class => [
                'enabled' => true,
                'parameters' => [
                    'allowedRegions' => ['PR', 'BR', 'OS'] // Prague, Brno, Ostrava
                ]
            ],
            \App\Domain\Rules\RandomRejectRule::class => [
                'enabled' => true,
                'parameters' => [
                    'targetRegion' => 'PR', // Prague
                    'rejectionChance' => 30 // Percentage chance (0-100) of rejection
                ]
            ],
            // Add more eligibility rules here
        ],
        
        // Modifier rules
        'modifiers' => [
            \App\Domain\Rules\RegionalInterestRule::class => [
                'enabled' => true,
                'parameters' => [
                    'targetRegion' => 'OS', // Ostrava
                    'rateIncrease' => 5.0 // Interest rate increase in percentage points
                ]
            ],
            // Add more modifier rules here
        ],
    ],
];