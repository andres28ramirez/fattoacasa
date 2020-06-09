@extends('layouts.principal')

@section('title','Compras · Fatto a Casa')

@section('titulo','FATTO A CASA - COMPRAS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-compras')}}">Listado de Compras</a>
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
    </ul>
@endsection

@section('info')

    <!-- SCRIPT PARA REDIRECCIÓN BOTONES DE LA TABLA -->
    <script>
        function redirect_table(e){
            switch (e) {
                case "agregar":
                    location.href = "{{ route('agg-compra') }}";
                    break;
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
                            var registro = "{{ route('list-compras') }}";
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
                case "eliminar":
                    // aqui reviso los campos que estan en check y tomo su ID
                    var table = "compra-proveedor";
                    var url = "{{ route('delete-compras') }}";
                    var report_url = "{{ route('report-error') }}";
                    var compras = new Array();
                    $("#check-compra-proveedor .check-data").each(function( index ) {
                        if ($(this).prop('checked') == true){
                            compras.push($(this).val());
                        }
                    });

                    if(compras.length > 0){
                        swal({
                            title: "Eliminar registros",
                            text: "¿Esta seguro de eliminar las compras seleccionados?",
                            icon: "warning",
                            buttons: ["Cancelar","Aceptar"],
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                ajaxDelete(compras,url,table,report_url);
                            }
                        });
                    }
                    break;
                case "registros":
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var proveedor = "{{ $persona }}"; if(!proveedor) proveedor = "todos";
                    var estado = "{{ $estado }}"; if(!estado) estado = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = $('select[id="num-register-compra-proveedor"] option:selected').val();
                    var registro = "{{ route('list-compras') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+id+"/"+proveedor+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('list-compras') }}";
                    location.href = registro;
                    break;
                case "print":
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var proveedor = "{{ $persona }}"; if(!proveedor) proveedor = "todos";
                    var estado = "{{ $estado }}"; if(!estado) estado = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var registro = "{{ route('pdf-compras') }}";
                    var ruta = registro+"/"+id+"/"+proveedor+"/"+estado+"/"+tiempo+"/"+fecha_1+"/"+fecha_2;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var id = "{{ $id }}"; if(!id) id = "todos";
                    var proveedor = "{{ $persona }}"; if(!proveedor) proveedor = "todos";
                    var estado = "{{ $estado }}"; if(!estado) estado = "todos";

                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('list-compras') }}";
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
                "table-id" => "compra-proveedor",
                "title" => "Presiona sobre la fila para ver el detallado de la compra.",
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
                        "nombre" => "Estado",
                        "bd-name" => "pendiente",
                    ),
                    array(
                        "nombre" => "Añadir",
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
                "estado-6" => true,
                "opciones-7" => true,
                "pago" => true,
                "desperdicio" => true,
            );

            foreach ($compras as $buy) {
                $data_content["id"] = $buy->id;
                $data_content["dato-1"] = $buy->id;
                $data_content["dato-2"] = $buy->proveedor->nombre;
                $data_content["dato-3"] = $buy->monto." Bs";
                $data_content["dato-4"] = $buy->fecha;
                $data_content["dato-5"] = $buy->credito." días";

                if(!$buy->pendiente){
                    if( strtotime($buy->fecha."+ ".$buy->credito." days") - strtotime(date("d-m-Y")) > 3*86400)
                        $data_content["estado-6"] = "Pendiente";
                    else{
                        if( strtotime($buy->fecha."+ ".$buy->credito." days") - strtotime(date("d-m-Y")) > 0*86400)
                            $data_content["estado-6"] = "Por Caducar";
                        else
                            $data_content["estado-6"] = "Caducado";
                    }
                }
                else
                    $data_content["estado-6"] = "Pagado";

                $data_content["opciones-7"] = true;
                $data_content["pago"] = $buy->pendiente;
                $data_content["desperdicio"] = true;

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
                            "action" => "save-compra-pago",
                            "title" => "",
                            "form-id" => "form-pay-compra",
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
                                    "label-name" => "Núm. Referencia del Pago",
                                    "icon" => "fa-money",
                                    "type" => "text",
                                    "id_name" => "form-referencia",
                                    "form_name" => "referencia",
                                    "placeholder" => "Ingresa la referencia del pago",
                                    "validate" => "Referencia es requerida",
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

    <!-- MODAL PARA AGREGAR EL DESPERDICIO -->
    <div class="modal fade" id="modal-add-desperdicio" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="titulo">Agregar Desperdicio a la Compra</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="m-auto text-center col-12 py-5" id="desperdicio-spinner">
                        <i class="fa fa-5x fa-lg fa-spinner fa-spin" style="color: #028936"></i>
                    </div>
                    <div class="d-none" id="form-desperidico-data">
                        <h6 class="d-block text-center">Puedes editar el desperdicio modificandolo directamente los valores dados</h6>
                        <form method="POST" action="" class="validate-form" id="desperdicio-add">
                        @csrf
                            <!-- INFORMACIÓN DE LOS PRODUCTOS E LA COMPRA O VENTA -->
                            <div class="form-row justify-content-center">
                                <div class="col-12 p-2" >
                                    <div class="input-group row justify-content-center">
                                        <div class="col">
                                            <!-- PRODUCTO -->
                                            <strong>Producto:</strong>
                                        </div>
                                        <div class="col">
                                            <!-- PRECIO -->
                                            <strong>Cantidad (Kg/Und):</strong>
                                        </div>
                                        <div class="col">
                                            <!-- DESPERDICIO -->
                                            <strong>Desperdicio:</strong>
                                        </div> 
                                    </div>                        
                                </div>

                                <!-- APARTADO DE LOS PRODUCTOS UNO A UNO -->
                                <div id="form-product-data" class="col-12">
                                </div>
                            </div>

                            <div class="form-row btn-submit">
                                <div class="m-auto"> 
                                    <button class="form-btn">Editar</button>
                                </div>
                            </div> 
                        </form>
                        <input type="hidden" id="desperdicio-form" value="">
                    </div>
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

        //ABRIR MODAL PARA VER DETALLADO DE PRODUCTOS
            $(".tr-compra-proveedor").click(function() {
                var id = $(this).parent().attr("id");
                var url = "{{ route('detail-compra') }}";
                location.href = url+"/"+id;
            });

        //EVENTOS DE LOS PAGOS PARA MOSTRAR SU MODAL
            $(".pago-add").click(function() {
                var id = $(this).attr("id"); //ID DEL VALOR QUE VAMOS A AGREGAR
                $("#add-id-form-pay-compra").val(id);
                $('#modal-add-pago #titulo').text("Agregar Pago a la Compra - Cod ("+id+")");
                $('#modal-add-pago').modal(true);
            });

        //EVENTO DEL DESPERDICIO
            $(".desperdicio-add").click(function() {
                var id = $(this).attr("id"); //ID DEL VALOR QUE VAMOS A AGREGAR
                var url = "{{ route('products-compra') }}";
                var form = "form-desperidico-data"; //ES EL DIV DEL FORMULARIO
                var form_data = "form-product-data"; //ES EL DIV DONDE ESTA EL APARTADO DE CADA PRODUCTO
                var spinner = "desperdicio-spinner"; //ES EL SPINNER ANTES DEL FORMULARIO
                $("#desperdicio-form").val(id);
                $('#modal-add-desperdicio #titulo').text("Agregar Desperdicio a la Compra - Cod ("+id+")");

                //FORMATEO EL MODAL Y LO MUESTRO
                $("#"+form).addClass("d-none");
                $("#"+form_data).empty(); //BORRO EL CONTENIDO DONDE SALE LOS PRODUCTOS
                $("#"+spinner).removeClass("d-none");
                $('#modal-add-desperdicio').modal(true);
                
                //AGREGO LOS PRODUCTOS DE DICHA COMPRA Y FORMATEO EL MODAL
                ajaxDesperdicio(id,url,form,form_data,spinner);
            });

            $('#desperdicio-add').on('submit',function(){
                var id = parseInt($("#desperdicio-form").val(), 10); //ID DE LA COMPRA QUE ANEXAREMOS DESPERDICIO
                var url = "{{ route('save-desperdicio') }}";
                var report_url = "{{ route('report-error') }}";
                ajaxUpdateDesperdicio(id,url,report_url);
                return false;
            });

            const verificar = (e) => {
                var valor_actual = parseFloat($("#form-desperdicio-"+e).val());
                var valor_maximo = parseFloat($("#form-cantidad-"+e).val());
                if(valor_maximo < valor_actual){
                    swal({
                        title: 'El desperdicio no puede ser mayor!',
                        icon: 'warning',
                        closeOnClickOutside: false,
                        button: 'Aceptar',
                    });
                    $(".swal-button--confirm").addClass('bg-success');
                    $("#form-desperdicio-"+e).val("0");
                }
                else if(valor_actual < 0){
                    swal({
                        title: 'El desperdicio no puede ser menor a 0!',
                        icon: 'warning',
                        closeOnClickOutside: false,
                        button: 'Aceptar',
                    });
                    $(".swal-button--confirm").addClass('bg-success');
                    $("#form-desperdicio-"+e).val("0");
                }
            }

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
@endsection