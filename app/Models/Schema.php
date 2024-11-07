<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schema extends Model
{
    use HasFactory, HasUuid;

    protected $hidden = [
        'id',
        'creator_id'
    ];

    protected $fillable = [
        'name',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function nodes()
    {
        return $this->hasMany(Node::class);
    }

    public function memberRoles()
    {
        return $this->belongsToMany(MemberRole::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
