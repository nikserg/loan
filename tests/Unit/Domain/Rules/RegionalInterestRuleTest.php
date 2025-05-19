<?php

namespace Tests\Unit\Domain\Rules;

use App\Domain\Rules\RegionalInterestRule;
use App\Models\Client;
use App\Models\Loan;
use Tests\TestCase;

class RegionalInterestRuleTest extends TestCase
{
    /**
     * Test interest rate is increased for client from target region
     */
    public function test_interest_rate_is_increased_for_client_from_target_region(): void
    {
        // Arrange
        $targetRegion = 'PR';
        $rateIncrease = 2.5;
        $regionalInterestRule = new RegionalInterestRule($targetRegion, $rateIncrease);
        
        $client = new Client();
        $client->region = $targetRegion;
        
        $loan = new Loan();
        $loan->rate = 10.0;
        $expectedRate = $loan->rate + $rateIncrease;
        
        // Act
        $regionalInterestRule->applyModification($client, $loan);
        
        // Assert
        $this->assertEquals($expectedRate, $loan->rate);
    }
    
    /**
     * Test interest rate is not changed for client from non-target region
     */
    public function test_interest_rate_is_not_changed_for_client_from_non_target_region(): void
    {
        // Arrange
        $targetRegion = 'PR';
        $rateIncrease = 2.5;
        $regionalInterestRule = new RegionalInterestRule($targetRegion, $rateIncrease);
        
        $client = new Client();
        $client->region = 'BR'; // Different region
        
        $loan = new Loan();
        $originalRate = 10.0;
        $loan->rate = $originalRate;
        
        // Act
        $regionalInterestRule->applyModification($client, $loan);
        
        // Assert
        $this->assertEquals($originalRate, $loan->rate);
    }
    
    /**
     * Test with zero rate increase
     */
    public function test_zero_rate_increase_doesnt_change_rate(): void
    {
        // Arrange
        $targetRegion = 'PR';
        $rateIncrease = 0.0;
        $regionalInterestRule = new RegionalInterestRule($targetRegion, $rateIncrease);
        
        $client = new Client();
        $client->region = $targetRegion;
        
        $loan = new Loan();
        $originalRate = 10.0;
        $loan->rate = $originalRate;
        
        // Act
        $regionalInterestRule->applyModification($client, $loan);
        
        // Assert
        $this->assertEquals($originalRate, $loan->rate);
    }
    
    /**
     * Test with negative rate increase (discount)
     */
    public function test_negative_rate_increase_decreases_rate(): void
    {
        // Arrange
        $targetRegion = 'PR';
        $rateIncrease = -1.5; // Rate discount
        $regionalInterestRule = new RegionalInterestRule($targetRegion, $rateIncrease);
        
        $client = new Client();
        $client->region = $targetRegion;
        
        $loan = new Loan();
        $loan->rate = 10.0;
        $expectedRate = $loan->rate + $rateIncrease; // Should decrease
        
        // Act
        $regionalInterestRule->applyModification($client, $loan);
        
        // Assert
        $this->assertEquals($expectedRate, $loan->rate);
    }
}