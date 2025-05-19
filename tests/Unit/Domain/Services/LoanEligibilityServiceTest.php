<?php

namespace Tests\Unit\Domain\Services;

use App\Domain\Contracts\LoanEligibilityRule;
use App\Domain\Services\LoanEligibilityService;
use App\Models\Client;
use Mockery;
use Tests\TestCase;

class LoanEligibilityServiceTest extends TestCase
{
    private LoanEligibilityService $eligibilityService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->eligibilityService = new LoanEligibilityService();
    }
    
    /**
     * Test a client is eligible when all rules pass
     */
    public function test_client_is_eligible_when_all_rules_pass(): void
    {
        // Arrange
        $client = new Client();
        
        $passingRule1 = Mockery::mock(LoanEligibilityRule::class);
        $passingRule1->shouldReceive('isEligible')->with($client)->andReturn(true);
        
        $passingRule2 = Mockery::mock(LoanEligibilityRule::class);
        $passingRule2->shouldReceive('isEligible')->with($client)->andReturn(true);
        
        $rules = [$passingRule1, $passingRule2];
        
        // Act
        $result = $this->eligibilityService->checkEligibility($client, $rules);
        
        // Assert
        $this->assertTrue($result['eligible']);
        $this->assertEmpty($result['failed_rules']);
        $this->assertEmpty($result['messages']);
    }
    
    /**
     * Test a client is not eligible when any rule fails
     */
    public function test_client_is_not_eligible_when_any_rule_fails(): void
    {
        // Arrange
        $client = new Client();
        
        $passingRule = Mockery::mock(LoanEligibilityRule::class);
        $passingRule->shouldReceive('isEligible')->with($client)->andReturn(true);
        
        $failingRule = Mockery::mock(LoanEligibilityRule::class);
        $failingRule->shouldReceive('isEligible')->with($client)->andReturn(false);
        $failingRule->shouldReceive('getMessage')->andReturn('Rule failed message');
        
        $rules = [$passingRule, $failingRule];
        
        // Act
        $result = $this->eligibilityService->checkEligibility($client, $rules);
        
        // Assert
        $this->assertFalse($result['eligible']);
        $this->assertCount(1, $result['failed_rules']);
        $this->assertCount(1, $result['messages']);
        $this->assertEquals('Rule failed message', $result['messages'][0]);
    }
    
    /**
     * Test a client is not eligible when multiple rules fail
     */
    public function test_client_is_not_eligible_when_multiple_rules_fail(): void
    {
        // Arrange
        $client = new Client();
        
        $failingRule1 = Mockery::mock(LoanEligibilityRule::class);
        $failingRule1->shouldReceive('isEligible')->with($client)->andReturn(false);
        $failingRule1->shouldReceive('getMessage')->andReturn('Rule 1 failed message');
        
        $failingRule2 = Mockery::mock(LoanEligibilityRule::class);
        $failingRule2->shouldReceive('isEligible')->with($client)->andReturn(false);
        $failingRule2->shouldReceive('getMessage')->andReturn('Rule 2 failed message');
        
        $rules = [$failingRule1, $failingRule2];
        
        // Act
        $result = $this->eligibilityService->checkEligibility($client, $rules);
        
        // Assert
        $this->assertFalse($result['eligible']);
        $this->assertCount(2, $result['failed_rules']);
        $this->assertCount(2, $result['messages']);
        $this->assertEquals('Rule 1 failed message', $result['messages'][0]);
        $this->assertEquals('Rule 2 failed message', $result['messages'][1]);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
