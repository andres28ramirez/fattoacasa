<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto_Receta extends Model
{
    protected $table = 'producto_receta';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function producto_final(){
        return $this->belongsTo('App\Producto', 'id_producto_final');
    }

    public function ingrediente(){
        return $this->belongsTo('App\Producto', 'id_ingrediente');
    }
}
