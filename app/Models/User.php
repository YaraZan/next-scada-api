<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'role_id',
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

    public function isRoot(): bool
    {
        return optional($this->role)->name === 'root';
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class)
            ->withPivot('member_role_id')
            ->withTimestamps();
    }

    public function memberRoleInWorkspace($workspaceId)
    {
        $workspace = $this->workspaces()->where('workspace_id', $workspaceId)->first();
        return $workspace ? MemberRole::find($workspace->pivot->member_role_id) : null;
    }
}
