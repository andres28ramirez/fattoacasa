<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Cliente;
use App\Inventario;
use App\Producto;
use App\Despacho;
use App\Orden_Producto;
use App\Venta;
use App\Compra;
use App\Desperdicio;
use App\Gasto_Costo;
use App\Pago;
use App\Pago_Nomina;

class IndicadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showlogistic(){
        //TOP 5 ENTRADAS
            $entradas = Orden_Producto::select(DB::raw('SUM(cantidad) as cantidad'),'id_producto')->
                                    where('id_compra','!=',0)->
                                    groupBy('id_producto')->orderBy('cantidad', 'desc')->skip(0)->take(5)->get();

        //TOP 5 SALIDAS
            $datos = array();
            foreach($entradas as $row){
                array_push($datos,$row->id_producto);
            }
            $salidas = Orden_Producto::select(DB::raw('SUM(cantidad) as cantidad'),'id_producto')->
                                        whereIn('id_producto',$datos)->where('id_venta','!=',0)->
                                        groupBy('id_producto')->orderBy('cantidad', 'desc')->skip(0)->take(5)->get();
            
        //ENTRADAS DE INVENTARIO POR DIA
            $dia_entradas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, DATE_FORMAT(fecha, "%w") as dia')
                                        ->join('compra', 'orden_producto.id_compra', '=', 'compra.id')->
                                        where('id_compra','!=',0)->
                                        groupBy('dia')->orderBy('dia', 'asc')->get();

        //SALIDAS DE INVENTARIO POR DIA
            $dia_salidas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, DATE_FORMAT(fecha, "%w") as dia')
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        where('id_venta','!=',0)->
                                        groupBy('dia')->orderBy('dia', 'asc')->get();

        //DESPACHOS FINALIZADOS
            $despachos_f = Despacho::where('entregado',1)->get();

        //DESPACHOS PENDIENTES
            $despachos_p = Despacho::where('entregado',0)->get();

        //INVENTARIO VENDIDO
            $sales = Orden_Producto::select(DB::raw('SUM(cantidad) as cantidad'),'id_producto')->where('id_venta','!=',0)->first();

        //INVENTARIO VENDIDO POR MES
            $ventas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, MONTH(fecha) as mes')
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        where('id_venta','!=',0)->groupBy('mes')->get();

        //DESPACHOS TOTALES
            $despachos = Despacho::selectRaw('COUNT(id_venta) as total, MONTH(fecha) as mes')->groupBy('mes')->get();

        //ROTACION DE INVENTARIO
            //VENTAS / INVENTARIO (Productos Frescos, Productos Procesados y Total)
            /* $venta_total = 0; $inv_total = 0; */ $rotacion_total = 0;
            $venta_fresco = 0; $inv_fresco = 0; $rotacion_fresco = 0;
            $venta_procesado = 0; $inv_procesado = 0; $rotacion_procesado = 0;
            $orden = Orden_Producto::where('id_venta','!=',0)->get();
            $inventario = Inventario::all();

            foreach($inventario as $row){
                //$inv_total+= $row->cantidad;
                $row->producto->categoria->nombre == "Productos Frescos" ? $inv_fresco+= $row->cantidad : "";
                $row->producto->categoria->nombre == "Productos Procesados" ? $inv_procesado+= $row->cantidad : "";
            }

            foreach($orden as $row){
                //$venta_total+= floatval($row->precio);
                $row->producto->categoria->nombre == "Productos Frescos" ? $venta_fresco+=floatval($row->precio) : "";
                $row->producto->categoria->nombre == "Productos Procesados" ? $venta_procesado+=floatval($row->precio) : "";
            }
            //$inv_total != 0 ? $inv_total : $inv_total=1;
            $inv_fresco != 0 ? $inv_fresco : $inv_fresco=1; 
            $inv_procesado != 0 ? $inv_procesado : $inv_procesado=1;

            //$rotacion_total = ($venta_total/$inv_total);
            $rotacion_fresco = ($venta_fresco/$inv_fresco);
            $rotacion_procesado = ($venta_procesado/$inv_procesado);
            $rotacion_total = $rotacion_fresco + $rotacion_procesado;

