<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gasto_Costo extends Model
{
    protected $table = 'gasto_costo';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function egreso(){
        return $this->hasMany('App\Egreso', 'id_gasto_costo');
    }
}
