<?php

namespace Tests\Unit\Domain\Services;

use App\Domain\Contracts\ClientRepositoryInterface;
use App\Domain\Contracts\LoanModifierRule;
use App\Domain\Contracts\LoanRepositoryInterface;
use App\Domain\Services\LoanApplicationService;
use App\Domain\Services\LoanEligibilityService;
use App\Models\Client;
use App\Models\Loan;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class LoanApplicationServiceTest extends TestCase
{
    private LoanApplicationService $applicationService;
    private LoanEligibilityService $eligibilityService;
    private ClientRepositoryInterface $clientRepository;
    private LoanRepositoryInterface $loanRepository;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->eligibilityService = Mockery::mock(LoanEligibilityService::class);
        $this->clientRepository = Mockery::mock(ClientRepositoryInterface::class);
        $this->loanRepository = Mockery::mock(LoanRepositoryInterface::class);
        
        $this->applicationService = new LoanApplicationService(
            $this->eligibilityService,
            $this->clientRepository,
            $this->loanRepository
        );
    }
    
    /**
     * Test application is rejected when client is not found
     */
    public function test_application_is_rejected_when_client_not_found(): void
    {
        // Arrange
        $clientId = 1;
        $loanData = [
            'name' => 'Test Loan',
            'amount' => 1000,
            'rate' => 10,
        ];
        
        $this->clientRepository->shouldReceive('findById')
            ->with($clientId)
            ->once()
            ->andReturn(null);
        
        // Act
        $result = $this->applicationService->processApplication(
            $clientId,
            $loanData,
            [],
            []
        );
        
        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals('Client not found', $result['message']);
    }
    
    /**
     * Test application is rejected when client is not eligible
     */
    public function test_application_is_rejected_when_client_not_eligible(): void
    {
        // Arrange
        $clientId = 1;
        $client = new Client();
        $client->id = $clientId;
        
        $loanData = [
            'name' => 'Test Loan',
            'amount' => 1000,
            'rate' => 10,
        ];
        
        $eligibilityRules = [Mockery::mock('rule1'), Mockery::mock('rule2')];
        $modifierRules = [];
        
        $eligibilityResult = [
            'eligible' => false,
            'failed_rules' => ['rule1'],
            'messages' => ['Failed rule message'],
        ];
        
        $this->clientRepository->shouldReceive('findById')
            ->with($clientId)
            ->once()
            ->andReturn($client);
            
        $this->eligibilityService->shouldReceive('checkEligibility')
            ->with($client, $eligibilityRules)
            ->once()
            ->andReturn($eligibilityResult);
        
        // Act
        $result = $this->applicationService->processApplication(
            $clientId,
            $loanData,
            $eligibilityRules,
            $modifierRules
        );
        
        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals('Loan application rejected', $result['message']);
        $this->assertEquals(['Failed rule message'], $result['reasons']);
    }
    
    /**
     * Test application is approved and loan modifiers are applied
     */
    public function test_application_is_approved_and_modifiers_applied(): void
    {
        // Arrange
        $client = Client::factory()->create([
            'score' => 700,
            'income' => 2000,
            'age' => 30,
            'region' => 'PR'
        ]);
        
        $clientId = $client->id;
        
        $loanData = [
            'name' => 'Test Loan',
            'amount' => 1000,
            'rate' => 10,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addYear(),
        ];
        
        $loan = new Loan();
        $loan->id = 1;
        $loan->client_id = $clientId;
        $loan->name = $loanData['name'];
        $loan->amount = $loanData['amount'];
        $loan->rate = $loanData['rate'];
        $loan->start_date = $loanData['start_date'];
        $loan->end_date = $loanData['end_date'];
        $loan->status = 'approved';
        
        $eligibilityRules = [Mockery::mock('rule1')];
        
        $modifier = Mockery::mock(LoanModifierRule::class);
        $modifier->shouldReceive('applyModification')
            ->with($client, Mockery::type(Loan::class))
            ->once();
            
        $modifierRules = [$modifier];
        
        $eligibilityResult = [
            'eligible' => true,
            'failed_rules' => [],
            'messages' => [],
        ];
        
        $this->clientRepository->shouldReceive('findById')
            ->with($clientId)
            ->once()
            ->andReturn($client);
            
        $this->eligibilityService->shouldReceive('checkEligibility')
            ->with($client, $eligibilityRules)
            ->once()
            ->andReturn($eligibilityResult);
            
        $this->loanRepository->shouldReceive('create')
            ->once()
            ->andReturn($loan);
        
        // Act
        $result = $this->applicationService->processApplication(
            $clientId,
            $loanData,
            $eligibilityRules,
            $modifierRules
        );
        
        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Loan application approved', $result['message']);
        $this->assertEquals($loan, $result['loan']);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
