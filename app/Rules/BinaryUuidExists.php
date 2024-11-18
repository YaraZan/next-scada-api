<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class BinaryUuidExists implements ValidationRule
{
    protected string $table;
    protected string $column;

    /**
     * Create a new rule instance.
     *
     * @param  string  $table
     * @param  string  $column
     */
    public function __construct(string $table, string $column = 'uuid')
    {
        $this->table = $table;
        $this->column = $column;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $binaryUuid = hex2bin(str_replace('-', '', (string) $value));

        $exists = DB::table($this->table)
            ->where($this->column, $binaryUuid)
            ->exists();

        if (!$exists) {
            $fail("The selected $attribute is invalid.");
        }
    }
}
