<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Despacho extends Model
{
    protected $table = 'despacho';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function venta(){
        return $this->belongsTo('App\Venta', 'id_venta');
    }

    public function trabajador(){
        return $this->belongsTo('App\Trabajador', 'id_trabajador');
    }
}
