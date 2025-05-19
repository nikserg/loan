<?php

namespace Tests\Unit\Domain\Rules;

use App\Domain\Rules\RandomRejectRule;
use App\Models\Client;
use Mockery;
use Tests\TestCase;

class RandomRejectRuleTest extends TestCase
{
    /**
     * Test client from non-target region is always eligible
     */
    public function test_client_from_non_target_region_is_always_eligible(): void
    {
        // Arrange
        $targetRegion = 'PR';
        $rejectionChance = 50; // 50% chance of rejection
        $randomRejectRule = new RandomRejectRule($targetRegion, $rejectionChance);
        
        $client = new Client();
        $client->region = 'BR'; // Different region than target
        
        // Act
        $result = $randomRejectRule->isEligible($client);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * Test message includes target region
     */
    public function test_get_message_returns_correct_message(): void
    {
        // Arrange
        $targetRegion = 'PR';
        $rejectionChance = 50;
        $randomRejectRule = new RandomRejectRule($targetRegion, $rejectionChance);
        
        // Act
        $message = $randomRejectRule->getMessage();
        
        // Assert
        $this->assertStringContainsString($targetRegion, $message);
    }
    
    /**
     * Test with 100% rejection chance always rejects
     */
    public function test_with_100_percent_rejection_chance_always_rejects(): void
    {
        // Arrange
        $targetRegion = 'PR';
        $rejectionChance = 100; // 100% chance of rejection
        $randomRejectRule = new RandomRejectRule($targetRegion, $rejectionChance);
        
        $client = new Client();
        $client->region = $targetRegion;
        
        // Act
        $result = $randomRejectRule->isEligible($client);
        
        // Assert
        $this->assertFalse($result);
    }
    
    /**
     * Test with 0% rejection chance always accepts
     */
    public function test_with_0_percent_rejection_chance_always_accepts(): void
    {
        // Arrange
        $targetRegion = 'PR';
        $rejectionChance = 0; // 0% chance of rejection
        $randomRejectRule = new RandomRejectRule($targetRegion, $rejectionChance);
        
        $client = new Client();
        $client->region = $targetRegion;
        
        // Act
        $result = $randomRejectRule->isEligible($client);
        
        // Assert
        $this->assertTrue($result);
    }
}