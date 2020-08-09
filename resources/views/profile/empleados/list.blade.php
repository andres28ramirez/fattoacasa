@extends('layouts.principal')

@section('title','Empleados · Fatto a Casa')

@section('titulo','FATTO A CASA - EMPLEADOS')

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
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-workers') }}">Empleados</a>
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
            <a class="nav-link text-secondary" href="{{ route('list-backups') }}">Restauración y Respaldo</a>
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
                    location.href = "{{ route('agg-worker') }}";
                    break;
                case "filtrar":
                    $('#table-filter').modal(true);
                    //CAPTURAR EVENTO SUBMIT DE FILTRAR INFORMACIÓN
                    $("#submit-form-list-worker").unbind('click').click(function(event){
                        $("#form-list-worker").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var banco = $('#form-list-worker select[id="form-banco"] option:selected').val(); if(!banco) banco = "todos";
                            var tipo = $('#form-list-worker select[id="form-tipo"] option:selected').val();
                            var nombre = $('#form-list-worker input[id="form-name"]').val(); if(!nombre) nombre = "todos";

                            var cantidad = "{{$registros}}";
                            var registro = "{{ route('list-workers') }}";
                            var orden = "{{$order}}";
                            
                            var ruta = registro+"/"+cantidad+"/"+tipo+"/"+nombre+"/"+banco+"/"+orden;
                            
                            if(nombre && tipo && banco)
                                location.href = ruta;
                            
                            return false;
                        });
                    });
                    break;
                case "eliminar":
                    // aqui reviso los campos que estan en check y tomo su ID
                    var table = "lista-empleados";
                    var url = "{{ route('delete-worker') }}";
                    var report_url = "{{ route('report-error') }}";
                    var trabajadores = new Array();
                    $("#check-lista-empleados .check-data").each(function( index ) {
                        if ($(this).prop('checked') == true){
                            trabajadores.push($(this).val());
                        }
                    });

                    if(trabajadores.length > 0){
                        swal({
                            title: "Eliminar registros",
                            text: "¿Esta seguro de eliminar los trabajadores seleccionaos?",
                            icon: "warning",
                            buttons: ["Cancelar","Aceptar"],
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                ajaxDelete(trabajadores,url,table,report_url);
                            }
                        });
                    }
                    break;
                case "registros":
                    var nombre = "{{ $nombre }}"; if(!nombre) nombre = "todos";
                    var tipo = "{{ $tipo }}"; if(!tipo) tipo = "todos";
                    var banco = "{{ $banco }}"; if(!banco) banco = "todos";

                    var cantidad = $('select[id="num-register-lista-empleados"] option:selected').val();
                    var registro = "{{ route('list-workers') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+tipo+"/"+nombre+"/"+banco+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('list-workers') }}";
                    location.href = registro;
                    break;
                case "print":
                    var nombre = "{{ $nombre }}"; if(!nombre) nombre = "todos";
                    var tipo = "{{ $tipo }}"; if(!tipo) tipo = "todos";
                    var banco = "{{ $banco }}"; if(!banco) banco = "todos";

                    var registro = "{{ route('pdf-list-workers') }}";
                    var ruta = registro+"/"+tipo+"/"+nombre+"/"+banco;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var nombre = "{{ $nombre }}"; if(!nombre) nombre = "todos";
                    var tipo = "{{ $tipo }}"; if(!tipo) tipo = "todos";
                    var banco = "{{ $banco }}"; if(!banco) banco = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('list-workers') }}";
                    var orden = e;

                    var ruta = registro+"/"+cantidad+"/"+tipo+"/"+nombre+"/"+banco+"/"+orden;
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
                "table-id" => "lista-empleados",
                "title" => "Presiona sobre una fila para editar o ver al detalle el empleado",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "Nombre",
                        "bd-name" => "nombre",
                    ),
                    array(
                        "nombre" => "Cédula",
                        "bd-name" => "cedula",
                    ),
                    array(
                        "nombre" => "Teléfono",
                        "bd-name" => "telefono",
                    ),
                    array(
                        "nombre" => "Tipo de Empleado",
                        "bd-name" => "tipo",
                    ),
                    array(
                        "nombre" => "Banco",
                        "bd-name" => "banco",
                    ),
                    array(
                        "nombre" => "Nro. de Cuenta",
                        "bd-name" => "num_cuenta",
                    ),
                ),
                "content" => array(),
            );

            $data_content = array(
                "id" => 1,
                "dato-1" => "Andres",
                "dato-2" => "232323232",
                "dato-3" => "0412-7942183",
                "dato-4" => "Despachador",
                "dato-5" => "BOD",
                "dato-6" => "0116-.....",
            );

            foreach ($trabajadores as $worker) {
                $data_content["id"] = $worker->id;
                $data_content["dato-1"] = $worker->nombre." ".$worker->apellido;
                $data_content["dato-2"] = $worker->cedula;
                $data_content["dato-3"] = $worker->telefono;
                $data_content["dato-4"] = $worker->tipo;
                $data_content["dato-5"] = $worker->banco;
                $data_content["dato-6"] = $worker->num_cuenta;

                array_push($data_list["content"],$data_content);
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $trabajadores->links() }}
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
                            "form-id" => "form-list-worker",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Nombre del Trabajador (Opcional*)",
                                    "icon" => "fa-user",
                                    "type" => "text",
                                    "id_name" => "form-name",
                                    "form_name" => "name",
                                    "placeholder" => "Ingrese el nombre o la porción del mismo que desea",
                                    "validate" => "Nombre es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Tipo de Empleado",
                                    "icon" => "fa-book",
                                    "id_name" => "form-tipo",
                                    "form_name" => "tipo",
                                    "title" => "Selecciona un Tipo",
                                    "options" => array(
                                        array(
                                            "value" => "todos",
                                            "nombre" => "Cualquier tipo de empleado",
                                        ),
                                        array(
                                            "value" => "Administrador",
                                            "nombre" => "Administrador",
                                        ),
                                        array(
                                            "value" => "Operador",
                                            "nombre" => "Operador",
                                        ),
                                        array(
                                            "value" => "Despachador",
                                            "nombre" => "Despachador",
                                        ),
                                        array(
                                            "value" => "Contador",
                                            "nombre" => "Contador",
                                        ),
                                        array(
                                            "value" => "Mantenimiento",
                                            "nombre" => "Mantenimiento",
                                        ),
                                    ),
                                    "validate" => "Tiempo a evaluar es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Banco del Empleado (Opcional*)",
                                    "icon" => "fa-home",
                                    "id_name" => "form-banco",
                                    "form_name" => "banco",
                                    "title" => "Selecciona un Banco",
                                    "options" => array(
                                        array(
                                            "value" => "Bancamiga",
                                            "nombre" => "Bancamiga",
                                        ),
                                        array(
                                            "value" => "BanCaribe",
                                            "nombre" => "BanCaribe",
                                        ),
                                        array(
                                            "value" => "Banco Activo",
                                            "nombre" => "Banco Activo",
                                        ),
                                        array(
                                            "value" => "Banco Agrícola de Venezuela",
                                            "nombre" => "Banco Agrícola de Venezuela",
                                        ),
                                        array(
                                            "value" => "Banco Bicentenario del Pueblo",
                                            "nombre" => "Banco Bicentenario del Pueblo",
                                        ),
                                        array(
                                            "value" => "Banco Caroní",
                                            "nombre" => "Banco Caroní",
                                        ),
                                        array(
                                            "value" => "Banco de Venezuela",
                                            "nombre" => "Banco de Venezuela",
                                        ),
                                        array(
                                            "value" => "Banco del Tesoro",
                                            "nombre" => "Banco del Tesoro",
                                        ),
                                        array(
                                            "value" => "Banco Exterior",
                                            "nombre" => "Banco Exterior",
                                        ),
                                        array(
                                            "value" => "Banco Mercantil",
                                            "nombre" => "Banco Mercantil",
                                        ),
                                        array(
                                            "value" => "Banco Nacional de Crédito BNC",
                                            "nombre" => "Banco Nacional de Crédito BNC",
                                        ),
                                        array(
                                            "value" => "Banco Plaza",
                                            "nombre" => "Banco Plaza",
                                        ),
                                        array(
                                            "value" => "Banco Sofitasa",
                                            "nombre" => "Banco Sofitasa",
                                        ),
                                        array(
                                            "value" => "Banco Venezolano de Crédito",
                                            "nombre" => "Banco Venezolano de Crédito",
                                        ),
                                        array(
                                            "value" => "Banesco",
                                            "nombre" => "Banesco",
                                        ),
                                        array(
                                            "value" => "Banplus",
                                            "nombre" => "Banplus",
                                        ),
                                        array(
                                            "value" => "BBVA Provincial",
                                            "nombre" => "BBVA Provincial",
                                        ),
                                        array(
                                            "value" => "BFC Banco Fondo Común",
                                            "nombre" => "BFC Banco Fondo Común",
                                        ),
                                        array(
                                            "value" => "BOD",
                                            "nombre" => "BOD",
                                        ),
                                        array(
                                            "value" => "DELSUR",
                                            "nombre" => "DELSUR",
                                        ),
                                    ),
                                    "validate" => "Banco es requerido",
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
        //REDIRECCIONAR AL DETALLADO DEL TRABAJADOR
            $(".tr-lista-empleados").click(function() {
                var id = $(this).parent().attr("id");
                var url = "{{ route('detail-worker') }}";
                location.href = url+"/"+id;
            });

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
@endsection