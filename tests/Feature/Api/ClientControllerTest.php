<?php

namespace Tests\Feature\Api;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    
    /**
     * Test retrieving all clients
     */
    public function test_can_retrieve_all_clients(): void
    {
        // Arrange
        Client::factory()->count(3)->create();
        
        // Act
        $response = $this->getJson('/api/clients');
        
        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
    
    /**
     * Test creating a new client
     */
    public function test_can_create_client(): void
    {
        // Arrange
        $clientData = [
            'name' => $this->faker->name,
            'age' => $this->faker->numberBetween(18, 60),
            'city' => $this->faker->city,
            'region' => 'PR', // Prague
            'income' => $this->faker->randomFloat(2, 1000, 5000),
            'score' => $this->faker->numberBetween(300, 850),
            'pin' => $this->faker->unique()->numerify('###-##-####'),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
        ];
        
        // Act
        $response = $this->postJson('/api/clients', $clientData);
        
        // Assert
        $response->assertStatus(201)
            ->assertJsonPath('data.name', $clientData['name']);
            
        $this->assertDatabaseHas('clients', [
            'name' => $clientData['name'],
            'email' => $clientData['email'],
        ]);
    }
    
    /**
     * Test retrieving a specific client
     */
    public function test_can_retrieve_client(): void
    {
        // Arrange
        $client = Client::factory()->create();
        
        // Act
        $response = $this->getJson("/api/clients/{$client->id}");
        
        // Assert - we're only checking that we get a 200 response with a data object
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['name']]);
    }
    
    /**
     * Test updating a client
     */
    public function test_can_update_client(): void
    {
        // Arrange
        $client = Client::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
            'income' => 2500.00,
        ];
        
        // Act
        $response = $this->putJson("/api/clients/{$client->id}", $updateData);
        
        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('data.name', $updateData['name']);
            
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => $updateData['name'],
        ]);
    }
    
    /**
     * Test deleting a client
     */
    public function test_can_delete_client(): void
    {
        // Arrange
        $client = Client::factory()->create();
        
        // Act
        $response = $this->deleteJson("/api/clients/{$client->id}");
        
        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('message', 'Client deleted successfully');
            
        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
        ]);
    }
    
    /**
     * Test validation errors when creating a client
     */
    public function test_validation_errors_when_creating_client(): void
    {
        // Arrange
        $invalidData = [
            'name' => '', // Empty name should fail validation
            'age' => 200, // Age too high
            'email' => 'invalid-email', // Invalid email format
        ];
        
        // Act
        $response = $this->postJson('/api/clients', $invalidData);
        
        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'age', 'email']);
    }
}
