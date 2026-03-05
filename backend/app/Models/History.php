<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';

    protected $fillable = [
        'id_user',
        'name',
        'email',
        'phone',
        'price'
    ];
}
