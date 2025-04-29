<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmittedForm extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'form_name',
        'form_json',
    ];

    protected $casts = ['form_json' => 'array'];
}
