<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agenda_Persona extends Model
{
    protected $table = 'agenda_persona';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function agenda(){
        return $this->belongsTo('App\Agenda', 'id_agenda');
    }

    public function trabajador(){
        return $this->belongsTo('App\Trabajador', 'id_trabajador');
    }

    public function cliente(){
        return $this->belongsTo('App\Cliente', 'id_cliente');
    }

    public function proveedor(){
        return $this->belongsTo('App\Proveedor', 'id_proveedor');
    }
}
