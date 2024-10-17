<?php

namespace App\Models;

use App\HasUuid;
use App\ProtocolEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory, HasUuid;

    protected $hidden = [
        'id'
    ];

    protected $fillable = [
        'protocol',
        'name',
        'opc_name',
        'connection_string',
        'host',
    ];

    protected function casts(): array
    {
        return [
            'protocol' => ProtocolEnum::class,
        ];
    }

    public function schemas()
    {
        return $this->hasMany(Schema::class);
    }

    public function memberRoles()
    {
        return $this->hasMany(MemberRole::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('uuid', 'member_role_id')
            ->withTimestamps();
    }
}
