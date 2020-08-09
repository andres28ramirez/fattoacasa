<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cliente;
use App\Inventario;
use App\Proveedor;
use App\Suministro;
use App\Venta;
use App\Compra;
use App\Despacho;
use App\Calendario;
use App\Producto;
use App\Producto_Receta;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //BLOQUES TOTALES
            //NRO DE CLIENTES
            $clientes = Cliente::selectRaw('COUNT(id) as total')->first();

            //NRO DE PRODUCTOS EN INVENTARIO
            $inventarios = Inventario::selectRaw('SUM(cantidad) as total')->first();

            //NRO DE PROVEEDORES
            $proveedores = Proveedor::selectRaw('COUNT(id) as total')->first();

            //NRO DE PRODUCTOS EN SUMINISTRO
            $suministros = Suministro::selectRaw('SUM(cantidad) as total')->first();

        //CHART VALUES
            $ventas = Venta::selectRaw('COUNT(id) as total, MONTH(fecha) as mes')->groupBy('mes')->get();
            $ventas_data = array();
            for($i = 1; $i <= 12; $i++){
                $grabar = true;
                foreach($ventas as $row){
                    if($i == $row->mes){
                        array_push($ventas_data,$row->total); $grabar=false;
                    }
                }
                $grabar ? array_push($ventas_data,0) : "";
            }
            
            $compras = Compra::selectRaw('COUNT(id) as total, MONTH(fecha) as mes')->groupBy('mes')->get();
            $compras_data = array();
            for($i = 1; $i <= 12; $i++){
                $grabar = true;
                foreach($compras as $row){
                    if($i == $row->mes){
                        array_push($compras_data,$row->total); $grabar=false;
                    }
                }
                $grabar ? array_push($compras_data,0) : "";
            }

        //DESPACHO TABLE
            $despachos = Despacho::where('entregado',0)->orderBy('fecha', 'asc')->skip(0)->take(5)->get();

        //CUENTAS POR COBRAR TABLE
            $cuentas = Venta::where('pendiente',0)->orderBy('fecha', 'asc')->skip(0)->take(5)->get();

        //AGENDA
            //$agendas = Calendario::where('activo',0)->orderBy('start', 'asc')->skip(0)->take(3)->get();
            $agendas = Calendario::all();
            $agenda_cliente = 0; $agenda_proveedor = 0; $agenda_trabajador = 0;

            foreach($agendas as $row){
                if($row->cliente_id)
                    $agenda_cliente++;
                
                if($row->proveedor_id)
                    $agenda_proveedor++;

                if($row->trabajador_id)
                    $agenda_trabajador++;
            }
        
        //INVENTARIO PROXIMO A INEXISTENCIA
            $id_final = Producto_Receta::select('id_producto_final')->get();
            $prod_inventario = Producto::whereIn('id',$id_final)->get();
            $inventario = Inventario::selectRaw('SUM(cantidad) as total, id_producto')->groupBy('id_producto')->get();
            $datos_inventario = array();

            foreach ($prod_inventario as $pro) {
                $data_content["id"] = $pro->id;
                $data_content["nombre"] = $pro->nombre;
                $data_content["descripcion"] = $pro->descripcion;
                $cantidad = 0;
                foreach ($inventario as $row) {
                    if($pro->id == $row->id_producto){
                        $cantidad = $row->total;
                        continue;
                    }
                }
                $data_content["cantidad"] = $cantidad;
                array_push($datos_inventario,$data_content);
            }

        //SUMINISTRO PROXIMO A INEXISTENCIA
            $id_final = Producto_Receta::select('id_ingrediente')->get();
            $prod_suministro = Producto::whereIn('id',$id_final)->get();
            $suministro = Suministro::selectRaw('SUM(cantidad) as total, id_producto')->groupBy('id_producto')->get();
            $datos_suministro = array();

            foreach ($prod_suministro as $pro) {
                $data["id"] = $pro->id;
                $data["nombre"] = $pro->nombre;
                $data["descripcion"] = $pro->descripcion;
                $cantidad = 0;
                foreach ($suministro as $row) {
                    if($pro->id == $row->id_producto){
                        $cantidad = $row->total;
                        continue;
                    }
                }
                $data["cantidad"] = $cantidad;
                array_push($datos_suministro,$data);
            }


        return view('home', [
            'clientes' => $clientes,
            'proveedores' => $proveedores,
            'inventarios' => $inventarios,
            'suministros' => $suministros,
            'ventas_data' => $ventas_data,
            'compras_data' => $compras_data,
            'despachos' => $despachos,
            'cuentas' => $cuentas,
            'agenda_cliente' => $agenda_cliente,
            'agenda_proveedor' => $agenda_proveedor,
            'agenda_trabajador' => $agenda_trabajador,
            'd_inventario' => $datos_inventario,
            'd_suministro' => $datos_suministro,
        ]);
    }

    public function ajaxindicator(Request $request){ 
        //$id lleva que chart estaremos filtrando
        $id = json_decode($request->input('id'));
        $busqueda = json_decode($request->input('busqueda'));  
        try{
            //DATOS QUE MANDARE PARA EL FILTRADO QUE SERIAN LOS LABELS Y VALORES
            $datos = array(); //LLEVARA DENTRO LABELS Y LOS VALUES
            $values1 = array(); $values2 = array();
            //PERIODO O FORMA DE BUSQUEDA
            if($busqueda[0] == "Mensual"){
                $periodo = 'MONTH(fecha) as periodo';
                $labels = array("ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sep.", "oct.", "nov.", "dic.");
                $inicio = 1; $fin = 12; //para el for al grabar los values
            }
            else{
                $periodo = 'DATE_FORMAT(fecha, "%w") as periodo';
                $labels = array("dom.", "lun.", "mar.", "mie.", "jue.", "vie.", "sab.");
                $inicio = 0; $fin = 6; //para el for al grabar los values
            }

            if($id=="c1") { //CHART RELACION COMPRAS Y VENTAS
                if($busqueda[1]=="Año"){
                    $ventas = Venta::selectRaw('COUNT(id) as total, '.$periodo)->
                                    whereYear('fecha', $busqueda[4])->groupBy('periodo')->get();
                    
                    $compras = Compra::selectRaw('COUNT(id) as total, '.$periodo)->
                                    whereYear('fecha', $busqueda[4])->groupBy('periodo')->get();
                }
                elseif($busqueda[1]=="Específico"){
                    $ventas = Venta::selectRaw('COUNT(id) as total, '.$periodo)->
                    whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                    
                    $compras = Compra::selectRaw('COUNT(id) as total, '.$periodo)->
                    whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                }
                else{
                    $ventas = Venta::selectRaw('COUNT(id) as total, '.$periodo)->groupBy('periodo')->get();
                    
                    $compras = Compra::selectRaw('COUNT(id) as total, '.$periodo)->groupBy('periodo')->get();
                }

                for($i = $inicio; $i <= $fin; $i++){
                    $grabar_entradas = true;
                    $grabar_salidas = true;
                    
                    foreach($compras as $row){
                        if($i == $row->periodo){
                            array_push($values1,$row->total); $grabar_entradas=false;
                        }
                    }

                    foreach($ventas as $row){
                        if($i == $row->periodo){
                            array_push($values2,$row->total); $grabar_salidas=false;
                        }
                    }

                    $grabar_entradas ? array_push($values1,0) : "";
                    $grabar_salidas ? array_push($values2,0) : "";
                }
                array_push($datos,$labels,'Compras','Ventas',$values1,$values2);      
            } 

            return response()->json($datos); 
        }
        catch(\Illuminate\Database\QueryException $e){
            return response()->json(false);       
        }
    }
}
