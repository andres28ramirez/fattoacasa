@extends('layouts.principal')

@section('title','Ventas · Fatto a Casa')

@section('titulo','FATTO A CASA - CUENTAS POR COBRAR')

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
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-cuentas')}}">Cuentas por Cobrar</a>
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
                            var cliente = $('#form-list-ventas select[id="form-cliente"] option:selected').val();

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
                            var registro = "{{ route('list-cuentas') }}";
                            var orden = "{{$order}}";
                            
                            var ruta = registro+"/"+cantidad+"/"+id+"/"+cliente+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                            
                            if(id && cliente && tiempo){
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

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = $('select[id="num-register-venta-pagar"] option:selected').val();
                    var registro = "{{ route('list-cuentas') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+id+"/"+cliente+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('list-cuentas') }}";
                    location.href = registro;
                    break;
                case "print":
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var cliente = "{{ $persona }}"; if(!cliente) cliente = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var registro = "{{ route('pdf-ventas-cuentas') }}";
                    var ruta = registro+"/"+id+"/"+cliente+"/"+tiempo+"/"+fecha_1+"/"+fecha_2;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var cliente = "{{ $persona }}"; if(!cliente) cliente = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('list-cuentas') }}";
                    var orden = e;

                    var ruta = registro+"/"+cantidad+"/"+id+"/"+cliente+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
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
        
        <div class="col-12 d-none" id="referencia-alerta">
            <h3 class="text-center alert alert-danger">La referencia de pago ya se encuentra registrada!</h3>
        </div>

        @php
            if($persona)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(
                "table-id" => "venta-pagar",
                "title" => "Presiona sobre la fila para ver los productos de la venta.",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "ID - Venta",
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
                    array(
                        "nombre" => "Añadir",
                        "bd-name" => "añadir",
                    ),
                    array(
                        "nombre" => "Contactar",
                        "bd-name" => "correo",
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
                "estado-6" => false,
                "opciones-7" => true,
                "pago" => false,
                "desperdicio" => false,
                "contact-8" => "andresramirez2025@gmail.com",
            );

            foreach ($ventas as $sell) {
                $data_content["id"] = $sell->id;
                $data_content["dato-1"] = $sell->id;
                $data_content["dato-2"] = $sell->cliente->nombre;
                $data_content["dato-3"] = number_format($sell->monto,2, ",", ".")." Bs";
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

                $data_content["opciones-7"] = true;
                $data_content["pago"] = $sell->pendiente;
                $data_content["desperdicio"] = false;
                $data_content["contact-8"] = $sell->cliente->correo;

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
                            "form-id" => "form-list-ventas",
                            
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

    <!-- MODAL PARA AGREGAR EL PAGO -->
    <div class="modal fade" id="modal-add-pago" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="titulo">Agregar Pago a la Compra</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php
                        $data_form = array(
                            "action" => "save-venta-pago",
                            "title" => "",
                            "form-id" => "form-pay-venta",
                            "add-id" => "",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Banco del Pago",
                                    "icon" => "fa-home",
                                    "id_name" => "form-banco",
                                    "form_name" => "banco",
                                    "title" => "Selecciona un Banco",
                                    "options" => array(
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
                                    "component-type" => "input",
                                    "label-name" => "Fecha",
                                    "icon" => "fa-calendar",
                                    "type" => "date",
                                    "id_name" => "form-fecha-pago",
                                    "form_name" => "fecha_pago",
                                    "placeholder" => "Ingresa la Fecha del pago",
                                    "validate" => "Fecha es requerida",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Núm. Referencia del Pago (*)",
                                    "icon" => "fa-money",
                                    "type" => "text",
                                    "id_name" => "form-referencia",
                                    "form_name" => "referencia",
                                    "placeholder" => "Ingresa la referencia del pago",
                                    "validate" => "Referencia es requerida",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Nota de Pago",
                                    "icon" => "fa-bookmark",
                                    "type" => "text",
                                    "id_name" => "form-nota-pago",
                                    "form_name" => "nota_pago",
                                    "placeholder" => "Ingrese la nota de pago",
                                    "validate" => "Nota es requerida",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                            ),
                        );
                    @endphp
                    @include('includes.general_form',['data'=>$data_form])
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA VER EL DETALLADO DE PRODUCTOS -->
    @php
        $data_modal = array(    
            "modal-id" => "venta-detail",
            "title" => "Productos de la Venta",
        );
    @endphp
    @include('includes.detail_sell_buy',['data'=>$data_modal])

@endsection

@section('scripts')
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#ip").addClass("active");
            $("#ip").removeClass("icono_head");
            $(".ip").removeClass("icono_color");

        //ELIMINAR LOS BOTONES DE AGREGAR-ELIMINAR-FILTRAR-DESCARGAR
            $("#add-venta-pagar").remove();
            $("#delete-venta-pagar").remove();

        //ELIMINAR TODOS LOS CHECK
            $("#th-venta-pagar").remove();
            $(".td-venta-pagar").remove();

        //ABRIR MODAL PARA VER DETALLADO DE PRODUCTOS
            $(".tr-venta-pagar").click(function() {
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

        //EVENTOS DE LOS PAGOS PARA MOSTRAR SU MODAL
            $(".pago-add").click(function() {
                var id = $(this).attr("id"); //ID DEL VALOR QUE VAMOS A AGREGAR
                $("#add-id-form-pay-venta").val(id);
                $('#modal-add-pago #titulo').text("Agregar Pago a la Venta - Cod ("+id+")");
                $('#modal-add-pago').modal(true);
            });

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
        
        //ALERTA PARA CUANDO LA REFERENCIA DE PAGO ESTE DUPLICADA
            if ( $(".invalid-feedback").length > 0 ) {
                $("#referencia-alerta").removeClass("d-none");
            }

        //EVENTO PARA EL CORREO DE CONTACTAR A CLIENTE
            function Enviarinformacion(accion,correo){
                $.ajax(
                    {
                        type: "POST",
                        url:"{{ url('mail/send') }}"+accion,
                        data: {
                            "_token": $("meta[name='csrf-token']").attr("content")
                        },
                        beforeSend: function(){
                            swal({
                                title: "Enviando Correo, porfavor espere...",
                                closeOnClickOutside: false,
                                button: 'Aceptar',
                            });
                            $(".swal-title").prepend('<i class="fa fa-3x fa-lg fa-spinner fa-spin d-block p-4" style="color: #028936"></i>');
                            $(".swal-button").addClass("bg-secondary");
                        },                
                        success: function(msg){
                            console.log(msg);
                            swal({
                                title: 'Se ha enviado el correo de forma exitosa a ' + correo,
                                icon: 'success',
                                closeOnClickOutside: false,
                                button: 'Aceptar',
                            });
                            $(".swal-button--confirm").addClass('bg-success');
                        },
                        error: function(){ 
                            swal({
                                title: 'No se ha podido enviar el correo, intentelo más tarde...',
                                icon: 'error',
                                closeOnClickOutside: false,
                                button: 'Aceptar',
                            });
                            $(".swal-button--confirm").addClass('bg-danger');
                        },
                    }
                );
            }
            
            $(".email-send").click(function() {
                var correo = $(this).children("input").val();
                Enviarinformacion('/'+$(this).parent().attr("id"),correo);
                /* swal({
                    title: 'Se ha enviado el correo de forma exitosa a ' + correo,
                    icon: 'success',
                    closeOnClickOutside: false,
                    button: 'Aceptar',
                });
                $(".swal-button--confirm").addClass('bg-success'); */
                //alert('Se ha enviado el correo a ' + correo);
            });
    </script>
@endsection