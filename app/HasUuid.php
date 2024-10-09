<?php

namespace App;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot function from Laravel to automatically generate and store UUIDs.
     */
    protected static function boot()
    {
        static::creating(function ($model) {
            // Generate UUID and store as binary(16)
            $model->uuid = hex2bin(str_replace('-', '', (string) Str::uuid()));
        });
    }

    /**
     * Accessor to convert binary UUID to string when retrieving.
     */
    public function getUuidAttribute($value)
    {
        return vsprintf('%s-%s-%s-%s-%s', str_split(bin2hex($value), 4));
    }

    /**
     * Mutator to convert UUID string to binary when setting.
     */
    public function setUuidAttribute($value)
    {
        $this->attributes['uuid'] = hex2bin(str_replace('-', '', $value));
    }
}
