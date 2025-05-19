<?php

namespace App\Domain\Contracts;

use App\Models\Loan;
use Illuminate\Pagination\LengthAwarePaginator;

interface LoanRepositoryInterface
{
    /**
     * Get a loan by ID
     */
    public function findById(int $id): ?Loan;
    
    /**
     * Get all loans
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get loans for a specific client
     */
    public function getByClientId(int $clientId, int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Create a new loan
     */
    public function create(array $data): Loan;
    
    /**
     * Update an existing loan
     */
    public function update(int $id, array $data): ?Loan;
    
    /**
     * Delete a loan
     */
    public function delete(int $id): bool;
}