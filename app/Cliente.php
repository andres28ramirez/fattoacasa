<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function zona(){
        return $this->belongsTo('App\Zona', 'id_zona');
    }

    public function agenda_persona(){
        return $this->hasMany('App\Agenda_Persona', 'id_cliente');
    }

    public function venta(){
        return $this->hasMany('App\Venta', 'id_cliente');
    }
}
