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
        'id',
        'owner_id',
    ];

    protected $fillable = [
        'protocol',
        'name',
        'opc_name',
        'connection_string',
        'host',
    ];

    protected $casts = [
        'protocol' => ProtocolEnum::class,
    ];

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
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('member_role_id')
            ->withTimestamps();
    }
}
