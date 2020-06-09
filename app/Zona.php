<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = 'zona';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function cliente(){
        return $this->hasMany('App\Cliente', 'id_zona');
    }

    public function proveedor(){
        return $this->hasMany('App\Proveedor', 'id_zona');
    }
}
