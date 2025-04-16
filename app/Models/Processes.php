<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Processes extends Model
{
    /** @use HasFactory<\Database\Factories\ProcessesFactory> */
    use HasFactory;

    protected $fiillable = [
        'table_id',
        'workshop_id',
        'status',
    ];

    public function table()
    {
        return $this->belongsTo(Tables::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshops::class);
    }
}
