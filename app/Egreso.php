<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Egreso extends Model
{
    protected $table = 'egreso';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function compra(){
        return $this->belongsTo('App\Compra', 'id_compra');
    }

    public function gastocosto(){
        return $this->belongsTo('App\Gasto_Costo', 'id_gasto_costo');
    }

    public function nomina(){
        return $this->belongsTo('App\Pago_Nomina', 'id_pago_nomina');
    }
}
