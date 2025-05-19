<?php

namespace App\Domain\Contracts;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClientRepositoryInterface
{
    /**
     * Get a client by ID
     */
    public function findById(int $id): ?Client;
    
    /**
     * Get all clients
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Create a new client
     */
    public function create(array $data): Client;
    
    /**
     * Update an existing client
     */
    public function update(int $id, array $data): ?Client;
    
    /**
     * Delete a client
     */
    public function delete(int $id): bool;
}