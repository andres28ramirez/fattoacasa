<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\CPCMail;
use App\Venta;
use App\Incidencia;

class MailController extends Controller
{
    //
    public function send($id = null){

        DB::beginTransaction();
        try{
            $venta = Venta::find($id);
            $receiver = $venta->cliente->correo;

            Mail::to($receiver)->send(new CPCMail($venta));

             //Grabamos ahora el reporte de la creación del proveedor
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user     = $id;
                $report->name        = $user->name;
                $report->activity    = "Nuevo Correo";
                $report->description = "Nuevo Correo Enviado (".$venta->cliente->correo. " - ".$venta->cliente->nombre.")";

                //Grabamos el reporte de almacenamiento en el sistema
                $report->save();
                
                DB::commit();
            }catch (\Illuminate\Database\QueryException $e){
                //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
                //Asignar los valores al nuevo objeto de reporte
                DB::rollback();
                $report = new Incidencia();
                $report->id_user     = null;
                $report->name        = "Error en el Sistema";
                $report->activity    = "Al Enviar correo";
                $report->description = "Error al enviar correo - Código SQL [".$e->getCode()."]";
    
                //Grabamos el reporte de error en el sistema
                $report->save();
    
                DB::commit();
                return redirect()->url('list-cuentas')->with('status', 'Error al Enviar el Correo');
            }
    }
}
