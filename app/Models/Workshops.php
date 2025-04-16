<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshops extends Model
{
    /** @use HasFactory<\Database\Factories\WorkshopsFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'max_tables',
    ];

    public function processes()
    {
        return $this->hasMany(Processes::class);
    }
}
