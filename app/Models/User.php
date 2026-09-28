<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function privileges(): HasMany
    {
        return $this->hasMany(AdminPrivilege::class);
    }

    /**
     * The Dormitory Owner is the admin holding manage_users -- the same
     * rule the 'privileges' middleware (EnsureCanManagePrivileges) uses.
     */
    public function isOwner(): bool
    {
        if ($this->role?->role_name !== 'admin') {
            return false;
        }

        return $this->relationLoaded('privileges')
            ? $this->privileges->contains('privilege_name', 'manage_users')
            : $this->privileges()->where('privilege_name', 'manage_users')->exists();
    }

    /**
     * 'owner', 'admin', or null (tenants). Drives the Owner / Admin tag
     * shown next to staff names across the app (see partials/role-tag).
     */
    public function roleTag(): ?string
    {
        if ($this->role?->role_name !== 'admin') {
            return null;
        }

        return $this->isOwner() ? 'owner' : 'admin';
    }

    /**
     * The tenant record this login account belongs to, if any.
     * Admin/owner accounts have none.
     */
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }
}