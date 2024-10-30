<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRole extends Model
{
    use HasFactory, HasUuid;

    protected $hidden = [
        'id'
    ];

    protected $fillable = [
        'name',
        'color',
        'can_write_tags',
        'can_create_schemas',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function schemas()
    {
        return $this->belongsToMany(Schema::class);
    }
}
