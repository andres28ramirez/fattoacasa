@extends('layouts.principal')

@section('title','Back-ups · Fatto a Casa')

@section('titulo','FATTO A CASA - BACK-UPS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('perfil')}}">Perfil</a>
        </li>
    @if(Auth::user()->tipo != "operador")
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-users') }}">Usuarios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-workers') }}">Empleados</a>
        </li>
    @endif
        <!-- <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-reports') }}">Reportes Generados</a>
        </li> -->
    @if(Auth::user()->tipo != "operador")
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-system') }}">Reportes del Sistema</a>
        </li>
    @endif
    @if(Auth::user()->tipo == "admin")
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-backups') }}">Restauración (Back-Ups)</a>
        </li>
    @endif
    </ul>
@endsection

@section('info')
    @if(session('message'))
        <h3 class="text-center alert alert-success">{{ session('message') }}</h3>
    @endif

    @if(session('status'))
        <h3 class="text-center alert alert-danger">{{ session('status') }}</h3>
    @endif

    <div class="row justify-content-center my-3 px-2">
        <h3>Administrar copias de seguridad de la base de datos</h3>
        <div class="col-12 clearfix">
            <a id="create-new-backup-button" href="{{ route('create-backup') }}" class="btn btn-success pull-right"
            style="margin-bottom:2em;">
                <i class="fa fa-plus"></i> Crear nuevo Backup
            </a>
        </div>
        <div class="col-12">
            @if (count($backups))
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Tamaño</th>
                        <th>Fecha</th>
                        <th>Edad</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($backups as $backup)
                        <tr>
                            <td>{{ $backup['file_name'] }}</td>
                            <td>{{ $backup['file_size'] }}</td>
                            @php 
                                setlocale(LC_TIME, 'spanish');
                                echo "<td>".strftime("%A, %d de %B de %Y",$backup['last_modified'])."</td>";
                            @endphp
                            <td>
                                {{ $backup['age'] }}
                            </td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-secondary download-btn"
                                    href="{{ route('download-backup', ['file_name' => $backup['file_name']]) }}">
                                    <i class="fa fa-cloud-download"></i> 
                                    Descargar
                                </a>
                                <a class="btn btn-sm btn-danger delete-btn" data-button-type="delete"
                                    href="{{ route('delete-backup', ['file_name' => $backup['file_name']]) }}">
                                    <i class="fa fa-trash-o"></i>
                                    Borrar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="well">
                    <h5>No existen copias de seguridad en el sistema</h5>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL PARA FILTRAR LA TABLA -->
    <div class="modal fade" id="table-filter" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="titulo">Filtrar Tabla</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php
                        $data_form = array(
                            "action" => "",
                            "title" => "",
                            "form-id" => "form-list-report",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Incidencia Usuario o Sistema (Opcional*)",
                                    "icon" => "fa-list-alt",
                                    "type" => "text",
                                    "id_name" => "form-name",
                                    "form_name" => "form-name",
                                    "placeholder" => "Ingrese el nombre o la porción del mismo que desea",
                                    "validate" => "Código es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Tiempo a Evaluar",
                                    "icon" => "fa-hourglass-half",
                                    "id_name" => "form-tiempo",
                                    "form_name" => "form-tiempo",
                                    "title" => "Selecciona un tiempo",
                                    "options" => array(
                                        array(
                                            "value" => "Específico",
                                            "nombre" => "Específico",
                                        ),
                                        array(
                                            "value" => "todos",
                                            "nombre" => "Cualquier Fecha",
                                        ),
                                    ),
                                    "validate" => "Tiempo a evaluar es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Primera fecha",
                                    "icon" => "fa-calendar",
                                    "type" => "date",
                                    "id_name" => "form-fecha-1",
                                    "form_name" => "form-fecha-1",
                                    "placeholder" => "",
                                    "validate" => "Primera fecha es requerida",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Segunda fecha",
                                    "icon" => "fa-calendar",
                                    "type" => "date",
                                    "id_name" => "form-fecha-2",
                                    "form_name" => "form-fecha-2",
                                    "placeholder" => "",
                                    "validate" => "Segunda fecha es requerida",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                            ),
                        );
                    @endphp
                    @include('includes.general_form',['data'=>$data_form])
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script>
        //ELIMINAR LOS BOTONES DE AGREGAR-ELIMINAR-FILTRAR-DESCARGAR
            $("#add-lista-reportes").remove();
            $("#print-lista-reportes").remove();

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");

        //EVENTOS QUE ABREN EL MODAL QUE MUESTRA CARGANDO
            $("#create-new-backup-button").click(function() {
                swal({
                    title: "Creando copia de seguridad, porfavor espere...",
                    closeOnClickOutside: false,
                    button: 'Aceptar',
                });
                $(".swal-title").prepend('<i class="fa fa-3x fa-lg fa-spinner fa-spin d-block p-4" style="color: #028936"></i>');
                $(".swal-button").addClass("bg-secondary");
            });

            $(".delete-btn").click(function() {
                swal({
                    title: "Eliminando copia de seguridad, porfavor espere...",
                    closeOnClickOutside: false,
                    button: 'Aceptar',
                });
                $(".swal-title").prepend('<i class="fa fa-3x fa-lg fa-spinner fa-spin d-block p-4" style="color: #028936"></i>');
                $(".swal-button").addClass("bg-secondary");
            });

            $(".download-btn").click(function() {
                swal({
                    title: "Descargando copia de seguridad, porfavor espere...",
                    closeOnClickOutside: false,
                    button: 'Aceptar',
                });
                $(".swal-title").prepend('<i class="fa fa-3x fa-lg fa-spinner fa-spin d-block p-4" style="color: #028936"></i>');
                $(".swal-button").addClass("bg-secondary");
            });
            
    </script>
@endsection