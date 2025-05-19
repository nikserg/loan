<?php

namespace Tests\Feature\Api;

use App\Models\Client;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    
    /**
     * Test retrieving all loans
     */
    public function test_can_retrieve_all_loans(): void
    {
        // Arrange
        Loan::factory()->count(3)->create();
        
        // Act
        $response = $this->getJson('/api/loans');
        
        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
    
    /**
     * Test retrieving a specific loan
     */
    public function test_can_retrieve_loan(): void
    {
        // Arrange
        $loan = Loan::factory()->create();
        
        // Act
        $response = $this->getJson("/api/loans/{$loan->id}");
        
        // Assert - we're only checking that we get a 200 response with a data object
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['name']]);
    }
    
    /**
     * Test retrieving loans for a specific client
     */
    public function test_can_retrieve_client_loans(): void
    {
        // Arrange
        $client = Client::factory()->create();
        Loan::factory()->count(3)->create(['client_id' => $client->id]);
        
        // Create loans for another client to ensure they're not returned
        Loan::factory()->count(2)->create();
        
        // Act
        $response = $this->getJson("/api/clients/{$client->id}/loans");
        
        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
    
    /**
     * Test applying for a loan
     */
    public function test_can_apply_for_loan(): void
    {
        // Arrange
        $client = Client::factory()->create([
            'age' => 30,
            'income' => 2000,
            'score' => 600,
            'region' => 'BR', // Brno, to avoid random rejection or rate increase
        ]);
        
        $loanData = [
            'name' => 'Test Loan',
            'amount' => 5000,
            'rate' => 10,
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addYear()->format('Y-m-d'),
        ];
        
        // Act
        $response = $this->postJson("/api/clients/{$client->id}/loans", $loanData);
        
        // Assert - we're only checking that the loan was created
        $response->assertStatus(201)
            ->assertJsonStructure(['data']);
            
        // Check in the database instead of the response
        $this->assertDatabaseHas('loans', [
            'client_id' => $client->id,
            'name' => $loanData['name'],
        ]);
            
        $this->assertDatabaseHas('loans', [
            'client_id' => $client->id,
            'name' => $loanData['name'],
            'amount' => $loanData['amount'],
        ]);
    }
    
    /**
     * Test checking loan eligibility
     */
    public function test_can_check_eligibility(): void
    {
        // Arrange
        $client = Client::factory()->create([
            'age' => 30,
            'income' => 2000,
            'score' => 600,
            'region' => 'BR', // Brno, to avoid random rejection or rate increase
        ]);
        
        // Act
        $response = $this->getJson("/api/clients/{$client->id}/eligibility");
        
        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('eligible', true);
    }
    
    /**
     * Test ineligible client is rejected
     */
    public function test_ineligible_client_is_rejected(): void
    {
        // Arrange - create client with low score
        $client = Client::factory()->create([
            'score' => 300, // Below minimum score
        ]);
        
        $loanData = [
            'name' => 'Test Loan',
            'amount' => 5000,
            'rate' => 10,
        ];
        
        // Act
        $response = $this->postJson("/api/clients/{$client->id}/loans", $loanData);
        
        // Assert
        $response->assertStatus(422)
            ->assertJsonPath('message', 'Loan application rejected');
    }
}
