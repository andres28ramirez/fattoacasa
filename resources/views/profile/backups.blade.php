@extends('layouts.principal')

@section('title','Respaldos · Fatto a Casa')

@section('titulo','FATTO A CASA - RESPALDOS')

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
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-backups') }}">Restauración y Respaldo</a>
        </li>
    @endif
    </ul>
@endsection

@section('info')
    <style>
        @media (max-width: 1260px) {  
            .delete-btn{
                margin-top: 5px;
            }
        }

        @media (max-width: 1100px) {  
            .delete-btn{
                margin-top: 0px;
            }
        }

        @media (max-width: 910px) {  
            .delete-btn{
                margin-top: 5px;
            }
        }

        @media (max-width: 500px) {  
            .resize-text{
                font-size: 20px;
            }

            .resize-text-btn{
                font-size: 10px;
            }
        }
    </style>

    @if(session('message'))
        <h3 class="text-center alert alert-success">{{ session('message') }}</h3>
    @endif

    @if(session('status'))
        <h3 class="text-center alert alert-danger">{{ session('status') }}</h3>
    @endif

    <div class="row justify-content-center my-3 px-2">
        <h3 class="resize-text text-center">Administrar copias de seguridad de la base de datos</h3>
        <div class="col-12 clearfix">
            <a id="create-new-backup-button" href="{{ route('create-backup') }}" class="btn btn-success pull-right resize-text-btn"
            style="margin-bottom:2em;">
                <i class="fa fa-plus"></i> Crear nuevo Respaldo
            </a>
        </div>
        <div class="col-12 clearfix">
            <form method="post" action="{{ route('restore-backup') }}" class="validate-form" id="form-restore" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group text-right row container justify-content-end mr-0 ml-0 p-0" id="form_file">
                        <div class="my-auto"><label class="resize-text-btn">Base de Datos (archivo .sql):</label></div>
                        <div class="input-group validate-input col-md-4 col-sm-6 pr-0" data-validate="Porfavor seleccione un archivo">
                            <input class="form-control input100 req-true resize-text-btn" style="height: calc(2.19rem + 10px)" 
                            type="file" accept=".sql" id="form_file" name="form_file">
                        </div>                    
                    </div>
                </div> 
                <button class="btn btn-secondary resize-text-btn pull-right mb-2" style="margin-top: -10px;" type="submit" id="submit-form-restore">
                    <i class="fa fa-database"></i> Restaurar Base de Datos
                </button> 
            </form>
            <input type="hidden" id="" class="id-form" value="form-restore">
        </div>
        <div class="col-12 table-responsive">
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
                                echo "<td>".strftime("%a, %d de %B de %Y",$backup['last_modified'])."</td>";
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
                                <a class="btn btn-sm btn-danger delete-btn delete-btn" data-button-type="delete"
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

@endsection

@section('scripts')
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script src="{{ asset('js/form_validate.js') }}"></script>
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

            $("#submit-form-restore").click(function() {
                swal({
                    title: "Restaurando copia de seguridad, porfavor espere...",
                    closeOnClickOutside: false,
                    button: 'Aceptar',
                });
                $(".swal-title").prepend('<i class="fa fa-3x fa-lg fa-spinner fa-spin d-block p-4" style="color: #028936"></i>');
                $(".swal-button").addClass("bg-secondary");
            });
            
    </script>
@endsection