        return view('indicators.logistic', [
            'entradas' => $entradas,
            'salidas' => $salidas,
            'despachos_f' => $despachos_f,
            'despachos_p' => $despachos_p,
            'sales' => $sales,
            'despachos' => $despachos,
            'dia_entradas' => $dia_entradas,
            'dia_salidas' => $dia_salidas,
            'rotacion_total' => $rotacion_total,
            'rotacion_fresco' => $rotacion_fresco,
            'rotacion_procesado' => $rotacion_procesado,
            'ventas' => $ventas,
        ]);
    }

    public function showsell(){
        //CHART 1 AUMENTO DE VENTAS Y EL TOTAL EN CANTIDAD
            $ventas = Venta::selectRaw('COUNT(id) as total, MONTH(fecha) as mes')->groupBy('mes')->get();
            $bar_data = array();
            for($i = 1; $i <= 12; $i++){
                $grabar = true;
                foreach($ventas as $row){
                    if($i == $row->mes){
                        array_push($bar_data,$row->total); $grabar=false;
                    }
                }
                $grabar ? array_push($bar_data,0) : "";
            }
            $bar_line = array();
            $anterior = 0;
            foreach ($bar_data as $dato) {
                if($anterior==0)
                    $valor = 0;
                else
                    $valor = round( (($dato*100)/$anterior)-100, 2);
                $anterior = $dato; 
                array_push($bar_line, $valor);
            }

        //BLOQUE DE TOTALES
            //VALOR PROMEDIO DE VENTA
            $venta_promedio = Venta::selectRaw('AVG(monto) as promedio')->first();

            //PLAZO MEDIO DE PAGOS
            $ingresos = Venta::selectRaw('SUM(monto) as suma')->whereNotNull('id_pago')->first();
            $cobrar = Venta::selectRaw('SUM(monto) as suma')->whereNull('id_pago')->first();
            !empty($ingresos->suma) ? $ingresos=$ingresos->suma : $ingresos=1;
            !empty($cobrar->suma) ? $cobrar=$cobrar->suma : $cobrar=1;
            $pagos_dias = (365 / ($ingresos/$cobrar));

            //COBROS PENDIENTES
            $pendientes = Venta::selectRaw('COUNT(id) as total')->whereNull('id_pago')->first();

            //PAGOS RECIBIDOS
            $datos = Venta::select('id_pago')->whereNotNull('id_pago')->get();
            $pagos = Pago::selectRaw('COUNT(id) as total')->whereIn('id',$datos)->first();

        //TOP 10 PRODUCTOS VENDIDOS
            $top10 = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, id_producto')
                                    ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                    groupBy('id_producto')->orderBy('cantidad', 'desc')->get();
            $total_top10=0;
            $top10_products = array(); $value = array("cantidad", "text");
            foreach($top10 as $row){
                $total_top10+= $row->cantidad;
                $value["cantidad"] = $row->cantidad;
                $value["text"] = $row->producto->nombre;
                array_push($top10_products,$value);
            }

        //CHART 2 TOTAL VENTAS REALIZADAS
            //$ventas y $bar_data CONSULTA DEL CHART 1 es lo que necesito en este indicador
        
        //CHART 3 RENTABILIDAD - BENEFICIO DE VENTA
            //INGRESOS DE VENTAS - EGRESOS DE COMPRAS POR CADA MES Y SACO EL BENEFICIO
            //BAR ES EL INGRESO Y LA LINE ES EL DE BENEFICIO
            $c3_bar_data = array();
            $c3_bar_line = array();
            $ingresos = Venta::selectRaw('SUM(monto) as monto, MONTH(fecha) as mes')->groupBy('mes')->get();
            $egresos = Compra::selectRaw('SUM(monto) as monto, MONTH(fecha) as mes')->groupBy('mes')->get();
            //INGRESOS
                for($i = 1; $i <= 12; $i++){
                    $grabar = true;
                    foreach($ingresos as $row){
                        if($i == $row->mes){
                            array_push($c3_bar_data,$row->monto); $grabar=false;
                        }
                    }
                    $grabar ? array_push($c3_bar_data,0) : "";
                }
            //BENEFICIO
                for($i=0 ; $i < count($c3_bar_data) ; $i++){
                    $grabar = true;
                    foreach($egresos as $row){
                        if($i == $row->mes){
                            $valor = $c3_bar_data[$i] - $row->monto;
                            array_push($c3_bar_line,$valor); $grabar=false;
                        }
                    }
                    $grabar ? array_push($c3_bar_line,$c3_bar_data[$i]) : "";
                }

        return view('indicators.sell', [
            'bar_data' => $bar_data,
            'bar_line' => $bar_line,
            'venta_promedio' => $venta_promedio,
            'pagos_dias' => $pagos_dias,
            'pendientes' => $pendientes,
            'pagos' => $pagos,
            'top10_products' => $top10_products,
            'total_top10' => $total_top10,
            'c3_bar_data' => $c3_bar_data,
            'c3_bar_line' => $c3_bar_line,
        ]);
    }

    public function showbuy(){
        //CHART 1 EGRESOS GENERADOS POR COMPRAS
            $egresos = Compra::selectRaw('SUM(monto) as monto, MONTH(fecha) as mes')->groupBy('mes')->get();
            $c1_data = array();
            for($i = 1; $i <= 12; $i++){
                $grabar = true;
                foreach($egresos as $row){
                    if($i == $row->mes){
                        array_push($c1_data,$row->monto); $grabar=false;
                    }
                }
                $grabar ? array_push($c1_data,0) : "";
            }

        //CHART 2 COMPRAS TOTALES POR MES
            $compras = Compra::selectRaw('COUNT(id) as total, MONTH(fecha) as mes')->groupBy('mes')->get();
            $c2_data = array();
            for($i = 1; $i <= 12; $i++){
                $grabar = true;
                foreach($compras as $row){
                    if($i == $row->mes){
                        array_push($c2_data,$row->total); $grabar=false;
                    }
                }
                $grabar ? array_push($c2_data,0) : "";
            }

        //BLOQUE DE TOTALES
            //VALOR PROMEDIO DE COMPRA
            $compra_promedio = Compra::selectRaw('AVG(monto) as promedio')->first();

            //PLAZO MEDIO DE PAGOS
            $egresos = Compra::selectRaw('SUM(monto) as suma')->whereNotNull('id_pago')->first();
            $pagar = Compra::selectRaw('SUM(monto) as suma')->whereNull('id_pago')->first();
            !empty($egresos->suma) ? $egresos=$egresos->suma : $egresos=1;
            !empty($pagar->suma) ? $pagar=$pagar->suma : $pagar=1;
            $pagos_dias = (365 / ($egresos/$pagar));

            //CUENTAS POR PAGAR
            $pendientes = Compra::selectRaw('COUNT(id) as total')->whereNull('id_pago')->first();

        //CHART 3 CALIDAD GLOBAL POR PROVEEDOR
            $compra_global = Compra::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
            ->join('orden_producto', 'orden_producto.id_compra', '=', 'compra.id')->groupBy('id_proveedor')->get();

            $desperdicio_global = Desperdicio::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
            ->join('compra', 'desperdicio.id_compra', '=', 'compra.id')->groupBy('id_proveedor')->get();

            $calidad_global = array();
            $dato = array("porcentaje","nombre");
            foreach($compra_global as $compra){
                $grabar = true;
                foreach($desperdicio_global as $desperdicio){
                    if($compra->id_proveedor == $desperdicio->id_proveedor){
                        $dato["porcentaje"] = round(($compra->cantidad - $desperdicio->cantidad)*100/$compra->cantidad,2);
                        $dato["nombre"] = $compra->proveedor->nombre;
                        array_push($calidad_global,$dato); 
                        $grabar=false;
                    }
                }
                if($grabar){
                    $dato["porcentaje"] = 100;
                    $dato["nombre"] = $compra->proveedor->nombre;
                    array_push($calidad_global,$dato); 
                }
            }

            //ORDENAMOS EL ARRAY DE CALIDAD GLOBAL PARA LIMITARNOS A SOLO CINCO
            foreach ($calidad_global as $clave => $fila) {
                $porcentaje[$clave] = $fila['porcentaje'];
                $nombre[$clave] = $fila['nombre'];
            }
            if(!empty($calidad_global)){
                if(count($calidad_global)!=1)
                array_multisort($porcentaje, SORT_DESC, $nombre, SORT_ASC, $calidad_global);
            }

            //ARREGLAMOS AHORA LOS DATOS QUE VAMOS A ENVIAR
            $c3_data = array(); $c3_labels = array();
            for($i = 0 ; $i < count($calidad_global) ; $i++){
                if($i == 5) break;
                array_push($c3_data,$calidad_global[$i]["porcentaje"]/100);
                array_push($c3_labels,$calidad_global[$i]["nombre"]);
            }

        //CHART 4 CALIDAD ESPECIFICA POR PRODUCTO (ID_PRODUCTO 1 PRIMERO)
            $compra_especifica = Compra::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
            ->join('orden_producto', 'orden_producto.id_compra', '=', 'compra.id')->
            where('id_producto',1)->groupBy('id_proveedor')->get();

            $desperdicio_especifico = Desperdicio::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
            ->join('compra', 'desperdicio.id_compra', '=', 'compra.id')->
            where('id_producto',1)->groupBy('id_proveedor')->get();

            $calidad_especifica = array();
            $dato = array("porcentaje","nombre");
            foreach($compra_especifica as $compra){
                $grabar = true;
                foreach($desperdicio_especifico as $desperdicio){
                    if($compra->id_proveedor == $desperdicio->id_proveedor){
                        $dato["porcentaje"] = round(($compra->cantidad - $desperdicio->cantidad)*100/$compra->cantidad,2);
                        $dato["nombre"] = $compra->proveedor->nombre;
                        array_push($calidad_especifica,$dato); 
                        $grabar=false;
                    }
                }
                if($grabar){
                    $dato["porcentaje"] = 100;
                    $dato["nombre"] = $compra->proveedor->nombre;
                    array_push($calidad_especifica,$dato); 
                }
            }

            //ORDENAMOS EL ARRAY DE CALIDAD ESPECIFICA PARA LIMITARNOS A SOLO CINCO
            foreach ($calidad_especifica as $clave => $fila) {
                $porcentaje[$clave] = $fila['porcentaje'];
                $nombre[$clave] = $fila['nombre'];
            }

            if(!empty($calidad_especifica)){
                if(count($calidad_especifica)!=1)
                    array_multisort($porcentaje, SORT_DESC, $nombre, SORT_ASC, $calidad_especifica);
            }

            //ARREGLAMOS AHORA LOS DATOS QUE VAMOS A ENVIAR
            $c4_data = array(); $c4_labels = array();
            for($i = 0 ; $i < count($calidad_especifica) ; $i++){
                if($i == 5) break;
                array_push($c4_data,$calidad_especifica[$i]["porcentaje"]/100);
                array_push($c4_labels,$calidad_especifica[$i]["nombre"]);
            }
        
        //PARA FILTRAR EN EL CHART 4 POR PRODUCTOS
            $products = Producto::all();
            foreach($products as $pro){ $titulo_chart = $pro->nombre; break; };
            
        return view('indicators.buy', [
            'c1_data' => $c1_data,
            'c2_data' => $c2_data,
            'compra_promedio' => $compra_promedio,
            'pagos_dias' => $pagos_dias,
            'pendientes' => $pendientes,
            'calidad_global' => $calidad_global,
            'c3_data' => $c3_data,
            'c3_labels' => $c3_labels,
            'c4_data' => $c4_data,
            'c4_labels' => $c4_labels,
            'products' => $products,
            'titulo_chart' => $titulo_chart,
        ]);
    }

    public function showclient(){
        //CHART 1 CLIENTES CAPTADOS POR MES
            $captados = Cliente::selectRaw('count(id) as total, MONTH(created_at) as mes')->groupBy('mes')->get();
            $c1_data = array();
            for($i = 1; $i <= 12; $i++){
                $grabar = true;
                foreach($captados as $row){
                    if($i == $row->mes){
                        array_push($c1_data,$row->total); $grabar=false;
                    }
                }
                $grabar ? array_push($c1_data,0) : "";
            }

        //BLOQUE DE TOTALES
            //VALORES PARA CALCULAR TASA DE DESERCION Y LEALTAD
            $fecha_actual = date('Y-m-d');
            $nuevafecha = strtotime ( '-3 month' , strtotime ( $fecha_actual ) ) ;
            $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
            $clientes_rentables = Venta::where('fecha','>',$nuevafecha)->groupBy('id_cliente')->get();
            //TOTAL DE CLIENTES
            $clients = Cliente::all();

            //TASA DE LEALTAD
            $total_rentables = 0; $total_clientes = 0;
            foreach($clients as $row){ $total_clientes++; } $total_clientes == 0 ? $total_clientes=1 : "";
            foreach($clientes_rentables as $row){ $total_rentables++; }
            $porcentaje_leales = ($total_rentables*100)/$total_clientes;

            //TASA DE DESERCIÓN
            $porcentaje_desertores = 100-$porcentaje_leales;

            //REPETICIÓN DE COMPRA
            $repeticion = Venta::selectRaw('COUNT(id) as total')->groupBy('id_cliente')->orderBy('total','desc')->first();

        //CHART 2 TOP 5 RENTABILIDAD POR CLIENTE
            $ventas = Venta::selectRaw('COUNT(id) as total')->first();

            $compras_clientes = Venta::selectRaw('COUNT(id_cliente) as total, id_cliente')->
            groupBy('id_cliente')->orderBy('total', 'desc')->skip(0)->take(5)->get();

            $c2_data = array(); $c2_labels = array();
            $dato = array("porcentaje","nombre");
            foreach($compras_clientes as $compra){
                array_push($c2_data,round(($compra->total)*100/$ventas->total,2)/100);
                array_push($c2_labels,$compra->cliente->nombre);
            }

        //CHART 3 PRODUCTOS COMPRADOS GENERALMENTE POR UN CLIENTE
            foreach($clients as $row){ $titulo_chart = $row->nombre; $valor = $row->id; break; };
            $cliente_compra = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, MONTH(fecha) as mes')
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        where('id_cliente',$valor)->groupBy('mes')->get();
            $c3_data = array();
            for($i = 1; $i <= 12; $i++){
                $grabar = true;
                foreach($cliente_compra as $row){
                    if($i == $row->mes){
                        array_push($c3_data,$row->cantidad); $grabar=false;
                    }
                }
                $grabar ? array_push($c3_data,0) : "";
            }
        
        //PARA FILTRAR EN EL CHART 3 POR CLIENTES
            //$clients viene de bloque totales
            
        return view('indicators.client', [
            'porcentaje_desertores' => $porcentaje_desertores,
            'porcentaje_leales' => $porcentaje_leales,
            'repeticion' => $repeticion,
            'c1_data' => $c1_data,
            'c2_data' => $c2_data,
            'c2_labels' => $c2_labels,
            'c3_data' => $c3_data,
            'clients' => $clients,
            'titulo_chart' => $titulo_chart,
        ]);
    }

    public function showfinance(){
        //CHART 1 COMPARACIÓN AUMENTO DE INGRESOS
            $ventas = Venta::selectRaw('SUM(monto) as monto, MONTH(fecha) as mes')->groupBy('mes')->get();
            $bar_data = array();
            for($i = 1; $i <= 12; $i++){
                $grabar = true;
                foreach($ventas as $row){
                    if($i == $row->mes){
                        array_push($bar_data,$row->monto); $grabar=false;
                    }
                }
                $grabar ? array_push($bar_data,0) : "";
            }
            $bar_line = array();
            $anterior = 0;
            foreach ($bar_data as $dato) {
                if($anterior==0)
                    $valor = 0;
                else
                    $valor = round( (($dato*100)/$anterior)-100, 2);
                $anterior = $dato; 
                array_push($bar_line, $valor);
            }

        //BLOQUE DE TOTALES 1
            //TOTAL DE COSTOS
            $costos = Gasto_Costo::selectRaw('sum(monto) as total')->first();
            $costos ? $costos=$costos->total : $costos=0;

            $pagos = Pago_Nomina::selectRaw('sum(monto) as total')->first();
            $pagos ? $pagos=$pagos->total : $pagos=0;

            $costos_total = $costos + $pagos;

            //UTILIDAD
            $ingresos = Venta::selectRaw('sum(monto) as total')->first();
            $ingresos ? $ingresos=$ingresos->total : $ingresos=0;

            $utilidad_total = $ingresos - $costos_total;

            //RENTABILIDAD
            $ingresos == 0 ? $ingresos=1 : "";
            $rentabilidad = round(($utilidad_total / $ingresos)*100,2);

        //TOP 5 INGRESOS POR PRODUCTOS
            $top5 = Orden_Producto::selectRaw('SUM(precio*cantidad) as total, id_producto')
                                    ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                    groupBy('id_producto')->orderBy('total', 'desc')->skip(0)->take(5)->get();
            $total_top5=0;
            $top5_products = array(); $value = array("cantidad", "text");
            foreach($top5 as $row){
                $total_top5+= $row->total;
                $value["cantidad"] = $row->total;
                $value["text"] = $row->producto->nombre;
                array_push($top5_products,$value);
            }

        //CHART 2 TOTAL INGRESOS
            //$ventas y $bar_data CONSULTA DEL CHART 1 es lo que necesito en este indicador

        //CHART 3 TOTAL EGRESOS
            $costos = Gasto_Costo::selectRaw('SUM(monto) as monto, MONTH(fecha) as mes')->groupBy('mes')->get();
            $pagos = Pago_Nomina::selectRaw('SUM(monto) as monto, MONTH(mes) as mes')->groupBy('mes')->get();
            $compras = Compra::selectRaw('SUM(monto) as monto, MONTH(fecha) as mes')->groupBy('mes')->get();

            $c3_data = array();
            for($i = 1; $i <= 12; $i++){
                $egreso=0;
                foreach($costos as $row){ $i == $row->mes ? $egreso+=$row->monto : ""; }

                foreach($pagos as $row){ $i == $row->mes ? $egreso+=$row->monto : ""; }

                foreach($compras as $row){ $i == $row->mes ? $egreso+=$row->monto : ""; }
                
                array_push($c3_data,$egreso);
            }

        //TOP 5 COSTO DE PRODUCCIÓN EN DETERMINADO PRODUCTO
            $top5 = Orden_Producto::selectRaw('SUM(precio*cantidad) as total, id_producto')
                                ->join('compra', 'orden_producto.id_compra', '=', 'compra.id')->
                                groupBy('id_producto')->orderBy('total', 'desc')->skip(0)->take(5)->get();
            $total_buys5=0;
            $top5_buys = array(); $value = array("cantidad", "text");
            foreach($top5 as $row){
                $total_buys5+= $row->total;
                $value["cantidad"] = $row->total;
                $value["text"] = $row->producto->nombre;
                array_push($top5_buys,$value);
            }

        return view('indicators.finance', [
            'bar_data' => $bar_data,
            'bar_line' => $bar_line,
            'costos_total' => $costos_total,
            'rentabilidad' => $rentabilidad,
            'utilidad_total' => $utilidad_total,
            'top5_products' => $top5_products,
            'total_top5' => $total_top5,
            'top5_buys' => $top5_buys,
            'total_buys5' => $total_buys5,
            'c3_data' => $c3_data,
        ]);
    }

    public function ajaxlogistic(Request $request){ 
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
                case "c2": //CHART 2 DESPACHOS TOTALES
                    if($busqueda[1]=="Año"){
                        $despachos = Despacho::selectRaw('COUNT(id_venta) as total, '.$periodo)->
                                    whereYear('fecha', $busqueda[4])->groupBy('periodo')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $despachos = Despacho::selectRaw('COUNT(id_venta) as total, '.$periodo)->
                                    whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                    }
                    else{
                        $despachos = Despacho::selectRaw('COUNT(id_venta) as total, '.$periodo)->groupBy('periodo')->get();
                    }
                    for($i = $inicio; $i <= $fin; $i++){
                        $grabar = true;
                        foreach($despachos as $row){
                            if($i == $row->periodo){
                                array_push($values1,$row->total); $grabar=false;
                            }
                        }
                        $grabar ? array_push($values1,0) : "";
                    }
                    array_push($datos,$labels,$values1);
                    break;
                case "c3": //CHART 3 ENTRADAS - SALIDAS DE INVENTARIO
                    if($busqueda[1]=="Año"){
                        $dia_entradas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('compra', 'orden_producto.id_compra', '=', 'compra.id')->
                                        where('id_compra','!=',0)->whereYear('fecha', $busqueda[4])->
                                        groupBy('periodo')->orderBy('periodo', 'asc')->get();

                        $dia_salidas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        where('id_venta','!=',0)->whereYear('fecha', $busqueda[4])->
                                        groupBy('periodo')->orderBy('periodo', 'asc')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $dia_entradas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('compra', 'orden_producto.id_compra', '=', 'compra.id')->
                                        whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->
                                        where('id_compra','!=',0)->groupBy('periodo')->orderBy('periodo', 'asc')->get();

                        $dia_salidas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->
                                        where('id_venta','!=',0)->groupBy('periodo')->orderBy('periodo', 'asc')->get();
                    }
                    else{
                        $dia_entradas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('compra', 'orden_producto.id_compra', '=', 'compra.id')->
                                        where('id_compra','!=',0)->groupBy('periodo')->orderBy('periodo', 'asc')->get();

                        $dia_salidas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        where('id_venta','!=',0)->groupBy('periodo')->orderBy('periodo', 'asc')->get();
                    }
                    for($i = $inicio; $i <= $fin; $i++){
                        $grabar_entradas = true;
                        $grabar_salidas = true;
                        
                        foreach($dia_entradas as $row){
                            if($i == $row->periodo){
                                array_push($values1,$row->cantidad); $grabar_entradas=false;
                            }
                        }
    
                        foreach($dia_salidas as $row){
                            if($i == $row->periodo){
                                array_push($values2,$row->cantidad); $grabar_salidas=false;
                            }
                        }
    
                        $grabar_entradas ? array_push($values1,0) : "";
                        $grabar_salidas ? array_push($values2,0) : "";
                    }
                    array_push($datos,$labels,'Entradas','Salidas',$values1,$values2);
                    break;
                case "c5": //CHART 5 INVENTARIO VENDIDO
                    if($busqueda[1]=="Año"){
                        $ventas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        whereYear('fecha', $busqueda[4])->where('id_venta','!=',0)->groupBy('periodo')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $ventas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->
                                        where('id_venta','!=',0)->groupBy('periodo')->get();
                    }
                    else{
                        $ventas = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        where('id_venta','!=',0)->groupBy('periodo')->get();
                    }
                    for($i = $inicio; $i <= $fin; $i++){
                        $grabar = true;
                        foreach($ventas as $row){
                            if($i == $row->periodo){
                                array_push($values1,$row->cantidad); $grabar=false;
                            }
                        }
                        $grabar ? array_push($values1,0) : "";
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

    public function ajaxsells(Request $request){ 
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
                case "c1": //CHART 1 AUMENTO DE VENTAS Y EL TOTAL EN CANTIDAD
                    if($busqueda[1]=="Año"){
                        $ventas = Venta::selectRaw('COUNT(id) as total, '.$periodo)->
                                        whereYear('fecha', $busqueda[4])->groupBy('periodo')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $ventas = Venta::selectRaw('COUNT(id) as total, '.$periodo)->
                                    whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                    }
                    else{
                        $ventas = Venta::selectRaw('COUNT(id) as total, '.$periodo)->groupBy('periodo')->get();
                    }
                    //values1 sera bar_data y values2 sera bar_line
                    for($i = $inicio; $i <= $fin; $i++){
                        $grabar = true;
                        foreach($ventas as $row){
                            if($i == $row->periodo){
                                array_push($values1,$row->total); $grabar=false;
                            }
                        }
                        $grabar ? array_push($values1,0) : "";
                    }
                    $anterior = 0;
                    foreach ($values1 as $dato) {
                        if($anterior==0)
                            $valor = 0;
                        else
                            $valor = round( (($dato*100)/$anterior)-100, 2);
                        $anterior = $dato; 
                        array_push($values2, $valor);
                    }
                    array_push($datos,$labels,'Num. Ventas','Aum. Ventas',$values1,$values2);        
                    break;
                case "c2": //CHART 2 TOTAL VENTAS REALIZADAS
                    if($busqueda[1]=="Año"){
                        $ventas = Venta::selectRaw('COUNT(id) as total, '.$periodo)->
                                        whereYear('fecha', $busqueda[4])->groupBy('periodo')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $ventas = Venta::selectRaw('COUNT(id) as total, '.$periodo)->
                                    whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                    }
                    else{
                        $ventas = Venta::selectRaw('COUNT(id) as total, '.$periodo)->groupBy('periodo')->get();
                    }

                    for($i = $inicio; $i <= $fin; $i++){
                        $grabar = true;
                        foreach($ventas as $row){
                            if($i == $row->periodo){
                                array_push($values1,$row->total); $grabar=false;
                            }
                        }
                        $grabar ? array_push($values1,0) : "";
                    }
                    array_push($datos,$labels,$values1);
                    break;
                case "c3": //CHART 3 RENTABILIDAD - BENEFICIO DE VENTA
                    if($busqueda[1]=="Año"){
                        $ingresos = Venta::selectRaw('SUM(monto) as monto, '.$periodo)->
                                        whereYear('fecha', $busqueda[4])->groupBy('periodo')->get();
                        
                        $egresos = Compra::selectRaw('SUM(monto) as monto, '.$periodo)->
                                        whereYear('fecha', $busqueda[4])->groupBy('periodo')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $ingresos = Venta::selectRaw('SUM(monto) as monto, '.$periodo)->
                        whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                        
                        $egresos = Compra::selectRaw('SUM(monto) as monto, '.$periodo)->
                        whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                    }
                    else{
                        $ingresos = Venta::selectRaw('SUM(monto) as monto, '.$periodo)->groupBy('periodo')->get();
                        
                        $egresos = Compra::selectRaw('SUM(monto) as monto, '.$periodo)->groupBy('periodo')->get();
                    }
                    //BAR ES EL INGRESO ($values1) Y LA LINE ES EL DE BENEFICIO ($values2)
                    //INGRESOS
                        for($i = $inicio; $i <= $fin; $i++){
                            $grabar = true;
                            foreach($ingresos as $row){
                                if($i == $row->periodo){
                                    array_push($values1,$row->monto); $grabar=false;
                                }
                            }
                            $grabar ? array_push($values1,0) : "";
                        }
                    //BENEFICIO
                        for($i=0 ; $i < count($values1) ; $i++){
                            $grabar = true;
                            foreach($egresos as $row){
                                if($i == $row->periodo){
                                    $valor = $values1[$i] - $row->monto;
                                    array_push($values2,$valor); $grabar=false;
                                }
                            }
                            $grabar ? array_push($values2,$values1[$i]) : "";
                        }
                    array_push($datos,$labels,'Ingresos Ven.','Beneficios Ven.',$values1,$values2);
                    break;
            } 

            return response()->json($datos); 
        }
        catch(\Illuminate\Database\QueryException $e){
            return response()->json(false);       
        }
    }

    public function ajaxbuys(Request $request){ 
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
                case "c1": //CHART 1 EGRESOS GENERADOS POR COMPRAS
                    if($busqueda[1]=="Año"){
                        $egresos = Compra::selectRaw('SUM(monto) as monto, '.$periodo)->
                                    whereYear('fecha', $busqueda[4])->groupBy('periodo')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $egresos = Compra::selectRaw('SUM(monto) as monto, '.$periodo)->
                                    whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                    }
                    else{
                        $egresos = Compra::selectRaw('SUM(monto) as monto, '.$periodo)->groupBy('periodo')->get();
                    }
                    //values1 sera bar_data y values2 sera bar_line
                    for($i = $inicio; $i <= $fin; $i++){
                        $grabar = true;
                        foreach($egresos as $row){
                            if($i == $row->periodo){
                                array_push($values1,$row->monto); $grabar=false;
                            }
                        }
                        $grabar ? array_push($values1,0) : "";
                    }
                    array_push($datos,$labels,$values1);        
                    break;
                case "c2": //CHART 2 COMPRAS TOTALES POR MES (PIE)
                    if($busqueda[1]=="Año"){
                        $compras = Compra::selectRaw('COUNT(id) as total, '.$periodo)->
                                        whereYear('fecha', $busqueda[4])->groupBy('periodo')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $compras = Compra::selectRaw('COUNT(id) as total, '.$periodo)->
                                    whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                    }
                    else{
                        $compras = Compra::selectRaw('COUNT(id) as total, '.$periodo)->groupBy('periodo')->get();
                    }

                    for($i = $inicio; $i <= $fin; $i++){
                        $grabar = true;
                        foreach($compras as $row){
                            if($i == $row->periodo){
                                array_push($values1,$row->total); $grabar=false;
                            }
                        }
                        $grabar ? array_push($values1,0) : "";
                    }
                    array_push($datos,$labels,$values1);
                    break;
                case "c3": //CHART 3 CALIDAD GLOBAL POR PROVEEDOR
                    if($busqueda[0]=="Año"){
                        $compra_global = Compra::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('orden_producto', 'orden_producto.id_compra', '=', 'compra.id')->
                        whereYear('fecha', $busqueda[3])->groupBy('id_proveedor')->get();

                        $desperdicio_global = Desperdicio::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('compra', 'desperdicio.id_compra', '=', 'compra.id')->
                        whereYear('fecha', $busqueda[3])->groupBy('id_proveedor')->get();
                    }
                    elseif($busqueda[0]=="Específico"){
                        $compra_global = Compra::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('orden_producto', 'orden_producto.id_compra', '=', 'compra.id')->
                        whereBetween('fecha', [$busqueda[1].'-01', $busqueda[2].'-30'])->groupBy('id_proveedor')->get();

                        $desperdicio_global = Desperdicio::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('compra', 'desperdicio.id_compra', '=', 'compra.id')->
                        whereBetween('fecha', [$busqueda[1].'-01', $busqueda[2].'-30'])->groupBy('id_proveedor')->get();  
                    }
                    else{
                        $compra_global = Compra::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('orden_producto', 'orden_producto.id_compra', '=', 'compra.id')->groupBy('id_proveedor')->get();

                        $desperdicio_global = Desperdicio::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('compra', 'desperdicio.id_compra', '=', 'compra.id')->groupBy('id_proveedor')->get();
                    }

                    $calidad_global = array();
                    $dato = array("porcentaje","nombre");
                    foreach($compra_global as $compra){
                        $grabar = true;
                        foreach($desperdicio_global as $desperdicio){
                            if($compra->id_proveedor == $desperdicio->id_proveedor){
                                $dato["porcentaje"] = round(($compra->cantidad - $desperdicio->cantidad)*100/$compra->cantidad,2);
                                $dato["nombre"] = $compra->proveedor->nombre;
                                array_push($calidad_global,$dato); 
                                $grabar=false;
                            }
                        }
                        if($grabar){
                            $dato["porcentaje"] = 100;
                            $dato["nombre"] = $compra->proveedor->nombre;
                            array_push($calidad_global,$dato); 
                        }
                    }

                    //ORDENAMOS EL ARRAY DE CALIDAD GLOBAL PARA LIMITARNOS A SOLO CINCO
                    foreach ($calidad_global as $clave => $fila) {
                        $porcentaje[$clave] = $fila['porcentaje'];
                        $nombre[$clave] = $fila['nombre'];
                    }
                    if(!empty($calidad_global)){
                        if(count($calidad_global)!=1)
                        array_multisort($porcentaje, SORT_DESC, $nombre, SORT_ASC, $calidad_global);
                    }

                    //ARREGLAMOS AHORA LOS DATOS QUE VAMOS A ENVIAR
                    $values1 = array(); $labels = array();
                    for($i = 0 ; $i < count($calidad_global) ; $i++){
                        if($i == 5) break;
                        array_push($values1,$calidad_global[$i]["porcentaje"]/100);
                        array_push($labels,$calidad_global[$i]["nombre"]);
                    }

                    array_push($datos,$labels,$values1);
                    break;
                case "c4": //CHART 4 CALIDAD ESPECIFICA POR PRODUCTO
                    if($busqueda[0]=="Año"){
                        $compra_global = Compra::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('orden_producto', 'orden_producto.id_compra', '=', 'compra.id')->where('id_producto',$busqueda[4])->
                        whereYear('fecha', $busqueda[3])->groupBy('id_proveedor')->get();

                        $desperdicio_global = Desperdicio::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('compra', 'desperdicio.id_compra', '=', 'compra.id')->where('id_producto',$busqueda[4])->
                        whereYear('fecha', $busqueda[3])->groupBy('id_proveedor')->get();
                    }
                    elseif($busqueda[0]=="Específico"){
                        $compra_global = Compra::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('orden_producto', 'orden_producto.id_compra', '=', 'compra.id')->where('id_producto',$busqueda[4])->
                        whereBetween('fecha', [$busqueda[1].'-01', $busqueda[2].'-30'])->groupBy('id_proveedor')->get();

                        $desperdicio_global = Desperdicio::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('compra', 'desperdicio.id_compra', '=', 'compra.id')->where('id_producto',$busqueda[4])->
                        whereBetween('fecha', [$busqueda[1].'-01', $busqueda[2].'-30'])->groupBy('id_proveedor')->get();  
                    }
                    else{
                        $compra_global = Compra::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('orden_producto', 'orden_producto.id_compra', '=', 'compra.id')->
                        where('id_producto',$busqueda[4])->groupBy('id_proveedor')->get();

                        $desperdicio_global = Desperdicio::selectRaw('SUM(cantidad) as cantidad, id_proveedor')
                        ->join('compra', 'desperdicio.id_compra', '=', 'compra.id')->
                        where('id_producto',$busqueda[4])->groupBy('id_proveedor')->get();
                    }

                    $calidad_global = array();
                    $dato = array("porcentaje","nombre");
                    foreach($compra_global as $compra){
                        $grabar = true;
                        foreach($desperdicio_global as $desperdicio){
                            if($compra->id_proveedor == $desperdicio->id_proveedor){
                                $dato["porcentaje"] = round(($compra->cantidad - $desperdicio->cantidad)*100/$compra->cantidad,2);
                                $dato["nombre"] = $compra->proveedor->nombre;
                                array_push($calidad_global,$dato); 
                                $grabar=false;
                            }
                        }
                        if($grabar){
                            $dato["porcentaje"] = 100;
                            $dato["nombre"] = $compra->proveedor->nombre;
                            array_push($calidad_global,$dato); 
                        }
                    }

                    //ORDENAMOS EL ARRAY DE CALIDAD GLOBAL PARA LIMITARNOS A SOLO CINCO
                    foreach ($calidad_global as $clave => $fila) {
                        $porcentaje[$clave] = $fila['porcentaje'];
                        $nombre[$clave] = $fila['nombre'];
                    }
                    if(!empty($calidad_global)){
                        if(count($calidad_global)!=1)
                        array_multisort($porcentaje, SORT_DESC, $nombre, SORT_ASC, $calidad_global);
                    }

                    //ARREGLAMOS AHORA LOS DATOS QUE VAMOS A ENVIAR
                    $values1 = array(); $labels = array();
                    for($i = 0 ; $i < count($calidad_global) ; $i++){
                        if($i == 5) break;
                        array_push($values1,$calidad_global[$i]["porcentaje"]/100);
                        array_push($labels,$calidad_global[$i]["nombre"]);
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

    public function ajaxclients(Request $request){ 
        //$id lleva que chart estaremos filtrando
        $id = json_decode($request->input('id'));
        $busqueda = json_decode($request->input('busqueda'));  
        try{
            //DATOS QUE MANDARE PARA EL FILTRADO QUE SERIAN LOS LABELS Y VALORES
            $datos = array(); //LLEVARA DENTRO LABELS Y LOS VALUES
            $values1 = array(); $values2 = array();
            //PERIODO O FORMA DE BUSQUEDA
            if($busqueda[0] == "Mensual"){
                $id == "c1" ? $periodo = 'MONTH(created_at) as periodo' : $periodo = 'MONTH(fecha) as periodo';
                $labels = array("ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sep.", "oct.", "nov.", "dic.");
                $inicio = 1; $fin = 12; //para el for al grabar los values
            }
            else{
                $id == "c1" ? $periodo = 'DATE_FORMAT(created_at, "%w") as periodo' : $periodo = 'DATE_FORMAT(fecha, "%w") as periodo';
                $labels = array("dom.", "lun.", "mar.", "mie.", "jue.", "vie.", "sab.");
                $inicio = 0; $fin = 6; //para el for al grabar los values
            }

            switch ($id) {
                case "c1": //CHART 1 CLIENTES CAPTADOS POR MES
                    if($busqueda[1]=="Año"){
                        $captados = Cliente::selectRaw('count(id) as total, '.$periodo)->
                                        whereYear('created_at', $busqueda[4])->groupBy('periodo')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $captados = Cliente::selectRaw('count(id) as total, '.$periodo)->
                        whereBetween('created_at', [$busqueda[2].'-01', $busqueda[3].'-30'])->groupBy('periodo')->get();
                    }
                    else{
                        $captados = Cliente::selectRaw('count(id) as total, '.$periodo)->groupBy('periodo')->get();
                    }

                    for($i = $inicio; $i <= $fin; $i++){
                        $grabar = true;
                        foreach($captados as $row){
                            if($i == $row->periodo){
                                array_push($values1,$row->total); $grabar=false;
                            }
                        }
                        $grabar ? array_push($values1,0) : "";
                    }
                    array_push($datos,$labels,$values1);        
                    break;
                case "c3": //CHART 3 PRODUCTOS COMPRADOS GENERALMENTE POR UN CLIENTE
                    if($busqueda[1]=="Año"){
                        $cliente_compra = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        where('id_cliente',$busqueda[5])->whereYear('fecha', $busqueda[4])->
                                        groupBy('periodo')->get();
                    }
                    elseif($busqueda[1]=="Específico"){
                        $cliente_compra = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        whereBetween('fecha', [$busqueda[2].'-01', $busqueda[3].'-30'])->
                                        where('id_cliente',$busqueda[5])->groupBy('periodo')->get();
                    }
                    else{
                        $cliente_compra = Orden_Producto::selectRaw('SUM(cantidad) as cantidad, '.$periodo)
                                        ->join('venta', 'orden_producto.id_venta', '=', 'venta.id')->
                                        where('id_cliente',$busqueda[5])->groupBy('periodo')->get();
                    }

                    for($i = $inicio; $i <= $fin; $i++){
                        $grabar = true;
                        foreach($cliente_compra as $row){
                            if($i == $row->periodo){
                                array_push($values1,$row->cantidad); $grabar=false;
                            }
                        }
                        $grabar ? array_push($values1,0) : "";
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
                case "c1": //CHART 1 COMPARACIÓN AUMENTO DE INGRESOS
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
                    //values1 sera bar_data y values2 sera bar_line
                    for($i = $inicio; $i <= $fin; $i++){
                        $grabar = true;
                        foreach($ventas as $row){
                            if($i == $row->periodo){
                                array_push($values1,$row->monto); $grabar=false;
                            }
                        }
                        $grabar ? array_push($values1,0) : "";
                    }
                    $anterior = 0;
                    foreach ($values1 as $dato) {
                        if($anterior==0)
                            $valor = 0;
                        else
                            $valor = round( (($dato*100)/$anterior)-100, 2);
                        $anterior = $dato; 
                        array_push($values2, $valor);
                    }
                    array_push($datos,$labels,'Tot. Ingresos','Aum. Ingresos',$values1,$values2);        
                    break;
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
}
