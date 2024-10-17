<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schema extends Model
{
    use HasFactory, HasUuid;

    protected $hidden = [
        'id'
    ];

    protected $fillable = [
        'name',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
