<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function cliente(){
        return $this->belongsTo('App\Cliente', 'id_cliente');
    }

    public function pago(){
        return $this->belongsTo('App\Pago', 'id_pago');
    }

    public function despacho(){
        return $this->hasMany('App\Despacho', 'id_venta');
    }

    public function order_producto(){
        return $this->hasMany('App\Orden_Producto', 'id_venta');
    }
}
