<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    protected $table = 'indicador';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');
}
