<?php

namespace Tests\Unit\Domain\Rules;

use App\Domain\Rules\IncomeRule;
use App\Models\Client;
use Tests\TestCase;

class IncomeRuleTest extends TestCase
{
    /**
     * Test client with income above minimum is eligible
     */
    public function test_client_with_sufficient_income_is_eligible(): void
    {
        // Arrange
        $minimumIncome = 1000.0;
        $incomeRule = new IncomeRule($minimumIncome);
        
        $client = new Client();
        $client->income = $minimumIncome + 100.0; // Income above minimum
        
        // Act
        $result = $incomeRule->isEligible($client);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * Test client with exact minimum income is eligible
     */
    public function test_client_with_exact_minimum_income_is_eligible(): void
    {
        // Arrange
        $minimumIncome = 1000.0;
        $incomeRule = new IncomeRule($minimumIncome);
        
        $client = new Client();
        $client->income = $minimumIncome; // Exactly minimum income
        
        // Act
        $result = $incomeRule->isEligible($client);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * Test client with income below minimum is not eligible
     */
    public function test_client_with_insufficient_income_is_not_eligible(): void
    {
        // Arrange
        $minimumIncome = 1000.0;
        $incomeRule = new IncomeRule($minimumIncome);
        
        $client = new Client();
        $client->income = $minimumIncome - 0.01; // Just below minimum
        
        // Act
        $result = $incomeRule->isEligible($client);
        
        // Assert
        $this->assertFalse($result);
    }
    
    /**
     * Test getMessage returns correct message
     */
    public function test_get_message_returns_correct_message(): void
    {
        // Arrange
        $minimumIncome = 1000.0;
        $incomeRule = new IncomeRule($minimumIncome);
        
        // Act
        $message = $incomeRule->getMessage();
        
        // Assert
        $this->assertStringContainsString((string) $minimumIncome, $message);
        $this->assertStringContainsString('$', $message);
    }
}