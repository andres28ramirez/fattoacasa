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
            <a class="nav-link text-secondary" href="{{ route('list-workers') }}">Empleados</a>
        </li>
    @endif
        <!-- <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-reports') }}">Reportes Generados</a>
        </li> -->
    @if(Auth::user()->tipo != "operador")
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-system') }}">Reportes del Sistema</a>
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
                case "filtrar":
                    $('#table-filter').modal(true);
                    //CAPTURAR EVENTO SUBMIT DE FILTRAR INFORMACIÓN
                    $("#submit-form-list-report").unbind('click').click(function(event){
                        $("#form-list-report").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var nombre = $('#form-list-report input[id="form-name"]').val(); if(!nombre) nombre = "todos";

                            var tiempo = $('#form-list-report select[id="form-tiempo"] option:selected').val();
                            var fecha_1 = "todos";
                            var fecha_2 = "todos";
                            switch (tiempo) {
                                case "Específico":
                                    fecha_1 = $('#form-list-report input[id="form-fecha-1"]').val();
                                    fecha_2 = $('#form-list-report input[id="form-fecha-2"]').val();
                                    break;
                            }

                            var cantidad = "{{$registros}}";
                            var registro = "{{ route('list-system') }}";
                            var orden = "{{$order}}";
                            
                            var ruta = registro+"/"+cantidad+"/"+nombre+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                            
                            if(nombre && tiempo){
                                if(tiempo!="todos"){
                                    if(!(Date.parse(fecha_2) < Date.parse(fecha_1)) && fecha_1 && fecha_2)
                                        location.href = ruta;
                                }
                                else
                                    location.href = ruta;
                            }
                            return false;
                        });
                    });
                    break;
                case "eliminar":
                    // aqui reviso los campos que estan en check y tomo su ID
                    var table = "lista-reportes";
                    var url = "{{ route('delete-incidencia') }}";
                    var report_url = "{{ route('report-error') }}";
                    var reportes = new Array();
                    $("#check-lista-reportes .check-data").each(function( index ) {
                        if ($(this).prop('checked') == true){
                            reportes.push($(this).val());
                        }
                    });

                    if(reportes.length > 0){
                        swal({
                            title: "Eliminar registros",
                            text: "¿Esta seguro de eliminar los reportes seleccionaos?",
                            icon: "warning",
                            buttons: ["Cancelar","Aceptar"],
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                ajaxDelete(reportes,url,table,report_url);
                            }
                        });
                    }
                    break;
                case "registros":
                    var nombre = "{{ $nombre }}"; if(!nombre) nombre = "todos";
                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = $('select[id="num-register-lista-reportes"] option:selected').val();
                    var registro = "{{ route('list-system') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+nombre+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('list-system') }}";
                    location.href = registro;
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var nombre = "{{ $nombre }}"; if(!nombre) nombre = "todos";
                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('list-system') }}";
                    var orden = e;

                    var ruta = registro+"/"+cantidad+"/"+nombre+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
            }
        }
    </script>

    <div class="row justify-content-center my-3 px-2">
        @php
            if($tiempo)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(
                "table-id" => "lista-reportes",
                "title" => "Reporte de modificaciones e incidencias generadas en el Sistema",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "Código",
                        "bd-name" => "id",
                    ),
                    array(
                        "nombre" => "Usuario/General",
                        "bd-name" => "name",
                    ),
                    array(
                        "nombre" => "Actividad",
                        "bd-name" => "activity",
                    ),
                    array(
                        "nombre" => "Descripción",
                        "bd-name" => "description",
                    ),
                    array(
                        "nombre" => "Fecha",
                        "bd-name" => "created_at",
                    ),
                ),
                "content" => array(),
            );

            $data_content = array(
                "id" => 1,
                "dato-1" => "1",
                "dato-2" => "Sistema General / Andres Ramirez",
                "dato-3" => "Módulo X",
                "dato-4" => "Error, Añadido, Editado, Eliminacion",
                "dato-5" => "28-03-2020 hora tal",
            );

            foreach ($reportes as $report) {
                $data_content["id"] = $report->id;
                $data_content["dato-1"] = $report->id;
                $data_content["dato-2"] = $report->name;
                $data_content["dato-3"] = $report->activity;
                $data_content["dato-4"] = $report->description;
                $data_content["dato-5"] = $report->created_at;

                array_push($data_list["content"],$data_content);
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $reportes->links() }}
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
    </script>
@endsection