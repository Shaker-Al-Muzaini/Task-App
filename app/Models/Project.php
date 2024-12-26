<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    public const NOT_STARTED = 0;
    public const PENDING = 1;
    public const COMPLETED = 2;

    protected $fillable = ['name', 'status', 'startDate', 'endDate', 'slug'];

    public static function createSlug(string $name): string
    {
        return Str::slug($name) . '-' . time();
    }
    public function task_progress()
    {
        return $this->hasOne(TaskProgress::class, 'projectId', 'id');
    }
}
