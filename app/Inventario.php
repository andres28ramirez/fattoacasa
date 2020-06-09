<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventario';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function producto(){
        return $this->belongsTo('App\Producto', 'id_producto');
    }
}
