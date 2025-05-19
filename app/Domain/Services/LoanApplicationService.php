<?php

namespace App\Domain\Services;

use App\Domain\Contracts\ClientRepositoryInterface;
use App\Domain\Contracts\LoanEligibilityRule;
use App\Domain\Contracts\LoanModifierRule;
use App\Domain\Contracts\LoanRepositoryInterface;
use App\Models\Client;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Service for loan application processing
 */
readonly class LoanApplicationService
{
    /**
     * LoanApplicationService constructor.
     */
    public function __construct(
        private LoanEligibilityService $eligibilityService,
        private ClientRepositoryInterface $clientRepository,
        private LoanRepositoryInterface $loanRepository
    ) {
    }

    /**
     * Process a loan application
     *
     * @param int $clientId
     * @param array $loanData
     * @param array $eligibilityRules
     * @param array $modifierRules
     * @return array
     */
    public function processApplication(
        int $clientId,
        array $loanData,
        array $eligibilityRules,
        array $modifierRules
    ): array {
        // Get client
        $client = $this->clientRepository->findById($clientId);

        if (!$client) {
            return [
                'success' => false,
                'message' => 'Client not found',
            ];
        }

        // Check eligibility
        $eligibilityResult = $this->eligibilityService->checkEligibility($client, $eligibilityRules);

        if (!$eligibilityResult['eligible']) {
            $this->notifyClient($client, 'loan_rejected', [
                'reasons' => $eligibilityResult['messages']
            ]);

            return [
                'success' => false,
                'message' => 'Loan application rejected',
                'reasons' => $eligibilityResult['messages'],
            ];
        }

        // Create loan with initial data
        $loan = $this->createLoan($client, $loanData);

        // Apply modifiers
        $this->applyLoanModifiers($client, $loan, $modifierRules);

        // Save updated loan
        $loan->save();

        // Notify client
        $this->notifyClient($client, 'loan_approved', [
            'loan_id' => $loan->id,
            'amount' => $loan->amount,
            'rate' => $loan->rate,
            'start_date' => $loan->start_date,
            'end_date' => $loan->end_date,
        ]);

        return [
            'success' => true,
            'message' => 'Loan application approved',
            'loan' => $loan,
        ];
    }

    /**
     * Create a new loan
     */
    private function createLoan(Client $client, array $data): Loan
    {
        $loanData = [
            'client_id' => $client->id,
            'name' => $data['name'],
            'amount' => $data['amount'],
            'rate' => $data['rate'],
            'start_date' => $data['start_date'] ?? Carbon::now(),
            'end_date' => $data['end_date'] ?? Carbon::now()->addYear(),
            'status' => 'approved',
        ];

        return $this->loanRepository->create($loanData);
    }

    /**
     * Apply loan modifiers
     */
    private function applyLoanModifiers(Client $client, Loan $loan, array $modifiers): void
    {
        foreach ($modifiers as $modifier) {
            if ($modifier instanceof LoanModifierRule) {
                $modifier->applyModification($client, $loan);
            }
        }
    }

    /**
     * Notify client about loan status
     *
     * This is a stub implementation that logs notifications
     */
    private function notifyClient(Client $client, string $type, array $data = []): void
    {
        $message = match ($type) {
            'loan_approved' => "Loan approved: Amount {$data['amount']}, Rate {$data['rate']}%",
            'loan_rejected' => "Loan rejected. Reasons: " . implode(', ', $data['reasons']),
            default => "Notification: $type",
        };

        Log::info("[" . Carbon::now() . "] Notification to client [{$client->name}]: {$message}");
    }
}
