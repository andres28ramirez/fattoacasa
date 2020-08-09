<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Incidencia;
use App\Compra;
use App\Orden_Producto;
use App\Producto;
use App\Producto_Receta;
use App\Desperdicio;
use App\Suministro;
use App\Proveedor;
use App\Zona;
use App\Pago;
use App\Egreso;

class CompraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('p_compra_venta');
    }
  
    public function createCompra()
    {
        //Función que me devuelve mi query paginado y dode yo le indico dentro de paginate(n)
        //la cantidad de N elementos que quiero que muestre
        //para el llevar la pagina siguiente lo realiza a traves de ?page=n en la URL que activa este evento
        $providers = Proveedor::all();
        $products = Producto::all();

        return view('compras.add', [
            'providers' => $providers,
            'products' => $products,
        ]);
    }

    public function saveCompra(Request $request)
    {
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'credito' => 'required|numeric',
                'fecha' => 'required',
                'id_proveedor' => 'required',
                'monto' => 'required|numeric',
            ]);

            $credito      = $request->input('credito');
            $fecha        = $request->input('fecha');
            $id_proveedor = $request->input('id_proveedor');
            $monto        = $request->input('monto');
            $pendiente    = false;
            
            //Asignar los valores al nuevo objeto de compra
            $compra = new Compra();
            $compra->id_proveedor = $id_proveedor;
            $compra->fecha        = $fecha;
            $compra->monto        = $monto;
            $compra->credito      = $credito;
            $compra->pendiente    = $pendiente;
            $compra->id_pago      = null;

            //Revisamos si llego pago y de ser asi lo grabamos
            $banco      = $request->input('banco');
            $referencia = $request->input('referencia');
            $fecha_pago = $request->input('fecha_pago');
            $nota_pago  = $request->input('nota_pago');
            if($banco && $fecha_pago && $nota_pago){
                if($referencia){
                    $validate = $this->validate($request, [
                        'banco' => 'required|string',
                        'fecha_pago' => 'required|string',
                        'referencia' => 'string|max:255|unique:pago',
                        'nota_pago' => 'required|string|max:255',
                    ]);
                }
                else{
                    $validate = $this->validate($request, [
                        'banco' => 'required|string',
                        'fecha_pago' => 'required|string',
                        'nota_pago' => 'required|string|max:255',
                    ]);
                    $referencia = null;
                }
                $pago = new Pago();
                $pago->banco      = $banco;
                $pago->referencia = $referencia;
                $pago->fecha_pago = $fecha_pago;
                $pago->nota_pago  = $nota_pago;
                $pago->save();
                
                $compra->pendiente = true;
                $compra->id_pago   = $pago->id;
            }
            
            //Grabamos la compra
            $compra->save();

            //GRABAMOS LOS PRODUCTOS DE LA COMPRA EN ORDEN Y SUMINISTRO
            $id_compra = $compra->id;
            for ($i = 1; $i <= 30; $i++) {
                if($request->has('form-producto-'.$i)){
                    //RECOJEMOS VALORES
                    $id_producto      = $request->input('form-producto-'.$i);
                    $cantidad         = $request->input('form-cantidad-'.$i);
                    $precio           = $request->input('form-price-'.$i);
                    $fecha_expiracion = $request->input('form-expiracion-'.$i);

                    //ORDEN
                    $orden = new Orden_Producto();
                    $orden->id_venta    = null;
                    $orden->id_compra   = $id_compra;
                    $orden->id_producto = $id_producto;
                    $orden->cantidad    = $cantidad;
                    $orden->precio      = $precio;
                    $orden->save();

                    //SUMINISTRO
                    $suministro = new Suministro();
                    $suministro->id_compra   = $id_compra;
                    $suministro->id_producto  = $id_producto;
                    $suministro->id_proveedor = $id_proveedor;
                    $suministro->precio       = $precio;
                    $suministro->cantidad     = $cantidad;
                    $suministro->expedicion   = $fecha_expiracion;
                    $suministro->save();
                }
            }

            //GRABAMOS EL EGRESO
            $egreso = new Egreso();
            $egreso->id_compra = $compra->id;
            $egreso->monto     = $monto;
            $egreso->save();
            
            //Arreglo las notificaciones de suministro
                //Nuevo para que me acomode las notificaciones
                $this->notifications();

            //GRABAR EL REPORTE DE GUARDADO EXITOSO
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Compras";
            $report->description = "Compra Añadida - Código (".$id_compra.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-compras')->with('message', 'Compra añadida Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Compras";
            $report->description = "Error al almacenar compra - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-compras')->with('status', 'Error al Almacenar la información');
        }
    }

    public function savePago(Request $request)
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
            //id de la compra
            $id_compra = $request->input('id');
            $referencia = $request->input('referencia');

            if($referencia){
                $validate = $this->validate($request, [
                    'banco' => 'required|string',
                    'fecha_pago' => 'required|string',
                    'referencia' => 'string|max:255|unique:pago',
                    'nota_pago' => 'required|string|max:255',
                ]);
            }
            else{
                $validate = $this->validate($request, [
                    'banco' => 'required|string',
                    'fecha_pago' => 'required|string',
                    'nota_pago' => 'required|string|max:255',
                ]);
                $referencia = null;
            }

            $banco      = $request->input('banco');
            $fecha_pago = $request->input('fecha_pago');
            $nota_pago  = $request->input('nota_pago');
            
            //Buscamos la compra correspondiente al pago
            $compra = Compra::find($id_compra);

            $pago = new Pago();
            $pago->banco      = $banco;
            $pago->referencia = $referencia;
            $pago->fecha_pago = $fecha_pago;
            $pago->nota_pago  = $nota_pago;
            $pago->save();
            
            $compra->pendiente = true;
            $compra->id_pago   = $pago->id;
            
            //Grabamos el pago en la compra a traves de un update
            $compra->update();
            
            //GRABAR EL REPORTE DE GUARDADO EXITOSO
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Compras";
            $report->description = "Pago añadido - Código de Compra (".$id_compra.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-compras')->with('message', 'Pago anexado a la compra ('.$id_compra.')');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Compras";
            $report->description = "Error al almacenar pago - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-compras')->with('status', 'Error al Almacenar el Pago');
        }
    }

    public function saveDesperdicio(Request $request)
    {
        DB::beginTransaction();
        try{
            //id de la compra
            $id_compra = json_decode($request->input('id'));

            //productos con su desperdicio
            $productos = json_decode($request->input('products'));

            foreach($productos as &$pro){
                $desperdicio = Desperdicio::where('id_compra',$id_compra)->where('id_producto',$pro->id_producto)->first();
                //Si tiene algo asociado de arranque o no
                if ($desperdicio){ 
                    $desperdicio->cantidad = $pro->cantidad;
                    $desperdicio->update();
                }
                else{
                    $desperdicio = new Desperdicio();
                    $desperdicio->id_compra   = $id_compra;
                    $desperdicio->id_producto = $pro->id_producto;
                    $desperdicio->cantidad    = $pro->cantidad;
                    $desperdicio->save();
                }
            }
            
            //GRABAR EL REPORTE DE GUARDADO EXITOSO
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Compras";
            $report->description = "Despericio añadido - Código de Compra (".$id_compra.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return response()->json(true);
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Compras";
            $report->description = "Error al almacenar desperdicio - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return response()->json(false);
        }
    }

    public function showCompras($registros = 10, $id = null, $persona = null, $estado = null, $tiempo = null, 
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

        return view('compras.list', [
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

    public function showSuministros($registros = 10, $id_zona = null, $persona = null, $order = 'id'){
        //Función que me devuelve mi query paginado y dode yo le indico dentro de paginate(n)
        //la cantidad de N elementos que quiero que muestre
        //para el llevar la pagina siguiente lo realiza a traves de ?page=n en la URL que activa este evento

        //acomodar por si me modifiquen el $order
        if($order!="nombre" && $order!="rif_cedula" && $order!="telefono" && $order!="correo" && $order!="id_zona" && $order!="direccion")
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

        return view('compras.suministro.list', [
            'providers' => $providers,
            'registros' => $registros,
            'order' => $order,
            'id_zona' => $id_zona,
            'persona' => $persona,
            'zones' => $zones,
        ]);
    }

    public function showCuentasPP($registros = 10, $id = null, $persona = null, $tiempo = null, 
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
                                    where('pendiente',0)->
                                    where('id_proveedor','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $compras = Compra::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente',0)->
                                    where('id_proveedor','like', $persona == "todos" ? "%%" : $persona)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $compras = Compra::where('pendiente',0)->orderBy($order, 'desc')->paginate($registros);
        }

        //Para filtrar por proveedor
        $proveedores = Proveedor::all();

        return view('compras.cuentas.list', [
            'compras' => $compras,
            'registros' => $registros,
            'order' => $order,
            'id' => $id,
            'persona' => $persona,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
            'proveedores' => $proveedores,
        ]);
    }

    public function showCuentasPagadas($registros = 10, $id = null, $referencia = null, $banco = null, $tiempo = null, 
                                    $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
        //Función que me devuelve mi query paginado y dode yo le indico dentro de paginate(n)
        //la cantidad de N elementos que quiero que muestre
        //para el llevar la pagina siguiente lo realiza a traves de ?page=n en la URL que activa este evento

        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="monto")
            $order = "id";

        if($id && $referencia && $banco && $tiempo){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Pago::select('id')->where(function($q) use ($referencia) {
                                                $q->where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")
                                                ->orwhere('referencia',$referencia == "todos" ? null : "%%");
                                            })->
                                            where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                            whereBetween('fecha_pago', [$fecha_1, $fecha_2])->get();

                $compras = Compra::where('id_pago','!=',null)->
                                    where('id', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    whereIn('id_pago',$datos)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Pago::select('id')->where(function($q) use ($referencia) {
                                                $q->where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")
                                                ->orwhere('referencia',$referencia == "todos" ? null : "%%");
                                            })->
                                            where('banco','like',$banco == "todos" ? "%%" : $banco)->get();

                $compras = Compra::where('id_pago','!=',null)->
                                    where('id', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    whereIn('id_pago',$datos)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $compras = Compra::where('id_pago','!=',null)->orderBy($order, 'desc')->paginate($registros);
        }

        return view('compras.pagos.list', [
            'compras' => $compras,
            'registros' => $registros,
            'order' => $order,
            'id' => $id,
            'referencia' => $referencia,
            'banco' => $banco,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
        ]);
    }

    public function showDescartadas($registros = 10, $id = null, $persona = null, $estado = null, $tiempo = null, 
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
                $compras = Compra::onlyTrashed()->where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_proveedor','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $compras = Compra::onlyTrashed()->where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_proveedor','like', $persona == "todos" ? "%%" : $persona)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $compras = Compra::onlyTrashed()->orderBy($order, 'desc')->paginate($registros);
        }

        //Para filtrar por proveedor
        $proveedores = Proveedor::all();

        return view('compras.descartadas.list', [
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

    public function detailCompra($id = null)
    {
        //$id va ser el id de la compra que deseo
        $compra = Compra::find($id);

        //recojo todos los proveedores
        $providers = Proveedor::all();

        //recojo todos los productos de la compra
        $productos = Orden_Producto::where('id_compra',$id)->get();

        //Para colocar el listado de productos para acomodar la orden
        $pr_final = Producto_Receta::select('id_producto_final')->get();

        $orden_productos = Producto::whereNotIn('id',$pr_final)->get();

        //$id va ser el id de la compra
        if(empty($id) || empty($compra))
        return redirect()->route('list-compras');

        return view('compras.detail_compra', [
            'compra' => $compra,
            'providers' => $providers,
            'productos' => $productos,
            'orden_productos' => $orden_productos,
        ]);
    }

    public function detailCompraProducts(Request $request)
    {   
        //$id de la compra
        $id = json_decode($request->input('values'));

        try{
            //recibo los productos de la compra
            $productos = Orden_Producto::where('id_compra',$id)->get();

            //recibo el desperdicio de la misma si existe
            $desperdicios = Desperdicio::where('id_compra',$id)->get();

            //$datos va grabar los productos, cantidad, id y desperdicio
            $datos = array();
            $content = array("id","nombre","cantidad","desperdicio");
            
            foreach ($productos as $pro) {
                $content["id"] = $pro->id_producto;
                $content["nombre"] = $pro->producto->nombre;
                $content["cantidad"] = $pro->cantidad;
                $content["desperdicio"] = 0;
                foreach ($desperdicios as $desperdicio){
                    if($desperdicio->id_producto == $pro->id_producto)
                        $content["desperdicio"] = $desperdicio->cantidad;
                }

                array_push($datos,$content);
            }

            return response()->json($datos); 
        }
        catch(\Illuminate\Database\QueryException $e){
            return response()->json(false);       
        }
    }

    public function detailSuministroProducts(Request $request)
    {   
        //$id de la compra
        $id = json_decode($request->input('values'));

        try{
            //recibo los productos de la compra
            $productos = Suministro::where('id_proveedor',$id)->groupBy('id_producto')->get();

            //$datos va grabar los productos, cantidad, id y desperdicio
            $datos = array();
            $content = array("nombre","precio");
            
            foreach ($productos as $pro) {
                $content["nombre"] = $pro->producto->nombre;
                $content["precio"] = number_format($pro->precio,2, ",", ".");

                array_push($datos,$content);
            }

            return response()->json($datos); 
        }
        catch(\Illuminate\Database\QueryException $e){
            return response()->json(false);       
        }
    }

    public function detailPago($id = null)
    {   
        //$id va ser el id del pago que deseo ver
        $datos = Pago::select('id')->where('id',$id)->get();
        $compra = Compra::whereIn('id_pago',$datos)->first();

        //$id va ser el id de la compra
        if(empty($id) || empty($compra))
        return redirect()->route('cp');

        //recojo todos los productos de la compra
        $productos = Orden_Producto::where('id_compra',$compra->id)->get();

        return view('compras.pagos.detail', [
            'compra' => $compra,
            'productos' => $productos,
        ]);
    }

    public function updateCompra(Request $request)
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
            //id de la compra
            $id = $request->input('id');
            
            $validate = $this->validate($request, [
                'credito' => 'required|numeric',
                'fecha' => 'required',
                'id_proveedor' => 'required',
            ]);

            $credito      = $request->input('credito');
            $fecha        = $request->input('fecha');
            $id_proveedor = $request->input('id_proveedor');
            
            //Asignar los valores al nuevo objeto de compra y ubicamos los suministros
            $compra = Compra::find($id);
            $suministro = Suministro::where('id_proveedor',$compra->id_proveedor)->get();
            $compra->id_proveedor = $id_proveedor;
            $compra->fecha        = $fecha;
            $compra->credito      = $credito;

            //Revisamos si llego pago y de ser asi lo grabamos
            $banco      = $request->input('banco');
            $referencia = $request->input('referencia');
            $fecha_pago = $request->input('fecha_pago');
            $nota_pago  = $request->input('nota_pago');
            if($banco && $fecha_pago && $nota_pago){
                if($compra->pago){
                    if($referencia){
                        $validate = $this->validate($request, [
                            'banco' => 'required|string',
                            'fecha_pago' => 'required|string',
                            'referencia' => 'required|string|max:255|unique:pago,referencia,'.$compra->pago->id,
                            'nota_pago' => 'required|string|max:255',
                        ]);
                    }
                    else{
                        $validate = $this->validate($request, [
                            'banco' => 'required|string',
                            'fecha_pago' => 'required|string',
                            'nota_pago' => 'required|string|max:255',
                        ]);
                        $referencia = null;
                    }
                }
                else{
                    if($referencia){
                        $validate = $this->validate($request, [
                            'banco' => 'required|string',
                            'fecha_pago' => 'required|string',
                            'referencia' => 'string|max:255|unique:pago',
                            'nota_pago' => 'required|string|max:255',
                        ]);
                    }
                    else{
                        $validate = $this->validate($request, [
                            'banco' => 'required|string',
                            'fecha_pago' => 'required|string',
                            'nota_pago' => 'required|string|max:255',
                        ]);
                        $referencia = null;
                    }
                }
                //reviso si existe pago asociado
                $compra->pago ? $pago = Pago::find($compra->id_pago) : $pago = new Pago();
                $pago->banco      = $banco;
                $pago->referencia = $referencia;
                $pago->fecha_pago = $fecha_pago;
                $pago->nota_pago  = $nota_pago;

                //vuelvo a revisar a ver si hago save o update
                $compra->pago ? $pago->update() : $pago->save();
                
                $compra->pendiente = true;
                $compra->id_pago   = $pago->id;
            }
            
            //SUMINISTRO: Grabamos los cambios de suministro proveedor
            foreach($suministro as $sum){
                $sum->id_proveedor = $id_proveedor;
                $sum->update();
            }

            //Grabamos los cambios de la compra
            $compra->update();

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Compras";
            $report->description = "Compra Editada Código (".$id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-compra', ['id' => $compra->id])->with('message', 'Compra Editada Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Compras";
            $report->description = "Error al editar compra - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-compras')->with('status', 'Error al Editar la información');
        }
    }

    public function updatePago(Request $request)
    {
        DB::beginTransaction();
        try{
            //id del pago
            $id = $request->input('id');
            $referencia = $request->input('referencia');
            
            if($referencia){
                $validate = $this->validate($request, [
                    'banco' => 'required|string',
                    'fecha_pago' => 'required|string',
                    'referencia' => 'required|string|max:255|unique:pago,referencia,'.$id,
                    'nota_pago' => 'required|string|max:255',
                ]);
            }
            else{
                $validate = $this->validate($request, [
                    'banco' => 'required|string',
                    'fecha_pago' => 'required|string',
                    'nota_pago' => 'required|string|max:255',
                ]);
                $referencia = null;
            }
            
            $banco      = $request->input('banco');
            $fecha_pago = $request->input('fecha_pago');
            $nota_pago  = $request->input('nota_pago');
            
            //obtenemos el objeto del pago para hacerle el update
            $pago = Pago::find($id);
            $pago->banco      = $banco;
            $pago->referencia = $referencia;
            $pago->fecha_pago = $fecha_pago;
            $pago->nota_pago  = $nota_pago;

            //grabo el update del pago
            $pago->update();

            //obtenemos la compra del pago para sacar su código y grabarlo
            $compra = Compra::where('id_pago',$id)->first();

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Compras";
            $report->description = "Pago Editado - Código de Compra (".$compra->id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('compra-pago', ['id' => $pago->id])->with('message', 'Pago Editado Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Compras";
            $report->description = "Error al editar pago - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-compras')->with('status', 'Error al Editar la información');
        }
    }

    public function updateOrden(Request $request)
    {
        DB::beginTransaction();
        try{
            //id y nuevo monto de COMPRA
            $id_compra = $request->input('vc_id');
            $monto = $request->input('vc_monto');

            //ACOMODAMOS EL MONTO DE LA COMPRA
            $compra = Compra::find($id_compra);
            $compra->monto = $monto;
            $compra->update();

            //ELIMINAMOS TODA LA ORDEN PRIMERO
            Orden_Producto::where('id_compra', $id_compra)->delete();

            //GRABAMOS LOS PRODUCTOS DE LA COMPRA EN LA ORDEN
            for ($i = 1; $i <= 30; $i++) {
                if($request->has('form-producto-'.$i)){
                    //RECOJEMOS VALORES
                    $id_producto      = $request->input('form-producto-'.$i);
                    $cantidad         = $request->input('form-cantidad-'.$i);
                    $precio           = $request->input('form-price-'.$i);

                    //ORDEN
                    $orden = new Orden_Producto();
                    $orden->id_compra   = $id_compra;
                    $orden->id_venta    = null;
                    $orden->id_producto = $id_producto;
                    $orden->cantidad    = $cantidad;
                    $orden->precio      = $precio;
                    $orden->save();
                }
            }

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Compras";
            $report->description = "Orden Editada - Código de Compra (".$id_compra.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-compra', ['id' => $id_compra])->with('message', 'Orden Editada Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Compras";
            $report->description = "Error al editar orden - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-compra')->with('status', 'Error al Editar la información');
        }
    }

    public function deleteCompra(Request $request){
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $compra = Compra::find($valor);
                //$desperdicio = Desperdicio::where('id_compra',$valor)->get();
                //$orden = Orden_Producto::where('id_compra',$valor)->get();
                //$egreso = Egreso::where('id_compra',$valor)->first();
                
                //Eliminar Orden
                /* if($orden && count($orden)>=1){
                    foreach ($orden as $row) {
                        //Elimino cada producto de la orden
                        $row->delete();
                    }
                }

                //Eliminar Desperdicio
                if($desperdicio && count($desperdicio)>=1){
                    foreach ($desperdicio as $row) {
                        //Elimino cada desperdicio por producto de la orden
                        $row->delete();
                    }
                } */

                //Eliminamos Egreso
                //$egreso->delete();

                $codigo = $compra->id;
                //Elimino compra
                $compra->delete();

                //Eliminar Pago
                /* if($compra->id_pago){
                    $pago = Pago::find($compra->id_pago);
                    $pago->delete();
                } */

                array_push($valores, $valor);
                
                //GRABAMOS EL REPORTE DE ELIMINADO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $user_id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user = $user_id;
                $report->name    = $user->name;
                $report->activity    = "Módulo Compras";
                $report->description = "Compra Descartada - Código (".$codigo.")";

                //Grabamos el reporte de eliminado en el sistema
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

    public function reintegrarCompra($id = null){
        //$id va ser el id de la compra que deseo
        $compra = Compra::onlyTrashed()->where('id', $id)->first();

        if(empty($id) || empty($compra))
        return redirect()->route('discard-compras');

        DB::beginTransaction();
        try{
            //ACOMODAMOS LA COMPRA LA REINTEGRAMOS
            $compra->restore();

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Compras";
            $report->description = "Compra Anexada Nuevamente - Código (".$compra->id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-compras')->with('message', 'Compra reintegrada Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Compras";
            $report->description = "Error al reintegrar compra - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('discard-compras')->with('status', 'Error al reintegrar Venta');
        }
    }

    public function downloadCompra($id = null, $persona = null, $estado = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null){
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
            $persona != "todos" ? $filtro .= " | Proveedor Nro: ".$persona : "";
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

    public function downloadSuministro($id_zona = null, $persona = null){
        if($id_zona && $persona){
            $filtro = "";
            if($id_zona=="todos" && $persona=="todos"){
                $providers = Proveedor::get();
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
            $id_zona != "todos" ? $filtro .= "Zona Código: ".$id_zona : "";
            $persona != "todos" ? $filtro .= " | Por persona natural o jurídica tipo: ".$persona : "";
        }
        else{
            $providers = Proveedor::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los proveedores" : "";
        $datos = array();
        $titulos = array('Proveedor', 'Zona', 'Dirección', 'Teléfono', 'Correo', 'CI/RIF');

        foreach ($providers as $provider) {
            $data_provider["dato-1"] = $provider->nombre;
            $data_provider["dato-2"] = $provider->zona->nombre;
            $data_provider["dato-3"] = $provider->direccion;
            $data_provider["dato-4"] = $provider->telefono;
            $data_provider["dato-5"] = $provider->correo;
            $data_provider["dato-6"] = $provider->tipo_cid."".$provider->rif_cedula;
            array_push($datos,$data_provider);
        }

        return $this->download("Reporte_Suministradores.pdf", "Registro de Proveedores (Suministro)", $filtro, $datos, $titulos);
    }

    public function downloadCuentasPP($id = null, $persona = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null){
        if($id && $persona && $tiempo){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $compras = Compra::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente',0)->
                                    where('id_proveedor','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $compras = Compra::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente',0)->
                                    where('id_proveedor','like', $persona == "todos" ? "%%" : $persona)->get();
            }

            $tiempo != "todos" ? $filtro .= "Entre ".$fecha_1." a ".$fecha_2 : "";
            $id != "todos" ? $filtro .= " | Código: ".$id : "";
            $persona != "todos" ? $filtro .= " | Proveedor Nro: ".$persona : "";
        }
        else{
            $compras = Compra::where('pendiente',0)->get();
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

        return $this->download("Reporte_ComprasporPagar.pdf", "Registro de Compras por Pagar", $filtro, $datos, $titulos);
    }

    public function downloadPago($id = null, $referencia = null, $banco = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null){
        if($id && $referencia && $banco && $tiempo){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Pago::select('id')->where(function($q) use ($referencia) {
                                                $q->where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")
                                                ->orwhere('referencia',$referencia == "todos" ? null : "%%");
                                            })->
                                            where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                            whereBetween('fecha_pago', [$fecha_1, $fecha_2])->get();

                $compras = Compra::where('id_pago','!=',null)->
                                    where('id', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    whereIn('id_pago',$datos)->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Pago::select('id')->where(function($q) use ($referencia) {
                                                $q->where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")
                                                ->orwhere('referencia',$referencia == "todos" ? null : "%%");
                                            })->
                                            where('banco','like',$banco == "todos" ? "%%" : $banco)->get();

                $compras = Compra::where('id_pago','!=',null)->
                                    where('id', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    whereIn('id_pago',$datos)->get();
            }
            $tiempo != "todos" ? $filtro .= "Entre ".$fecha_1." a ".$fecha_2 : "";
            $referencia != "todos" ? $filtro .= " | Referencia de Pago: ".$referencia : "";
            $banco != "todos" ? $filtro .= " | Banco: ".$banco : "";
            $id != "todos" ? $filtro .= " | Código de compra: ".$id : "";
        }
        else{
            $compras = Compra::where('id_pago','!=',null)->get();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los pagos" : "";
        $datos = array();
        $titulos = array('Proveedor', 'Banco', 'Referencia', 'Fecha', 'Código-Compra', 'Monto');

        foreach ($compras as $buy) {
            foreach($buy->pago->compra as $element){
                $data_content["dato-1"] = $element->proveedor->nombre;
            }
            $data_content["dato-2"] = $buy->pago->banco;
            $data_content["dato-3"] = $buy->pago->referencia ? $buy->pago->referencia : "No posee";
            $data_content["dato-4"] = $buy->pago->fecha_pago;
            $data_content["dato-5"] = $buy->id;
            $data_content["dato-6"] = number_format($buy->monto,2, ",", ".")." Bs";
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Pagos_Compras.pdf", "Registro de Pagos en Compras", $filtro, $datos, $titulos);
    }

    public function downloadDetailCompra($id = null)
    {   
        //$id va ser el id del despacho que deseo ver
        $compra = Compra::find($id);

        //$id va ser el id de la compra
        if(empty($id) || empty($compra))
        return redirect()->route('list-compras');

        //recojo todos los productos de la compra
        $productos = Orden_Producto::where('id_compra',$compra->id)->get();

        $nombre = "Reporte_Compra(".$compra->id.").pdf";
        $header = "Compra - COD(".$compra->id.")";
        $datos = array();
        $total = 0;
        $titulos = array('Producto', 'Cantidad (Kg/Und)', 'Precio', 'Total');

        foreach ($productos as $row) {
            $data_content["dato-1"] = $row->producto->nombre;
            $data_content["dato-2"] = $row->cantidad;
            $data_content["dato-3"] = number_format($row->precio,2, ",", ".")." Bs";
            $data_content["dato-4"] = number_format($row->cantidad*$row->precio,2, ",", ".")." Bs";
            $total += $row->cantidad*$row->precio;
            array_push($datos,$data_content);
        }

        $pdf = resolve('dompdf.wrapper');
        $pdf->loadView('layouts.compradetailpdf',[
            'nombre' => $nombre,
            'datos' => $datos,
            'header' => $header,
            'informacion' => $compra,
            'titulos' => $titulos,
            'datos' => $datos,
            'total' => $total,
        ]); 
        return $pdf->stream($nombre);
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

    //Nuevo para que acomode las notificaciones
    public function notifications()
    {
        $total = 0;

        if(\Auth::user()->permiso_logistica){
            //Acomodamos Suminitro por expirar
            $suministro = Suministro::all();
            $expirar = 0; $caducar = 0;
            foreach ($suministro as $product) {
                if($product->expedicion){
                    if( strtotime($product->expedicion) - strtotime(date("d-m-Y")) < 3*86400){
                        if( strtotime($product->expedicion) - strtotime(date("d-m-Y")) > 0*86400){
                            $expirar++;
                            $total++;
                        }
                        else{
                            $caducar++;
                            $total++;
                        }
                    }
                }
            }
            session()->put('suministro-expirar', $expirar);
            session()->put('suministro-caducar', $caducar);
        }
        $cuenta_expirar = session()->get('cobrar-expirar');
        $cuenta_caducar = session()->get('cobrar-caducar');

        $inventario_expirar = session()->get('inventario-expirar');
        $inventario_caducar = session()->get('inventario-caducar');
        
        $total_final = $cuenta_expirar + $cuenta_caducar + $inventario_expirar + $inventario_caducar + $total;
        session()->put('notificaciones', $total_final);
    }
}
