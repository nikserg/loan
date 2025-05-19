<?php

namespace Tests\Unit\Domain\Rules;

use App\Domain\Rules\RegionRule;
use App\Models\Client;
use Tests\TestCase;

class RegionRuleTest extends TestCase
{
    /**
     * Test client from allowed region is eligible
     */
    public function test_client_from_allowed_region_is_eligible(): void
    {
        // Arrange
        $allowedRegions = ['PR', 'CR', 'BR'];
        $regionRule = new RegionRule($allowedRegions);
        
        $client = new Client();
        $client->region = 'PR'; // Allowed region
        
        // Act
        $result = $regionRule->isEligible($client);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * Test client from disallowed region is not eligible
     */
    public function test_client_from_disallowed_region_is_not_eligible(): void
    {
        // Arrange
        $allowedRegions = ['PR', 'CR', 'BR'];
        $regionRule = new RegionRule($allowedRegions);
        
        $client = new Client();
        $client->region = 'SK'; // Not in allowed regions
        
        // Act
        $result = $regionRule->isEligible($client);
        
        // Assert
        $this->assertFalse($result);
    }
    
    /**
     * Test getMessage returns correct message with all allowed regions
     */
    public function test_get_message_returns_correct_message(): void
    {
        // Arrange
        $allowedRegions = ['PR', 'CR', 'BR'];
        $regionRule = new RegionRule($allowedRegions);
        
        // Act
        $message = $regionRule->getMessage();
        
        // Assert
        foreach ($allowedRegions as $region) {
            $this->assertStringContainsString($region, $message);
        }
    }
    
    /**
     * Test with empty allowed regions list
     */
    public function test_client_with_empty_allowed_regions_is_never_eligible(): void
    {
        // Arrange
        $allowedRegions = [];
        $regionRule = new RegionRule($allowedRegions);
        
        $client = new Client();
        $client->region = 'PR';
        
        // Act
        $result = $regionRule->isEligible($client);
        
        // Assert
        $this->assertFalse($result);
    }
}