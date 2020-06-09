<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function zona(){
        return $this->belongsTo('App\Zona', 'id_zona');
    }

    public function suministro(){
        return $this->hasMany('App\Suministro', 'id_proveedor');
    }

    public function compra(){
        return $this->hasMany('App\Compra', 'id_proveedor');
    }

    public function agenda_persona(){
        return $this->hasMany('App\Agenda_Persona', 'id_proveedor');
    }
}
