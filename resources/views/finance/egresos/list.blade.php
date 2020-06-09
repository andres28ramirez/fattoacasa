@extends('layouts.principal')

@section('title','Finanzas · Fatto a Casa')

@section('titulo','FATTO A CASA - EGRESOS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-ingresos')}}">Ingresos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-egresos') }}">Egresos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-gasto-costo') }}">Gastos y Costos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-nomina') }}">Nómina</a>
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
                case "filtrar":
                    $('#table-filter').modal(true);
                    //CAPTURAR EVENTO SUBMIT DE FILTRAR INFORMACIÓN
                    $("#submit-form-list-egresos").unbind('click').click(function(event){
                        $("#form-list-egresos").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var tipo = $('#form-list-egresos select[id="form-tipo"] option:selected').val(); if(!tipo) tipo = "todos";
                            var tiempo = $('#form-list-egresos select[id="form-tiempo"] option:selected').val();
                            var fecha_1 = "todos";
                            var fecha_2 = "todos";
                            switch (tiempo) {
                                case "Específico":
                                    fecha_1 = $('#form-list-egresos input[id="form-fecha-1"]').val();
                                    fecha_2 = $('#form-list-egresos input[id="form-fecha-2"]').val();
                                    break;
                            }

                            var cantidad = "{{$registros}}";
                            var registro = "{{ route('list-egresos') }}";
                            var orden = "{{$order}}";
                            
                            var ruta = registro+"/"+cantidad+"/"+tipo+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                            
                            if(tipo && tiempo){
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
                case "registros":
                    var tipo = "{{ $tipo }}"; if(!tipo) tipo = "todos";
                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = $('select[id="num-register-lista-egresos"] option:selected').val();
                    var registro = "{{ route('list-egresos') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+tipo+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('list-egresos') }}";
                    location.href = registro;
                    break;
                case "print":
                    var tipo = "{{ $tipo }}"; if(!tipo) tipo = "todos";
                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var registro = "{{ route('pdf-list-egresos') }}";
                    var ruta = registro+"/"+tipo+"/"+tiempo+"/"+fecha_1+"/"+fecha_2;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var tipo = "{{ $tipo }}"; if(!tipo) tipo = "todos";
                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('list-egresos') }}";
                    var orden = e;

                    var ruta = registro+"/"+cantidad+"/"+tipo+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
            }
        }
    </script>

    <!-- GRÁFICO DE EGRESOS -->
    <div class="row justify-content-center my-3 px-2">
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $data_singleline = array(
                    "texto" => "TOTAL EGRESOS GLOBALES",
                    "canva" => "c3",
                    "labels" => array("ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sep.", "oct.", "nov.", "dic."),
                    "datos" => $c3_data,
                    "bgcolors" => "rgba(255,129,0,0.7)",
                    "brcolors" => "rgba(128,66,5,0.8)",
                    "chartHeight" => "50vh"
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-finance-c3",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "select",
                            "label-name" => "Periodo a Evaluar",
                            "icon" => "fa-leanpub",
                            "id_name" => "form-periodo",
                            "form_name" => "form-periodo",
                            "title" => "Selecciona un periodo",
                            "options" => array(
                                array(
                                    "value" => "Mensual",
                                    "nombre" => "Mensual",
                                ),
                                array(
                                    "value" => "Días",
                                    "nombre" => "Días de la Semana",
                                ),
                            ),
                            "validate" => "Periodo es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
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
                                    "value" => "Año",
                                    "nombre" => "Año",
                                ),
                                array(
                                    "value" => "Específico",
                                    "nombre" => "Específico",
                                ),
                                array(
                                    "value" => "Todos",
                                    "nombre" => "Todas las Fechas",
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
                            "form_name" => "form-año",
                            "placeholder" => "Ingrese el año deseado",
                            "validate" => "Año es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Primera fecha",
                            "icon" => "fa-calendar",
                            "type" => "month",
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
                            "type" => "month",
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
            @include('includes.single_line',['data'=>$data_singleline, 'data_form'=>$data_form])
        </div>
    </div>

    <!-- TABLE DE EGRESOS -->
    <div class="row justify-content-center my-3 px-2">
        @php
            if($tiempo)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(
                "table-id" => "lista-egresos",
                "title" => "Lista de Egresos",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "Tipo",
                        "bd-name" => "tipo",
                    ),
                    array(
                        "nombre" => "Persona",
                        "bd-name" => "persona",
                    ),
                    array(
                        "nombre" => "Monto",
                        "bd-name" => "monto",
                    ),
                    array(
                        "nombre" => "Fecha",
                        "bd-name" => "created_at",
                    ),
                    array(
                        "nombre" => "Fecha-Pago",
                        "bd-name" => "fecha_pago",
                    ),
                    array(
                        "nombre" => "Referencia",
                        "bd-name" => "referencia",
                    ),
                ),
                "content" => array(),
            );

            $data_content = array(
                "id" => 1,
                "dato-1" => "Compra",
                "dato-2" => "Andres Ramirez",
                "dato-3" => "19.000 Bs",
                "dato-4" => "28-06-1996",
                "dato-5" => "28-06-1996",
                "dato-6" => "145643248",
            );

            foreach ($egresos as $row) {
                $data_content["id"] = 1;
                if($row->compra){
                    $data_content["dato-1"] = "Compra";
                    $data_content["dato-2"] = $row->compra->proveedor->nombre;
                    $data_content["dato-3"] = $row->monto." Bs";
                    $data_content["dato-4"] = $row->compra->fecha;
                    $data_content["dato-5"] = $row->compra->pago ? $row->compra->pago->fecha_pago : "No posee";
                    $data_content["dato-6"] = $row->compra->pago ? $row->compra->pago->referencia : "No posee";
                }
                else if($row->gastocosto){
                    $data_content["dato-1"] = $row->gastocosto->tipo;
                    $data_content["dato-2"] = $row->gastocosto->descripcion;
                    $data_content["dato-3"] = $row->monto." Bs";
                    $data_content["dato-4"] = $row->gastocosto->fecha;
                    $data_content["dato-5"] = "No posee";
                    $data_content["dato-6"] = "No posee";
                }
                else if($row->nomina){
                    $data_content["dato-1"] = "Pago Nómina";
                    $data_content["dato-2"] = $row->nomina->trabajador->nombre." ".$row->nomina->trabajador->apellido;
                    $data_content["dato-3"] = $row->monto." Bs";
                    setlocale(LC_TIME, "spanish");
                    $data_content["dato-4"] = strftime("%B", strtotime($row->nomina->mes));
                    $data_content["dato-5"] = $row->nomina->pago->fecha_pago;
                    $data_content["dato-6"] = $row->nomina->pago->referencia;
                }

                array_push($data_list["content"],$data_content);
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $egresos->links() }}
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
                            "form-id" => "form-list-egresos",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Tipo de Egreso (Opcional*)",
                                    "icon" => "fa-list-alt",
                                    "id_name" => "form-tipo",
                                    "form_name" => "form-tipo",
                                    "title" => "Selecciona un tipo",
                                    "options" => array(
                                        array(
                                            "value" => "Compra",
                                            "nombre" => "Compra",
                                        ),
                                        array(
                                            "value" => "Gasto",
                                            "nombre" => "Gasto",
                                        ),
                                        array(
                                            "value" => "Costo",
                                            "nombre" => "Costo",
                                        ),
                                        array(
                                            "value" => "Pago de Nómina",
                                            "nombre" => "Pago de Nómina",
                                        ),
                                    ),
                                    "validate" => "Tiempo a evaluar es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Fecha de Egreso",
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

    <input type="hidden" id="url-filter" value="{{ route('filter-indicators') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script src="{{ asset('js/ajaxIndicators.js') }}"></script>
    <script src="{{ asset('js/indicatorsDownload.js') }}"></script>
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#if").addClass("active");
            $("#if").removeClass("icono_head");
            $(".if").removeClass("icono_color");
        
        //ELIMINAR LOS BOTONES DE AGREGAR-ELIMINAR-FILTRAR-DESCARGAR
            $("#add-lista-egresos").remove();
            $("#delete-lista-egresos").remove();

        //ELIMINAR TODOS LOS CHECK
            $("#th-lista-egresos").remove();
            $(".td-lista-egresos").remove();

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
    <script type="text/javascript" src="{{ asset('js/singleline.js') }}"></script>
@endsection