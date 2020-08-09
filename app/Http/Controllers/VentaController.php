<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Incidencia;
use App\Venta;
use App\Orden_Producto;
use App\Producto;
use App\Producto_Receta;
use App\Despacho;
use App\Inventario;
use App\Cliente;
use App\Zona;
use App\Pago;
use App\Trabajador;

class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('p_compra_venta');
    }

    public function createVenta()
    {
        $clients = Cliente::all();
        $products = Inventario::groupBy('id_producto')->get();

        return view('ventas.add', [
            'clients' => $clients,
            'products' => $products,
        ]);
    }

    public function saveVenta(Request $request)
    {
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'credito' => 'required|numeric',
                'fecha' => 'required',
                'id_cliente' => 'required',
                'monto' => 'required|numeric',
                'nota' => 'required|string|max:255',
            ]);

            $credito    = $request->input('credito');
            $fecha      = $request->input('fecha');
            $id_cliente = $request->input('id_cliente');
            $monto      = $request->input('monto');
            $nota       = $request->input('nota');
            $pendiente  = false;
            
            //Asignar los valores al nuevo objeto de compra
            $venta = new Venta();
            $venta->id_cliente = $id_cliente;
            $venta->fecha      = $fecha;
            $venta->monto      = $monto;
            $venta->credito    = $credito;
            $venta->nota       = $nota;
            $venta->pendiente  = $pendiente;
            $venta->id_pago    = null;

            //Revisamos si llego pago y de ser asi lo grabamos
            $banco      = $request->input('banco');
            $referencia = $request->input('referencia');
            $fecha_pago = $request->input('fecha_pago');
            $nota_pago = $request->input('nota_pago');
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
                
                $venta->pendiente = true;
                $venta->id_pago   = $pago->id;
            }
            
            //Grabamos la venta
            $venta->save();

            //Comprobamos que la cantidad del producto de la venta existe en inventario
            //Quedara en true si al final todo fue correcto
            $grabar = true;
            
            //Grabara todos los productos que le falte X cantidad de la venta
            $restantes = array();

            //GRABAMOS LOS PRODUCTOS DE LA VENTA EN ORDEN, RESTAMOS INVENTARIO Y REVISAMOS SI HAY CAPACIDAD
            $id_venta = $venta->id;
            for ($i = 1; $i <= 30; $i++) {
                if($request->has('form-producto-'.$i)){
                    //RECOJEMOS VALORES
                    $id_producto      = $request->input('form-producto-'.$i);
                    $cantidad         = $request->input('form-cantidad-'.$i);
                    $precio           = $request->input('form-price-'.$i);
                    $fecha_expiracion = $request->input('form-expiracion-'.$i);

                    //ORDEN
                    $orden = new Orden_Producto();
                    $orden->id_venta    = $id_venta;
                    $orden->id_compra   = null;
                    $orden->id_producto = $id_producto;
                    $orden->cantidad    = $cantidad;
                    $orden->precio      = $precio;
                    $orden->save();

                    $producto_data = Producto::find($id_producto);
                    $inv_cantidad = Inventario::select(DB::raw('SUM(cantidad) as cantidad'))->where('id_producto',$id_producto)->groupBy('id_producto')->first();
                    if($inv_cantidad){
                        if($cantidad - $inv_cantidad->cantidad <= 0){
                            //Modificamos el inventario
                            $modificar = true;
                            $inventario = Inventario::where('id_producto',$id_producto)->orderBy('expedicion', 'asc')->get();
                            foreach ($inventario as &$row){
                                if($modificar){
                                    if($cantidad - $row->cantidad < 0){
                                        $modificar = false;
                                        $row->cantidad = $row->cantidad - $cantidad;
                                        $row->update();
                                    }
                                    else{
                                        $cantidad = $cantidad - $row->cantidad;
                                        $row->delete();
                                        if($cantidad == 0) $modificar = false;
                                    }
                                }
                            }
                        }
                        else{
                            $grabar = false;
                            $producto_restante = array("nombre","cantidad");
                            $producto_restante["nombre"] = $producto_data->nombre;
                            $producto_restante["cantidad"] = $cantidad - $inv_cantidad->cantidad;
                            array_push($restantes,$producto_restante);
                        }
                    }
                    else{
                        $grabar = false;
                        $producto_restante = array("nombre","cantidad");
                        $producto_restante["nombre"] = $producto_data->nombre;
                        $producto_restante["cantidad"] = $cantidad;
                        array_push($restantes,$producto_restante);
                    }
                }
            }
            
            if($grabar){
                //Arreglo la variable session de cuentas por cobrar
                if(!$venta->pendiente)
                    $this->resetNotification($venta,'save');

                //Arreglo las notificaciones de inventario
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
                $report->activity    = "Módulo Ventas";
                $report->description = "Venta Añadida - Código (".$id_venta.")";

                //Grabamos el reporte de almacenamiento en el sistema
                $report->save();

                DB::commit();
                return redirect()->route('list-ventas')->with('message', 'Venta añadida Exitosamente!');
            }
            else{
                DB::rollback();
                return redirect()->route('agg-venta')->with('fallo', $restantes);
            }
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Ventas";
            $report->description = "Error al almacenar venta - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-ventas')->with('status', 'Error al Almacenar la información');
        }
    }

    public function savePago(Request $request)
    {
        DB::beginTransaction();
        try{
            //id de la venta
            $id_venta = $request->input('id');
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
            $venta = Venta::find($id_venta);

            $pago = new Pago();
            $pago->banco      = $banco;
            $pago->referencia = $referencia;
            $pago->fecha_pago = $fecha_pago;
            $pago->nota_pago  = $nota_pago;
            $pago->save();
            
            $venta->pendiente = true;
            $venta->id_pago   = $pago->id;
            
            //Grabamos el pago en la venta a traves de un update
            $venta->update();

            //Acomodamos las notificaciones
                $this->resetNotification($venta, 'delete');
            
            //GRABAR EL REPORTE DE GUARDADO EXITOSO
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Ventas";
            $report->description = "Pago añadido - Código de Venta (".$id_venta.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-ventas')->with('message', 'Pago anexado a la venta ('.$id_venta.')');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Ventas";
            $report->description = "Error al almacenar pago - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-ventas')->with('status', 'Error al Almacenar el Pago');
        }
    }

    public function saveDespacho(Request $request)
    {
        DB::beginTransaction();
        try{
            //id de la venta
            $id_venta = $request->input('id');

            $validate = $this->validate($request, [
                'id_trabajador' => 'required',
                'fecha' => 'required',
                'nota' => 'required|string|max:255',
            ]);

            $id_trabajador = $request->input('id_trabajador');
            $fecha         = $request->input('fecha');
            $nota          = $request->input('nota');
            
            //Grabamos el despacho de la venta
            $despacho = new Despacho();
            $despacho->id_venta      = $id_venta;
            $despacho->id_trabajador = $id_trabajador;
            $despacho->fecha         = $fecha;
            $despacho->nota          = $nota;
            $despacho->entregado     = false;
            $despacho->save();
            
            //GRABAR EL REPORTE DE GUARDADO EXITOSO
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Ventas";
            $report->description = "Despacho añadido - Código de Venta (".$id_venta.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-despachos')->with('message', 'Despacho anexado a la venta ('.$id_venta.')');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Ventas";
            $report->description = "Error al almacenar despacho - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-pedidos')->with('status', 'Error al Almacenar el Despacho');
        }
    }

    public function showVentas($registros = 10, $id = null, $persona = null, $estado = null, $tiempo = null, 
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

        return view('ventas.list', [
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

    public function showPedidos($registros = 10, $id = null, $persona = null, $estado = null, $tiempo = null, 
                                $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="id_cliente" && $order!="monto" && $order!="fecha" && $order!="credito" && $order!="pendiente")
            $order = "id";

        if($id && $persona && $tiempo){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Despacho::select('id_venta')->get();
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    whereNotIn('id',$datos)->orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Despacho::select('id_venta')->get();
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereNotIn('id',$datos)->orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $datos = Despacho::select('id_venta')->get();
            $ventas = Venta::whereNotIn('id',$datos)->orderBy($order, 'desc')->paginate($registros);
        }

        //Para filtrar por cliente
        $clientes = Cliente::all();

        //Para añadir el trabajador que hara el despacho
        $trabajadores = Trabajador::where('tipo','Despachador')->get();

        return view('ventas.pedidos.list', [
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
            'trabajadores' => $trabajadores,
        ]);
    }

    public function showDespachos($registros = 10, $id = null, $persona = null, $despachador = null, $estado = null, 
                                $tiempo = null, $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id_venta" && $order!="id_trabajador" && $order!="fecha" && $order!="entregado")
            $order = "id";

        if($id && $persona && $despachador && $tiempo){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Venta::select('id')->where('id_cliente', 'like', $persona == "todos" ? "%%" : $persona)->get();
                $despachos = Despacho::where('id_venta', 'like', $id == "todos" ? "%%" : $id)->
                                    where('id_trabajador', 'like', $despachador == "todos" ? "%%" : $despachador)->
                                    where('entregado','like', $estado == "todos" ? "%%" : $estado)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    whereIn('id_venta',$datos)->orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Venta::select('id')->where('id_cliente', 'like', $persona == "todos" ? "%%" : $persona)->get();
                $despachos = Despacho::where('id_venta', 'like', $id == "todos" ? "%%" : $id)->
                                    where('id_trabajador', 'like', $despachador == "todos" ? "%%" : $despachador)->
                                    where('entregado','like', $estado == "todos" ? "%%" : $estado)->
                                    whereIn('id_venta',$datos)->orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $despachos = Despacho::orderBy($order, 'desc')->paginate($registros);
        }

        //Para filtrar por cliente
        $clientes = Cliente::all();

        //Para filtrar por despachador
        $trabajadores = Trabajador::where('tipo','Despachador')->get();

        return view('ventas.despacho.list', [
            'despachos' => $despachos,
            'registros' => $registros,
            'order' => $order,
            'id' => $id,
            'persona' => $persona,
            'despachador' => $despachador,
            'estado' => $estado,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
            'clientes' => $clientes,
            'trabajadores' => $trabajadores,
        ]);
    }

    public function showCuentas($registros = 10, $id = null, $persona = null, $tiempo = null, 
                                $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="id_cliente" && $order!="monto" && $order!="fecha" && $order!="credito" && $order!="pendiente")
        $order = "id";

        if($id && $persona && $tiempo){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente',0)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente',0)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $ventas = Venta::where('pendiente',0)->orderBy($order, 'desc')->paginate($registros);
        }

        //Para filtrar por proveedor
        $clientes = Cliente::all();

        return view('ventas.cuentas.list', [
            'ventas' => $ventas,
            'registros' => $registros,
            'order' => $order,
            'id' => $id,
            'persona' => $persona,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
            'clientes' => $clientes,
        ]);
    }

    public function showPagos($registros = 10, $id = null, $referencia = null, $banco = null, $tiempo = null, 
                                $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
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

                $ventas = Venta::where('id_pago','!=',null)->
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

                $ventas = Venta::where('id_pago','!=',null)->
                                    where('id', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    whereIn('id_pago',$datos)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $ventas = Venta::where('id_pago','!=',null)->orderBy($order, 'desc')->paginate($registros);
        }

        return view('ventas.pagos.list', [
            'ventas' => $ventas,
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
        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="id_cliente" && $order!="monto" && $order!="fecha" && $order!="credito" && $order!="pendiente")
            $order = "id";

        if($id && $persona && $tiempo){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $ventas = Venta::onlyTrashed()->where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $ventas = Venta::onlyTrashed()->where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $ventas = Venta::onlyTrashed()->orderBy($order, 'desc')->paginate($registros);
        }

        //Para filtrar por cliente
        $clientes = Cliente::all();

        return view('ventas.descartadas.list', [
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

    public function detailVenta($id = null)
    {
        //$id va ser el id de la venta que deseo
        $venta = Venta::find($id);

        //recojo todos los proveedores
        $clients = Cliente::all();

        //recojo todos los productos de la venta
        $productos = Orden_Producto::where('id_venta',$id)->get();

        //Para colocar el listado de productos para acomodar la orden
        $pr_final = Producto_Receta::select('id_producto_final')->get();

        $orden_productos = Producto::whereIn('id',$pr_final)->get();

        if(empty($id) || empty($venta))
        return redirect()->route('list-ventas');

        return view('ventas.detail', [
            'venta' => $venta,
            'clients' => $clients,
            'productos' => $productos,
            'orden_productos' => $orden_productos,
        ]);
    }

    public function detailPago($id = null)
    {
        //$id va ser el id del pago que deseo ver
        $datos = Pago::select('id')->where('id',$id)->get();
        $venta = Venta::whereIn('id_pago',$datos)->first();

        //$id va ser el id de la compra
        if(empty($id) || empty($venta))
        return redirect()->route('list-pagos');

        //recojo todos los productos de la compra
        $productos = Orden_Producto::where('id_venta',$venta->id)->get();

        return view('ventas.pagos.detail', [
            'venta' => $venta,
            'productos' => $productos,
        ]);
    }

    public function detailDespacho($id = null)
    {
        //$id va ser el id del despacho que deseo ver
        $despacho = Despacho::find($id);

        //$id va ser el id de la compra
        if(empty($id) || empty($despacho))
        return redirect()->route('list-despachos');

        //recojo todos los productos de la compra
        $productos = Orden_Producto::where('id_venta',$despacho->id_venta)->get();

        //trabajadores que hacen despacho
        $trabajadores = Trabajador::where('tipo','Despachador')->get();

        return view('ventas.despacho.detail', [
            'despacho' => $despacho,
            'productos' => $productos,
            'trabajadores' => $trabajadores,
        ]);
    }

    public function updateVenta(Request $request)
    {
        DB::beginTransaction();
        try{
            //id de la venta
            $id = $request->input('id');
            
            $validate = $this->validate($request, [
                'credito' => 'required|numeric',
                'fecha' => 'required',
                'id_cliente' => 'required',
                'nota' => 'required|string|max:255',
            ]);

            $credito      = $request->input('credito');
            $fecha        = $request->input('fecha');
            $id_cliente   = $request->input('id_cliente');
            $nota         = $request->input('nota');
            
            //Asignar los valores al nuevo objeto de compra y ubicamos los suministros
            $venta = Venta::find($id);
            $past = Venta::find($id); //para acomodar las notificaciones
            $venta->id_cliente   = $id_cliente;
            $venta->fecha        = $fecha;
            $venta->credito      = $credito;
            $venta->nota         = $nota;

            //Revisamos si llego pago y de ser asi lo grabamos
            $banco      = $request->input('banco');
            $referencia = $request->input('referencia');
            $fecha_pago = $request->input('fecha_pago');
            $nota_pago  = $request->input('nota_pago');
            if($banco && $fecha_pago && $nota_pago){
                if($venta->pago){
                    if($referencia){
                        $validate = $this->validate($request, [
                            'banco' => 'required|string',
                            'fecha_pago' => 'required|string',
                            'referencia' => 'required|string|max:255|unique:pago,referencia,'.$venta->pago->id,
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
                $venta->pago ? $pago = Pago::find($venta->id_pago) : $pago = new Pago();
                $pago->banco      = $banco;
                $pago->referencia = $referencia;
                $pago->fecha_pago = $fecha_pago;
                $pago->nota_pago  = $nota_pago;

                //vuelvo a revisar a ver si hago save o update
                $venta->pago ? $pago->update() : $pago->save();
                
                $venta->pendiente = true;
                $venta->id_pago   = $pago->id;
            }
            
            //Grabamos los cambios de la venta
            $venta->update();

            //Arreglo la variable session de cuentas por cobrar
            if(!$past->pendiente)
                $this->resetNotificationUpdate($past, $venta);

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Ventas";
            $report->description = "Venta Editada Código (".$id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-venta', ['id' => $venta->id])->with('message', 'Venta Editada Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Ventas";
            $report->description = "Error al editar venta - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-ventas')->with('status', 'Error al Editar la información');
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
            $nota_pago  = $request->input('nota_pago');
            $fecha_pago = $request->input('fecha_pago');
            
            //obtenemos el objeto del pago para hacerle el update
            $pago = Pago::find($id);
            $pago->banco      = $banco;
            $pago->referencia = $referencia;
            $pago->fecha_pago = $fecha_pago;
            $pago->nota_pago  = $nota_pago;

            //grabo el update del pago
            $pago->update();

            //obtenemos la compra del pago para sacar su código y grabarlo
            $venta = Venta::where('id_pago',$id)->first();

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Ventas";
            $report->description = "Pago Editado - Código de Venta (".$venta->id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('venta-pago', ['id' => $pago->id])->with('message', 'Pago Editado Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Ventas";
            $report->description = "Error al editar pago - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-ventas')->with('status', 'Error al Editar la información');
        }
    }

    public function updateDespacho(Request $request)
    {
        DB::beginTransaction();
        try{
            //id del despacho
            $id = $request->input('id');
            
            $validate = $this->validate($request, [
                'id_trabajador' => 'required',
                'fecha' => 'required',
                'nota' => 'required|string|max:255',
            ]);

            $id_trabajador = $request->input('id_trabajador');
            $fecha         = $request->input('fecha');
            $nota          = $request->input('nota');
            $entregado     = $request->input('entregado');
            
            //Grabamos el despacho de la venta
            $despacho = Despacho::find($id);
            $despacho->id_trabajador = $id_trabajador;
            $despacho->fecha         = $fecha;
            $despacho->nota          = $nota;
            $despacho->entregado     = $entregado;
            $despacho->update();

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Ventas";
            $report->description = "Despacho Editado - Código de Venta (".$despacho->id_venta.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-despacho', ['id' => $despacho->id])->with('message', 'Despacho Editado Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Ventas";
            $report->description = "Error al editar despacho - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-despachos')->with('status', 'Error al Editar la información');
        }
    }

    public function updateOrden(Request $request)
    {
        DB::beginTransaction();
        try{
            //id y nuevo monto de venta
            $id_venta = $request->input('vc_id');
            $monto = $request->input('vc_monto');

            //ACOMODAMOS EL MONTO DE LA VENTA
            $venta = Venta::find($id_venta);
            $venta->monto = $monto;
            $venta->update();

            //ELIMINAMOS TODA LA ORDEN PRIMERO
            Orden_Producto::where('id_venta', $id_venta)->delete();

            //GRABAMOS LOS PRODUCTOS DE LA VENTA EN LA ORDEN
            for ($i = 1; $i <= 30; $i++) {
                if($request->has('form-producto-'.$i)){
                    //RECOJEMOS VALORES
                    $id_producto      = $request->input('form-producto-'.$i);
                    $cantidad         = $request->input('form-cantidad-'.$i);
                    $precio           = $request->input('form-price-'.$i);

                    //ORDEN
                    $orden = new Orden_Producto();
                    $orden->id_venta    = $id_venta;
                    $orden->id_compra   = null;
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
            $report->activity    = "Módulo Ventas";
            $report->description = "Orden Editada - Código de Venta (".$id_venta.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-venta', ['id' => $id_venta])->with('message', 'Orden Editada Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Ventas";
            $report->description = "Error al editar orden - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-ventas')->with('status', 'Error al Editar la información');
        }
    }

    public function deleteVenta(Request $request){
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $venta = Venta::find($valor);
                //$despacho = Despacho::where('id_venta',$valor)->first();
                //$orden = Orden_Producto::where('id_venta',$valor)->get();
                
                //Eliminar Orden
                /* if($orden && count($orden)>=1){
                    foreach ($orden as $row) {
                        //Elimino cada producto de la orden
                        $row->delete();
                    }
                }

                //Eliminar Despacho
                if($despacho)
                    $despacho->delete(); */

                $codigo = $venta->id;
                //Elimino venta
                $venta->delete();

                //Eliminar Pago
                /* if($venta->id_pago){
                    $pago = Pago::find($venta->id_pago);
                    $pago->delete();
                } */

                array_push($valores, $valor);

                //Arreglo la variable session de cuentas por cobrar
                if(!$venta->pendiente)
                    $this->resetNotification($venta,'delete');
                
                //GRABAMOS EL REPORTE DE ELIMINADO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $user_id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user = $user_id;
                $report->name    = $user->name;
                $report->activity    = "Módulo Ventas";
                $report->description = "Venta Eliminada - Código (".$codigo.")";

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

    public function deleteDespacho(Request $request){
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $despacho = Despacho::find($valor);
                
                //codigo de venta del despacho
                $codigo = $despacho->id_venta;
                //Eliminar Despacho
                $despacho->delete();
                array_push($valores, $valor);
                
                //GRABAMOS EL REPORTE DE ELIMINADO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $user_id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user = $user_id;
                $report->name    = $user->name;
                $report->activity    = "Módulo Ventas";
                $report->description = "Despacho Eliminado - Código de Venta (".$codigo.")";

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

    public function reintegrarVenta($id = null){
        //$id va ser el id de la venta que deseo
        $venta = Venta::onlyTrashed()->where('id', $id)->first();

        if(empty($id) || empty($venta))
        return redirect()->route('discard-ventas');

        DB::beginTransaction();
        try{
            //ACOMODAMOS LA VENTA LA REINTEGRAMOS
            $venta->restore();

            //ACOMODAMOS LA NOTIFICACION
            //Arreglo la variable session de cuentas por cobrar
            if(!$venta->pendiente)
                $this->resetNotification($venta,'save');

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Ventas";
            $report->description = "Venta Anexada Nuevamente - Código (".$venta->id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-ventas')->with('message', 'Venta reintegrada Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Ventas";
            $report->description = "Error al reintegrar venta - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('discard-ventas')->with('status', 'Error al reintegrar Venta');
        }
    }

    public function resetNotification($sell, $metodo){
        $total = session()->get('notificaciones');
        $expirar = session()->get('cobrar-expirar');
        $caducar = session()->get('cobrar-caducar');

        //$metodo = delete, save
        if( strtotime($sell->fecha."+ ".$sell->credito." days") - strtotime(date("d-m-Y")) <= 3*86400){
            if( strtotime($sell->fecha."+ ".$sell->credito." days") - strtotime(date("d-m-Y")) > 0*86400){
                $metodo == 'delete' ? $expirar-- : $expirar++;
                $metodo == 'delete' ? $total-- : $total++;
            }
            else{
                $metodo == 'delete' ? $caducar-- : $caducar++;
                $metodo == 'delete' ? $total-- : $total++;
            }
        }

        session()->put('cobrar-expirar', $expirar);
        session()->put('cobrar-caducar', $caducar);
        session()->put('notificaciones', $total);
    }

    public function resetNotificationUpdate($past_sell, $new_sell){
        $total = session()->get('notificaciones');
        $expirar = session()->get('cobrar-expirar');
        $caducar = session()->get('cobrar-caducar');

        //$past_sell = valor antes del update
        //$new_sell = valor luego del update

        if( strtotime($past_sell->fecha."+ ".$past_sell->credito." days") - strtotime(date("d-m-Y")) <= 3*86400){
            if( strtotime($past_sell->fecha."+ ".$past_sell->credito." days") - strtotime(date("d-m-Y")) > 0*86400){
                $expirar--;
                $total--;
            }
            else{
                $caducar--;
                $total--;
            }
        }

        if(!$new_sell->pendiente){
            if( strtotime($new_sell->fecha."+ ".$new_sell->credito." days") - strtotime(date("d-m-Y")) <= 3*86400){
                if( strtotime($new_sell->fecha."+ ".$new_sell->credito." days") - strtotime(date("d-m-Y")) > 0*86400){
                    $expirar++;
                    $total++;
                }
                else{
                    $caducar++;
                    $total++;
                }
            }
        }

        session()->put('cobrar-expirar', $expirar);
        session()->put('cobrar-caducar', $caducar);
        session()->put('notificaciones', $total);
    }

    //Nuevo para que acomode las notificaciones
    public function notifications()
    {
        $total = 0;

        if(\Auth::user()->permiso_logistica){
            //Acomodamos Inventario por expirar
            $inventario = Inventario::all();
            $expirar = 0; $caducar = 0;
            foreach ($inventario as $product) {
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
            session()->put('inventario-expirar', $expirar);
            session()->put('inventario-caducar', $caducar);
        }
        $cuenta_expirar = session()->get('cobrar-expirar');
        $cuenta_caducar = session()->get('cobrar-caducar');

        $suministro_expirar = session()->get('suministro-expirar');
        $suministro_caducar = session()->get('suministro-caducar');
        
        $total_final = $cuenta_expirar + $cuenta_caducar + $suministro_expirar + $suministro_caducar + $total;
        session()->put('notificaciones', $total_final);
    }

    public function downloadVenta($id = null, $persona = null, $estado = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null){
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

        $filtro == "" ? $filtro = "Todas las ventas" : "";
        $datos = array();
        $titulos = array('Código', 'Cliente', 'Monto', 'Fecha', 'Crédito', 'Estado');

        foreach ($ventas as $sell) {
            $data_content["dato-1"] = $sell->id;
            $data_content["dato-2"] = $sell->cliente->nombre;
            $data_content["dato-3"] = number_format($sell->monto,2, ",", ".")." Bs";
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

    public function downloadPedido($id = null, $persona = null, $estado = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null){
        if($id && $persona && $tiempo){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Despacho::select('id_venta')->get();
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    whereNotIn('id',$datos)->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Despacho::select('id_venta')->get();
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente','like', $estado == "todos" ? "%%" : $estado)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereNotIn('id',$datos)->get();
            }

            $tiempo != "todos" ? $filtro .= "Entre ".$fecha_1." a ".$fecha_2 : "";
            $id != "todos" ? $filtro .= " | Código: ".$id : "";
            if($estado != "todos")  $estado ? $estado = "Pagado" : $estado = "Pendiente";
            $estado != "todos" ? $filtro .= " | Estado: ".$estado : "";
            $persona != "todos" ? $filtro .= " | Cliente Nro: ".$persona : "";
        }
        else{
            $datos = Despacho::select('id_venta')->get();
            $ventas = Venta::whereNotIn('id',$datos)->get();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todas los pedidos" : "";
        $datos = array();
        $titulos = array('Código', 'Cliente', 'Monto', 'Fecha', 'Crédito', 'Estado');

        foreach ($ventas as $sell) {
            $data_content["id"] = $sell->id;
            $data_content["dato-1"] = $sell->id;
            $data_content["dato-2"] = $sell->cliente->nombre;
            $data_content["dato-3"] = number_format($sell->monto,2, ",", ".")." Bs";
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

        return $this->download("Reporte_Pedidos.pdf", "Registro de Pedidos", $filtro, $datos, $titulos);
    }

    public function downloadDespacho($id = null, $persona = null, $despachador = null, $estado = null, 
                                        $tiempo = null, $fecha_1 = null, $fecha_2 = null)
    {
        if($id && $persona && $despachador && $tiempo){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Venta::select('id')->where('id_cliente', 'like', $persona == "todos" ? "%%" : $persona)->get();
                $despachos = Despacho::where('id_venta', 'like', $id == "todos" ? "%%" : $id)->
                                    where('id_trabajador', 'like', $despachador == "todos" ? "%%" : $despachador)->
                                    where('entregado','like', $estado == "todos" ? "%%" : $estado)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    whereIn('id_venta',$datos)->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Venta::select('id')->where('id_cliente', 'like', $persona == "todos" ? "%%" : $persona)->get();
                $despachos = Despacho::where('id_venta', 'like', $id == "todos" ? "%%" : $id)->
                                    where('id_trabajador', 'like', $despachador == "todos" ? "%%" : $despachador)->
                                    where('entregado','like', $estado == "todos" ? "%%" : $estado)->
                                    whereIn('id_venta',$datos)->get();
            }

            $tiempo != "todos" ? $filtro .= "Entre ".$fecha_1." a ".$fecha_2 : "";
            $id != "todos" ? $filtro .= " | Código de Venta: ".$id : "";
            $despachador != "todos" ? $filtro .= " | Trabajador Nro: ".$estado : "";
            if($estado != "todos")  $estado ? $estado = "Finalizado" : $estado = "Pendiente";
            $estado != "todos" ? $filtro .= " | Estado: ".$estado : "";
            $persona != "todos" ? $filtro .= " | Cliente Nro: ".$persona : "";
        }
        else{
            $despachos = Despacho::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todas los despachos" : "";
        $datos = array();
        $titulos = array('Código de Venta', 'Cliente', 'Monto', 'Fecha Despacho', 'Despachador', 'Estado');

        foreach ($despachos as $despacho) {
            if($despacho->venta){
                $data_content["dato-1"] = $despacho->id_venta;
                $data_content["dato-2"] = $despacho->venta->cliente->nombre;
                $data_content["dato-3"] = number_format($despacho->venta->monto,2, ",", ".")." Bs";
                $data_content["dato-4"] = $despacho->fecha;
                $data_content["dato-5"] = $despacho->trabajador ? $despacho->trabajador->nombre." ".$despacho->trabajador->apellido : "No posee";
                $data_content["dato-6"] = $despacho->entregado ? "Finalizado" : "Pendiente";
                array_push($datos,$data_content);
            }
        }

        return $this->download("Reporte_Despachos.pdf", "Registro de Despachos", $filtro, $datos, $titulos);
    }

    public function downloadCuenta($id = null, $persona = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null)
    {
        if($id && $persona && $tiempo){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente',0)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('pendiente',0)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->get();
            }

            $tiempo != "todos" ? $filtro .= "Entre ".$fecha_1." a ".$fecha_2 : "";
            $id != "todos" ? $filtro .= " | Código: ".$id : "";
            $persona != "todos" ? $filtro .= " | Cliente Nro: ".$persona : "";
        }
        else{
            $ventas = Venta::where('pendiente',0)->get();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todas las ventas" : "";
        $datos = array();
        $titulos = array('Código', 'Cliente', 'Monto', 'Fecha', 'Crédito', 'Estado');

        foreach ($ventas as $sell) {
            $data_content["dato-1"] = $sell->id;
            $data_content["dato-2"] = $sell->cliente->nombre;
            $data_content["dato-3"] = number_format($sell->monto,2, ",", ".")." Bs";
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

        return $this->download("Reporte_VentasporCobrar.pdf", "Registro de Ventas por Cobrar", $filtro, $datos, $titulos);
    }

    public function downloadPago($id = null, $referencia = null, $banco = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null)
    {    
        if($id && $referencia && $banco && $tiempo){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Pago::select('id')->where(function($q) use ($referencia) {
                                                $q->where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")
                                                ->orwhere('referencia',$referencia == "todos" ? null : "%%");
                                            })->
                                            where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                            whereBetween('fecha_pago', [$fecha_1, $fecha_2])->get();

                $ventas = Venta::where('id_pago','!=',null)->
                                    where('id', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    whereIn('id_pago',$datos)->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Pago::select('id')->where(function($q) use ($referencia) {
                                                $q->where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")
                                                ->orwhere('referencia',$referencia == "todos" ? null : "%%");
                                            })->
                                            where('banco','like',$banco == "todos" ? "%%" : $banco)->get();

                $ventas = Venta::where('id_pago','!=',null)->
                                    where('id', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    whereIn('id_pago',$datos)->get();
            }
            $tiempo != "todos" ? $filtro .= "Entre ".$fecha_1." a ".$fecha_2 : "";
            $referencia != "todos" ? $filtro .= " | Referencia de Pago: ".$referencia : "";
            $banco != "todos" ? $filtro .= " | Banco: ".$banco : "";
            $id != "todos" ? $filtro .= " | Código de venta: ".$id : "";
        }
        else{
            $ventas = Venta::where('id_pago','!=',null)->get();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los pagos" : "";
        $datos = array();
        $titulos = array('Cliente', 'Banco', 'Referencia', 'Fecha', 'Código-Venta', 'Monto');

        foreach ($ventas as $sell) {
            foreach($sell->pago->venta as $element){
                $data_content["dato-1"] = $element->cliente->nombre;
            }
            $data_content["dato-2"] = $sell->pago->banco;
            $data_content["dato-3"] = $sell->pago->referencia ? $sell->pago->referencia : "No posee";
            $data_content["dato-4"] = $sell->pago->fecha_pago;
            $data_content["dato-5"] = $sell->id;
            $data_content["dato-6"] = number_format($sell->monto,2, ",", ".")." Bs";
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Pagos_Ventas.pdf", "Registro de Pagos en Ventas", $filtro, $datos, $titulos);
    }

    public function downloadDetailDespacho($id = null)
    {   
        //$id va ser el id del despacho que deseo ver
        $despacho = Despacho::find($id);

        //$id va ser el id de la compra
        if(empty($id) || empty($despacho))
        return redirect()->route('list-despachos');

        //recojo todos los productos de la compra
        $productos = Orden_Producto::where('id_venta',$despacho->id_venta)->get();

        $nombre = "Reporte_Despacho_Venta(".$despacho->id_venta.").pdf";
        $header = "Despacho para Venta - COD(".$despacho->id_venta.")";
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
        $pdf->loadView('layouts.despachopdf',[
            'nombre' => $nombre,
            'datos' => $datos,
            'header' => $header,
            'despacho' => $despacho,
            'titulos' => $titulos,
            'datos' => $datos,
            'total' => $total,
        ]); 
        return $pdf->stream($nombre);
    }

    public function downloadDetailVenta($id = null)
    {   
        //$id va ser el id del despacho que deseo ver
        $venta = Venta::find($id);

        //$id va ser el id de la compra
        if(empty($id) || empty($venta))
        return redirect()->route('list-ventas');

        //recojo todos los productos de la compra
        $productos = Orden_Producto::where('id_venta',$venta->id)->get();

        $nombre = "Reporte_Venta(".$venta->id.").pdf";
        $header = "Venta - COD(".$venta->id.")";
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
        $pdf->loadView('layouts.ventadetailpdf',[
            'nombre' => $nombre,
            'datos' => $datos,
            'header' => $header,
            'informacion' => $venta,
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
}
