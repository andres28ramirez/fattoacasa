<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Proveedor;
use App\Zona;
use App\Reporte;
use App\Incidencia;
use App\Calendario;
use App\Compra;
use App\Orden_Producto;
use App\Suministro;
use Illuminate\Http\Response;

class ProveedorController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('p_cliente_proveedor');
    }

    public function index($registros = 10, $id_zona = null, $persona = null, $order = 'id')
    {   
        //Función que me devuelve mi query paginado y dode yo le indico dentro de paginate(n)
        //la cantidad de N elementos que quiero que muestre
        //para el llevar la pagina siguiente lo realiza a traves de ?page=n en la URL que activa este evento

        //acomodar por si me modifiquen el $order
        if($order!="nombre" && $order!="persona_contacto" && $order!="rif_cedula" && $order!="telefono" && $order!="correo" && $order!="id_zona" && $order!="direccion")
            $order = "id";

        if($id_zona && $persona){
            if($id_zona=="todos" && $persona=="todos"){
                $providers = Proveedor::orderBy($order, 'desc')->paginate($registros);
            }
            else if($id_zona=="todos"){
                $providers = Proveedor::where('tipo_cid',$persona)->orderBy($order, 'desc')->paginate($registros);
            }
            else if($persona=="todos"){
                $providers = Proveedor::where('id_zona',$id_zona)->orderBy($order, 'desc')->paginate($registros);
            }
            else{
                $providers = Proveedor::where('id_zona',$id_zona)->where('tipo_cid',$persona)->orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $providers = Proveedor::orderBy($order, 'desc')->paginate($registros);
        }

        //Recojo todas las zonas que tenemos para el filtrado
        $zones = Zona::all();

        return view('providers.list', [
            'providers' => $providers,
            'registros' => $registros,
            'order' => $order,
            'id_zona' => $id_zona,
            'persona' => $persona,
            'zones' => $zones,
        ]);
    }

    public function buys($registros = 10, $id = null, $persona = null, $estado = null, $tiempo = null, 
                        $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {   
        //Función que me devuelve mi query paginado y dode yo le indico dentro de paginate(n)
        //la cantidad de N elementos que quiero que muestre
        //para el llevar la pagina siguiente lo realiza a traves de ?page=n en la URL que activa este evento

        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="id_proveedor" && $order!="monto" && $order!="fecha" && $order!="credito" && $order!="pendiente")
            $order = "id";

        if($id && $persona && $tiempo){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $compras = Compra::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_proveedor','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $compras = Compra::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_proveedor','like', $persona == "todos" ? "%%" : $persona)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $compras = Compra::orderBy($order, 'desc')->paginate($registros);
        }

        //Para filtrar por proveedor
        $proveedores = Proveedor::all();

        return view('providers.buy_list', [
            'compras' => $compras,
            'registros' => $registros,
            'order' => $order,
            'id' => $id,
            'persona' => $persona,
            'estado' => $estado,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
            'proveedores' => $proveedores,
        ]);
    }

    public function create()
    {
        //Función que me devuelve mi query paginado y dode yo le indico dentro de paginate(n)
        //la cantidad de N elementos que quiero que muestre
        //para el llevar la pagina siguiente lo realiza a traves de ?page=n en la URL que activa este evento
        $zones = Zona::all();

        return view('providers.add', [
            'zones' => $zones
        ]);
    }

    public function save(Request $request)
    {   
        //VALIDACIONES DE FORMULARIO
        /* $validate = $this->validate($request, [
            REGLAS DEL REQUEST
            unique:users //UNICO EN LA TABLA DE USUARIOS DEL DATO QUE LLEGA
            required //DATO REQUERIDO
            string //QUE SEA DATO TIPO STRING
            max:n //MAXIMO DE N CARACTERES
            email // QUE SEA UN EMAIL
            confirmed //EN CASO DE CONTRASEÑAS QUE COMPARTAN MISMOS VALORES
            min:n //MINIMO DE N CARACTERES
            unique:users,nick,id //CAMPO UNICO EN LA TABLA PERO EXISTE LA EXCEPCIÓN SI ES EL MISMO VALOR DEL MISMO USUARIO
        ]); */
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'nombre' => 'required|string|max:255',
                'persona_contacto' => 'required|string|max:255',
                'tipo_cid' => 'required|string|max:255',
                'rif_cedula' => 'required|string|max:255|unique:proveedor',
                'telefono' => 'required|string|max:255',
                'correo' => 'required|string|email|max:255|unique:proveedor',
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
            $proveedor = new Proveedor();
            $proveedor->nombre           = $name;
            $proveedor->persona_contacto = $persona;
            $proveedor->tipo_cid         = $tipo;
            $proveedor->rif_cedula       = $cid;
            $proveedor->telefono         = $phone;
            $proveedor->correo           = $email;
            $proveedor->direccion        = $direction;
            $proveedor->id_zona          = $zone;

            //Grabamos el proveedor
            $proveedor->save();

            //Grabamos ahora el reporte de la creación del proveedor
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Proveedores";
            $report->description = "Proveedor Añadido Cédula/Rif (".$cid.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();
            
            DB::commit();
            return redirect()->route('list-prov')->with('message', 'Proveedor añadido Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Proveedores";
            $report->description = "Error al almacenar proveedor - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-prov')->with('status', 'Error al Almacenar la información');
        }
    }

    public function show($id = null)
    {   
        //$id va ser el id del proveedor que deseo
        $provider = Proveedor::find($id);

        //recojo los productos que ofrece el proveedor
        $suministro = Suministro::where('id_proveedor',$id)->groupBy('id_producto')->orderBy('id', 'asc')->get();

        //recojo los eventos donde aparece el cliente
        $eventos = Calendario::where('proveedor_id',$id)->orderBy('start', 'asc')->get();
         
        //recojo todas las zonas
        $zones = Zona::all();

        //$id va ser el id del proveedor
        if(empty($id) || empty($provider))
        return redirect()->route('list-prov');

        return view('providers.detail', [
            'provider' => $provider,
            'suministro' => $suministro,
            'zones' => $zones,
            'eventos' => $eventos,
        ]);
    }

    public function detailCompraProducts(Request $request)
    {   
        //$id de la compra
        $id = json_decode($request->input('values'));

        try{
            //recibo los productos de la compra
            $productos = Orden_Producto::where('id_compra',$id)->get();

            //$datos va grabar los productos, cantidad, id y desperdicio
            $datos = array();
            $content = array("id","nombre","cantidad","precio");
            
            foreach ($productos as $pro) {
                $content["nombre"] = $pro->producto->nombre;
                $content["cantidad"] = $pro->cantidad;
                $content["precio"] = number_format($pro->precio,2, ",", ".");

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
        //VALIDACIONES DE FORMULARIO
        /* $validate = $this->validate($request, [
            REGLAS DEL REQUEST
            unique:users //UNICO EN LA TABLA DE USUARIOS DEL DATO QUE LLEGA
            required //DATO REQUERIDO
            string //QUE SEA DATO TIPO STRING
            max:n //MAXIMO DE N CARACTERES
            email // QUE SEA UN EMAIL
            confirmed //EN CASO DE CONTRASEÑAS QUE COMPARTAN MISMOS VALORES
            min:n //MINIMO DE N CARACTERES
            unique:users,nick,id //CAMPO UNICO EN LA TABLA PERO EXISTE LA EXCEPCIÓN SI ES EL MISMO VALOR DEL MISMO USUARIO
        ]); */
        DB::beginTransaction();
        try{
            //id del proveedor
            $id = $request->input('id');
            
            $validate = $this->validate($request, [
                'nombre' => 'required|string|max:255',
                'persona_contacto' => 'required|string|max:255',
                'tipo_cid' => 'required|string|max:255',
                'rif_cedula' => 'required|string|max:255|unique:proveedor,rif_cedula,'.$id,
                'telefono' => 'required|string|max:255',
                'correo' => 'required|string|email|max:255|unique:proveedor,correo,'.$id,
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
            
            //Asignar los valores al proveedor que estamos editando
            $proveedor = Proveedor::find($id);
            $proveedor->nombre           = $name;
            $proveedor->persona_contacto = $persona;
            $proveedor->tipo_cid         = $tipo;
            $proveedor->rif_cedula       = $cid;
            $proveedor->telefono         = $phone;
            $proveedor->correo           = $email;
            $proveedor->direccion        = $direction;
            $proveedor->id_zona          = $zone;

            //Grabamos los cambios
            $proveedor->update();

            //Grabamos ahora el reporte de la creación del proveedor
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Proveedores";
            $report->description = "Proveedor Editado Cédula/Rif (".$cid.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-prov', ['id' => $proveedor->id])->with('message', 'Proveedor Editado Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Proveedores";
            $report->description = "Error al editar proveedor - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-prov')->with('status', 'Error al Editar la información');
        }
    }

    public function delete(Request $request)
    {
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $proveedor = Proveedor::find($valor);
                $agenda = Calendario::where('id_proveedor',$valor)->get();

                //Eliminar agenda de la persona
                if($agenda && count($agenda)>=1){
                    foreach ($agenda as $row) {
                        //Elimino agenda persona
                        $row->delete();
                    }
                }
                
                $cid = $proveedor->rif_cedula;
                //Elimino proveedor
                $proveedor->delete();
                array_push($valores, $valor);
                
                //GRABAMOS EL REPORTE DE ELIMINADO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $user_id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user = $user_id;
                $report->name    = $user->name;
                $report->activity    = "Módulo Proveedores";
                $report->description = "Proveedor Eliminado Cédula/Rif (".$cid.")";

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

    public function downloadProveedor($id_zona = null, $persona = null)
    {    
        if($id_zona && $persona){
            $filtro = "";
            if($id_zona=="todos" && $persona=="todos"){
                $providers = Proveedor::all();
            }
            else if($id_zona=="todos"){
                $providers = Proveedor::where('tipo_cid',$persona)->get();
            }
            else if($persona=="todos"){
                $providers = Proveedor::where('id_zona',$id_zona)->get();
            }
            else{
                $providers = Proveedor::where('id_zona',$id_zona)->where('tipo_cid',$persona)->get();
            }
            $persona != "todos" ? $filtro .= "Personalidad: ".$persona : "";
            $id_zona != "todos" ? $filtro .= " | Zona código: ".$id_zona : "";
        }
        else{
            $providers = Proveedor::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los proveedores" : "";
        $datos = array();
        $titulos = array('Nombre', 'P.Contacto', 'CI/RIF', 'Teléfono', 'Correo', 'Zona', 'Dirección');

        foreach ($providers as $provider) {
            $data_provider["dato-1"] = $provider->nombre;
            $data_provider["dato-2"] = $provider->persona_contacto;
            $data_provider["dato-3"] = $provider->tipo_cid."".$provider->rif_cedula;
            $data_provider["dato-4"] = $provider->telefono;
            $data_provider["dato-5"] = $provider->correo;
            $data_provider["dato-6"] = $provider->zona->nombre;
            $data_provider["dato-7"] = $provider->direccion;
            array_push($datos,$data_provider);
        }

        return $this->download("Reporte_Proveedores.pdf", "Registro de Proveedores", $filtro, $datos, $titulos);
    }

    public function downloadCompra($id = null, $persona = null, $estado = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null)
    {    
        if($id && $persona && $tiempo){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $compras = Compra::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_proveedor','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $compras = Compra::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_proveedor','like', $persona == "todos" ? "%%" : $persona)->get();
            }
            $tiempo != "todos" ? $filtro .= "Entre ".$fecha_1." a ".$fecha_2 : "";
            $id != "todos" ? $filtro .= " | Código: ".$id : "";
            if($estado != "todos")  $estado ? $estado = "Pagado" : $estado = "Pendiente";
            $estado != "todos" ? $filtro .= " | Estado: ".$estado : "";
            $persona != "todos" ? $filtro .= " | Cliente Nro: ".$persona : "";
        }
        else{
            $compras = Compra::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todas las compras" : "";
        $datos = array();
        $titulos = array('Código', 'Proveedor', 'Monto', 'Fecha', 'Crédito', 'Estado');

        foreach ($compras as $buy) {
            $data_content["dato-1"] = $buy->id;
            $data_content["dato-2"] = $buy->proveedor->nombre;
            $data_content["dato-3"] = number_format($buy->monto,2, ",", ".")." Bs";
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

        return $this->download("Reporte_Compras.pdf", "Registro de Compras", $filtro, $datos, $titulos);
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
