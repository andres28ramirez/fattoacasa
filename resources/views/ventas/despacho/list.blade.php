@extends('layouts.principal')

@section('title','Ventas · Fatto a Casa')

@section('titulo','FATTO A CASA - DESPACHOS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-ventas')}}">Listado de Ventas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-pedidos') }}">Pedidos sin Despacho</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-despachos')}}">Despachos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-cuentas')}}">Cuentas por Cobrar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-pagos')}}">Pagos Recibidos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('discard-ventas')}}">Ventas Descartadas</a>
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
                    $("#submit-form-list-ventas").unbind('click').click(function(event){
                        $("#form-list-ventas").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var id = $('#form-list-ventas input[id="form-codigo"]').val(); if(!id) id = "todos";
                            var cliente = $('#form-list-ventas select[id="form-cliente"] option:selected').val(); if(!cliente) cliente = "todos";
                            var trabajador = $('#form-list-ventas select[id="form-trabajador"] option:selected').val(); if(!trabajador) trabajador = "todos";
                            var estado = $('#form-list-ventas select[id="form-estado"] option:selected').val();

                            var tiempo = $('#form-list-ventas select[id="form-tiempo"] option:selected').val();
                            var fecha_1 = "todos";
                            var fecha_2 = "todos";
                            switch (tiempo) {
                                case "Específico":
                                    fecha_1 = $('#form-list-ventas input[id="form-fecha-1"]').val();
                                    fecha_2 = $('#form-list-ventas input[id="form-fecha-2"]').val();
                                    break;
                            }

                            var cantidad = "{{$registros}}";
                            var registro = "{{ route('list-despachos') }}";
                            var orden = "{{$order}}";
                            
                            var ruta = registro+"/"+cantidad+"/"+id+"/"+cliente+"/"+trabajador+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                            
                            if(id && cliente && trabajador && estado && tiempo){
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
                    var table = "despacho-cliente";
                    var url = "{{ route('delete-despacho') }}";
                    var report_url = "{{ route('report-error') }}";
                    var despachos = new Array();
                    $("#check-despacho-cliente .check-data").each(function( index ) {
                        if ($(this).prop('checked') == true){
                            despachos.push($(this).val());
                        }
                    });

                    if(despachos.length > 0){
                        swal({
                            title: "Eliminar registros",
                            text: "¿Esta seguro de eliminar los despachos seleccionados?",
                            icon: "warning",
                            buttons: ["Cancelar","Aceptar"],
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                ajaxDelete(despachos,url,table,report_url);
                            }
                        });
                    }
                    break;
                case "registros":
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var cliente = "{{ $persona }}"; if(!cliente) cliente = "todos";
                    var estado = "{{ $estado }}"; if(!estado) estado = "todos";
                    var trabajador = "{{ $despachador }}"; if(!trabajador) trabajador = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = $('select[id="num-register-despacho-cliente"] option:selected').val();
                    var registro = "{{ route('list-despachos') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+id+"/"+cliente+"/"+trabajador+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('list-despachos') }}";
                    location.href = registro;
                    break;
                case "print":
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var cliente = "{{ $persona }}"; if(!cliente) cliente = "todos";
                    var estado = "{{ $estado }}"; if(!estado) estado = "todos";
                    var trabajador = "{{ $despachador }}"; if(!trabajador) trabajador = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var registro = "{{ route('pdf-despachos') }}";
                    var ruta = registro+"/"+id+"/"+cliente+"/"+trabajador+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var cliente = "{{ $persona }}"; if(!cliente) cliente = "todos";
                    var estado = "{{ $estado }}"; if(!estado) estado = "todos";
                    var trabajador = "{{ $despachador }}"; if(!trabajador) trabajador = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('list-despachos') }}";
                    var orden = e;

                    var ruta = registro+"/"+cantidad+"/"+id+"/"+cliente+"/"+trabajador+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
            }
        }
    </script>

    <style>
        .border-right{
            border-top-right-radius: 0px;
            border-bottom-right-radius: 0px;
        }

        .border-left{
            border-top-left-radius: 0px;
            border-bottom-left-radius: 0px;
        }
    </style>

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
            if($persona)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(
                "table-id" => "despacho-cliente",
                "title" => "Presiona sobre la fila para ver a detalle y editar el despacho.",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "Código de Venta",
                        "bd-name" => "id_venta",
                    ),
                    array(
                        "nombre" => "Cliente",
                        "bd-name" => "id_cliente",
                    ),
                    array(
                        "nombre" => "Monto",
                        "bd-name" => "monto",
                    ),
                    array(
                        "nombre" => "Fecha de Venta",
                        "bd-name" => "fecha_venta",
                    ),
                    array(
                        "nombre" => "Despachador",
                        "bd-name" => "id_trabajador",
                    ),
                    array(
                        "nombre" => "Fecha de Despacho",
                        "bd-name" => "fecha",
                    ),
                    array(
                        "nombre" => "Estado de Despacho",
                        "bd-name" => "entregado",
                    ),
                ),
                "content" => array(),
            );

            $data_content = array(
                "id" => 1,
                "dato-1" => "1",
                "dato-2" => "Excelsior Gama",
                "dato-3" => "25869,123 Bs",
                "dato-4" => "28-06-1996",
                "dato-5" => "Andres",
                "dato-6" => "01-07-1996",
                "estado-7" => false,
            );

            foreach ($despachos as $despacho) {
                if($despacho->venta){
                    $data_content["id"] = $despacho->id;
                    $data_content["dato-1"] = $despacho->id_venta;
                    $data_content["dato-2"] = $despacho->venta->cliente->nombre;
                    $data_content["dato-3"] = number_format($despacho->venta->monto,2, ",", ".")." Bs";
                    $data_content["dato-4"] = $despacho->venta->fecha;
                    $data_content["dato-5"] = $despacho->trabajador ? $despacho->trabajador->nombre." ".$despacho->trabajador->apellido : "No posee";
                    $data_content["dato-6"] = $despacho->fecha;
                    $data_content["estado-7"] = $despacho->entregado ? "Finalizado" : "Pendiente";

                    array_push($data_list["content"],$data_content);
                }
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $despachos->links() }}
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
                        $clients = array ();
                        $one_content = array(
                            "value" => "todos",
                            "nombre" => "Todos los Clientes",
                        );

                        foreach ($clientes as $cliente) {
                            $one_content["value"] = $cliente->id;
                            $one_content["nombre"] = $cliente->nombre;
                            array_push($clients,$one_content);
                        }

                        $workers = array ();
                        unset($one_content);
                        $one_content = array(
                            "value" => "todos",
                            "nombre" => "Todos los Clientes",
                        );

                        foreach ($trabajadores as $trabajador) {
                            $one_content["value"] = $trabajador->id;
                            $one_content["nombre"] = $trabajador->nombre." ".$trabajador->apellido;
                            array_push($workers,$one_content);
                        }

                        $data_form = array(
                            "action" => "",
                            "title" => "",
                            "form-id" => "form-list-ventas",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Cliente (Opcional*)",
                                    "icon" => "fa-book",
                                    "id_name" => "form-cliente",
                                    "form_name" => "id_cliente",
                                    "title" => "Selecciona un Cliente",
                                    "options" => $clients,
                                    "validate" => "Cliente es requerido",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Despachador (Opcional*)",
                                    "icon" => "fa-book",
                                    "id_name" => "form-trabajador",
                                    "form_name" => "id_trabajador",
                                    "title" => "Selecciona un Despachador",
                                    "options" => $workers,
                                    "validate" => "Despachados es requerido",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Estado del Despacho",
                                    "icon" => "fa-info",
                                    "id_name" => "form-estado",
                                    "form_name" => "pendiente",
                                    "title" => "Selecciona un Estado",
                                    "options" => array(
                                        array(
                                            "value" => "todos",
                                            "nombre" => "Cualquier Estado de Despacho",
                                        ),
                                        array(
                                            "value" => "1",
                                            "nombre" => "Despachos Finalizados",
                                        ),
                                        array(
                                            "value" => "0",
                                            "nombre" => "Despachos Pendientes",
                                        ),
                                    ),
                                    "validate" => "Estado es requerido",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Código de Venta (Opcional*)",
                                    "icon" => "fa-list-alt",
                                    "type" => "number",
                                    "id_name" => "form-codigo",
                                    "form_name" => "form-codigo",
                                    "placeholder" => "Ingrese el código o la porción del mismo que desea",
                                    "validate" => "Código es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Fecha del Despacho",
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
        //ACOMODO LA BARRA DE NAVEGACION
            $("#ip").addClass("active");
            $("#ip").removeClass("icono_head");
            $(".ip").removeClass("icono_color");
        
        //ELIMINAR LOS BOTONES DE AGREGAR-ELIMINAR-FILTRAR-DESCARGAR
            $("#add-despacho-cliente").remove();

        //ABRIR MODAL PARA VER DETALLADO DE PRODUCTOS
            $(".tr-despacho-cliente").click(function() {
                var id = $(this).parent().attr("id");
                var url = "{{ route('detail-despacho') }}";
                location.href = url+"/"+id;
            });

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
@endsection