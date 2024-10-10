<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot function from Laravel to automatically generate and store UUIDs.
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            $model->uuid = hex2bin(str_replace('-', '', (string) Str::uuid()));
        });
    }

    /**
     * Accessor to convert binary UUID to string when retrieving.
     */
    public function getUuidAttribute($value)
    {
        // Convert binary UUID to hexadecimal string
        $hex = bin2hex($value);

        // Format the hex string into the correct UUID format
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),   // 8 characters
            substr($hex, 8, 4),   // 4 characters
            substr($hex, 12, 4),  // 4 characters
            substr($hex, 16, 4),  // 4 characters
            substr($hex, 20, 12)   // 12 characters
        );
    }


    /**
     * Find a model by its UUID.
     */
    public static function findByUuid(string $uuid)
    {
        $binaryUuid = hex2bin(str_replace('-', '', $uuid));

        return static::where('uuid', '=', $binaryUuid)->firstOrFail();
    }
}
