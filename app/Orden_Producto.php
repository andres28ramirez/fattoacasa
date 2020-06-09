<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orden_Producto extends Model
{
    protected $table = 'orden_producto';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function venta(){
        return $this->belongsTo('App\Venta', 'id_venta');
    }

    public function compra(){
        return $this->belongsTo('App\Compra', 'id_compra');
    }

    public function producto(){
        return $this->belongsTo('App\Producto', 'id_producto');
    }
}
