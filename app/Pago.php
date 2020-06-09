<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function nomina(){
        return $this->hasMany('App\Pago_Nomina', 'id_pago');
    }

    public function compra(){
        return $this->hasMany('App\Compra', 'id_pago');
    }

    public function venta(){
        return $this->hasMany('App\Venta', 'id_pago');
    }
}
