<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workshops extends Model
{
    /** @use HasFactory<\Database\Factories\WorkshopsFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'max_tables',
        'user_id',
    ];

    public function processes()
    {
        return $this->hasMany(Processes::class);
    }

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
