<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Client
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property int $age
 * @property string $city
 * @property string $region
 * @property float $income
 * @property int $score
 * @property string $pin
 * @property string $email
 * @property string $phone
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Loan> $loans
 */
class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'age',
        'city',
        'region',
        'income',
        'score',
        'pin',
        'email',
        'phone',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'age' => 'integer',
        'income' => 'decimal:2',
        'score' => 'integer',
    ];

    /**
     * Get the loans for the client.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
