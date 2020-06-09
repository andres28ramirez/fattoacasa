<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pago_Nomina extends Model
{
    protected $table = 'pago_nomina';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function pago(){
        return $this->belongsTo('App\Pago', 'id_pago');
    }

    public function trabajador(){
        return $this->belongsTo('App\Trabajador', 'id_trabajador');
    }

    public function egreso(){
        return $this->hasMany('App\Egreso', 'id_pago_nomina');
    }
}
