<?php

namespace App\Providers;

use App\Domain\Contracts\ClientRepositoryInterface;
use App\Domain\Contracts\LoanEligibilityRule;
use App\Domain\Contracts\LoanModifierRule;
use App\Domain\Contracts\LoanRepositoryInterface;
use App\Domain\Services\LoanEligibilityService;
use App\Repositories\ClientRepository;
use App\Repositories\LoanRepository;
use Illuminate\Support\ServiceProvider;
use OpenApi\Attributes as OA;

#[OA\Info(version: "1.0.0", title: "Medication REST Api")]
class LoanServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LoanEligibilityService::class);

        // Register repositories
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(LoanRepositoryInterface::class, LoanRepository::class);

        // Auto-register rule dependencies from config
        $this->registerRuleDependencies();

        // Register rule collections
        $this->registerRuleCollections();
    }

    /**
     * Register rule dependencies based on config
     */
    private function registerRuleDependencies(): void
    {
        // Register eligibility rule dependencies
        foreach (config('loan.rules.eligibility', []) as $ruleClass => $settings) {
            if (isset($settings['parameters']) && is_array($settings['parameters'])) {
                foreach ($settings['parameters'] as $paramName => $value) {
                    $this->app->when($ruleClass)
                        ->needs('$' . $paramName)
                        ->give($value);
                }
            }
        }

        // Register modifier rule dependencies
        foreach (config('loan.rules.modifiers', []) as $ruleClass => $settings) {
            if (isset($settings['parameters']) && is_array($settings['parameters'])) {
                foreach ($settings['parameters'] as $paramName => $value) {
                    $this->app->when($ruleClass)
                        ->needs('$' . $paramName)
                        ->give($value);
                }
            }
        }
    }

    /**
     * Register rule collections based on config
     */
    private function registerRuleCollections(): void
    {
        // Register eligibility rules
        $this->app->bind('loan.eligibility_rules', function ($app) {
            return $this->makeRules('loan.rules.eligibility', LoanEligibilityRule::class);
        });

        // Register modifier rules
        $this->app->bind('loan.modifier_rules', function ($app) {
            return $this->makeRules('loan.rules.modifiers', LoanModifierRule::class);
        });
    }

    /**
     * Create an array of rule instances from config
     */
    private function makeRules(string $configPath, string $interface): array
    {
        $rules = [];
        $configuredRules = config($configPath, []);

        foreach ($configuredRules as $ruleClass => $settings) {
            if (class_exists($ruleClass) &&
                in_array($interface, class_implements($ruleClass)) &&
                ($settings['enabled'] ?? true)) {
                $rules[] = app()->make($ruleClass);
            }
        }

        return $rules;
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish loan config file
        $this->publishes([
            __DIR__ . '/../../config/loan.php' => config_path('loan.php'),
        ], 'loan-config');
    }
}
