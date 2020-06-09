@extends('layouts.principal')

@section('title','Clientes · Fatto a Casa')

@section('titulo','FATTO A CASA - CLIENTES')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-client')}}">Listado</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('agg-client') }}">Añadir</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('sell-client') }}">Ventas Realizadas</a>
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
                    $("#submit-form-list-venta").unbind('click').click(function(event){
                        $("#form-list-venta").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var id = $('#form-list-venta input[id="form-codigo"]').val(); if(!id) id = "todos";
                            var cliente = $('#form-list-venta select[id="form-cliente"] option:selected').val();
                            var estado = $('#form-list-venta select[id="form-estado"] option:selected').val();

                            var tiempo = $('#form-list-venta select[id="form-tiempo"] option:selected').val();
                            var fecha_1 = "todos";
                            var fecha_2 = "todos";
                            switch (tiempo) {
                                case "Específico":
                                    fecha_1 = $('#form-list-venta input[id="form-fecha-1"]').val();
                                    fecha_2 = $('#form-list-venta input[id="form-fecha-2"]').val();
                                    break;
                            }

                            var cantidad = "{{$registros}}";
                            var registro = "{{ route('sell-client') }}";
                            var orden = "{{$order}}";
                            
                            var ruta = registro+"/"+cantidad+"/"+id+"/"+cliente+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                            
                            if(id && cliente && estado && tiempo){
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
                    var cliente = "{{ $persona }}"; if(!cliente) cliente = "todos";
                    var estado = "{{ $estado }}"; if(!estado) estado = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = $('select[id="num-register-ventas-cliente"] option:selected').val();
                    var registro = "{{ route('sell-client') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+id+"/"+cliente+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('sell-client') }}";
                    location.href = registro;
                    break;
                case "print":
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var cliente = "{{ $persona }}"; if(!cliente) cliente = "todos";
                    var estado = "{{ $estado }}"; if(!estado) estado = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var registro = "{{ route('pdf-sell-client') }}";
                    var ruta = registro+"/"+id+"/"+cliente+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2;
                    window.open(ruta);
                    break;
                default:
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var cliente = "{{ $persona }}"; if(!cliente) cliente = "todos";
                    var estado = "{{ $estado }}"; if(!estado) estado = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('sell-client') }}";
                    var orden = e;

                    var ruta = registro+"/"+cantidad+"/"+id+"/"+cliente+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
            }
        }
    </script>

    <div class="row justify-content-center my-3 px-2">
        @php
            if($persona)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(
                "table-id" => "ventas-cliente",
                "title" => "Presiona sobre la fila para ver el detallado de la venta.",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "ID",
                        "bd-name" => "id",
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
                        "nombre" => "Fecha",
                        "bd-name" => "fecha",
                    ),
                    array(
                        "nombre" => "Crédito",
                        "bd-name" => "credito",
                    ),
                    array(
                        "nombre" => "Estado",
                        "bd-name" => "pendiente",
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
                "estado-6" => true,
            );

            foreach ($ventas as $sell) {
                $data_content["id"] = $sell->id;
                $data_content["dato-1"] = $sell->id;
                $data_content["dato-2"] = $sell->cliente->nombre;
                $data_content["dato-3"] = $sell->monto." Bs";
                $data_content["dato-4"] = $sell->fecha;
                $data_content["dato-5"] = $sell->credito." días";

                if(!$sell->pendiente){
                    if( strtotime($sell->fecha."+ ".$sell->credito." days") - strtotime(date("d-m-Y")) > 3*86400)
                        $data_content["estado-6"] = "Pendiente";
                    else{
                        if( strtotime($sell->fecha."+ ".$sell->credito." days") - strtotime(date("d-m-Y")) > 0*86400)
                            $data_content["estado-6"] = "Por Caducar";
                        else
                            $data_content["estado-6"] = "Caducado";
                    }
                }
                else
                    $data_content["estado-6"] = "Pagado";

                array_push($data_list["content"],$data_content);
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $ventas->links() }}
        </nav>
    </div>

    <!-- MODAL PARA VER EL DETALLADO DE PRODUCTOS -->
    @php
        $data_modal = array(    
            "modal-id" => "venta-detail",
            "title" => "Venta Detallada",
        );
    @endphp
    @include('includes.detail_sell_buy',['data'=>$data_modal])

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
                        array_push($clients,$one_content);

                        foreach ($clientes as $cliente) {
                            $one_content["value"] = $cliente->id;
                            $one_content["nombre"] = $cliente->nombre;
                            array_push($clients,$one_content);
                        }

                        $data_form = array(
                            "action" => "",
                            "title" => "",
                            "form-id" => "form-list-venta",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Cliente",
                                    "icon" => "fa-book",
                                    "id_name" => "form-cliente",
                                    "form_name" => "id_cliente",
                                    "title" => "Selecciona un Cliente",
                                    "options" => $clients,
                                    "validate" => "Cliente es requerido",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Estado de la Venta",
                                    "icon" => "fa-info",
                                    "id_name" => "form-estado",
                                    "form_name" => "pendiente",
                                    "title" => "Selecciona un Estado",
                                    "options" => array(
                                        array(
                                            "value" => "todos",
                                            "nombre" => "Cualquier Estado de Venta",
                                        ),
                                        array(
                                            "value" => "1",
                                            "nombre" => "Ventas Pagadas",
                                        ),
                                        array(
                                            "value" => "0",
                                            "nombre" => "Ventas por Pagar",
                                        ),
                                    ),
                                    "validate" => "Estado es requerido",
                                    "requerido" => "req-true",
                                ),
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
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#ib").addClass("active");
            $("#ib").removeClass("icono_head");
            $(".ib").removeClass("icono_color");

        //ELIMINAR LOS BOTONES DE AGREGAR-ELIMINAR-FILTRAR-DESCARGAR
            $("#add-ventas-cliente").remove();
            $("#delete-ventas-cliente").remove();

        //ELIMINAR TODOS LOS CHECK
            $("#th-ventas-cliente").remove();
            $(".td-ventas-cliente").remove();

        //ABRIR MODAL PARA VER DETALLADO DE PRODUCTOS
            $(".tr-ventas-cliente").click(function() {
                var id = $(this).parent().attr("id"); //ID DEL VALOR QUE VAMOS A AGREGAR
                var url = "{{ route('products-client-venta') }}";
                var form = "div-product-data"; //ES EL DIV DE LOS PROUCTOS
                var form_data = "form-product-data"; //ES EL DIV DONDE ESTA EL APARTADO DE CADA PRODUCTO
                var spinner = "productos-spinner"; //ES EL SPINNER ANTES DEL MOSTRAR LOS PRODUCTOS

                //FORMATEO EL MODAL Y LO MUESTRO
                $("#"+form).addClass("d-none");
                $("#"+form_data).empty(); //BORRO EL CONTENIDO DONDE SALE LOS PRODUCTOS
                $("#"+spinner).removeClass("d-none");
                $('#venta-detail').modal(true);
                
                //AGREGO LOS PRODUCTOS DE DICHA COMPRA O VENTA Y FORMATEO EL MODAL
                ajaxDetailProducts(id,url,form,form_data,spinner);
            });

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
@endsection