<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Calendario;
use App\Cliente;
use App\Proveedor;
use App\Trabajador;
use App\Incidencia;


class CalendarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $clients = Cliente::all();
        $providers = Proveedor::all();
        $employers = Trabajador::all();

        return view('Calendar.index',[
            'clients' => $clients,
            'providers' => $providers,
            'employers' => $employers,
        ]);
    }

    public function store()
    {
        //
        DB::beginTransaction();
        try{
            $datosEvento=request()->except(['_token','_method']);
            Calendario::insert($datosEvento);
            //print_r($datosEvento['descripcion']);

            //Grabamos ahora el reporte de la creación del proveedor
                //Conseguimos el id del usuario
                $user = \Auth::user();
                $id   = $user->id;

                //Asignar los valores al nuevo objeto de reporte
                $report = new Incidencia();
                $report->id_user     = $id;
                $report->name        = $user->name;
                $report->activity    = "Módulo Calendario";
                $report->description = "Nuevo Evento añadido (".$datosEvento['title'].")";

                //Grabamos el reporte de almacenamiento en el sistema
                $report->save();
                
                DB::commit();
                //return redirect()->route('calendario')->with('message', 'Evento Añadido correctamente');
        }catch (\Illuminate\Database\QueryException $e){
            //GRABAR ERRORES EN LA BD Y SU CORRESPONDIENTE MENSAJE
            //Asignar los valores al nuevo objeto de reporte
            DB::rollback();
            $report = new Incidencia();
            $report->id_user     = null;
            $report->name        = "Error en el Sistema";
            $report->activity    = "Módulo Calendario";
            $report->description = "Error al almacenar evento en el calendario - Código SQL [".$e->getCode()."]";

            //Grabamos el reporte de error en el sistema
            $report->save();

            DB::commit();
            //return redirect()->route('calendario')->with('status', 'Error al Almacenar la información');
        }
    }

    public function show()
    {
        //
        $data['eventos']=Calendario::all();
        return response()->json($data['eventos']);
    }

    public function update(Request $request, $id)
    {
        //
        $datosEvento=request()->except(['_token','_method']);
        $data = Calendario::where('id', '=', $id)->update($datosEvento);
        //return response()->json($data);
    }

    public function destroy($id)
    {
        //
        $calendar = Calendario::findOrFail($id);
        Calendario::destroy($id);
        return response()->json($id);
    }
}
