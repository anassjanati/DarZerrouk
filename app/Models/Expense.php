<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_category_id',
        'user_id',
        'expense_number',
        'title',
        'amount',
        'expense_date',
        'payment_method',
        'reference_number',
        'receipt_image',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Get the expense category
     */
    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    /**
     * Get the user who recorded the expense
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate unique expense number
     */
    public static function generateExpenseNumber(): string
    {
        $prefix = 'EXP';
        $date = date('Ymd');
        $lastExpense = self::whereDate('created_at', today())->latest('id')->first();
        $number = $lastExpense ? (int)substr($lastExpense->expense_number, -3) + 1 : 1;
        return $prefix . '-' . $date . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Scope to get today's expenses
     */
    public function scopeToday($query)
    {
        return $query->whereDate('expense_date', today());
    }

    /**
     * Scope to get this month's expenses
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', date('m'))
                     ->whereYear('expense_date', date('Y'));
    }
}
