<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Processes extends Model
{
    /** @use HasFactory<\Database\Factories\ProcessesFactory> */
    use HasFactory;

    protected $fillable = [
        'table_id',
        'workshops_id',
        'status',
    ];

    public function tables()
    {
        return $this->belongsTo(Tables::class, 'table_id', 'id');
    }

    public function workshop()
    {
        return $this->belongsTo(Workshops::class);
    }
}
