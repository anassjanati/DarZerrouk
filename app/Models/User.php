<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    /**
     * Get the role of the user
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get all sales created by this user
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get all expenses recorded by this user
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get all purchase orders created by this user
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get all stock adjustments made by this user
     */
    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    /**
     * Get all cash register sessions for this user
     */
    public function cashRegisters(): HasMany
    {
        return $this->hasMany(CashRegister::class);
    }

    /**
     * Get all activity logs for this user
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user has any role in list
     */
    public function hasAnyRole(array $roles): bool
    {
        if (!$this->role) {
            return false;
        }

        return in_array($this->role->name, $roles, true);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is superviseur
     */
    public function isSuperviseur(): bool
    {
        return $this->hasRole('superviseur');
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    /**
     * Check if user is cashier
     */
    public function isCashier(): bool
    {
        return $this->hasRole('cashier');
    }

    /**
     * Check module permissions based on the user's role permissions
     * expects Role hasMany RolePermission with fields: module, can_view, can_create, can_edit, can_delete
     */
    public function canModule(string $module, string $action): bool
{
    if (! $this->role) {
        return false;
    }

    // Ensure permissions are loaded from DB
    $this->role->loadMissing('permissions');

    $perm = $this->role->permissions
        ->firstWhere('module', $module);

    if (! $perm) {
        return false;
    }

    return match ($action) {
        'view'   => (bool) $perm->can_view,
        'create' => (bool) $perm->can_create,
        'edit'   => (bool) $perm->can_edit,
        'delete' => (bool) $perm->can_delete,
        default  => false,
    };
}
}
