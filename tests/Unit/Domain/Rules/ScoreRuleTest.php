<?php

namespace Tests\Unit\Domain\Rules;

use App\Domain\Rules\ScoreRule;
use App\Models\Client;
use Tests\TestCase;

class ScoreRuleTest extends TestCase
{
    /**
     * Test client with sufficient score passes
     */
    public function test_client_with_sufficient_score_is_eligible(): void
    {
        // Arrange
        $minimumScore = 500;
        $scoreRule = new ScoreRule($minimumScore);
        $client = new Client();
        $client->score = $minimumScore + 1;
        
        // Act
        $result = $scoreRule->isEligible($client);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * Test client with exact minimum score fails
     */
    public function test_client_with_exact_minimum_score_is_not_eligible(): void
    {
        // Arrange
        $minimumScore = 500;
        $scoreRule = new ScoreRule($minimumScore);
        $client = new Client();
        $client->score = $minimumScore;
        
        // Act
        $result = $scoreRule->isEligible($client);
        
        // Assert
        $this->assertFalse($result);
    }
    
    /**
     * Test client with insufficient score fails
     */
    public function test_client_with_insufficient_score_is_not_eligible(): void
    {
        // Arrange
        $minimumScore = 500;
        $scoreRule = new ScoreRule($minimumScore);
        $client = new Client();
        $client->score = $minimumScore - 1;
        
        // Act
        $result = $scoreRule->isEligible($client);
        
        // Assert
        $this->assertFalse($result);
    }
    
    /**
     * Test getMessage returns correct message
     */
    public function test_get_message_returns_correct_message(): void
    {
        // Arrange
        $minimumScore = 500;
        $scoreRule = new ScoreRule($minimumScore);
        
        // Act
        $message = $scoreRule->getMessage();
        
        // Assert
        $this->assertStringContainsString((string) $minimumScore, $message);
    }
}
