<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'suspended_at',
        'suspension_reason',
        'permissions',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_changed_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'encrypted',
            'permissions' => 'array',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Many-to-many relationship with roles.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Determine if the user has enabled two-factor authentication.
     */
    public function hasEnabledTwoFactor(): bool
    {
        return ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Generate recovery codes for two-factor authentication.
     */
    public function generateRecoveryCodes(): void
    {
        $this->forceFill([
            'two_factor_recovery_codes' => json_encode(
                collect(range(1, 8))->map(fn () => Str::random(10) . '-' . Str::random(10))
            ),
        ])->save();
    }

    /**
     * Get the user's recovery codes.
     */
    public function getRecoveryCodes(): array
    {
        return json_decode($this->two_factor_recovery_codes, true) ?: [];
    }

    /**
     * Replace a recovery code with a new one.
     */
    public function replaceRecoveryCode(string $code): void
    {
        $this->forceFill([
            'two_factor_recovery_codes' => json_encode(
                collect($this->getRecoveryCodes())
                    ->reject(fn ($existingCode) => $existingCode === $code)
                    ->values()
                    ->all()
            ),
        ])->save();
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        // Check both the role column and the many-to-many roles relationship
        return $this->role === $role || $this->roles->contains('slug', $role);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        // Check user-specific permissions
        if (in_array($permission, $this->permissions ?? [])) {
            return true;
        }

        // Check permissions from assigned roles
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        // Check if the user has wildcard permission
        if (in_array('*', $this->permissions ?? [])) {
            return true;
        }

        // For backward compatibility, check if user is admin
        return $this->role === 'admin';
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        $this->roles()->attach($role);
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        $this->roles()->detach($role);
    }

    /**
     * Grant a permission to the user.
     */
    public function grantPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    /**
     * Revoke a permission from the user.
     */
    public function revokePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        $this->update(['permissions' => array_values($permissions)]);
    }
}
