<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'id'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
