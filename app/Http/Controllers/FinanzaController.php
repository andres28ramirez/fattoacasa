<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Incidencia;
use App\Egreso;
use App\Venta;
use App\Compra;
use App\Cliente;
use App\Gasto_Costo;
use App\Pago;
use App\Pago_Nomina;
use App\Trabajador;

class FinanzaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('p_finanzas');
    }

    public function createCostoGasto()
    {
        return view('finance.costo_gasto.add');
    }

    public function createNomina()
    {
        //Para colocar el empleado
        $trabajadores = Trabajador::all();

        return view('finance.nomina.add', [
            'trabajadores' => $trabajadores,
        ]);
    }

    public function saveGastoCosto(Request $request)
    {
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'tipo' => 'required|string|max:255|',
                'fecha' => 'required',
                'monto' => 'required',
                'descripcion' => 'required|string|max:255',
            ]);

            $tipo        = $request->input('tipo');
            $fecha       = $request->input('fecha');
            $monto       = $request->input('monto');
            $descripcion = $request->input('descripcion');
            
            //Creamos el objeto del gasto-costo lo setteamos y grabamos
            $gastocosto = new Gasto_Costo();
            $gastocosto->tipo        = $tipo;
            $gastocosto->fecha       = $fecha;
            $gastocosto->monto       = $monto;
            $gastocosto->descripcion = $descripcion;
            $gastocosto->save();
            
            //GRABAMOS EL EGRESO
            $egreso = new Egreso();
            $egreso->id_gasto_costo = $gastocosto->id;
            $egreso->monto          = $monto;
            $egreso->save();

            //GRABAR EL REPORTE DE GUARDADO EXITOSO
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Finanzas";
            $report->description = $tipo." añadido - Código (".$gastocosto->id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-gasto-costo')->with('message', $tipo.' añadido exitosamente');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Finanzas";
            $report->description = "Error al almacenar gasto/costo - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-gasto-costo')->with('status', 'Error al Almacenar el '.$tipo);
        }
    }

    public function saveNomina(Request $request)
    {
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'id_trabajador' => 'required',
                'mes' => 'required',
                'monto' => 'required',
                'banco' => 'required|string',
                'fecha_pago' => 'required|string',
                'referencia' => 'required|string|max:255|unique:pago',
            ]);

            $id_trabajador = $request->input('id_trabajador');
            $mes           = $request->input('mes');
            $monto         = $request->input('monto');
            $banco         = $request->input('banco');
            $fecha_pago    = $request->input('fecha_pago');
            $referencia    = $request->input('referencia');
            
            //Creamos el objeto del pago lo setteamos y grabamos
            $pago = new Pago();
            $pago->banco      = $banco;
            $pago->referencia = $referencia;
            $pago->fecha_pago = $fecha_pago;
            $pago->save();
            
            //GRABAMOS EL EGRESO
            $nomina = new Pago_Nomina();
            $nomina->id_trabajador = $id_trabajador;
            $nomina->mes           = $mes."-01"; //le agrego el dia solo para que grabe
            $nomina->monto         = $monto;
            $nomina->id_pago       = $pago->id; 
            $nomina->save();

            //GRABAMOS EL EGRESO
            $egreso = new Egreso();
            $egreso->id_pago_nomina = $nomina->id;
            $egreso->monto          = $monto;
            $egreso->save();

            //GRABAR EL REPORTE DE GUARDADO EXITOSO
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Finanzas";
            $report->description = "Pago de Nómina añadido - Código (".$nomina->id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-nomina')->with('message','Pago añadido exitosamente');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Finanzas";
            $report->description = "Error al almacenar pago de nómina - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-nomina')->with('status', 'Error al Almacenar el Pago');
        }
    }

    public function showIngresos($registros = 10, $id = null, $persona = null, $referencia = null, $tiempo = null, 
                                $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="id_cliente" && $order!="monto" && $order!="fecha")
            $order = "id";

        if($id && $persona && $tiempo && $referencia){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Pago::select('id')->where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->get();

                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    whereIn('id_pago',$datos)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Pago::select('id')->where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->get();

                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereIn('id_pago',$datos)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $ventas = Venta::where('id_pago','!=',null)->orderBy($order, 'desc')->paginate($registros);
        }

        //Para filtrar por cliente
        $clientes = Cliente::all();

        //Chart de Indicador de Ingresos
        $ingresos = Venta::selectRaw('SUM(monto) as monto, MONTH(fecha) as mes')->groupBy('mes')->get();
        $bar_data = array();
        for($i = 1; $i <= 12; $i++){
            $grabar = true;
            foreach($ingresos as $row){
                if($i == $row->mes){
                    array_push($bar_data,$row->monto); $grabar=false;
                }
            }
            $grabar ? array_push($bar_data,0) : "";
        }

        return view('finance.ingresos.list', [
            'ingresos' => $ventas,
            'bar_data' => $bar_data,
            'registros' => $registros,
            'order' => $order,
            'id' => $id,
            'persona' => $persona,
            'referencia' => $referencia,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
            'clientes' => $clientes,
        ]);
    }

    public function showEgresos($registros = 10, $tipo = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="monto" && $order!="created_at")
            $order = "id";

        if($tiempo && $tipo){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                switch ($tipo) {
                    case "todos":
                        $compras = Compra::select('id')->whereBetween('fecha', [$fecha_1, $fecha_2])->get();
                        $gastoscostos = Gasto_Costo::select('id')->whereBetween('fecha', [$fecha_1, $fecha_2])->get();
                        $nomina = Pago_Nomina::select('id')->whereBetween('created_at', [$fecha_1, $fecha_2])->get();

                        if(count($compras) && count($gastoscostos) && count($nomina)){
                            $egresos = Egreso::whereIn('id_compra',$compras)->orwhereIn('id_gasto_costo',$gastoscostos)->
                                                orwhereIn('id_pago_nomina',$nomina)->orderBy($order, 'desc')->paginate($registros);
                        }
                        else if(count($compras) && count($gastoscostos)){
                            $egresos = Egreso::whereIn('id_compra',$compras)->orwhereIn('id_gasto_costo',$gastoscostos)->
                                                orderBy($order, 'desc')->paginate($registros);
                        }
                        else if(count($compras) && count($nomina)){
                            $egresos = Egreso::whereIn('id_compra',$compras)->orwhereIn('id_pago_nomina',$nomina)->
                                                orderBy($order, 'desc')->paginate($registros);
                        }
                        else if(count($gastoscostos) && count($nomina)){
                            $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->orwhereIn('id_pago_nomina',$nomina)->
                                                orderBy($order, 'desc')->paginate($registros);
                        }
                        else if(count($compras)){
                            $egresos = Egreso::whereIn('id_compra',$compras)->orderBy($order, 'desc')->paginate($registros);
                        }
                        else if(count($gastoscostos)){
                            $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->orderBy($order, 'desc')->paginate($registros);
                        }
                        else if(count($nomina)){
                            $egresos = Egreso::whereIn('id_pago_nomina',$nomina)->orderBy($order, 'desc')->paginate($registros);
                        }
                        break;
                    case "Compra":
                        $compras = Compra::select('id')->whereBetween('fecha', [$fecha_1, $fecha_2])->get();
                        $egresos = Egreso::whereIn('id_compra',$compras)->orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Gasto":
                        $gastoscostos = Gasto_Costo::select('id')->where('tipo','Gasto')->whereBetween('fecha', [$fecha_1, $fecha_2])->get();
                        $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Costo":
                        $gastoscostos = Gasto_Costo::select('id')->where('tipo','Costo')->whereBetween('fecha', [$fecha_1, $fecha_2])->get();
                        $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Pago de Nómina":
                        $nomina = Pago_Nomina::select('id')->whereBetween('created_at', [$fecha_1, $fecha_2])->get();
                        $egresos = Egreso::whereIn('id_pago_nomina',$nomina)->orderBy($order, 'desc')->paginate($registros);
                        break;
                }
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                switch ($tipo) {
                    case "todos":
                        $egresos = Egreso::orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Compra":
                        $compras = Compra::select('id')->get();
                        $egresos = Egreso::whereIn('id_compra',$compras)->orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Gasto":
                        $gastoscostos = Gasto_Costo::select('id')->where('tipo','Gasto')->get();
                        $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Costo":
                        $gastoscostos = Gasto_Costo::select('id')->where('tipo','Costo')->get();
                        $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Pago de Nómina":
                        $nomina = Pago_Nomina::select('id')->get();
                        $egresos = Egreso::whereIn('id_pago_nomina',$nomina)->orderBy($order, 'desc')->paginate($registros);
                        break;
                }
            }
        }
        else{
            $egresos = Egreso::orderBy($order, 'desc')->paginate($registros);
        }

        //Chart de indicador de egresos globales
        $costos = Gasto_Costo::selectRaw('SUM(monto) as monto, MONTH(fecha) as mes')->groupBy('mes')->get();
        $pagos = Pago_Nomina::selectRaw('SUM(monto) as monto, MONTH(mes) as mes')->groupBy('mes')->get();
        $compras = Compra::selectRaw('SUM(monto) as monto, MONTH(fecha) as mes')->groupBy('mes')->get();

        $c3_data = array();
        for($i = 1; $i <= 12; $i++){
            $total=0;
            foreach($costos as $row){ $i == $row->mes ? $total+=$row->monto : ""; }

            foreach($pagos as $row){ $i == $row->mes ? $total+=$row->monto : ""; }

            foreach($compras as $row){ $i == $row->mes ? $total+=$row->monto : ""; }
            
            array_push($c3_data,$total);
        }

        return view('finance.egresos.list', [
            'c3_data' => $c3_data,
            'egresos' => $egresos,
            'registros' => $registros,
            'order' => $order,
            'tipo' => $tipo,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
        ]);
    }

    public function showGastoCosto($registros = 10, $tipo = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="tipo" && $order!="descripcion" && $order!="fecha" && $order!="monto")
            $order = "id";

        if($tiempo && $tipo){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $gastoscostos = Gasto_Costo::where('tipo', 'like', $tipo == "todos" ? "%%" : $tipo)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $gastoscostos = Gasto_Costo::where('tipo', 'like', $tipo == "todos" ? "%%" : $tipo)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $gastoscostos = Gasto_Costo::orderBy($order, 'desc')->paginate($registros);
        }

        return view('finance.costo_gasto.list', [
            'gastoscostos' => $gastoscostos,
            'registros' => $registros,
            'order' => $order,
            'tipo' => $tipo,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
        ]);
    }

    public function showNomina($registros = 10, $empleado = null, $tiempo = null, $ayo = null, $mes = null, $order = 'id')
    {
        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="id_trabajador" && $order!="created_at" && $order!="mes" && $order!="monto")
            $order = "id";

        if($tiempo && $empleado){
            if($tiempo=="todos"){//FILTRO CON TODO LO QUE MANDEN SIN FECH
                $nomina = Pago_Nomina::where('id_trabajador', 'like', $empleado == "todos" ? "%%" : $empleado)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                if($tiempo == "Año"){
                    $nomina = Pago_Nomina::where('id_trabajador', 'like', $empleado == "todos" ? "%%" : $empleado)->
                                    whereYear('mes', $ayo)->
                                    orderBy($order, 'desc')->paginate($registros);
                }
                else if($tiempo == "Mes"){
                    $nomina = Pago_Nomina::where('id_trabajador', 'like', $empleado == "todos" ? "%%" : $empleado)->
                                    whereMonth('mes', $mes)->
                                    orderBy($order, 'desc')->paginate($registros);
                }
            }
        }
        else{
            $nomina = Pago_Nomina::orderBy($order, 'desc')->paginate($registros);
        }

        //Para filtrar por empleado
        $trabajadores = Trabajador::all();

        return view('finance.nomina.list', [
            'nomina' => $nomina,
            'registros' => $registros,
            'order' => $order,
            'empleado' => $empleado,
            'tiempo' => $tiempo,
            'ayo' => $ayo,
            'mes' => $mes,
            'trabajadores' => $trabajadores,
        ]);
    }

    public function showPagos($registros = 10, $tipo = null, $referencia = null, $banco = null, $tiempo = null, 
                            $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="banco" && $order!="referencia" && $order!="fecha_pago")
            $order = "id";

        if($tipo && $referencia && $banco && $tiempo){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                switch ($tipo) {
                    case "todos":
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereBetween('fecha_pago', [$fecha_1, $fecha_2])->
                                        orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Compra":
                        $datos = Compra::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereBetween('fecha_pago', [$fecha_1, $fecha_2])->
                                        whereIn('id',$datos)->
                                        orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Venta":
                        $datos = Venta::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereBetween('fecha_pago', [$fecha_1, $fecha_2])->
                                        whereIn('id',$datos)->
                                        orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Nomina":
                        $datos = Pago_Nomina::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereBetween('fecha_pago', [$fecha_1, $fecha_2])->
                                        whereIn('id',$datos)->
                                        orderBy($order, 'desc')->paginate($registros);
                        break;
                }
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                switch ($tipo) {
                    case "todos":
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Compra":
                        $datos = Compra::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereIn('id',$datos)->
                                        orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Venta":
                        $datos = Venta::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereIn('id',$datos)->
                                        orderBy($order, 'desc')->paginate($registros);
                        break;
                    case "Nomina":
                        $datos = Pago_Nomina::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereIn('id',$datos)->
                                        orderBy($order, 'desc')->paginate($registros);
                        break;
                }
            }
        }
        else{
            $pagos = Pago::orderBy($order, 'desc')->paginate($registros);
        }

        return view('finance.pagos.list', [
            'pagos' => $pagos,
            'registros' => $registros,
            'order' => $order,
            'tipo' => $tipo,
            'referencia' => $referencia,
            'banco' => $banco,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
        ]);
    }

    public function detailGastoCosto($id = null)
    {
        //$id va ser el id del gasto o costo que llega
        $gastocosto = Gasto_Costo::find($id);

        //$id va ser el id de la compra
        if(empty($id) || empty($gastocosto))
        return redirect()->route('list-gasto-costo');

        //recojo todos los gastos y todos los costos
        $total_gastos = Gasto_Costo::where('tipo','Gasto')->get();
        $total_costos = Gasto_Costo::where('tipo','Costo')->get();

        return view('finance.costo_gasto.detail', [
            'gastocosto' => $gastocosto,
            'total_gastos' => $total_gastos,
            'total_costos' => $total_costos,
        ]);
    }

    public function detailPagos($id = null)
    {
        //$id va ser el id del pago que deseo ver
        $pago = Pago::find($id);

        //$id va ser el id de la compra
        if(empty($id) || empty($pago))
        return redirect()->route('finance-pagos');

        return view('finance.pagos.detail', [
            'pago' => $pago,
        ]);
    }

    public function detailNomina($id = null)
    {
        //$id va ser el id del pago de nomina
        $nomina = Pago_Nomina::find($id);

        //trabajadores
        $trabajadores = Trabajador::all();

        //$id va ser el id de la compra
        if(empty($id) || empty($nomina))
        return redirect()->route('list-nomina');

        return view('finance.nomina.detail', [
            'nomina' => $nomina,
            'trabajadores' => $trabajadores,
        ]);
    }

    public function updateGastoCosto(Request $request)
    {
        DB::beginTransaction();
        try{
            //id del gasto o costo
            $id = $request->input('id');
            
            $validate = $this->validate($request, [
                'tipo' => 'required|string|max:255|',
                'fecha' => 'required',
                'monto' => 'required',
                'descripcion' => 'required|string|max:255',
            ]);

            $tipo        = $request->input('tipo');
            $fecha       = $request->input('fecha');
            $monto       = $request->input('monto');
            $descripcion = $request->input('descripcion');
            
            //buscamos el objeto del gasto-costo lo setteamos y grabamos
            $gastocosto = Gasto_Costo::find($id);
            $gastocosto->tipo        = $tipo;
            $gastocosto->fecha       = $fecha;
            $gastocosto->monto       = $monto;
            $gastocosto->descripcion = $descripcion;
            $gastocosto->update();
            
            //Buscamos el egreso y los seteamos tambien
            $egreso = Egreso::where('id_gasto_costo',$id)->first();
            $egreso->monto = $monto;
            $egreso->update();

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Finanzas";
            $report->description = $tipo." Editado - Código (".$id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-gasto-costo', ['id' => $gastocosto->id])->with('message', $tipo.' Editado Correctamente');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Finanzas";
            $report->description = "Error al editar gasto/costo - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-gasto-costo')->with('status', 'Error al Editar la información');
        }
    }

    public function updateNomina(Request $request)
    {
        DB::beginTransaction();
        try{
            //id del pago de nomina
            $id = $request->input('id');
            $nomina = Pago_Nomina::find($id);

            $validate = $this->validate($request, [
                'id_trabajador' => 'required',
                'mes' => 'required',
                'monto' => 'required',
                'banco' => 'required|string',
                'fecha_pago' => 'required|string',
                'referencia' => 'required|string|max:255|unique:pago,referencia,'.$nomina->pago->id,
            ]);

            $id_trabajador = $request->input('id_trabajador');
            $mes           = $request->input('mes');
            $monto         = $request->input('monto');
            $banco         = $request->input('banco');
            $fecha_pago    = $request->input('fecha_pago');
            $referencia    = $request->input('referencia');
            
            //Creamos el objeto del pago lo setteamos y grabamos
            $pago = Pago::find($nomina->id_pago);
            $pago->banco      = $banco;
            $pago->referencia = $referencia;
            $pago->fecha_pago = $fecha_pago;
            $pago->update();
            
            //GRABAMOS EL PAGO DE NOMINA
            $nomina->id_trabajador = $id_trabajador;
            $nomina->mes           = $mes."-01"; //le agrego el dia solo para que grabe
            $nomina->monto         = $monto;
            $nomina->id_pago       = $pago->id; 
            $nomina->update();

            //Buscamos el egreso y los seteamos tambien
            $egreso = Egreso::where('id_pago_nomina',$id)->first();
            $egreso->monto = $monto;
            $egreso->update();

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Finanzas";
            $report->description = "Pago de Nómina Editado - Código (".$id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-nomina', ['id' => $nomina->id])->with('message', 'Pago Editado Correctamente');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Finanzas";
            $report->description = "Error al editar pago de nómina - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-nomina')->with('status', 'Error al Editar la información');
        }
    }

    public function updatePago(Request $request)
    {
        DB::beginTransaction();
        try{
            //id del pago
            $id = $request->input('id');
            
            $validate = $this->validate($request, [
                'banco' => 'required|string',
                'fecha_pago' => 'required|string',
                'referencia' => 'required|string|max:255|unique:pago,referencia,'.$id,
            ]);
            
            $banco      = $request->input('banco');
            $referencia = $request->input('referencia');
            $fecha_pago = $request->input('fecha_pago');
            
            //obtenemos el objeto del pago para hacerle el update
            $pago = Pago::find($id);
            $pago->banco      = $banco;
            $pago->referencia = $referencia;
            $pago->fecha_pago = $fecha_pago;

            //grabo el update del pago
            $pago->update();

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Finanzas";
            $report->description = "Pago Editado - Código de Pago (".$pago->id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-pago', ['id' => $pago->id])->with('message', 'Pago Editado Correctamente');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Finanzas";
            $report->description = "Error al editar pago - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('finance-pagos')->with('status', 'Error al Editar la información');
        }
    }

    public function deleteGastoCosto(Request $request)
    {
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $gastocosto = Gasto_Costo::find($valor);
                $egreso = Egreso::where('id_gasto_costo',$valor)->first();
                
                //Eliminamos Egreso
                $egreso->delete();
                
                //codigo de gasto o costo
                $codigo = $gastocosto->id;

                //Eliminar Gasto o Costo
                $gastocosto->delete();
                array_push($valores, $valor);
                
                //GRABAMOS EL REPORTE DE ELIMINADO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $user_id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user = $user_id;
                $report->name    = $user->name;
                $report->activity    = "Módulo Finanzas";
                $report->description = "Gasto o Costo Eliminado - Código (".$codigo.")";

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

    public function deleteNomina(Request $request)
    {
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $nomina = Pago_Nomina::find($valor);
                $egreso = Egreso::where('id_pago_nomina',$valor)->first();
                
                //Eliminamos Egreso
                $egreso->delete();
                
                //codigo de gasto o costo
                $codigo = $nomina->id;

                //Eliminar Gasto o Costo
                $nomina->delete();

                //Eliminar pago de la nomina
                $pago = Pago::find($nomina->id_pago);
                $pago->delete();

                array_push($valores, $valor);
                
                //GRABAMOS EL REPORTE DE ELIMINADO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $user_id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user = $user_id;
                $report->name    = $user->name;
                $report->activity    = "Módulo Finanzas";
                $report->description = "Pago de Nómina Eliminado - Código (".$codigo.")";

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

    public function ajaxfinance(Request $request){ 
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

            switch ($id) {
                case "c2": //CHART 2 TOTAL INGRESOS
                    if($busqueda[1]=="Año"){
                        $ventas = Venta::selectRaw('SUM(monto) as monto, '.$periodo)->
                                        whereYear('fecha', $busqueda[4])->groupBy('periodo')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $ventas = Venta::selectRaw('SUM(monto) as monto, '.$periodo)->
                                    whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                    }
                    else{
                        $ventas = Venta::selectRaw('SUM(monto) as monto, '.$periodo)->groupBy('periodo')->get();
                    }

                    for($i = $inicio; $i <= $fin; $i++){
                        $grabar = true;
                        foreach($ventas as $row){
                            if($i == $row->periodo){
                                array_push($values1,$row->monto); $grabar=false;
                            }
                        }
                        $grabar ? array_push($values1,0) : "";
                    }
                    array_push($datos,$labels,$values1);
                    break;
                case "c3": //CHART 3 TOTAL EGRESOS
                    $busqueda[0]=="Mensual" ? $p_pagos = 'MONTH(mes) as periodo' : $p_pagos = 'DATE_FORMAT(mes, "%w") as periodo';

                    if($busqueda[1]=="Año"){
                        $costos = Gasto_Costo::selectRaw('SUM(monto) as monto, '.$periodo)->
                                        whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                        
                        $pagos = Pago_Nomina::selectRaw('SUM(monto) as monto, '.$p_pagos)->
                                        whereYear('mes', $busqueda[4])->groupBy('periodo')->get();
                        
                        $compras = Compra::selectRaw('SUM(monto) as monto, '.$periodo)->
                                        whereYear('fecha', $busqueda[4])->groupBy('periodo')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $costos = Gasto_Costo::selectRaw('SUM(monto) as monto, '.$periodo)->
                        whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                        
                        $pagos = Pago_Nomina::selectRaw('SUM(monto) as monto, '.$p_pagos)->
                        whereBetween('mes', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                        
                        $compras = Compra::selectRaw('SUM(monto) as monto, '.$periodo)->
                        whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                    }
                    else{
                        $costos = Gasto_Costo::selectRaw('SUM(monto) as monto, '.$periodo)->groupBy('periodo')->get();
                        
                        $pagos = Pago_Nomina::selectRaw('SUM(monto) as monto, '.$p_pagos)->groupBy('periodo')->get();
                        
                        $compras = Compra::selectRaw('SUM(monto) as monto, '.$periodo)->groupBy('periodo')->get();
                    }

                    for($i = $inicio; $i <= $fin; $i++){
                        $egreso=0;
                        foreach($costos as $row){ $i == $row->periodo ? $egreso+=$row->monto : ""; }

                        foreach($pagos as $row){ $i == $row->periodo ? $egreso+=$row->monto : ""; }

                        foreach($compras as $row){ $i == $row->periodo ? $egreso+=$row->monto : ""; }
                        array_push($values1,$egreso);
                    }
                    array_push($datos,$labels,$values1);
                    break;
            } 

            return response()->json($datos); 
        }
        catch(\Illuminate\Database\QueryException $e){
            return response()->json(false);       
        }
    }

    public function downloadIngreso($id = null, $persona = null, $referencia = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null)
    {    
        if($id && $persona && $tiempo && $referencia){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Pago::select('id')->where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->get();

                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : $id)->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->
                                    whereIn('id_pago',$datos)->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Pago::select('id')->where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->get();

                $ventas = Venta::where('id', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    where('id_cliente','like', $persona == "todos" ? "%%" : $persona)->
                                    whereIn('id_pago',$datos)->get();
            }
            $tiempo != "todos" ? $filtro .= "Fecha de Ingreso entre ".$fecha_1." a ".$fecha_2 : "";
            $id != "todos" ? $filtro .= " | Código de Venta: ".$id : "";
            $referencia != "todos" ? $filtro .= " | Referencia Bancaria: ".$referencia : "";
            $persona != "todos" ? $filtro .= " | Proveedor Nro: ".$persona : "";
        }
        else{
            $ventas = Venta::where('id_pago','!=',null)->get();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los ingresos" : "";
        $datos = array();
        $titulos = array('Código Venta', 'Cliente', 'Monto', 'Fecha', 'Fecha de Pago', 'Referencia');

        foreach ($ventas as $row) {
            $data_content["dato-1"] = $row->id;
            $data_content["dato-2"] = $row->cliente->nombre;
            $data_content["dato-3"] = $row->monto." Bs";
            $data_content["dato-4"] = $row->fecha;
            $data_content["dato-5"] = $row->pago->fecha_pago;
            $data_content["dato-6"] = $row->pago->referencia;
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Ingreso.pdf", "Ingresos de la Empresa", $filtro, $datos, $titulos);
    }

    public function downloadEgreso($tipo = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null)
    {    
        if($tiempo && $tipo){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                switch ($tipo) {
                    case "todos":
                        $compras = Compra::select('id')->whereBetween('fecha', [$fecha_1, $fecha_2])->get();
                        $gastoscostos = Gasto_Costo::select('id')->whereBetween('fecha', [$fecha_1, $fecha_2])->get();
                        $nomina = Pago_Nomina::select('id')->whereBetween('created_at', [$fecha_1, $fecha_2])->get();

                        if(count($compras) && count($gastoscostos) && count($nomina)){
                            $egresos = Egreso::whereIn('id_compra',$compras)->orwhereIn('id_gasto_costo',$gastoscostos)->
                                                orwhereIn('id_pago_nomina',$nomina)->get();
                        }
                        else if(count($compras) && count($gastoscostos)){
                            $egresos = Egreso::whereIn('id_compra',$compras)->orwhereIn('id_gasto_costo',$gastoscostos)->get();
                        }
                        else if(count($compras) && count($nomina)){
                            $egresos = Egreso::whereIn('id_compra',$compras)->orwhereIn('id_pago_nomina',$nomina)->get();
                        }
                        else if(count($gastoscostos) && count($nomina)){
                            $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->orwhereIn('id_pago_nomina',$nomina)->get();
                        }
                        else if(count($compras)){
                            $egresos = Egreso::whereIn('id_compra',$compras)->get();
                        }
                        else if(count($gastoscostos)){
                            $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->get();
                        }
                        else if(count($nomina)){
                            $egresos = Egreso::whereIn('id_pago_nomina',$nomina)->get();
                        }
                        break;
                    case "Compra":
                        $compras = Compra::select('id')->whereBetween('fecha', [$fecha_1, $fecha_2])->get();
                        $egresos = Egreso::whereIn('id_compra',$compras)->get();
                        break;
                    case "Gasto":
                        $gastoscostos = Gasto_Costo::select('id')->where('tipo','Gasto')->whereBetween('fecha', [$fecha_1, $fecha_2])->get();
                        $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->get();
                        break;
                    case "Costo":
                        $gastoscostos = Gasto_Costo::select('id')->where('tipo','Costo')->whereBetween('fecha', [$fecha_1, $fecha_2])->get();
                        $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->get();
                        break;
                    case "Pago de Nómina":
                        $nomina = Pago_Nomina::select('id')->whereBetween('created_at', [$fecha_1, $fecha_2])->get();
                        $egresos = Egreso::whereIn('id_pago_nomina',$nomina)->get();
                        break;
                }
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                switch ($tipo) {
                    case "todos":
                        $egresos = Egreso::all();
                        break;
                    case "Compra":
                        $compras = Compra::select('id')->get();
                        $egresos = Egreso::whereIn('id_compra',$compras)->get();
                        break;
                    case "Gasto":
                        $gastoscostos = Gasto_Costo::select('id')->where('tipo','Gasto')->get();
                        $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->get();
                        break;
                    case "Costo":
                        $gastoscostos = Gasto_Costo::select('id')->where('tipo','Costo')->get();
                        $egresos = Egreso::whereIn('id_gasto_costo',$gastoscostos)->get();
                        break;
                    case "Pago de Nómina":
                        $nomina = Pago_Nomina::select('id')->get();
                        $egresos = Egreso::whereIn('id_pago_nomina',$nomina)->get();
                        break;
                }
            }
            $tiempo != "todos" ? $filtro .= "Fecha de Egreso entre ".$fecha_1." a ".$fecha_2 : "";
            $tipo != "todos" ? $filtro .= " | Tipo de Egreso: ".$tipo : "";
        }
        else{
            $egresos = Egreso::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los egresos" : "";
        $datos = array();
        $titulos = array('Tipo', 'Persona', 'Monto', 'Fecha', 'Fecha de Pago', 'Referencia');

        foreach ($egresos as $row) {
            if($row->compra){
                $data_content["dato-1"] = "Compra";
                $data_content["dato-2"] = $row->compra->proveedor->nombre;
                $data_content["dato-3"] = $row->monto." Bs";
                $data_content["dato-4"] = $row->compra->fecha;
                $data_content["dato-5"] = $row->compra->pago ? $row->compra->pago->fecha_pago : "No posee";
                $data_content["dato-6"] = $row->compra->pago ? $row->compra->pago->referencia : "No posee";
            }
            else if($row->gastocosto){
                $data_content["dato-1"] = $row->gastocosto->tipo;
                $data_content["dato-2"] = $row->gastocosto->descripcion;
                $data_content["dato-3"] = $row->monto." Bs";
                $data_content["dato-4"] = $row->gastocosto->fecha;
                $data_content["dato-5"] = "No posee";
                $data_content["dato-6"] = "No posee";
            }
            else if($row->nomina){
                $data_content["dato-1"] = "Pago Nómina";
                $data_content["dato-2"] = $row->nomina->trabajador->nombre." ".$row->nomina->trabajador->apellido;
                $data_content["dato-3"] = $row->monto." Bs";
                setlocale(LC_TIME, "spanish");
                $data_content["dato-4"] = strftime("%B", strtotime($row->nomina->mes));
                $data_content["dato-5"] = $row->nomina->pago->fecha_pago;
                $data_content["dato-6"] = $row->nomina->pago->referencia;
            }

            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Egreso.pdf", "Egresos de la Empresa", $filtro, $datos, $titulos);
    }

    public function downloadGastoCosto($tipo = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null)
    {    
        if($tiempo && $tipo){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $gastoscostos = Gasto_Costo::where('tipo', 'like', $tipo == "todos" ? "%%" : $tipo)->
                                    whereBetween('fecha', [$fecha_1, $fecha_2])->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $gastoscostos = Gasto_Costo::where('tipo', 'like', $tipo == "todos" ? "%%" : $tipo)->get();
            }
            $tiempo != "todos" ? $filtro .= "Fecha de Egreso entre ".$fecha_1." a ".$fecha_2 : "";
            $tipo != "todos" ? $filtro .= " | Tipo de Egreso: ".$tipo : "";
        }
        else{
            $gastoscostos = Gasto_Costo::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los gastos y costos" : "";
        $datos = array();
        $titulos = array('Código', 'Tipo', 'Descripción', 'Monto', 'Fecha');

        foreach ($gastoscostos as $row) {
            $data_content["dato-1"] = $row->id;
            $data_content["dato-2"] = $row->tipo;
            $data_content["dato-3"] = $row->descripcion;
            $data_content["dato-4"] = $row->monto." Bs";
            $data_content["dato-5"] = $row->fecha;
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Gasto_Costo.pdf", "Gastos / Costos de la Empresa", $filtro, $datos, $titulos);
    }

    public function downloadNomina($empleado = null, $tiempo = null, $ayo = null, $mes = null)
    {    
        if($tiempo && $empleado){
            $filtro = "";
            if($tiempo=="todos"){//FILTRO CON TODO LO QUE MANDEN SIN FECH
                $nomina = Pago_Nomina::where('id_trabajador', 'like', $empleado == "todos" ? "%%" : $empleado)->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                if($tiempo == "Año"){
                    $nomina = Pago_Nomina::where('id_trabajador', 'like', $empleado == "todos" ? "%%" : $empleado)->
                                    whereYear('mes', $ayo)->get();
                }
                else if($tiempo == "Mes"){
                    $nomina = Pago_Nomina::where('id_trabajador', 'like', $empleado == "todos" ? "%%" : $empleado)->
                                    whereMonth('mes', $mes)->get();
                }
            }
            if($tiempo == "Año") $filtro .= "Pagos del año: ".$ayo;
            if($tiempo == "Mes"){
                $mes == 1 ? $mes = "Enero" : ""; $mes == 2 ? $mes = "Febrero" : ""; $mes == 3 ? $mes = "Marzo" : "";
                $mes == 4 ? $mes = "Abril" : ""; $mes == 5 ? $mes = "Mayo" : ""; $mes == 6 ? $mes = "Junio" : "";
                $mes == 7 ? $mes = "Julio" : ""; $mes == 8 ? $mes = "Agosto" : ""; $mes == 9 ? $mes = "Septiembre" : "";
                $mes == 10 ? $mes = "Octubre" : ""; $mes == 11 ? $mes = "Noviembre" : ""; $mes == 12 ? $mes = "Diciembre" : ""; 
                $filtro .= "Pagos de la fecha: ".$mes;
            }
            $empleado != "todos" ? $filtro .= " | Código de Empleado: ".$empleado : "";
        }
        else{
            $nomina = Pago_Nomina::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los pagos de nómina" : "";
        $datos = array();
        $titulos = array('Código', 'Empleado', 'Año', 'Mes', 'Monto', 'Referencia de Pago');

        foreach ($nomina as $row) {
            $data_content["id"] = $row->id;
            $data_content["dato-1"] = $row->id;
            $data_content["dato-2"] = $row->trabajador->nombre." ".$row->trabajador->apellido;
            $data_content["dato-3"] = strftime("%Y", strtotime($row->mes));
            setlocale(LC_TIME, "spanish"); 
            $data_content["dato-4"] = strftime("%B", strtotime($row->mes));
            $data_content["dato-5"] = $row->monto." Bs";
            $data_content["dato-6"] = $row->pago->referencia;
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Nómina.pdf", "Pagos de Nómina de la Empresa", $filtro, $datos, $titulos);
    }

    public function downloadPago($tipo = null, $referencia = null, $banco = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null)
    {    
        if($tipo && $referencia && $banco && $tiempo){
            $filtro = "";
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                switch ($tipo) {
                    case "todos":
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereBetween('fecha_pago', [$fecha_1, $fecha_2])->get();
                        break;
                    case "Compra":
                        $datos = Compra::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereBetween('fecha_pago', [$fecha_1, $fecha_2])->
                                        whereIn('id',$datos)->get();
                        break;
                    case "Venta":
                        $datos = Venta::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereBetween('fecha_pago', [$fecha_1, $fecha_2])->
                                        whereIn('id',$datos)->get();
                        break;
                    case "Nomina":
                        $datos = Pago_Nomina::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereBetween('fecha_pago', [$fecha_1, $fecha_2])->
                                        whereIn('id',$datos)->get();
                        break;
                }
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                switch ($tipo) {
                    case "todos":
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->get();
                        break;
                    case "Compra":
                        $datos = Compra::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereIn('id',$datos)->get();
                        break;
                    case "Venta":
                        $datos = Venta::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereIn('id',$datos)->get();
                        break;
                    case "Nomina":
                        $datos = Pago_Nomina::select('id_pago')->where('id_pago','!=',null)->get();
                        $pagos = Pago::where('referencia','like',$referencia == "todos" ? "%%" : "%".$referencia."%")->
                                        where('banco','like',$banco == "todos" ? "%%" : $banco)->
                                        whereIn('id',$datos)->get();
                        break;
                }
            }
            $tiempo != "todos" ? $filtro .= "Pagos entre ".$fecha_1." a ".$fecha_2 : "";
            $referencia != "todos" ? $filtro .= " | Referencia de Pago: ".$referencia : "";
            $banco != "todos" ? $filtro .= " | Banco: ".$banco : "";
            $tipo != "todos" ? $filtro .= " | Evento del pago: ".$tipo : "";
        }
        else{
            $pagos = Pago::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los pagos" : "";
        $datos = array();
        $titulos = array('Código', 'Banco', 'Referencia', 'Fecha', 'Tipo', 'Monto');

        foreach ($pagos as $row) {
            $data_content["dato-1"] = $row->id;
            $data_content["dato-2"] = $row->banco;
            $data_content["dato-3"] = $row->referencia;
            $data_content["dato-4"] = $row->fecha_pago;
            foreach($row->compra as $fila){
                $data_content["dato-5"] = "Compra";
                $data_content["dato-6"] = $fila->monto;
            }
            foreach($row->venta as $fila){
                $data_content["dato-5"] = "Venta";
                $data_content["dato-6"] = $fila->monto;
            }
            foreach($row->nomina as $fila){
                $data_content["dato-5"] = "Nómina";
                $data_content["dato-6"] = $fila->monto;
            }
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Pagos.pdf", "Registro de Pagos", $filtro, $datos, $titulos);
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
