<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tables extends Model
{
    /** @use HasFactory<\Database\Factories\TablesFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'material',
        'status',
        'price',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }

    public function processes()
    {
        return $this->hasMany(Processes::class);
    }
}
