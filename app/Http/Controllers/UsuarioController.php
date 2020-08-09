<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth; //Nuevo
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Trabajador;
use App\Pago_Nomina;
use App\Despacho;
use App\Incidencia;
use App\Reporte;
use App\Calendario;
use App\Egreso;
use App\Suministro; //Nuevo
use App\Inventario; //Nuevo
use App\Venta; //Nuevo
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class UsuarioController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createUser()
    {
        return view('profile.usuarios.add');
    }

    public function createWorker()
    {
        return view('profile.empleados.add');
    }

    public function createBackup()
    {
        try {
            // start the backup process
            Artisan::call('backup:run');
            $output = Artisan::output();
            // log the results
            Log::info("Backpack\BackupManager -- new backup started from admin interface \r\n" . $output);
            // return the results as a response to the ajax call
            return redirect()->back()->with('message', 'Nueva copia de seguridad creada!');
        }catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('status', "Error al crear la copia de seguridad");
        }
    }

    public function restoreBackup(Request $request){
        //Forma de eliminar la BD (funciona)
        //Schema::getConnection()->getDoctrineSchemaManager()->dropDatabase("manual_fatto");

        //Forma para crear la BD (no funciona ya que elimino lo de arriba)
        //DB::getConnection()->statement('CREATE DATABASE :schema', array('schema' => "manual_fatto"));

        //Schema es la forma con laravel para eliminar las tablas
        $file_path = $request->file('form_file');
        if($file_path){
            //Deshabilitar las llaves foraneas
            Schema::disableForeignKeyConstraints();
            //Tablas
                Schema::dropIfExists('calendario');
                Schema::dropIfExists('categoria');
                Schema::dropIfExists('cliente');
                Schema::dropIfExists('compra');
                Schema::dropIfExists('despacho');
                Schema::dropIfExists('desperdicio');
                Schema::dropIfExists('egreso');
                Schema::dropIfExists('gasto_costo');
                Schema::dropIfExists('incidencia');
                Schema::dropIfExists('indicador');
                Schema::dropIfExists('inventario');
                Schema::dropIfExists('migrations');
                Schema::dropIfExists('orden_producto');
                Schema::dropIfExists('pago');
                Schema::dropIfExists('pago_nomina');
                Schema::dropIfExists('password_resets');
                Schema::dropIfExists('producto');
                Schema::dropIfExists('producto_receta');
                Schema::dropIfExists('proveedor');
                Schema::dropIfExists('reporte');
                Schema::dropIfExists('suministro');
                Schema::dropIfExists('trabajador');
                Schema::dropIfExists('users');
                Schema::dropIfExists('venta');
                Schema::dropIfExists('zona');
            //Habilitar las llaves foraneas
            Schema::enableForeignKeyConstraints();

            //Restauración de la Base de Datos
            // Variable temporal que lleva la query actual
            $templine = '';
            
            // Cada linea dentro del archivo .sql
            $lines = file($file_path);
            
            $error = '';
            
            // Loop through each line
            foreach ($lines as $line){
                // Skip it if it's a comment
                if(substr($line, 0, 2) == '--' || $line == ''){
                    continue;
                }
                
                // Add this line to the current segment
                $templine .= $line;
                
                // If it has a semicolon at the end, it's the end of the query
                if (substr(trim($line), -1, 1) == ';'){
                    // Perform the query
                    if(!DB::unprepared($templine)){
                        $error .= 'Error performing query "<b>' . $templine . '</b>"';
                    }
                    
                    // Reset temp variable to empty
                    $templine = '';
                }
            }
            //return !empty($error)?$error:true;

            $message = "Base de Datos fue restaurada correctamente\n ".$error;
            //Nuevo para que me acomode las notificaciones
            $this->notifications();
        }
        else
            $message = "Base de Datos no pudo ser restaurada";

        //Redirección:
        return redirect()->back()->with('message', $message);
    }

    public function saveUser(Request $request)
    {
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $name     = $request->input('name');
            $username = $request->input('username');
            $password = $request->input('password');
            $email    = $request->input('email');
            $tipo     = $request->input('tipo');
            //permisos de usuario
            $p_logistica            = $request->input('check-log');
            $p_compra_venta         = $request->input('check-cv');
            $p_finanzas             = $request->input('check-fin');
            $p_clientes_proveedores = $request->input('check-cp');

            $user = new User();
            $user->name     = $name;
            $user->username = $username;
            $user->email    = $email;
            $user->password = Hash::make($password);
            $user->tipo     = $tipo;
            $user->permiso_logistica = $p_logistica ? 1 : 0;
            $user->permiso_compra    = $p_compra_venta ? 1 : 0;
            $user->permiso_venta     = $p_compra_venta ? 1 : 0;
            $user->permiso_finanzas  = $p_finanzas ? 1 : 0;
            $user->permiso_cliente   = $p_clientes_proveedores ? 1 : 0;
            $user->permiso_proveedor = $p_clientes_proveedores ? 1 : 0;
            $user->save();

            //Grabamos ahora el reporte de la adicion del usuario
            //Conseguimos el usuario
            $usuario = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $usuario->id;
            $report->name        = $usuario->name;
            $report->activity    = "Configuracion";
            $report->description = "Usuario Añadido - Id de Usuario (".$user->id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();
            
            DB::commit();
            return redirect()->route('list-users')->with('message', 'Usuario añadido exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Configuración";
            $report->description = "Error al añadir usuario - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::rollback();
            return redirect()->route('list-users')->with('status', 'Error al Almacenar el Usuario');
        }
    }

    public function saveWorker(Request $request)
    {
        DB::beginTransaction();
        try{
            $validate = $this->validate($request, [
                'cedula' => 'required|string|max:255|unique:trabajador',
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'telefono' => 'required|string|max:255',
                'tipo' => 'required',
                'banco' => 'required',
                'num_cuenta' => 'required|string|max:255|unique:trabajador',
            ]);

            $cedula     = $request->input('cedula');
            $nombre     = $request->input('nombre');
            $apellido   = $request->input('apellido');
            $telefono   = $request->input('telefono');
            $tipo       = $request->input('tipo');
            $banco      = $request->input('banco');
            $num_cuenta = $request->input('num_cuenta');

            $worker = new Trabajador();
            $worker->cedula     = $cedula;
            $worker->nombre     = $nombre;
            $worker->apellido   = $apellido;
            $worker->telefono   = $telefono;
            $worker->tipo       = $tipo;
            $worker->banco      = $banco;
            $worker->num_cuenta = $num_cuenta;
            $worker->save();

            //Grabamos ahora el reporte de la adicion del usuario
            //Conseguimos el usuario
            $usuario = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $usuario->id;
            $report->name        = $usuario->name;
            $report->activity    = "Configuracion";
            $report->description = "Trabajador Añadido - Cedula (".$cedula.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();
            
            DB::commit();
            return redirect()->route('list-workers')->with('message', 'Trabajador añadido exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Configuración";
            $report->description = "Error al añadir trabajador - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::rollback();
            return redirect()->route('list-workers')->with('status', 'Error al añadir Trabajador');
        }
    }

    public function showPerfil()
    {
        $id = \Auth::user()->id;

        //Número de Reportes del Sistema
        if(\Auth::user()->tipo != "admin")
            $n_sistemas = Incidencia::where('id_user',$id)->get();
        else
            $n_sistemas = Incidencia::all();
        
        return view('profile.perfil', [
            'n_sistemas' => $n_sistemas,
        ]);
    }

    public function showUsers($registros = 10, $tipo = null, $name = null, $username = null, $order = 'id')
    {
        $user = \Auth::user();
        //valido que si llega un operador lo hecho para atras
        if($user->tipo == "operador")
            return redirect()->route('home');

        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="username" && $order!="name" && $order!="tipo")
            $order = "id";

        if($tipo && $name && $username){
            if($user->tipo == "admin"){//FILTRO CON TODOS LOS USUARIOS
                $usuarios = User::where('tipo', 'like', $tipo == "todos" ? "%%" : $tipo)->
                                    where('name', 'like', $name == "todos" ? "%%" : "%".$name."%")->
                                    where('username', 'like', $username == "todos" ? "%%" : "%".$username."%")->
                                    where('tipo','!=','admin')->orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON SOLO USUARIOS ESTILO OPERADOR
                $usuarios = User::where('tipo', 'like', $tipo == "todos" ? "%%" : $tipo)->
                                    where('name', 'like', $name == "todos" ? "%%" : "%".$name."%")->
                                    where('username', 'like', $username == "todos" ? "%%" : "%".$username."%")->
                                    where('tipo','operador')->orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            if($user->tipo == "admin")
                $usuarios = User::where('tipo','!=','admin')->orderBy($order, 'desc')->paginate($registros);
            else
                $usuarios = User::where('tipo','operador')->orderBy($order, 'desc')->paginate($registros);
        }

        return view('profile.usuarios.list', [
            'usuarios' => $usuarios,
            'registros' => $registros,
            'order' => $order,
            'tipo' => $tipo,
            'name' => $name,
            'username' => $username,
        ]);
    }

    public function showWorkers($registros = 10, $tipo = null, $nombre = null, $banco = null, $order = 'id')
    {
        $user = \Auth::user();
        //valido que si llega un operador lo hecho para atras
        if($user->tipo == "operador")
            return redirect()->route('home');

        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="nombre" && $order!="telefono" && $order!="tipo" && $order!="banco" && $order!="num_cuenta")
            $order = "id";

        if($tipo && $nombre && $banco){
            $trabajadores = Trabajador::where('tipo', 'like', $tipo == "todos" ? "%%" : $tipo)->
                                    where('nombre', 'like', $nombre == "todos" ? "%%" : "%".$nombre."%")->
                                    where('banco', 'like', $banco == "todos" ? "%%" : $banco)->
                                    orderBy($order, 'desc')->paginate($registros);
        }
        else{
            $trabajadores = Trabajador::orderBy($order, 'desc')->paginate($registros);
        }

        return view('profile.empleados.list', [
            'trabajadores' => $trabajadores,
            'registros' => $registros,
            'order' => $order,
            'tipo' => $tipo,
            'nombre' => $nombre,
            'banco' => $banco,
        ]);
    }

    public function showReports($registros = 15, $nombre = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="url" && $order!="name" && $order!="tipo" && $order!="created_at")
            $order = "id";

        if($tiempo && $nombre){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $reportes = Reporte::where('name', 'like', $nombre == "todos" ? "%%" : "%".$nombre."%")->
                                    whereBetween('created_at', [$fecha_1, $fecha_2])->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $reportes = Reporte::where('name', 'like', $nombre == "todos" ? "%%" : "%".$nombre."%")->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $reportes = Reporte::orderBy($order, 'desc')->paginate($registros);
        }

        return view('profile.report_general', [
            'reportes' => $reportes,
            'registros' => $registros,
            'order' => $order,
            'nombre' => $nombre,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
        ]);
    }

    public function showSystem($registros = 15, $nombre = null, $tiempo = null, $fecha_1 = null, $fecha_2 = null, $order = 'id')
    {
        $user = \Auth::user();
        //valido que si llega un operador lo hecho para atras
        if($user->tipo == "operador")
            return redirect()->route('home');

        //compruebo que el orden no sea distintas a las opciones que puede tomar sino, le impongo que sea ID
        if($order!="id" && $order!="activity" && $order!="description" && $order!="created_at" && $order!="id_user")
            $order = "id";

        if($tiempo && $nombre){
            if($tiempo!="todos"){//FILTRO CON TODO LO QUE MANDEN MAS FECHA
                $reportes = Incidencia::where('name', 'like', $nombre == "todos" ? "%%" : "%".$nombre."%")->
                                    whereBetween('created_at', [$fecha_1, $fecha_2])->
                                    orderBy($order, 'desc')->paginate($registros);
            }
            else{//FILTRO CON TODO LO QUE MANDEN MENOS FECHA
                $reportes = Incidencia::where('name', 'like', $nombre == "todos" ? "%%" : "%".$nombre."%")->
                                    orderBy($order, 'desc')->paginate($registros);
            }
        }
        else{
            $reportes = Incidencia::orderBy($order, 'desc')->paginate($registros);
        }

        return view('profile.report_system', [
            'reportes' => $reportes,
            'registros' => $registros,
            'order' => $order,
            'nombre' => $nombre,
            'tiempo' => $tiempo,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
        ]);
    }

    public function showBackups()
    {
        $disk = Storage::disk('backups');

        $files = $disk->files('Laravel');
        $backups = [];
        
        // make an array of backup files, with their filesize and creation date
        foreach ($files as $f) {
            // only take the zip files into account
            if (substr($f, -4) == '.zip' && $disk->exists($f)) {
                $backups[] = [
                    'file_path' => $f,
                    'file_name' => str_replace('Laravel' . '/', '', $f),
                    'file_size' => $this->humanFilesize($disk->size($f)),
                    'last_modified' => $disk->lastModified($f),
                    'age' => $this->LongTimeFilter($disk->lastModified($f)),
                ];
            }
        }
        // reverse the backups, so the newest one would be on top
        $backups = array_reverse($backups);

        return view('profile.backups')->with(compact('backups'));
    }

    public function detailUser($id = null)
    {
        $user = \Auth::user();
        //valido que si llega un operador lo hecho para atras
        if($user->tipo == "operador")
            return redirect()->route('home');
        
        if($id==1)
            return redirect()->route('home');

        //$id va ser el id del usuario
        $user = User::find($id);

        //Número de Reportes del Sistema
        $n_sistemas = Incidencia::where('id_user',$id)->get();

        //$id representa al 
        if(empty($id) || empty($user))
        return redirect()->route('list-users');

        return view('profile.usuarios.detail', [
            'user' => $user,
            'modificados' => $n_sistemas,
        ]);
    }

    public function detailWorker($id = null)
    {
        $user = \Auth::user();
        //valido que si llega un operador lo hecho para atras
        if($user->tipo == "operador")
            return redirect()->route('home');

        //$id va ser el id del trabajador que deseo
        $worker = Trabajador::find($id);

        //Pagos del años actual del empleado
        //$nomina = Pago_Nomina::where('id_trabajador', $id)->whereYear('mes', date("Y"))->get();

        //recojo los eventos donde aparece el cliente
        $eventos = Calendario::where('trabajador_id',$id)->orderBy('start', 'asc')->get();

        //$id va ser el id del proveedor
        if(empty($id) || empty($worker))
        return redirect()->route('list-workers');

        return view('profile.empleados.detail', [
            'worker' => $worker,
            //'nomina' => $nomina,
            'eventos' => $eventos,
        ]);
    }
    
    public function updatePerfil(Request $request)
    {
        DB::beginTransaction();
        try{
            //id del usuario
            $user = \Auth::user();
            $id = $request->input('id');
            
            $validate = $this->validate($request, [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username,'.$id,
                'email' => 'required|string|email|max:255|unique:users,email,'.$id,
                'password' => 'required|string|max:255|unique:users,password,'.$id,
            ]);
            
            $name     = $request->input('name');
            $username = $request->input('username');
            $email    = $request->input('email');
            $password = $request->input('password');
            $password = $user->password == $password ? $password : Hash::make($password);
            
            //Asignar nuestro valores nuevos al objeto de usuario (actualizarlos)
            $user->name     = $name;
            $user->username = $username;
            $user->email    = $email;
            $user->password = $password; 

            //Actualizar los datos del usuario en la Base de Datos
            $user->update();

            DB::commit();
            return redirect()->route('perfil')->with('message', 'Perfil Editado Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            DB::rollback();
            return redirect()->route('perfil')->with('status', 'Error al Editar la información');
        }
    }

    public function updateUser(Request $request)
    {
        DB::beginTransaction();
        try{
            //id del usuario
            $id = $request->input('id');
            
            $validate = $this->validate($request, [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username,'.$id,
                'password' => 'required|string|max:255|unique:users,password,'.$id,
                'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            ]);
            
            $name     = $request->input('name');
            $username = $request->input('username');
            $password = $request->input('password');
            $email    = $request->input('email');
            $tipo     = $request->input('tipo');
            //permisos de usuario
            $p_logistica            = $request->input('check-log');
            $p_compra_venta         = $request->input('check-cv');
            $p_finanzas             = $request->input('check-fin');
            $p_clientes_proveedores = $request->input('check-cp');
            
            //Asignar nuestro valores nuevos al objeto de usuario (actualizarlos)
            $user = User::find($id);
            $user->name     = $name;
            $user->username = $username;
            $user->email    = $email;
            $user->password = $user->password == $password ? $password : Hash::make($password);
            $user->tipo     = $tipo;
            $user->permiso_logistica = $p_logistica ? 1 : 0;
            $user->permiso_compra    = $p_compra_venta ? 1 : 0;
            $user->permiso_venta     = $p_compra_venta ? 1 : 0;
            $user->permiso_finanzas  = $p_finanzas ? 1 : 0;
            $user->permiso_cliente   = $p_clientes_proveedores ? 1 : 0;
            $user->permiso_proveedor = $p_clientes_proveedores ? 1 : 0;

            //Actualizar los datos del usuario en la Base de Datos
            $user->update();

            //Grabamos ahora el reporte de la edicion del usuario
            //Conseguimos el usuario
            $usuario = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $usuario->id;
            $report->name        = $usuario->name;
            $report->activity    = "Configuración";
            $report->description = "Usuario Editado - ID del Usuario (".$user->id.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-user', ['id' => $id])->with('message', 'Usuario Editado Exitosamente');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Configuración";
            $report->description = "Error al editar usuario - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::rollback();
            return redirect()->route('list-user')->with('status', 'Error al Editar la información');
        }
    }

    public function updateWorker(Request $request)
    {
        DB::beginTransaction();
        try{
            //id del trabajador
            $id = $request->input('id');
            
            $validate = $this->validate($request, [
                'cedula' => 'required|string|max:255|unique:trabajador,cedula,'.$id,
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'telefono' => 'required|string|max:255',
                'tipo' => 'required',
                'banco' => 'required',
                'num_cuenta' => 'required|string|max:255|unique:trabajador,num_cuenta,'.$id,
            ]);

            $cedula     = $request->input('cedula');
            $nombre     = $request->input('nombre');
            $apellido   = $request->input('apellido');
            $telefono   = $request->input('telefono');
            $tipo       = $request->input('tipo');
            $banco      = $request->input('banco');
            $num_cuenta = $request->input('num_cuenta');

            $worker = Trabajador::find($id);
            $worker->cedula     = $cedula;
            $worker->nombre     = $nombre;
            $worker->apellido   = $apellido;
            $worker->telefono   = $telefono;
            $worker->tipo       = $tipo;
            $worker->banco      = $banco;
            $worker->num_cuenta = $num_cuenta;
            
            //Actualizar los datos del empleado en la Base de Datos
            $worker->update();

            //Grabamos ahora el reporte de la edicion del empleado
            //Conseguimos el usuario
            $usuario = \Auth::user();

            //Asignar los valores al nuevo objeto de reporte
            $report = new Incidencia();
            $report->id_user     = $usuario->id;
            $report->name        = $usuario->name;
            $report->activity    = "Configuración";
            $report->description = "Trabajador Editado - Cédula (".$cedula.")";

            //Grabamos el reporte de almacenamiento en el sistema
            $report->save();

            DB::commit();
            return redirect()->route('detail-worker', ['id' => $id])->with('message', 'Trabajador Editado Exitosamente!');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error del Sistema";
            $report->activity    = "Configuración";
            $report->description = "Error al editar trabajador - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::rollback();
            return redirect()->route('list-workers')->with('status', 'Error al Editar la información');
        }
    }

    public function deleteUser(Request $request){
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $user = User::find($valor);
                $incidencia = Incidencia::where('id_user',$valor)->get();
                $reporte = Reporte::where('id_user',$valor)->get();
                
                //Pasar id_user a null
                if($incidencia && count($incidencia)>=1){
                    foreach ($incidencia as $row) {
                        $row->id_user = null;
                        $row->update();
                    }
                }

                //Pasar id_user a null
                if($reporte && count($reporte)>=1){
                    foreach ($reporte as $row) {
                        $row->id_user = null;
                        $row->update();
                    }
                }

                $codigo = $user->id;
                //Elimino Usuario
                $user->delete();

                array_push($valores, $valor);
                
                //GRABAMOS EL REPORTE DE ELIMINADO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $user_id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user = $user_id;
                $report->name    = $user->name;
                $report->activity    = "Configuración";
                $report->description = "Usuario Eliminado - ID (".$codigo.")";

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

    public function deleteWorker(Request $request){
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $worker = Trabajador::find($valor);
                $despacho = Despacho::where('id_trabajador',$valor)->get();
                $agenda = Calendario::where('trabajador_id',$valor)->get();
                //$pago_nomina = Pago_Nomina::where('id_trabajador',$valor)->get();

                //Eliminar agenda de empleado
                if($agenda && count($agenda)>=1){
                    foreach ($agenda as $row) {
                        //Elimino agenda persona
                        $row->delete();
                    }
                }

                //Eliminar Despachos Asociados al trabajador
                if($despacho && count($despacho)>=1){
                    foreach ($despacho as $row) {
                        $row->id_trabajador = null;
                        $row->update();
                    }
                }

                //Eliminar Pagos de Nómina
                /* if($pago_nomina && count($pago_nomina)>=1){
                    foreach ($pago_nomina as $row) {
                        //AQUI DEBO DECIDIR SI ELIMINAR EL PAGO O SOLO LE PONGO QUE EL EMPLEADO YA NO EXISTE
                        //Elimino el egreso que tiene asociado el pago de nomina
                        $pago_egreso = Egreso::where('id_pago_nomina',$row->id)->first();
                        $pago_egreso->delete();
                        //Elimino pago de nomina de la persona
                        $row->delete();
                    }
                } */

                $codigo = $worker->id;
                //Elimino trabajador
                $worker->delete();

                array_push($valores, $valor);
                
                //GRABAMOS EL REPORTE DE ELIMINADO
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $user_id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user = $user_id;
                $report->name    = $user->name;
                $report->activity    = "Configuración";
                $report->description = "Trabajador Eliminado - Código (".$codigo.")";

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

    public function deleteReport(Request $request){
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $report = Reporte::find($valor);
                //Elimino reporte
                $report->delete();

                array_push($valores, $valor);
            }

            DB::commit();
            return response()->json($valores); 
        }
        catch(\Illuminate\Database\QueryException $e){
            DB::rollback();
            return response()->json("Error");       
        }
    }

    public function deleteIncidencia(Request $request){
        $id = json_decode($request->input('values'));
        $valores = array();
        DB::beginTransaction();
        try{
            foreach ($id as &$valor) {
                $report = Incidencia::find($valor);
                //Elimino incidencia
                $report->delete();

                array_push($valores, $valor);
            }

            DB::commit();
            return response()->json($valores); 
        }
        catch(\Illuminate\Database\QueryException $e){
            DB::rollback();
            return response()->json("Error");       
        }
    }

    public function deleteBackup($file_name)
    {
        $disk = Storage::disk('backups');
        if ($disk->exists('Laravel' . '/' . $file_name)) {
            $disk->delete('Laravel' . '/' . $file_name);
            return redirect()->back();
        } else {
            abort(404, "La copia de seguridad no existe.");
        }
    }

    public function downloadBackup($file_name)
    {
        $file = 'Laravel' . '/' . $file_name;
        $disk = Storage::disk('backups');
        if ($disk->exists($file)) {
            $fs = Storage::disk('backups')->getDriver();
            $stream = $fs->readStream($file);

            return FacadeResponse::stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                "Content-Type" => $fs->getMimetype($file),
                "Content-Length" => $fs->getSize($file),
                "Content-disposition" => "attachment; filename=\"" . basename($file) . "\"",
            ]);
        } else {
            abort(404, "La copia de seguridad no existe.");
        }
    }

    public function downloadUsuario($tipo = null, $name = null, $username = null)
    {    
        $user = \Auth::user();
        //valido que si llega un operador lo hecho para atras
        if($user->tipo == "operador")
            return redirect()->route('home');

        if($tipo && $name && $username){
            $filtro = "";
            if($user->tipo == "admin"){//FILTRO CON TODOS LOS USUARIOS
                $usuarios = User::where('tipo', 'like', $tipo == "todos" ? "%%" : $tipo)->
                                    where('name', 'like', $name == "todos" ? "%%" : "%".$name."%")->
                                    where('username', 'like', $username == "todos" ? "%%" : "%".$username."%")->
                                    where('tipo','!=','admin')->get();
            }
            else{//FILTRO CON SOLO USUARIOS ESTILO OPERADOR
                $usuarios = User::where('tipo', 'like', $tipo == "todos" ? "%%" : $tipo)->
                                    where('name', 'like', $name == "todos" ? "%%" : "%".$name."%")->
                                    where('username', 'like', $username == "todos" ? "%%" : "%".$username."%")->
                                    where('tipo','operador')->get();
            }
            $tipo != "todos" ? $filtro .= "Tipo de Usuario: ".$tipo : "";
            $name != "todos" ? $filtro .= " | Nombre del Usuario: ".$name : "";
            $username != "todos" ? $filtro .= " | Username: ".$username : "";
        }
        else{
            if($user->tipo == "admin")
                $usuarios = User::where('tipo','!=','admin')->get();
            else
                $usuarios = User::where('tipo','operador')->get();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los usuarios" : "";
        $datos = array();
        $titulos = array('Código', 'Nombre de Usuario', 'Nombre de la Persona', 'Tipo de Usuario');

        foreach ($usuarios as $usuario) {
            $data_content["dato-1"] = $usuario->id;
            $data_content["dato-2"] = $usuario->username;
            $data_content["dato-3"] = $usuario->name;
            $data_content["dato-4"] = $usuario->tipo == "administrador" ? "admin secundario" : $usuario->tipo;
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Usuarios.pdf", "Usuarios del Sistema", $filtro, $datos, $titulos);
    }

    public function downloadTrabajador($tipo = null, $nombre = null, $banco = null)
    {    
        $user = \Auth::user();
        //valido que si llega un operador lo hecho para atras
        if($user->tipo == "operador")
            return redirect()->route('home');

        if($tipo && $nombre && $banco){
            $filtro = "";
            $trabajadores = Trabajador::where('tipo', 'like', $tipo == "todos" ? "%%" : $tipo)->
                                    where('nombre', 'like', $nombre == "todos" ? "%%" : "%".$nombre."%")->
                                    where('banco', 'like', $banco == "todos" ? "%%" : $banco)->get();
            
            $tipo != "todos" ? $filtro .= "Tipo de Trabajador: ".$tipo : "";
            $nombre != "todos" ? $filtro .= " | Nombre del Trabajador: ".$nombre : "";
        }
        else{
            $trabajadores = Trabajador::all();
            $filtro = "";
        }

        $filtro == "" ? $filtro = "Todos los trabajadores" : "";
        $datos = array();
        $titulos = array('Nombre', 'Cédula', 'Teléfono', 'Tipo de Empleado', 'Banco', 'Nro. de Cuenta');

        foreach ($trabajadores as $worker) {
            $data_content["dato-1"] = $worker->nombre." ".$worker->apellido;
            $data_content["dato-2"] = $worker->cedula;
            $data_content["dato-3"] = $worker->telefono;
            $data_content["dato-4"] = $worker->tipo;
            $data_content["dato-5"] = $worker->banco;
            $data_content["dato-6"] = $worker->num_cuenta;
            array_push($datos,$data_content);
        }

        return $this->download("Reporte_Trabajadores.pdf", "Trabajadores de la Empresa", $filtro, $datos, $titulos);
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

    public function humanFilesize($size, $precision = 2) {
        $units = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $step = 1024;
        $i = 0;
    
        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }
        
        return round($size, $precision).$units[$i];
    }

    public static function LongTimeFilter($date) {
        if ($date == null) {
            return "Sin fecha";
        }
 
        $start_date = new \DateTime(date("d-m-Y H:i:s", $date));
        $since_start = $start_date->diff(new \DateTime(date("Y-m-d") . " " . date("H:i:s")));
 
        if ($since_start->y == 0) {
            if ($since_start->m == 0) {
                if ($since_start->d == 0) {
                    if ($since_start->h == 0) {
                        if ($since_start->i == 0) {
                            if ($since_start->s == 0) {
                                $result = $since_start->s . ' segundos';
                            } else {
                                if ($since_start->s == 1) {
                                    $result = $since_start->s . ' segundo';
                                } else {
                                    $result = $since_start->s . ' segundos';
                                }
                            }
                        } else {
                            if ($since_start->i == 1) {
                                $result = $since_start->i . ' minuto';
                            } else {
                                $result = $since_start->i . ' minutos';
                            }
                        }
                    } else {
                        if ($since_start->h == 1) {
                            $result = $since_start->h . ' hora';
                        } else {
                            $result = $since_start->h . ' horas';
                        }
                    }
                } else {
                    if ($since_start->d == 1) {
                        $result = $since_start->d . ' día';
                    } else {
                        $result = $since_start->d . ' días';
                    }
                }
            } else {
                if ($since_start->m == 1) {
                    $result = $since_start->m . ' mes';
                } else {
                    $result = $since_start->m . ' meses';
                }
            }
        } else {
            if ($since_start->y == 1) {
                $result = $since_start->y . ' año';
            } else {
                $result = $since_start->y . ' años';
            }
        }
 
        return "Hace " . $result;
    }

    //Nuevo para que acomode las notificaciones
    public function notifications()
    {
        $total = 0;

        if(Auth::user()->permiso_venta){
            //Acomodamos Cuentas por Cobrar
            $ventas = Venta::where('pendiente',0)->get();
            $expirar = 0; $caducar = 0;
            foreach ($ventas as $sell) {
                if( strtotime($sell->fecha."+ ".$sell->credito." days") - strtotime(date("d-m-Y")) <= 3*86400){
                    if( strtotime($sell->fecha."+ ".$sell->credito." days") - strtotime(date("d-m-Y")) > 0*86400){
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
        }

        if(Auth::user()->permiso_logistica){
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

            //Acomodamos Suministro por expirar
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

        session()->put('notificaciones', $total);
    }
}
