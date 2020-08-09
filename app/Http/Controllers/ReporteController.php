<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reporte;
use App\Incidencia;
use App\Compra;
use Illuminate\Http\Response;

class ReporteController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        /*EJEMPLO DE COMO FUNCIONA*/
        $pdf = resolve('dompdf.wrapper');

        $nombre = "Reporte_Compras.pdf";
        $header = "REGISTRO DE COMPRAS";
        $filtro = "Todos los registros";
        $compras = Compra::orderBy('id', 'desc')->get();
        $datos = array();
        $titulos = array('Código', 'Proveedor', 'Monto', 'Fecha', 'Crédito', 'Estado');

        foreach ($compras as $buy) {
            $data_content["dato-1"] = $buy->id;
            $data_content["dato-2"] = $buy->proveedor->nombre;
            $data_content["dato-3"] = $buy->monto." Bs";
            $data_content["dato-4"] = $buy->fecha;
            $data_content["dato-5"] = $buy->credito." días";
            if(!$buy->pendiente){
                if( strtotime($buy->fecha."+ ".$buy->credito." days") - strtotime(date("d-m-Y")) > 3*86400)
                    $data_content["dato-6"] = "Pendiente";
                else{
                    if( strtotime($buy->fecha."+ ".$buy->credito." days") - strtotime(date("d-m-Y")) > 0*86400)
                        $data_content["dato-6"] = "Por Caducar";
                    else
                        $data_content["dato-6"] = "Caducado";
                }
            }
            else
                $data_content["dato-6"] = "Pagado";
            array_push($datos,$data_content);
        }

        $pdf->loadView('layouts.pdf',[
                'nombre' => $nombre,
                'datos' => $datos,
                'header' => $header,
                'filtro' => $filtro,
                'titulos' => $titulos
            ]); //carga una vista completa de laravel

        //$pdf->download(); descarga el pdf 
        //$pdf->stream(); solo lo abre

        return $pdf->stream($nombre);
    }

    public function saveError(Request $request)
    {
        $message = json_decode($request->input('values'));

        //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
        //Asignar los valores al nuevo objeto de reporte
        $report = new Incidencia();
        $report->id_user     = null;
        $report->name        = "Error en el Sistema";
        $report->activity    = "Eliminación de Registro";
        $report->description = "Mensaje de Error [".$message."]";

        //Grabamos el reporte de error en el sistema
        $report->save();
        return response()->json(true); 
    }

    public function showManuals(){
        return view('manuales.list');
    }
}
