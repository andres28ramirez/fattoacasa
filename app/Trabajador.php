<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    protected $table = 'trabajador';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function agenda_persona(){
        return $this->hasMany('App\Agenda_Persona', 'id_trabajador');
    }

    public function pago_nomina(){
        return $this->hasMany('App\Pago_Nomina', 'id_trabajador');
    }
}
