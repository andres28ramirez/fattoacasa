<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Cliente;
use App\Zona;
use App\Reporte;
use App\Incidencia;
use App\Calendario;
use App\Venta;
use App\Orden_Producto;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Client;

class ClienteController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('p_cliente_proveedor');
    }

    public function index($registros = 10, $id_zona = null, $persona = null, $order = 'id')
    {
        //acomodar por si me modifiquen el $order
        if($order!="nombre" && $order!="persona_contacto" && $order!="rif_cedula" && $order!="telefono" && $order!="correo" && $order!="id_zona" && $order!="direccion")
            $order = "id";

        if($id_zona && $persona){
            if($id_zona=="todos" && $persona=="todos"){
                $clients = Cliente::orderBy($order, 'desc')->paginate($registros);
            }
            else if($id_zona=="todos"){
                $clients = Cliente::where('tipo_cid',$persona)->orderBy($order, 'desc')->paginate($registros);
            }
            else if($persona=="todos"){
                $clients = Cliente::where('id_zona',$id_zona)->orderBy($order, 'desc')->paginate($registros);
            }
            else{
                $clients = Cliente::where('id_zona',$id_zona)->where('tipo_cid',$persona)->orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $clients = Cliente::orderBy($order, 'desc')->paginate($registros);
        }

        //Recojo todas las zonas que tenemos para el filtrado
        $zones = Zona::all();

        return view('clients.list', [
            'clients' => $clients,
            'registros' => $registros,
            'order' => $order,
            'id_zona' => $id_zona,
            'persona' => $persona,
            'zones' => $zones,
        ]);
    }
    
    public function sell($registros = 10, $id = null, $persona = null, $estado = null, $tiempo = null, 
                        $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="id_cliente" && $order!="monto" && $order!="fecha" && $order!="credito" && $order!="pendiente")
            $order = "id";

        if($id && $persona && $tiempo){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $ventas = Venta::orderBy($order, 'desc')->paginate($registros);
        }

        //Para filtrar por cliente
        $clientes = Cliente::all();

        return view('clients.sell_list', [
            'ventas' => $ventas,
            'registros' => $registros,
            'order' => $order,
            'id' => $id,
            'persona' => $persona,
            'estado' => $estado,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
            'clientes' => $clientes,
        ]);
    }

    public function create()
    {
        $zones = Zona::all();

        return view('clients.add', [
            'zones' => $zones
        ]);
    }

    public function save(Request $request)
    {   
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'nombre' => 'required|string|max:255',
                'persona_contacto' => 'required|string|max:255',
                'tipo_cid' => 'required|string|max:255',
                'rif_cedula' => 'required|string|max:255|unique:cliente',
                'telefono' => 'required|string|max:255',
                'correo' => 'required|string|email|max:255|unique:cliente',
                'direccion' => 'required|string',
                'id_zona' => 'required',
            ]);

            $name      = $request->input('nombre');
            $persona   = $request->input('persona_contacto');
            $tipo      = $request->input('tipo_cid');
            $cid       = $request->input('rif_cedula');
            $phone     = $request->input('telefono');
            $email     = $request->input('correo');
            $direction = $request->input('direccion');
            $zone      = $request->input('id_zona');
            
            //Asignar los valores al nuevo objeto de proveedor
            $cliente = new Cliente();
            $cliente->nombre           = $name;
            $cliente->persona_contacto = $persona;
            $cliente->tipo_cid         = $tipo;
            $cliente->rif_cedula       = $cid;
            $cliente->telefono         = $phone;
            $cliente->correo           = $email;
            $cliente->direccion        = $direction;
            $cliente->id_zona          = $zone;

            //Grabamos el proveedor
            $cliente->save();

            //Grabamos ahora el reporte de la creación del proveedor
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Clientes";
            $report->description = "Cliente Añadido Cédula/Rif (".$cid.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();
            
            DB::commit();
            return redirect()->route('list-client')->with('message', 'Cliente Subido Correctamente');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Clientes";
            $report->description = "Error al almacenar cliente - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-client')->with('status', 'Error al Almacenar la información');
        }
    }

    public function show($id = null)
    {
        //$id va ser el id del cliente que deseo
        $client = Cliente::find($id);

        //recojo los eventos donde aparece el cliente
        $eventos = Calendario::where('cliente_id',$id)->orderBy('start', 'asc')->get();
        
        //recojo el numero de ventas que ha hecho el cliente
        $n_ventas = Venta::where('id_cliente',$id)->get();

        //recojo todas las zonas
        $zones = Zona::all();

        //$id va ser el id del proveedor
        if(empty($id) || empty($client))
        return redirect()->route('list-client');

        return view('clients.detail', [
            'client' => $client,
            'n_ventas' =>$n_ventas,
            'zones' => $zones,
            'eventos' => $eventos,
        ]);
    }

    public function detailVentaProducts(Request $request)
    {   
        //$id de la compra
        $id = json_decode($request->input('values'));

        try{
            //recibo los productos de la venta
            $productos = Orden_Producto::where('id_venta',$id)->get();

            //$datos va grabar los productos, cantidad, id y desperdicio
            $datos = array();
            $content = array("id","nombre","cantidad","precio");
            
            foreach ($productos as $pro) {
                $content["nombre"] = $pro->producto->nombre;
                $content["cantidad"] = $pro->cantidad;
                $content["precio"] = $pro->precio;

                array_push($datos,$content);
            }

            return response()->json($datos); 
        }
        catch(\Illuminate\Database\QueryException $e){
            return response()->json(false);       
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try{
            //id del proveedor
            $id = $request->input('id');
            
            $validate = $this->validate($request, [
                'nombre' => 'required|string|max:255',
                'persona_contacto' => 'required|string|max:255',
                'tipo_cid' => 'required|string|max:255',
                'rif_cedula' => 'required|string|max:255|unique:cliente,rif_cedula,'.$id,
                'telefono' => 'required|string|max:255',
                'correo' => 'required|string|email|max:255|unique:cliente,correo,'.$id,
                'direccion' => 'required|string',
                'id_zona' => 'required',
            ]);

            $name      = $request->input('nombre');
            $persona   = $request->input('persona_contacto');
            $tipo      = $request->input('tipo_cid');
            $cid       = $request->input('rif_cedula');
            $phone     = $request->input('telefono');
            $email     = $request->input('correo');
            $direction = $request->input('direccion');
            $zone      = $request->input('id_zona');
            
            //Asignar los valores al cliente que estamos editando
            $cliente = Cliente::find($id);
            $cliente->nombre           = $name;
            $cliente->persona_contacto = $persona;
            $cliente->tipo_cid         = $tipo;
            $cliente->rif_cedula       = $cid;
            $cliente->telefono         = $phone;
            $cliente->correo           = $email;
            $cliente->direccion        = $direction;
            $cliente->id_zona          = $zone;

            //Grabamos los cambios
            $cliente->update();

            //Grabamos ahora el reporte de la creación del cliente
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Clientes";
            $report->description = "Cliente Editado Cédula/Rif (".$cid.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-client', ['id' => $cliente->id])->with('message', 'Cliente Editado Correctamente');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Clientes";
            $report->description = "Error al editar cliente - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-client')->with('status', 'Error al Editar la información');
        }
    }

    public function delete(Request $request)
    {
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $cliente = Cliente::find($valor);
                $agenda = Calendario::where('id_cliente',$valor)->get();

                //Eliminar agenda de cliente
                if($agenda && count($agenda)>=1){
                    foreach ($agenda as $row) {
                        //Elimino agenda de la persona
                        $row->delete();
                    }
                }
                
                $cid = $cliente->rif_cedula;
                //Elimino cliente
                $cliente->delete();
                array_push($valores, $valor);
                
                //GRABAMOS EL REPORTE DE ELIMINADO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $user_id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user = $user_id;
                $report->name    = $user->name;
                $report->activity    = "Módulo Clientes";
                $report->description = "Cliente Eliminado Cédula/Rif (".$cid.")";

                //Grabamos el reporte de almacenamiento en el sistema
                $report->save();
            }

            DB::commit();
            return response()->json($valores); 
        }
        catch(\Illuminate\Database\QueryException $e){
            DB::rollback();
            return response()->json("Error");       
        }
    }

    public function downloadCliente($id_zona = null, $persona = null)
    {    
        if($id_zona && $persona){
            $filtro = "";
            if($id_zona=="todos" && $persona=="todos"){
                $clients = Cliente::all();
            }
            else if($id_zona=="todos"){
                $clients = Cliente::where('tipo_cid',$persona)->get();
            }
            else if($persona=="todos"){
                $clients = Cliente::where('id_zona',$id_zona)->get();
            }
            else{
                $clients = Cliente::where('id_zona',$id_zona)->where('tipo_cid',$persona)->get();
            }
            $persona != "todos" ? $filtro .= "Personalidad: ".$persona : "";
            $id_zona != "todos" ? $filtro .= " | Zona código: ".$id_zona : "";
        }
        else{
            $clients = Cliente::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los clientes" : "";
        $datos = array();
        $titulos = array('Nombre', 'P.Contacto', 'CI/RIF', 'Teléfono', 'Correo', 'Zona', 'Dirección');

        foreach ($clients as $client) {
            $data_content["dato-1"] = $client->nombre;
            $data_content["dato-2"] = $client->persona_contacto;
            $data_content["dato-3"] = $client->tipo_cid."".$client->rif_cedula;
            $data_content["dato-4"] = $client->telefono;
            $data_content["dato-5"] = $client->correo;
            $data_content["dato-6"] = $client->zona->nombre;
            $data_content["dato-7"] = $client->direccion;
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Clientes.pdf", "Registro de Clientes", $filtro, $datos, $titulos);
    }

    public function downloadVenta($id = null, $persona = null, $estado = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null)
    {    
        if($id && $persona && $tiempo){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->get();
            }
            $tiempo != "todos" ? $filtro .= "Entre ".$fecha_1." a ".$fecha_2 : "";
            $id != "todos" ? $filtro .= " | Código: ".$id : "";
            if($estado != "todos")  $estado ? $estado = "Pagado" : $estado = "Pendiente";
            $estado != "todos" ? $filtro .= " | Estado: ".$estado : "";
            $persona != "todos" ? $filtro .= " | Cliente Nro: ".$persona : "";
        }
        else{
            $ventas = Venta::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos las ventas" : "";
        $datos = array();
        $titulos = array('Código', 'Cliente', 'Monto', 'Fecha', 'Crédito', 'Estado');

        foreach ($ventas as $sell) {
            $data_content["id"] = $sell->id;
            $data_content["dato-1"] = $sell->id;
            $data_content["dato-2"] = $sell->cliente->nombre;
            $data_content["dato-3"] = $sell->monto." Bs";
            $data_content["dato-4"] = $sell->fecha;
            $data_content["dato-5"] = $sell->credito." días";
            if(!$sell->pendiente){
                if( strtotime($sell->fecha."+ ".$sell->credito." days") - strtotime(date("d-m-Y")) > 3*86400)
                    $data_content["dato-6"] = "Pendiente";
                else{
                    if( strtotime($sell->fecha."+ ".$sell->credito." days") - strtotime(date("d-m-Y")) > 0*86400)
                        $data_content["dato-6"] = "Por Caducar";
                    else
                        $data_content["dato-6"] = "Caducado";
                }
            }
            else
                $data_content["dato-6"] = "Pagado";

            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Ventas.pdf", "Registro de Ventas", $filtro, $datos, $titulos);
    }

    public function download($nombre, $header, $filtro, $datos, $titulos){
        $pdf = resolve('dompdf.wrapper');
        $pdf->loadView('layouts.pdf',[
            'nombre' => $nombre,
            'datos' => $datos,
            'header' => $header,
            'filtro' => $filtro,
            'titulos' => $titulos
        ]); 
        return $pdf->stream($nombre);
    }
}
