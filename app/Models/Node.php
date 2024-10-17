<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'name',
        'type',
        'properties',
        'position'
    ];

    public function schema()
    {
        return $this->belongsTo(Schema::class);
    }
}
