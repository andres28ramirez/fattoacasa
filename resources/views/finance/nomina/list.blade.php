@extends('layouts.principal')

@section('title','Finanzas · Fatto a Casa')

@section('titulo','FATTO A CASA - NÓMINA')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-ingresos')}}">Ingresos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-egresos') }}">Egresos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-gasto-costo') }}">Gastos y Costos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-nomina') }}">Nómina</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('finance-pagos') }}">Pagos</a>
        </li>
    </ul>
@endsection

@section('info')

    <!-- SCRIPT PARA REDIRECCIÓN BOTONES DE LA TABLA -->
    <script>
        function redirect_table(e){
            switch (e) {
                case "agregar":
                    location.href = "{{ route('agg-nomina') }}";
                    break;
                case "filtrar":
                    $('#table-filter').modal(true);
                    //CAPTURAR EVENTO SUBMIT DE FILTRAR INFORMACIÓN
                    $("#submit-form-list-nomina").unbind('click').click(function(event){
                        $("#form-list-nomina").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var empleado = $('#form-list-nomina select[id="form-worker"] option:selected').val(); if(!empleado) empleado = "todos";
                            var tiempo = $('#form-list-nomina select[id="form-tiempo"] option:selected').val();
                            var ayo = "todos";
                            var mes = "todos";
                            switch (tiempo) {
                                case "Año":
                                    ayo = $('#form-list-nomina input[id="form-año"]').val();
                                    break;
                                case "Mes":
                                    mes = $('#form-list-nomina select[id="form-mes"]').val();
                                    break;
                            }

                            var cantidad = "{{$registros}}";
                            var registro = "{{ route('list-nomina') }}";
                            var orden = "{{$order}}";
                            
                            var ruta = registro+"/"+cantidad+"/"+empleado+"/"+tiempo+"/"+ayo+"/"+mes+"/"+orden;
                            
                            if(empleado && tiempo){
                                location.href = ruta;
                            }
                            return false;
                        });
                    });
                    break;
                case "eliminar":
                    // aqui reviso los campos que estan en check y tomo su ID
                    var table = "lista-nomina";
                    var url = "{{ route('delete-nomina') }}";
                    var report_url = "{{ route('report-error') }}";
                    var nomina = new Array();
                    $("#check-lista-nomina .check-data").each(function( index ) {
                        if ($(this).prop('checked') == true){
                            nomina.push($(this).val());
                        }
                    });

                    if(nomina.length > 0){
                        swal({
                            title: "Eliminar registros",
                            text: "¿Esta seguro de eliminar los registros seleccionados?",
                            icon: "warning",
                            buttons: ["Cancelar","Aceptar"],
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                ajaxDelete(nomina,url,table,report_url);
                            }
                        });
                    }
                    break;
                case "registros":
                    var empleado = "{{ $empleado }}"; if(!empleado) empleado = "todos";
                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var ayo = "{{ $ayo }}"; if(!ayo) ayo = "todos";
                    var mes = "{{ $mes }}"; if(!mes) mes = "todos";

                    var cantidad = $('select[id="num-register-lista-nomina"] option:selected').val();
                    var registro = "{{ route('list-nomina') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+empleado+"/"+tiempo+"/"+ayo+"/"+mes+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('list-nomina') }}";
                    location.href = registro;
                    break;
                case "print":
                    var empleado = "{{ $empleado }}"; if(!empleado) empleado = "todos";
                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var ayo = "{{ $ayo }}"; if(!ayo) ayo = "todos";
                    var mes = "{{ $mes }}"; if(!mes) mes = "todos";

                    var registro = "{{ route('pdf-list-nomina') }}";
                    var ruta = registro+"/"+empleado+"/"+tiempo+"/"+ayo+"/"+mes;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var empleado = "{{ $empleado }}"; if(!empleado) empleado = "todos";
                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var ayo = "{{ $ayo }}"; if(!ayo) ayo = "todos";
                    var mes = "{{ $mes }}"; if(!mes) mes = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('list-nomina') }}";
                    var orden = e;

                    var ruta = registro+"/"+cantidad+"/"+empleado+"/"+tiempo+"/"+ayo+"/"+mes+"/"+orden;
                    location.href = ruta;
                    break;
            }
        }
    </script>

    <!-- TABLE DE COSTOS-GASTOS -->
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
            if($empleado)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(
                "table-id" => "lista-nomina",
                "title" => "Presiona sobre la fila para ver/editar en detalle el pago",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "Código",
                        "bd-name" => "id",
                    ),
                    array(
                        "nombre" => "Empleado",
                        "bd-name" => "id_trabajador",
                    ),
                    array(
                        "nombre" => "Año",
                        "bd-name" => "created_at",
                    ),
                    array(
                        "nombre" => "Mes",
                        "bd-name" => "mes",
                    ),
                    array(
                        "nombre" => "Monto",
                        "bd-name" => "monto",
                    ),
                    array(
                        "nombre" => "Referencia de Pago",
                        "bd-name" => "referencia",
                    ),
                ),
                "content" => array(),
            );

            $data_content = array(
                "id" => 3,
                "dato-1" => "3",
                "dato-2" => "Andres Ramirez",
                "dato-3" => "28-06-1996",
                "dato-4" => "Junio",
                "dato-5" => "20.000 Bs",
                "dato-6" => "645789432",
            );

            foreach ($nomina as $row) {
                $data_content["id"] = $row->id;
                $data_content["dato-1"] = $row->id;
                $data_content["dato-2"] = $row->trabajador->nombre." ".$row->trabajador->apellido;
                $data_content["dato-3"] = strftime("%Y", strtotime($row->mes));
                setlocale(LC_TIME, "spanish"); 
                $data_content["dato-4"] = strftime("%B", strtotime($row->mes));
                $data_content["dato-5"] = $row->monto." Bs";
                $data_content["dato-6"] = $row->pago->referencia;

                array_push($data_list["content"],$data_content);
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $nomina->links() }}
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
                        $workers = array ();

                        $one_content = array(
                            "value" => "ID TRABAJADOR",
                            "nombre" => "X Trabajador",
                        );

                        foreach ($trabajadores as $worker) {
                            $one_content["value"] = $worker->id;
                            $one_content["nombre"] = $worker->nombre." ".$worker->apellido;
                            array_push($workers,$one_content);
                        }

                        $data_form = array(
                            "action" => "",
                            "title" => "",
                            "form-id" => "form-list-nomina",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Empleado (Opcional*):",
                                    "icon" => "fa-book",
                                    "id_name" => "form-worker",
                                    "form_name" => "form-worker",
                                    "title" => "Selecciona un Empleado",
                                    "options" => $workers,
                                    "validate" => "Tiempo a evaluar es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Fecha de Pago",
                                    "icon" => "fa-hourglass-half",
                                    "id_name" => "form-tiempo",
                                    "form_name" => "form-tiempo",
                                    "title" => "Selecciona un tiempo",
                                    "options" => array(
                                        array(
                                            "value" => "Año",
                                            "nombre" => "Buscar por año",
                                        ),
                                        array(
                                            "value" => "Mes",
                                            "nombre" => "Buscar por mes",
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
                                    "label-name" => "Selecionar Año",
                                    "icon" => "fa-calendar-o",
                                    "type" => "number",
                                    "id_name" => "form-año",
                                    "form_name" => "año",
                                    "placeholder" => "Ingrese el año deseado",
                                    "validate" => "Año es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Selecionar Mes",
                                    "icon" => "fa-calendar-o",
                                    "id_name" => "form-mes",
                                    "form_name" => "mes",
                                    "title" => "Selecciona un tiempo",
                                    "options" => array(
                                        array(
                                            "value" => "1",
                                            "nombre" => "Enero",
                                        ),
                                        array(
                                            "value" => "2",
                                            "nombre" => "Febrero",
                                        ),
                                        array(
                                            "value" => "3",
                                            "nombre" => "Marzo",
                                        ),
                                        array(
                                            "value" => "4",
                                            "nombre" => "Abril",
                                        ),
                                        array(
                                            "value" => "5",
                                            "nombre" => "Mayo",
                                        ),
                                        array(
                                            "value" => "6",
                                            "nombre" => "Junio",
                                        ),
                                        array(
                                            "value" => "7",
                                            "nombre" => "Julio",
                                        ),
                                        array(
                                            "value" => "8",
                                            "nombre" => "Agosto",
                                        ),
                                        array(
                                            "value" => "9",
                                            "nombre" => "Septiembre",
                                        ),
                                        array(
                                            "value" => "10",
                                            "nombre" => "Octubre",
                                        ),
                                        array(
                                            "value" => "11",
                                            "nombre" => "Noviembre",
                                        ),
                                        array(
                                            "value" => "12",
                                            "nombre" => "Diciembre",
                                        ),
                                    ),
                                    "validate" => "Mes a evaluar es requerido",
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
        //ACOMODO LA BARRA DE NAVEGACION
            $("#if").addClass("active");
            $("#if").removeClass("icono_head");
            $(".if").removeClass("icono_color");

        //REDIRECCIONAR AL DETALLADO DE NOMINA
            $(".tr-lista-nomina").click(function() {
                var id = $(this).parent().attr("id");
                var url = "{{ route('detail-nomina') }}";
                location.href = url+"/"+id;
            });
                
        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
@endsection