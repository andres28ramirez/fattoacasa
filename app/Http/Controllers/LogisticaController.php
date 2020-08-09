<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Producto;
use App\Producto_Receta;
use App\Categoria;
use App\Reporte;
use App\Incidencia;
use App\Inventario;
use App\Proveedor;
use App\Suministro;

class LogisticaController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('p_logistica');
    }

    public function showInventario($registros = 10, $producto_name = null, $tiempo = null, 
                                    $fecha_1 = null, $fecha_2 = null, $order = 'expedicion')
    {
        //Función que me devuelve mi query paginado y dode yo le indico dentro de paginate(n)
        //la cantidad de N elementos que quiero que muestre
        //para el llevar la pagina siguiente lo realiza a traves de ?page=n en la URL que activa este evento

        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="expedicion" && $order!="id_producto" && $order!="precio" && $order!="cantidad")
            $order = "expedicion";

        if($producto_name && $tiempo){
            if($tiempo=="todos"){//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();
                $inventario = Inventario::whereIn('id_producto',$datos)->orderBy($order, 'desc')->paginate($registros);
            }
            else if($tiempo=="sin fecha"){ //FILTRO TODO LO QUE MANDEN PERO CON FECHA NULO
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();
                $inventario = Inventario::where('expedicion', null)->whereIn('id_producto',$datos)->orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();
                $inventario = Inventario::whereIn('id_producto',$datos)->whereBetween('expedicion', [$fecha_1, $fecha_2])->orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $inventario = Inventario::orderBy($order, 'desc')->paginate($registros);
        }

        //Para colocar el listado de productos al añadir o editar que vengan de suministro
        $pr_final = Producto_Receta::select('id_producto_final')->get();

        $products = Producto::whereIn('id',$pr_final)->get();
        //$products = Producto::all();

        return view('logistics.list_inventario', [
            'inventario' => $inventario,
            'registros' => $registros,
            'order' => $order,
            'producto_name' => $producto_name,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
            'products' => $products,
        ]);
    }

    public function showSuministro($registros = 10, $id = null, $persona = null, $producto_name = null, $tiempo = null, 
                                    $fecha_1 = null, $fecha_2 = null, $order = 'expedicion')
    {
        //Función que me devuelve mi query paginado y dode yo le indico dentro de paginate(n)
        //la cantidad de N elementos que quiero que muestre
        //para el llevar la pagina siguiente lo realiza a traves de ?page=n en la URL que activa este evento

        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="expedicion" && $order!="id_proveedor" && $order!="id_compra" && $order!="id_producto" && $order!="precio" && $order!="cantidad")
            $order = "expedicion";

        if($persona && $producto_name && $id && $tiempo){
            if($tiempo=="todos"){//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();

                $suministro = Suministro::where('id_proveedor', 'like', $persona == "todos" ? "%%" : $persona)->
                                    where('id_compra', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    whereIn('id_producto',$datos)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else if($tiempo=="sin fecha"){ //FILTRO TODO LO QUE MANDEN PERO CON FECHA NULO
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();

                $suministro = Suministro::where('id_proveedor', 'like', $persona == "todos" ? "%%" : $persona)->
                                    where('id_compra', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    where('expedicion', null)->
                                    whereIn('id_producto',$datos)->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();

                $suministro = Suministro::where('id_proveedor', 'like', $persona == "todos" ? "%%" : $persona)->
                                    where('id_compra', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    whereIn('id_producto',$datos)->
                                    whereBetween('expedicion', [$fecha_1, $fecha_2])->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $suministro = Suministro::orderBy($order, 'desc')->paginate($registros);
        }

        //Para colocar el proveedor al añadir o editar
        $providers = Proveedor::all();

        //Para colocar el listado de productos al añadir o editar
        $pr_final = Producto_Receta::select('id_producto_final')->get();

        $products = Producto::whereNotIn('id',$pr_final)->get();
        //$products = Producto::all();

        return view('logistics.list_suministro', [
            'suministro' => $suministro,
            'registros' => $registros,
            'order' => $order,
            'id' => $id,
            'persona' => $persona,
            'producto_name' => $producto_name,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
            'providers' => $providers,
            'products' => $products,
        ]);
    }

    public function showProducto($registros = 10, $id_categoria = null, $nombre = null, $order = 'id')
    {   
        //Función que me devuelve mi query paginado y dode yo le indico dentro de paginate(n)
        //la cantidad de N elementos que quiero que muestre
        //para el llevar la pagina siguiente lo realiza a traves de ?page=n en la URL que activa este evento

        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="nombre" && $order!="descripcion" && $order!="id_categoria")
            $order = "id";

        if($id_categoria && $nombre){
            if($id_categoria=="todos" && $nombre=="todos"){
                $products = Producto::orderBy($order, 'desc')->paginate($registros);
            }
            else if($id_categoria=="todos"){
                $products = Producto::where('nombre', 'like', '%' . $nombre . '%')->orderBy($order, 'desc')->paginate($registros);
            }
            else if($nombre=="todos"){
                $products = Producto::where('id_categoria',$id_categoria)->orderBy($order, 'desc')->paginate($registros);
            }
            else{
                $products = Producto::where('id_categoria',$id_categoria)->where('nombre', 'like', '%' . $nombre . '%')->orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $products = Producto::orderBy($order, 'desc')->paginate($registros);
        }

        //Para filtrar por categoria
        $categories = Categoria::all();

        return view('logistics.list_productos', [
            'products' => $products,
            'registros' => $registros,
            'order' => $order,
            'id_categoria' => $id_categoria,
            'nombre' => $nombre,
            'categories' => $categories,
        ]);
    }

    public function createProducto()
    {
        //Función que me devuelve mi query paginado y dode yo le indico dentro de paginate(n)
        //la cantidad de N elementos que quiero que muestre
        //para el llevar la pagina siguiente lo realiza a traves de ?page=n en la URL que activa este evento
        $categories = Categoria::all();
        $products = Producto::all();

        return view('logistics.add_product', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function saveProducto(Request $request)
    {
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'nombre' => 'required|string|max:255|unique:producto',
                'descripcion' => 'required|string|max:255',
                'id_categoria' => 'required',
            ]);

            $name         = $request->input('nombre');
            $description  = $request->input('descripcion');
            $categoria = $request->input('id_categoria');
            
            //Asignar los valores al nuevo objeto de producto
            $producto = new Producto();
            $producto->nombre       = $name;
            $producto->descripcion  = $description;
            $producto->id_categoria = $categoria;

            //Grabamos el producto
            $producto->save();
            
            //GRABAR EL RECETARIO
            $id_producto = $producto->id;
            for ($i = 1; $i <= 30; $i++) {
                if($request->has('form-producto-'.$i)){
                    $recetario = new Producto_Receta();
                    $recetario->id_producto_final = $id_producto;
                    $recetario->id_ingrediente = $request->input('form-producto-'.$i);
                    $recetario->cantidad = $request->input('form-cantidad-'.$i);
                    $recetario->save();
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
            $report->activity    = "Módulo Logística";
            $report->description = "Producto Añadido Código (".$id_producto.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-producto')->with('message', 'Producto añadido Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Logística";
            $report->description = "Error al almacenar producto - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-producto')->with('status', 'Error al Almacenar la información');
        }
    }

    public function saveSuministro(Request $request)
    {
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'id_producto' => 'required',
                'id_proveedor' => 'required',
                'precio' => 'required|string|max:255',
                'cantidad' => 'required|string|max:255',
            ]);

            $id_proveedor = $request->input('id_proveedor');
            $id_producto  = $request->input('id_producto');
            $precio       = $request->input('precio');
            $cantidad     = $request->input('cantidad');
            $expedicion   = $request->input('expedicion');
            
            //Asignar los valores al nuevo objeto de producto
            $suministro = new Suministro();
            $suministro->id_proveedor = $id_proveedor;
            $suministro->id_producto  = $id_producto;
            $suministro->precio       = $precio;
            $suministro->cantidad     = $cantidad;
            $suministro->expedicion   = $expedicion;

            //Grabamos los cambios
            $suministro->save();

            //Arreglo la variable session de suministro
            if($suministro->expedicion)
                $this->resetNotification($suministro,'save','suministro');

            //GRABAR EL REPORTE DE GUARDADO EXITOSO
            //Conseguimos el id del usuario
            $user = \Auth::user();
            $id   = $user->id;

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Logística";
            $report->description = "Suministro Añadido Código (".$id_producto.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-suministro')->with('message', 'Producto añadido Exitosamente');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Logística";
            $report->description = "Error al almacenar suministro - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-suministro')->with('status', 'Error al Almacenar la información');
        }
    }

    public function saveInventario(Request $request)
    {
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'id_producto' => 'required',
                'precio' => 'required|string|max:255',
                'cantidad' => 'required|string|max:255',
            ]);

            $id_producto  = $request->input('id_producto');
            $precio       = $request->input('precio');
            $cantidad     = $request->input('cantidad');
            $expedicion   = $request->input('expedicion');
            
            //Asignar los valores al nuevo objeto de producto
            $inventario = new Inventario();
            $inventario->id_producto  = $id_producto;
            $inventario->precio       = $precio;
            $inventario->cantidad     = $cantidad;
            $inventario->expedicion   = $expedicion;
            
            //Quedara en true si al final todo fue correcto
            $grabar = true;
            
            //Grabara todos los productos que le falte X cantidad
            $restantes = array();

            //Buscamos el producto que queremos para revisar su receta
            $producto = Producto::find($id_producto);
            foreach($producto->receta as $one){
                $ing_id       = $one->id_ingrediente;
                $ing_cantidad = $one->cantidad * $cantidad;
                
                $sum_cantidad = Suministro::select(DB::raw('SUM(cantidad) as cantidad'))->where('id_producto',$ing_id)->groupBy('id_producto')->first();
                if($sum_cantidad){
                    if($ing_cantidad - $sum_cantidad->cantidad <= 0){
                        //Modificamos el suministro
                        $modificar = true;
                        $suministro = Suministro::where('id_producto',$ing_id)->orderBy('expedicion', 'asc')->get();
                        foreach ($suministro as &$row){
                            if($modificar){
                                if($ing_cantidad - $row->cantidad < 0){
                                    $modificar = false;
                                    $row->cantidad = $row->cantidad - $ing_cantidad;
                                    $row->update();
                                }
                                else{
                                    $ing_cantidad = $ing_cantidad - $row->cantidad;
                                    $row->delete();
                                    if($ing_cantidad == 0) $modificar = false;
                                }
                            }
                        }
                    }
                    else{
                        $grabar = false;
                        $producto_restante = array("nombre","cantidad");
                        $producto_restante["nombre"] = $one->ingrediente->nombre;
                        $producto_restante["cantidad"] = $ing_cantidad - $sum_cantidad->cantidad;
                        array_push($restantes,$producto_restante);
                    }
                }
                else{
                    $grabar = false;
                    $producto_restante = array("nombre","cantidad");
                    $producto_restante["nombre"] = $one->ingrediente->nombre;
                    $producto_restante["cantidad"] = $ing_cantidad;
                    array_push($restantes,$producto_restante);
                }
            }

            if($grabar){
                //Grabamos los cambios
                $inventario->save();

                //Arreglo la variable session de inventario
                if($inventario->expedicion)
                    $this->resetNotification($inventario,'save','inventario');

                //GRABAR EL REPORTE DE GUARDADO EXITOSO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user     = $id;
                $report->name        = $user->name;
                $report->activity    = "Módulo Logística";
                $report->description = "Inventario Añadido Código (".$id_producto.")";

                //Grabamos el reporte de almacenamiento en el sistema
                $report->save();
                DB::commit();
                return redirect()->route('list-inventario')->with('message', 'Producto añadido Exitosamente');
            }
            else{
                DB::rollback();
                return redirect()->route('list-inventario')->with('fallo', $restantes);
            }
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Logística";
            $report->description = "Error al almacenar inventario - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-inventario')->with('status', 'Error al Almacenar la información');
        }
    }

    public function detailProducto($id = null)
    {
        //$id va ser el id del producto que deseo
        $product = Producto::find($id);

        //recojo todas las categorias
        $categories = Categoria::all();

        //recojo todos los productos por si quiere cambiar la receta
        $productos = Producto::all();

        if(empty($id) || empty($product))
        return redirect()->route('list-producto');

        return view('logistics.product_detail', [
            'product' => $product,
            'categories' => $categories,
            'productos' => $productos,
        ]);
    }

    public function detailSuministroInfo(Request $request)
    {   
        //$id de la compra
        $id = json_decode($request->input('values'));

        try{
            //recibo los productos de la compra
            $suministro = Suministro::find($id);

            $datos = array("id_proveedor", "id_producto", "precio", "cantidad", "expedicion");
            $datos["id_proveedor"] = $suministro->id_proveedor;
            $datos["id_producto"] = $suministro->id_producto;
            $datos["precio"] = $suministro->precio;
            $datos["cantidad"] = $suministro->cantidad;
            $datos["expedicion"] = $suministro->expedicion;

            return response()->json($datos); 
        }
        catch(\Illuminate\Database\QueryException $e){
            return response()->json(false);       
        }
    }

    public function detailInventarioInfo(Request $request)
    {   
        //$id de la compra
        $id = json_decode($request->input('values'));

        try{
            //recibo los productos de la compra
            $inventario = Inventario::find($id);

            $datos = array("id_producto", "precio", "cantidad", "expedicion");
            $datos["id_producto"] = $inventario->id_producto;
            $datos["precio"] = $inventario->precio;
            $datos["cantidad"] = $inventario->cantidad;
            $datos["expedicion"] = $inventario->expedicion;

            return response()->json($datos); 
        }
        catch(\Illuminate\Database\QueryException $e){
            return response()->json(false);       
        }
    }

    public function updateSuministro(Request $request)
    {
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'id_producto' => 'required',
                'id_proveedor' => 'required',
                'precio' => 'required|string|max:255',
                'cantidad' => 'required|string|max:255',
            ]);

            //id del suministro
            $id = $request->input('suministro-cod');
            $id_proveedor = $request->input('id_proveedor');
            $id_producto  = $request->input('id_producto');
            $precio       = $request->input('precio');
            $cantidad     = $request->input('cantidad');
            $expedicion   = $request->input('expedicion');
            
            //Asignar los valores al nuevo objeto de producto
            $suministro = Suministro::find($id);
            $past = Suministro::find($id);
            $suministro->id_proveedor = $id_proveedor;
            $suministro->id_producto  = $id_producto;
            $suministro->precio       = $precio;
            $suministro->cantidad     = $cantidad;
            $suministro->expedicion   = $expedicion;

            //Grabamos los cambios
            $suministro->update();
            
            //Arreglo la variable session de suministro
            if($past->expedicion || $suministro->expedicion)
                $this->resetNotificationUpdate($past, $suministro, 'suministro');

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Logística";
            $report->description = "Suministro Editado - Código (".$id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-suministro')->with('message', 'Suministro Editado Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Logística";
            $report->description = "Error al editar suministro - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-suministro')->with('status', 'Error al Editar la información');
        }
    }

    public function updateInventario(Request $request)
    {
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'id_producto' => 'required',
                'precio' => 'required|string|max:255',
                'cantidad' => 'required|string|max:255',
            ]);

            //id del inventario
            $id = $request->input('inventario-cod');
            $id_producto  = $request->input('id_producto');
            $precio       = $request->input('precio');
            $cantidad     = $request->input('cantidad');
            $expedicion   = $request->input('expedicion');
            
            //Asignar los valores al nuevo objeto de producto
            $inventario = Inventario::find($id);
            $past = Inventario::find($id); //para acomodar las notificaciones
            $inventario->id_producto  = $id_producto;
            $inventario->precio       = $precio;
            $inventario->cantidad     = $cantidad;
            $inventario->expedicion   = $expedicion;

            //Quedara en true si al final todo fue correcto
            $grabar = true;
            
            //Grabara todos los productos que le falte X cantidad
            $restantes = array();

            //Acomodamos la cantidad que vamos a sumar o restar (si es negativo no necesito validar nada)
            $cantidad = $cantidad - $past->cantidad;
            
            //Si es negativo me salto toda la validación y voy directo a arreglar suministro, sino tengo que validar
            if($cantidad < 0){
                //Buscamos el recetario del producto para arreglar el suministro
                $producto = Producto::find($id_producto);
                foreach($producto->receta as $one){
                    $ing_id       = $one->id_ingrediente;
                    $ing_cantidad = $one->cantidad * ($cantidad*-1);
                    
                    $sum_cantidad = Suministro::where('id_producto',$ing_id)->first();
                    if($sum_cantidad){//Existe el producto por lo tanto solo le agrego al ultimo la cantidad
                        $sum_cantidad->cantidad = $sum_cantidad->cantidad + $ing_cantidad;
                        $sum_cantidad->update();
                    }
                    else{//Ya no existe el producto en suministro asi que genero uno nuevo
                        $suministro = new Suministro();
                        $suministro->id_proveedor = 1;
                        $suministro->id_producto  = $ing_id;
                        $suministro->precio       = 1;
                        $suministro->cantidad     = $ing_cantidad;
                        $suministro->expedicion   = null;
                        $suministro->save();
                    }
                }
            }
            else if($cantidad > 0){    
                //Buscamos el producto que queremos para revisar su receta
                $producto = Producto::find($id_producto);
                foreach($producto->receta as $one){
                    $ing_id       = $one->id_ingrediente;
                    $ing_cantidad = $one->cantidad * $cantidad;
                    
                    $sum_cantidad = Suministro::select(DB::raw('SUM(cantidad) as cantidad'))->where('id_producto',$ing_id)->groupBy('id_producto')->first();
                    if($sum_cantidad){
                        if($ing_cantidad - $sum_cantidad->cantidad <= 0){
                            //Modificamos el suministro
                            $modificar = true;
                            $suministro = Suministro::where('id_producto',$ing_id)->orderBy('expedicion', 'asc')->get();
                            foreach ($suministro as &$row){
                                if($modificar){
                                    if($ing_cantidad - $row->cantidad < 0){
                                        $modificar = false;
                                        $row->cantidad = $row->cantidad - $ing_cantidad;
                                        $row->update();
                                    }
                                    else{
                                        $ing_cantidad = $ing_cantidad - $row->cantidad;
                                        $row->delete();
                                        if($ing_cantidad == 0) $modificar = false;
                                    }
                                }
                            }
                        }
                        else{
                            $grabar = false;
                            $producto_restante = array("nombre","cantidad");
                            $producto_restante["nombre"] = $one->ingrediente->nombre;
                            $producto_restante["cantidad"] = $ing_cantidad - $sum_cantidad->cantidad;
                            array_push($restantes,$producto_restante);
                        }
                    }
                    else{
                        $grabar = false;
                        $producto_restante = array("nombre","cantidad");
                        $producto_restante["nombre"] = $one->ingrediente->nombre;
                        $producto_restante["cantidad"] = $ing_cantidad;
                        array_push($restantes,$producto_restante);
                    }
                }
            }

            if($grabar){
                //Grabamos los cambios
                $inventario->update();

                //Arreglo la variable session de inventario
                    if($past->expedicion || $inventario->expedicion)
                    $this->resetNotificationUpdate($past, $inventario, 'inventario');

                //Grabamos ahora el reporte de la edicion del producto
                //Conseguimos el usuario
                $user = \Auth::user();

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user     = $user->id;
                $report->name        = $user->name;
                $report->activity    = "Módulo Logística";
                $report->description = "Inventario Editado - Código (".$id.")";

                //Grabamos el reporte de almacenamiento en el sistema
                $report->save();

                DB::commit();
                return redirect()->route('list-inventario')->with('message', 'Inventario Editado Exitosamente!');
            }
            else{
                DB::rollback();
                return redirect()->route('list-inventario')->with('fallo-edit', $restantes);
            }
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Logística";
            $report->description = "Error al editar inventario - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-inventario')->with('status', 'Error al Editar la información');
        }
    }

    public function updateProducto(Request $request)
    {
        DB::beginTransaction();
        try{
            //id del producto
            $id = $request->input('id');
            
            $validate = $this->validate($request, [
                'nombre' => 'required|string|max:255|unique:producto,nombre,'.$id,
                'descripcion' => 'required|string|max:255',
                'id_categoria' => 'required',
            ]);

            $name         = $request->input('nombre');
            $description  = $request->input('descripcion');
            $categoria = $request->input('id_categoria');
            
            //Asignar los valores al nuevo objeto de producto
            $producto = Producto::find($id);
            $producto->nombre       = $name;
            $producto->descripcion  = $description;
            $producto->id_categoria = $categoria;

            //Grabamos los cambios
            $producto->update();

            //Grabamos ahora el reporte de la edicion del producto
            //Conseguimos el usuario
            $user = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $user->id;
            $report->name        = $user->name;
            $report->activity    = "Módulo Logística";
            $report->description = "Producto Editado Código (".$id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-producto', ['id' => $producto->id])->with('message', 'Producto Editado Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Módulo Logística";
            $report->description = "Error al editar producto - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('list-producto')->with('status', 'Error al Editar la información');
        }
    }

    public function updateReceta(Request $request)
    {
        $id_producto = json_decode($request->input('id_producto'));
        $id_ingrediente = json_decode($request->input('id_ingrediente'));
        $cantidad = json_decode($request->input('cantidad'));
        
        DB::beginTransaction();
        try{
            DB::commit();
            if($id_ingrediente == 0 && $cantidad == 0){
                Producto_Receta::where('id_producto_final', $id_producto)->delete();;
            }
            else{
                $recetario = new Producto_Receta();
                $recetario->id_producto_final = $id_producto;
                $recetario->id_ingrediente = $id_ingrediente;
                $recetario->cantidad = $cantidad;
                $recetario->save();
            }
            return response()->json(true); 
        }
        catch(\Illuminate\Database\QueryException $e){
            DB::rollback();
            return response()->json(false);       
        }
    }

    public function deleteSuministro(Request $request){
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $suministro = Suministro::find($valor);

                $codigo = $suministro->id;
                //Elimino suministro
                $suministro->delete();
                array_push($valores, $valor);

                //Arreglo la variable session de suministro
                if($suministro->expedicion)
                    $this->resetNotification($suministro,'delete','suministro');
                
                //GRABAMOS EL REPORTE DE ELIMINADO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $user_id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user = $user_id;
                $report->name    = $user->name;
                $report->activity    = "Módulo Logística";
                $report->description = "Suministro Eliminado - Código (".$codigo.")";

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

    public function deleteInventario(Request $request){
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $inventario = Inventario::find($valor);

                $codigo = $inventario->id;
                $codigo_pro = $inventario->id_producto;
                $cantidad = $inventario->cantidad;
                //Elimino inventario
                $inventario->delete();
                array_push($valores, $valor);

                //Arreglo la variable session de inventario
                if($inventario->expedicion)
                    $this->resetNotification($inventario,'delete','inventario');
                
                //Acomodamos el suministro
                //Buscamos el recetario del producto para arreglar el suministro
                $producto = Producto::find($codigo_pro);
                foreach($producto->receta as $one){
                    $ing_id       = $one->id_ingrediente;
                    $ing_cantidad = $one->cantidad * $cantidad;
                    
                    $sum_cantidad = Suministro::where('id_producto',$ing_id)->first();
                    if($sum_cantidad){//Existe el producto por lo tanto solo le agrego al ultimo la cantidad
                        $sum_cantidad->cantidad = $sum_cantidad->cantidad + $ing_cantidad;
                        $sum_cantidad->update();
                    }
                    else{//Ya no existe el producto en suministro asi que genero uno nuevo
                        $suministro = new Suministro();
                        $suministro->id_proveedor = 1;
                        $suministro->id_producto  = $ing_id;
                        $suministro->precio       = 1;
                        $suministro->cantidad     = $ing_cantidad;
                        $suministro->expedicion   = null;
                        $suministro->save();
                    }
                }

                //GRABAMOS EL REPORTE DE ELIMINADO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $user_id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user = $user_id;
                $report->name    = $user->name;
                $report->activity    = "Módulo Logística";
                $report->description = "Inventario Eliminado - Código (".$codigo.")";

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

    public function resetNotification($log, $metodo, $almacen){
        //$almacen = inventario, suministro
        $total = session()->get('notificaciones');
        $expirar = $almacen == 'inventario' ? session()->get('inventario-expirar') : session()->get('suministro-expirar');
        $caducar = $almacen == 'inventario' ? session()->get('inventario-caducar') : session()->get('suministro-caducar');

        //$metodo = delete, save
        if( strtotime($log->expedicion) - strtotime(date("d-m-Y")) < 3*86400){
            if( strtotime($log->expedicion) - strtotime(date("d-m-Y")) > 0*86400){
                $metodo == 'delete' ? $expirar-- : $expirar++;
                $metodo == 'delete' ? $total-- : $total++;
            }
            else{
                $metodo == 'delete' ? $caducar-- : $caducar++;
                $metodo == 'delete' ? $total-- : $total++;
            }
        }

        session()->put('notificaciones', $total);
        $almacen == 'inventario' ? session()->put('inventario-expirar', $expirar) : session()->put('suministro-expirar', $expirar);
        $almacen == 'inventario' ? session()->put('inventario-caducar', $caducar) : session()->put('suministro-caducar', $caducar);
    }

    public function resetNotificationUpdate($past_log, $new_log, $almacen){
        //$almacen = inventario, suministro
        $total = session()->get('notificaciones');
        $expirar = $almacen == 'inventario' ? session()->get('inventario-expirar') : session()->get('suministro-expirar');
        $caducar = $almacen == 'inventario' ? session()->get('inventario-caducar') : session()->get('suministro-caducar');

        //$past_log = valor antes del update
        //$new_log = valor luego del update

        if($past_log->expedicion){
            if( strtotime($past_log->expedicion) - strtotime(date("d-m-Y")) < 3*86400){
                if( strtotime($past_log->expedicion) - strtotime(date("d-m-Y")) > 0*86400){
                    $expirar--;
                    $total--;
                }
                else{
                    $caducar--;
                    $total--;
                }
            }
        }

        if($new_log->expedicion){
            if( strtotime($new_log->expedicion) - strtotime(date("d-m-Y")) < 3*86400){
                if( strtotime($new_log->expedicion) - strtotime(date("d-m-Y")) > 0*86400){
                    $expirar++;
                    $total++;
                }
                else{
                    $caducar++;
                    $total++;
                }
            }
        }

        session()->put('notificaciones', $total);
        $almacen == 'inventario' ? session()->put('inventario-expirar', $expirar) : session()->put('suministro-expirar', $expirar);
        $almacen == 'inventario' ? session()->put('inventario-caducar', $caducar) : session()->put('suministro-caducar', $caducar);
    }

    public function downloadInventario($producto_name = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null)
    {    
        if($producto_name && $tiempo){
            $filtro = "";
            if($tiempo=="todos"){//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();
                $inventario = Inventario::whereIn('id_producto',$datos)->get();
            }
            else if($tiempo=="sin fecha"){ //FILTRO TODO LO QUE MANDEN PERO CON FECHA NULO
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();
                $inventario = Inventario::where('expedicion', null)->whereIn('id_producto',$datos)->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();
                $inventario = Inventario::whereIn('id_producto',$datos)->whereBetween('expedicion', [$fecha_1, $fecha_2])->get();
            }
            $tiempo == "Específico" ? $filtro .= "Fecha de Expiración entre ".$fecha_1." a ".$fecha_2 : "";
            $tiempo == "sin fecha" ? $filtro .= "Sin fecha de expiración " : "";
            $producto_name != "todos" ? $filtro .= " | Productos por: ".$producto_name : "";
        }
        else{
            $inventario = Inventario::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todo el inventario" : "";
        $datos = array();
        $titulos = array('Producto', 'Precio', 'Cantidad', 'Fecha de Expiración');

        foreach ($inventario as $product) {
            $data_content["dato-1"] = $product->producto->nombre;
            $data_content["dato-2"] = number_format($product->precio,2, ",", ".")." Bs";
            $data_content["dato-3"] = $product->cantidad." Kg/Und";
            $data_content["dato-4"] = $product->expedicion ? $product->expedicion : "no posee";
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Inventario.pdf", "Inventario de la Empresa", $filtro, $datos, $titulos);
    }

    public function downloadSuministro($id = null, $persona = null, $producto_name = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null)
    {    
        if($persona && $producto_name && $id && $tiempo){
            $filtro = "";
            if($tiempo=="todos"){//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();

                $suministro = Suministro::where('id_proveedor', 'like', $persona == "todos" ? "%%" : $persona)->
                                    where('id_compra', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    whereIn('id_producto',$datos)->get();
            }
            else if($tiempo=="sin fecha"){ //FILTRO TODO LO QUE MANDEN PERO CON FECHA NULO
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();

                $suministro = Suministro::where('id_proveedor', 'like', $persona == "todos" ? "%%" : $persona)->
                                    where('id_compra', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    where('expedicion', null)->
                                    whereIn('id_producto',$datos)->get();
            }
            else{//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $datos = Producto::select('id')->where('nombre','like',$producto_name == "todos" ? "%%" : "%".$producto_name."%")->get();

                $suministro = Suministro::where('id_proveedor', 'like', $persona == "todos" ? "%%" : $persona)->
                                    where('id_compra', 'like', $id == "todos" ? "%%" : "%".$id."%")->
                                    whereIn('id_producto',$datos)->
                                    whereBetween('expedicion', [$fecha_1, $fecha_2])->get();
            }
            $tiempo == "Específico" ? $filtro .= "Fecha de Expiración entre ".$fecha_1." a ".$fecha_2 : "";
            $tiempo == "sin fecha" ? $filtro .= "Sin fecha de expiración " : "";
            $producto_name != "todos" ? $filtro .= " | Productos por: ".$producto_name : "";
            $id != "todos" ? $filtro .= " | Código de Compra: ".$id : "";
            $persona != "todos" ? $filtro .= " | Proveedor Nro: ".$persona : "";
        }
        else{
            $suministro = Suministro::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todo el suministro" : "";
        $datos = array();
        $titulos = array('Código Compra', 'Proveedor', 'Producto', 'Precio', 'Cantidad', 'Fecha de Expiración');

        foreach ($suministro as $product) {
            $data_content["dato-1"] = $product->id_compra == 0 ? "Ingreso Manual" : $product->id_compra;
            $data_content["dato-2"] = $product->proveedor->nombre;
            $data_content["dato-3"] = $product->producto->nombre;
            $data_content["dato-4"] = number_format($product->precio,2, ",", ".")." Bs";
            $data_content["dato-5"] = $product->cantidad." Kg/Und";
            $data_content["dato-6"] = $product->expedicion ? $product->expedicion : "no posee";
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Suministro.pdf", "Suministro de la Empresa", $filtro, $datos, $titulos);
    }

    public function downloadProducto($id_categoria = null, $nombre = null)
    {    
        if($id_categoria && $nombre){
            $filtro = "";
            if($id_categoria=="todos" && $nombre=="todos"){
                $products = Producto::all();
            }
            else if($id_categoria=="todos"){
                $products = Producto::where('nombre', 'like', '%' . $nombre . '%')->get();
            }
            else if($nombre=="todos"){
                $products = Producto::where('id_categoria',$id_categoria)->get();
            }
            else{
                $products = Producto::where('id_categoria',$id_categoria)->where('nombre', 'like', '%' . $nombre . '%')->get();
            }
            $id_categoria != "todos" ? $filtro .= "Categoría Código: ".$id_categoria : "";
            $nombre != "todos" ? $filtro .= " | Productos por: ".$nombre : "";
        }
        else{
            $products = Producto::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los productos" : "";
        $datos = array();
        $titulos = array('Producto', 'Descripción', 'Categoría');

        foreach ($products as $product) {
            $data_content["dato-1"] = $product->nombre;
            $data_content["dato-2"] = $product->descripcion;
            $data_content["dato-3"] = $product->categoria->nombre;
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Productos.pdf", "Listado de Productos", $filtro, $datos, $titulos);
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
