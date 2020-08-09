@extends('layouts.principal')

@section('title','Ventas · Fatto a Casa')

@section('titulo','PAGOS RECIBIDOS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-ventas')}}">Listado de Ventas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-pedidos') }}">Pedidos sin Despacho</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-despachos')}}">Despachos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-cuentas')}}">Cuentas por Cobrar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-pagos')}}">Pagos Realizados</a>
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
                    $("#submit-form-list-pagos").unbind('click').click(function(event){
                        $("#form-list-pagos").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var id = $('#form-list-pagos input[id="form-codigo"]').val(); if(!id) id = "todos";
                            var referencia = $('#form-list-pagos input[id="form-referencia"]').val(); if(!referencia) referencia = "todos";
                            var banco = $('#form-list-pagos select[id="form-banco"] option:selected').val();

                            var tiempo = $('#form-list-pagos select[id="form-tiempo"] option:selected').val();
                            var fecha_1 = "todos";
                            var fecha_2 = "todos";
                            switch (tiempo) {
                                case "Específico":
                                    fecha_1 = $('#form-list-pagos input[id="form-fecha-1"]').val();
                                    fecha_2 = $('#form-list-pagos input[id="form-fecha-2"]').val();
                                    break;
                            }

                            var cantidad = "{{$registros}}";
                            var registro = "{{ route('list-pagos') }}";
                            var orden = "{{$order}}";
                            
                            var ruta = registro+"/"+cantidad+"/"+id+"/"+referencia+"/"+banco+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                            
                            if(id && referencia && banco && tiempo){
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
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var referencia = "{{ $referencia }}"; if(!referencia) referencia = "todos";
                    var banco = "{{ $banco }}"; if(!banco) banco = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = $('select[id="num-register-lista-pagos"] option:selected').val();
                    var registro = "{{ route('list-pagos') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+id+"/"+referencia+"/"+banco+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('list-pagos') }}";
                    location.href = registro;
                    break;
                case "print":
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var referencia = "{{ $referencia }}"; if(!referencia) referencia = "todos";
                    var banco = "{{ $banco }}"; if(!banco) banco = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var registro = "{{ route('pdf-ventas-pago') }}";
                    var ruta = registro+"/"+id+"/"+referencia+"/"+banco+"/"+tiempo+"/"+fecha_1+"/"+fecha_2;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var referencia = "{{ $referencia }}"; if(!referencia) referencia = "todos";
                    var banco = "{{ $banco }}"; if(!banco) banco = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('list-pagos') }}";
                    var orden = e;

                    var ruta = registro+"/"+cantidad+"/"+id+"/"+referencia+"/"+banco+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
            }
        }
    </script>

    <!-- TABLE DE COSTOS-GASTOS -->
    <div class="row justify-content-center my-3 px-2">
        @php
            if($banco)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(
                "table-id" => "lista-pagos",
                "title" => "Presione sobre el pago para editar y ver el detallado de la venta",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "Cliente",
                        "bd-name" => "id_cliente",
                    ),
                    array(
                        "nombre" => "Banco",
                        "bd-name" => "banco",
                    ),
                    array(
                        "nombre" => "Referencia",
                        "bd-name" => "referencia",
                    ),
                    array(
                        "nombre" => "Fecha",
                        "bd-name" => "fecha_pago",
                    ),
                    array(
                        "nombre" => "ID-Venta",
                        "bd-name" => "id_venta",
                    ),
                    array(
                        "nombre" => "Monto",
                        "bd-name" => "monto",
                    ),
                ),
                "content" => array(),
            );

            $data_content = array(
                "id" => 3,
                "dato-1" => "Andres",
                "dato-2" => "Banesco",
                "dato-3" => "16487943",
                "dato-4" => "13-03-2020",
                "dato-5" => "55",
                "dato-6" => "15.000 Bs",
            );

            foreach ($ventas as $sell) {
                $data_content["id"] = $sell->id_pago;
                foreach($sell->pago->venta as $element){
                    $data_content["dato-1"] = $element->cliente->nombre;
                }
                $data_content["dato-2"] = $sell->pago->banco;
                $data_content["dato-3"] = $sell->pago->referencia ? $sell->pago->referencia : "No posee";
                $data_content["dato-4"] = $sell->pago->fecha_pago;
                $data_content["dato-5"] = $sell->id;
                $data_content["dato-6"] = number_format($sell->monto,2, ",", ".")." Bs";

                array_push($data_list["content"],$data_content);
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $ventas->links() }}
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
                            "form-id" => "form-list-pagos",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Código de Venta (Opc.)",
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
                                    "component-type" => "input",
                                    "label-name" => "Número de Referencia Bancaria (Opc.)",
                                    "icon" => "fa-book",
                                    "type" => "text",
                                    "id_name" => "form-referencia",
                                    "form_name" => "form-referencia",
                                    "placeholder" => "Ingrese el código o la porción del mismo que desea",
                                    "validate" => "Nro. de Referencia es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Banco del Pago",
                                    "icon" => "fa-home",
                                    "id_name" => "form-banco",
                                    "form_name" => "banco",
                                    "title" => "Selecciona un Banco",
                                    "options" => array(
                                        array(
                                            "value" => "todos",
                                            "nombre" => "Cualquier Banco",
                                        ),
                                        array(
                                            "value" => "Otro",
                                            "nombre" => "Otro",
                                        ),
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
                                            "value" => "Específico",
                                            "nombre" => "Específico",
                                        ),
                                        array(
                                            "value" => "todos",
                                            "nombre" => "Todos los años",
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
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#ip").addClass("active");
            $("#ip").removeClass("icono_head");
            $(".ip").removeClass("icono_color");
        
        //ELIMINAR LOS BOTONES DE AGREGAR-ELIMINAR-FILTRAR-DESCARGAR
            $("#add-lista-pagos").remove();
            $("#delete-lista-pagos").remove();

        //ELIMINAR TODOS LOS CHECK
            $("#th-lista-pagos").remove();
            $(".td-lista-pagos").remove();

        //REDIRECCIONAR AL DETALLADO DEL PROVEEDOR
            $(".tr-lista-pagos").click(function() {
                var id = $(this).parent().attr("id");
                var url = "{{ route('venta-pago') }}";
                location.href = url+"/"+id;
            });
                
        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
@endsection