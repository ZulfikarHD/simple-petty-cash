<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'amount',
        'description',
        'transaction_date',
        'category_id',
        'user_id',
        'receipt_path',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['receipt_url', 'has_receipt'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the full URL for the receipt image.
     *
     * @return Attribute<string|null, never>
     */
    protected function receiptUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (empty($this->receipt_path)) {
                    return null;
                }

                return Storage::disk('public')->url($this->receipt_path);
            }
        );
    }

    /**
     * Check if the transaction has a receipt attached.
     *
     * @return Attribute<bool, never>
     */
    protected function hasReceipt(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => ! empty($this->receipt_path)
        );
    }
}
