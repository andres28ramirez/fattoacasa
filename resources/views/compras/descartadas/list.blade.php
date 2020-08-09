@extends('layouts.principal')

@section('title','Compras · Fatto a Casa')

@section('titulo','FATTO A CASA - COMPRAS DESCARTADAS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-compras')}}">Listado de Compras</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('suministros')}}">Suministros</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('cpp')}}">Cuentas por Pagar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('cp')}}">Pagos Realizados</a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('discard-compras')}}">Compras Descartadas</a>
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
                    $("#submit-form-list-compras").unbind('click').click(function(event){
                        $("#form-list-compras").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var id = $('#form-list-compras input[id="form-codigo"]').val(); if(!id) id = "todos";
                            var proveedor = $('#form-list-compras select[id="form-proveedor"] option:selected').val();
                            var estado = $('#form-list-compras select[id="form-estado"] option:selected').val();

                            var tiempo = $('#form-list-compras select[id="form-tiempo"] option:selected').val();
                            var fecha_1 = "todos";
                            var fecha_2 = "todos";
                            switch (tiempo) {
                                case "Específico":
                                    fecha_1 = $('#form-list-compras input[id="form-fecha-1"]').val();
                                    fecha_2 = $('#form-list-compras input[id="form-fecha-2"]').val();
                                    break;
                            }

                            var cantidad = "{{$registros}}";
                            var registro = "{{ route('discard-compras') }}";
                            var orden = "{{$order}}";
                            
                            var ruta = registro+"/"+cantidad+"/"+id+"/"+proveedor+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                            
                            if(id && proveedor && estado && tiempo){
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
                    var proveedor = "{{ $persona }}"; if(!proveedor) proveedor = "todos";
                    var estado = "{{ $estado }}"; if(!estado) estado = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = $('select[id="num-register-compra-descartadas"] option:selected').val();
                    var registro = "{{ route('discard-compras') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+id+"/"+proveedor+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('discard-compras') }}";
                    location.href = registro;
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var proveedor = "{{ $persona }}"; if(!proveedor) proveedor = "todos";
                    var estado = "{{ $estado }}"; if(!estado) estado = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('discard-compras') }}";
                    var orden = e;

                    var ruta = registro+"/"+cantidad+"/"+id+"/"+proveedor+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
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

        @media (max-width: 995px) {  
            #modal-add-desperdicio #responsive-title{
                display: none;
            }

            #modal-add-desperdicio #form-product-data{
                margin-top: 10px;
            }
        }

        @media (min-width: 996px) {  
            #modal-add-desperdicio .d-separator{
                display: none;
            }

            #modal-add-desperdicio .rl-producto{
                display: none;
            }

            #modal-add-desperdicio .rl-cantidad{
                display: none;
            }

            #modal-add-desperdicio .rl-desperdicio{
                display: none;
            }
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
                "table-id" => "compra-descartadas",
                "title" => "Compras que fueron descartadas, si presionas el botón activar lo reintegras a tus compras activas.",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "ID",
                        "bd-name" => "id",
                    ),
                    array(
                        "nombre" => "Proveedor",
                        "bd-name" => "id_proveedor",
                    ),
                    array(
                        "nombre" => "Monto",
                        "bd-name" => "monto",
                    ),
                    array(
                        "nombre" => "Fecha",
                        "bd-name" => "fecha",
                    ),
                    array(
                        "nombre" => "Crédito",
                        "bd-name" => "credito",
                    ),
                    array(
                        "nombre" => "Activar",
                        "bd-name" => "añadir",
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
                "dato-5" => "30 días",
                "opciones-6" => true,
                "integrar" => true,
            );

            foreach ($compras as $buy) {
                $data_content["id"] = $buy->id;
                $data_content["dato-1"] = $buy->id;
                $data_content["dato-2"] = $buy->proveedor->nombre;
                $data_content["dato-3"] = number_format($buy->monto,2, ",", ".")." Bs";
                $data_content["dato-4"] = $buy->fecha;
                $data_content["dato-5"] = $buy->credito." días";
                $data_content["opciones-6"] = true;
                $data_content["integrar"] = true;

                array_push($data_list["content"],$data_content);
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $compras->links() }}
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
                        $providers = array ();

                        $one_content = array(
                            "value" => "todos",
                            "nombre" => "Todos los Proveedores",
                        );
                        array_push($providers,$one_content);

                        foreach ($proveedores as $proveedor) {
                            $one_content["value"] = $proveedor->id;
                            $one_content["nombre"] = $proveedor->nombre;
                            array_push($providers,$one_content);
                        }

                        $data_form = array(
                            "action" => "",
                            "title" => "",
                            "form-id" => "form-list-compras",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Proveedor",
                                    "icon" => "fa-book",
                                    "id_name" => "form-proveedor",
                                    "form_name" => "id_proveedor",
                                    "title" => "Selecciona un Proveedor",
                                    "options" => $providers,
                                    "validate" => "Proveedor es requerido",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Estado de la Compra",
                                    "icon" => "fa-info",
                                    "id_name" => "form-estado",
                                    "form_name" => "pendiente",
                                    "title" => "Selecciona un Estado",
                                    "options" => array(
                                        array(
                                            "value" => "todos",
                                            "nombre" => "Cualquier Estado de Compra",
                                        ),
                                        array(
                                            "value" => "1",
                                            "nombre" => "Compras Pagadas",
                                        ),
                                        array(
                                            "value" => "0",
                                            "nombre" => "Compras por Pagar",
                                        ),
                                    ),
                                    "validate" => "Estado es requerido",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Código de Compra (Opc.)",
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
            $("#ic").addClass("active");
            $("#ic").removeClass("icono_head");
            $(".ic").removeClass("icono_color");

        //ELIMINAR LOS BOTONES DE AGREGAR-ELIMINAR-FILTRAR-DESCARGAR
            $("#add-compra-descartadas").remove();
            $("#delete-compra-descartadas").remove();
            $("#print-compra-descartadas").remove();

        //ELIMINAR TODOS LOS CHECK
            $("#th-compra-descartadas").remove();
            $(".td-compra-descartadas").remove();

        //EVENTOS DE LOS DESPACHOS PARA MOSTRAR SU MODAL
            $(".integrar-add").click(function() {
                var id = $(this).attr("id"); //ID DEL VALOR QUE VAMOS A AGREGAR
                var registro = "{{ route('reintegrar-compras') }}";
                location.href = registro+"/"+id;
            });

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
@endsection