<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use SoftDeletes;

    protected $table = 'compra';

    //Relacion One to Many - Uno a Muchos
    //->hasMany('App\Modelo');

    //Relacion Many to One - Muchos a Uno
    //->belongsTo('App\Modelo', 'tabla_id');

    public function proveedor(){
        return $this->belongsTo('App\Proveedor', 'id_proveedor');
    }

    public function pago(){
        return $this->belongsTo('App\Pago', 'id_pago');
    }

    public function desperdicio(){
        return $this->hasMany('App\Desperdicio', 'id_compra');
    }

    public function orden_producto(){
        return $this->hasMany('App\Orden_Producto', 'id_compra');
    }

    public function egreso(){
        return $this->hasMany('App\Egreso', 'id_compra');
    }
}
