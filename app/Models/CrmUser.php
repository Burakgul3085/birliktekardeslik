<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CrmUser extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'crm'
            && $this->is_active
            && in_array($this->role, ['super_admin', 'manager', 'staff', 'viewer'], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function canManageCrmUsers(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canWriteDonations(): bool
    {
        return in_array($this->role, ['super_admin', 'manager', 'staff'], true);
    }

    public function canDeleteRecords(): bool
    {
        return in_array($this->role, ['super_admin', 'manager'], true);
    }

    public function canWriteNotes(): bool
    {
        return in_array($this->role, ['super_admin', 'manager', 'staff'], true);
    }

    public function canEditNote(CrmNote $note): bool
    {
        if ($this->canDeleteRecords()) {
            return true;
        }

        return $this->canWriteNotes() && $note->crm_user_id === $this->id;
    }

    public function canDeleteNote(CrmNote $note): bool
    {
        return $this->canEditNote($note);
    }

    public function crmNotes(): HasMany
    {
        return $this->hasMany(CrmNote::class);
    }
}
