<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function categoria(){
        return $this->belongsTo('App\Categoria', 'id_categoria');
    }

    public function receta(){
        return $this->hasMany('App\Producto_Receta', 'id_producto_final');
    }

    public function inventario(){
        return $this->hasMany('App\Inventario', 'id_producto');
    }

    public function suministro(){
        return $this->hasMany('App\Suministro', 'id_producto');
    }

    public function order_producto(){
        return $this->hasMany('App\Orden_Producto', 'id_producto');
    }

    public function desperdicio(){
        return $this->hasMany('App\Desperdicio', 'id_producto');
    }
}
