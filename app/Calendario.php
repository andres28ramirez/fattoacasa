<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    //
     //
     protected $table = 'calendario';

     public $timestamps = false;

     //Relacion One to Many - Uno a Muchos
     //->hasMany('App\Modelo');
 
     //Relacion Many to One - Muchos a Uno
     //->belongsTo('App\Modelo', 'tabla_id');
 
     public function trabajador(){
         return $this->belongsTo('App\Trabajador', 'trabajador_id');
     }
 
     public function cliente(){
         return $this->belongsTo('App\Cliente', 'cliente_id');
     }
 
     public function proveedor(){
         return $this->belongsTo('App\Proveedor', 'proveedor_id');
     }
}
