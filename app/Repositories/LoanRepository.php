<?php

namespace App\Repositories;

use App\Domain\Contracts\LoanRepositoryInterface;
use App\Models\Loan;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class LoanRepository implements LoanRepositoryInterface
{
    /**
     * LoanRepository constructor.
     */
    public function __construct(
        private Loan $loan
    ) {
    }

    /**
     * Get a loan by ID
     */
    public function findById(int $id): ?Loan
    {
        return $this->loan->find($id);
    }

    /**
     * Get all loans
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->loan->paginate($perPage);
    }

    /**
     * Get loans for a specific client
     */
    public function getByClientId(int $clientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->loan->where('client_id', $clientId)->paginate($perPage);
    }

    /**
     * Create a new loan
     */
    public function create(array $data): Loan
    {
        return $this->loan->create($data);
    }

    /**
     * Update an existing loan
     */
    public function update(int $id, array $data): ?Loan
    {
        $loan = $this->findById($id);

        if (!$loan) {
            return null;
        }

        $loan->update($data);
        return $loan;
    }

    /**
     * Delete a loan
     */
    public function delete(int $id): bool
    {
        $loan = $this->findById($id);

        if (!$loan) {
            return false;
        }

        return $loan->delete();
    }
}
