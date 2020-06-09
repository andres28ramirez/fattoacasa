@extends('layouts.principal')

@section('title','Usuarios · Fatto a Casa')

@section('titulo','FATTO A CASA - USUARIOS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('perfil')}}">Perfil</a>
        </li>
    @if(Auth::user()->tipo != "operador")
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-users') }}">Usuarios</a>
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
            <a class="nav-link text-secondary" href="{{ route('list-backups') }}">Restauración (Back-Ups)</a>
        </li>
    @endif
    </ul>
@endsection

@section('info')

    <!-- SCRIPT PARA REDIRECCIÓN BOTONES DE LA TABLA -->
    <script>
        function redirect_table(e){
            switch (e) {
                case "agregar":
                    location.href = "{{ route('agg-user') }}";
                    break;
                case "filtrar":
                    $('#table-filter').modal(true);
                    //CAPTURAR EVENTO SUBMIT DE FILTRAR INFORMACIÓN
                    $("#submit-form-list-user").unbind('click').click(function(event){
                        $("#form-list-user").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var username = $('#form-list-user input[id="form-user"]').val(); if(!username) username = "todos";
                            var tipo = $('#form-list-user select[id="form-tipo"] option:selected').val();
                            var name = $('#form-list-user input[id="form-name"]').val(); if(!name) name = "todos";

                            var cantidad = "{{$registros}}";
                            var registro = "{{ route('list-users') }}";
                            var orden = "{{$order}}";
                            
                            var ruta = registro+"/"+cantidad+"/"+tipo+"/"+name+"/"+username+"/"+orden;
                            
                            if(username && tipo && name)
                                location.href = ruta;
                            
                            return false;
                        });
                    });
                    break;
                case "eliminar":
                    // aqui reviso los campos que estan en check y tomo su ID
                    var table = "lista-usuarios";
                    var url = "{{ route('delete-user') }}";
                    var report_url = "{{ route('report-error') }}";
                    var usuarios = new Array();
                    $("#check-lista-usuarios .check-data").each(function( index ) {
                        if ($(this).prop('checked') == true){
                            usuarios.push($(this).val());
                        }
                    });

                    if(usuarios.length > 0){
                        swal({
                            title: "Eliminar registros",
                            text: "¿Esta seguro de eliminar los usuarios seleccionaos?",
                            icon: "warning",
                            buttons: ["Cancelar","Aceptar"],
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                ajaxDelete(usuarios,url,table,report_url);
                            }
                        });
                    }
                    break;
                case "registros":
                    var username = "{{ $username }}"; if(!username) username = "todos";
                    var tipo = "{{ $tipo }}"; if(!tipo) tipo = "todos";
                    var name = "{{ $name }}"; if(!name) name = "todos";

                    var cantidad = $('select[id="num-register-lista-usuarios"] option:selected').val();
                    var registro = "{{ route('list-users') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+tipo+"/"+name+"/"+username+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('list-users') }}";
                    location.href = registro;
                    break;
                case "print":
                    var username = "{{ $username }}"; if(!username) username = "todos";
                    var tipo = "{{ $tipo }}"; if(!tipo) tipo = "todos";
                    var name = "{{ $name }}"; if(!name) name = "todos";

                    var registro = "{{ route('pdf-list-users') }}";
                    var ruta = registro+"/"+tipo+"/"+name+"/"+username;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var username = "{{ $username }}"; if(!username) username = "todos";
                    var tipo = "{{ $tipo }}"; if(!tipo) tipo = "todos";
                    var name = "{{ $name }}"; if(!name) name = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('list-users') }}";
                    var orden = e;

                    var ruta = registro+"/"+cantidad+"/"+tipo+"/"+name+"/"+username+"/"+orden;
                    location.href = ruta;
                    break;
            }
        }
    </script>

    <div class="row justify-content-center my-3 px-2">
        @if(session('message'))
            <div class="col-12">
                <h3 class="text-center alert alert-success">{{ session('message') }}</h3>
            </div>
        @endif

        @if(session('status'))
            <div class="col-12">
                <h3 class="text-center alert alert-danger">{{ session('status') }}</h3>
            </div>
        @endif

        @php
            if($tipo)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(
                "table-id" => "lista-usuarios",
                "title" => "Presiona sobre una fila para editar o ver al detalle el usuario",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "ID - Usuario",
                        "bd-name" => "id",
                    ),
                    array(
                        "nombre" => "Nombre de Usuario",
                        "bd-name" => "username",
                    ),
                    array(
                        "nombre" => "Nombre de la Persona",
                        "bd-name" => "name",
                    ),
                    array(
                        "nombre" => "Correo",
                        "bd-name" => "email",
                    ),
                    array(
                        "nombre" => "Tipo de Usuario",
                        "bd-name" => "tipo",
                    ),
                ),
                "content" => array(),
            );

            $data_content = array(
                "id" => 1,
                "dato-1" => "1",
                "dato-2" => "andres28ramirez",
                "dato-3" => "Andres Ramirez",
                "dato-4" => "Administrador",
            );

            foreach ($usuarios as $usuario) {
                $data_content["id"] = $usuario->id;
                $data_content["dato-1"] = $usuario->id;
                $data_content["dato-2"] = $usuario->username;
                $data_content["dato-3"] = $usuario->name;
                $data_content["dato-4"] = $usuario->email;
                $data_content["dato-5"] = $usuario->tipo == "administrador" ? "admin secundario" : $usuario->tipo;

                array_push($data_list["content"],$data_content);
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $usuarios->links() }}
        </nav>
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
                            "form-id" => "form-list-user",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Tipo de Usuario",
                                    "icon" => "fa-leanpub",
                                    "id_name" => "form-tipo",
                                    "form_name" => "tipo",
                                    "title" => "Selecciona un periodo",
                                    "options" => array(
                                        array(
                                            "value" => "todos",
                                            "nombre" => "Cualquier Tipo",
                                        ),
                                        array(
                                            "value" => "admin secundario",
                                            "nombre" => "Administrador",
                                        ),
                                        array(
                                            "value" => "operador",
                                            "nombre" => "Operador",
                                        ),
                                    ),
                                    "validate" => "Periodo es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Username del usuario (Opcional *)",
                                    "icon" => "fa-user",
                                    "type" => "text",
                                    "id_name" => "form-user",
                                    "form_name" => "username",
                                    "placeholder" => "Ingrese el username...",
                                    "validate" => "Nombre de usuario es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Nombre de la Persona (Opcional *)",
                                    "icon" => "fa-users",
                                    "type" => "text",
                                    "id_name" => "form-name",
                                    "form_name" => "name",
                                    "placeholder" => "Ingrese el nombre...",
                                    "validate" => "Nombre de persona es requerido",
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
        //REDIRECCIONAR AL DETALLADO DEL PROVEEDOR
            $(".tr-lista-usuarios").click(function() {
                var id = $(this).parent().attr("id");
                var url = "{{ route('detail-user') }}";
                location.href = url+"/"+id;
            });

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
@endsection