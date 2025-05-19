<?php

namespace App\Repositories;

use App\Domain\Contracts\ClientRepositoryInterface;
use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ClientRepository implements ClientRepositoryInterface
{
    /**
     * ClientRepository constructor.
     */
    public function __construct(
        private Client $client
    ) {
    }

    /**
     * Get a client by ID
     */
    public function findById(int $id): ?Client
    {
        return $this->client->find($id);
    }

    /**
     * Get all clients
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->client->paginate($perPage);
    }

    /**
     * Create a new client
     */
    public function create(array $data): Client
    {
        return $this->client->create($data);
    }

    /**
     * Update an existing client
     */
    public function update(int $id, array $data): ?Client
    {
        $client = $this->findById($id);

        if (!$client) {
            return null;
        }

        $client->update($data);
        return $client;
    }

    /**
     * Delete a client
     */
    public function delete(int $id): bool
    {
        $client = $this->findById($id);

        if (!$client) {
            return false;
        }

        return $client->delete();
    }
}
