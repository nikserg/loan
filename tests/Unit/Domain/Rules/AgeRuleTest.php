<?php

namespace Tests\Unit\Domain\Rules;

use App\Domain\Rules\AgeRule;
use App\Models\Client;
use Tests\TestCase;

class AgeRuleTest extends TestCase
{
    /**
     * Test client within age range is eligible
     */
    public function test_client_within_age_range_is_eligible(): void
    {
        // Arrange
        $minimumAge = 18;
        $maximumAge = 65;
        $ageRule = new AgeRule($minimumAge, $maximumAge);
        
        $client = new Client();
        $client->age = 30; // Age within range
        
        // Act
        $result = $ageRule->isEligible($client);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * Test client at minimum age is eligible
     */
    public function test_client_at_minimum_age_is_eligible(): void
    {
        // Arrange
        $minimumAge = 18;
        $maximumAge = 65;
        $ageRule = new AgeRule($minimumAge, $maximumAge);
        
        $client = new Client();
        $client->age = $minimumAge; // Exactly at minimum age
        
        // Act
        $result = $ageRule->isEligible($client);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * Test client at maximum age is eligible
     */
    public function test_client_at_maximum_age_is_eligible(): void
    {
        // Arrange
        $minimumAge = 18;
        $maximumAge = 65;
        $ageRule = new AgeRule($minimumAge, $maximumAge);
        
        $client = new Client();
        $client->age = $maximumAge; // Exactly at maximum age
        
        // Act
        $result = $ageRule->isEligible($client);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * Test client below minimum age is not eligible
     */
    public function test_client_below_minimum_age_is_not_eligible(): void
    {
        // Arrange
        $minimumAge = 18;
        $maximumAge = 65;
        $ageRule = new AgeRule($minimumAge, $maximumAge);
        
        $client = new Client();
        $client->age = $minimumAge - 1; // Below minimum age
        
        // Act
        $result = $ageRule->isEligible($client);
        
        // Assert
        $this->assertFalse($result);
    }
    
    /**
     * Test client above maximum age is not eligible
     */
    public function test_client_above_maximum_age_is_not_eligible(): void
    {
        // Arrange
        $minimumAge = 18;
        $maximumAge = 65;
        $ageRule = new AgeRule($minimumAge, $maximumAge);
        
        $client = new Client();
        $client->age = $maximumAge + 1; // Above maximum age
        
        // Act
        $result = $ageRule->isEligible($client);
        
        // Assert
        $this->assertFalse($result);
    }
    
    /**
     * Test getMessage returns correct message
     */
    public function test_get_message_returns_correct_message(): void
    {
        // Arrange
        $minimumAge = 18;
        $maximumAge = 65;
        $ageRule = new AgeRule($minimumAge, $maximumAge);
        
        // Act
        $message = $ageRule->getMessage();
        
        // Assert
        $this->assertStringContainsString((string) $minimumAge, $message);
        $this->assertStringContainsString((string) $maximumAge, $message);
    }
}