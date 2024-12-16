<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    // Project status constants
    public const NOT_STARTED = 0;
    public const PENDING = 1;
    public const COMPLETED = 2;

    // Mass assignment protection
    protected $fillable = ['name', 'status', 'startDate', 'endDate', 'slug'];


    /**
     * Generate a unique slug for the project.
     *
     * @param string $name
     * @return string
     */
    public static function createSlug(string $name): string
    {
        $code = Str::random(10) . time();
        return Str::slug($name) . '-' . $code;
    }
}
