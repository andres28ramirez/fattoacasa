@extends('layouts.principal')

@section('title','Logística · Fatto a Casa')

@section('titulo','FATTO A CASA - INVENTARIO')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-inventario')}}">Inventario</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-suministro') }}">Suministro</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-producto') }}">Portafolio de Productos</a>
        </li>
    </ul>
@endsection

@section('info')

    <!-- SCRIPT PARA REDIRECCIÓN BOTONES DE LA TABLA -->
    <script>
        function redirect_table(e){
            switch (e) {
                case "agregar":
                    $('#table-agregar').modal(true);
                    break;
                case "filtrar":
                    $('#table-filter').modal(true);
                    //CAPTURAR EVENTO SUBMIT DE FILTRAR INFORMACIÓN
                    $("#submit-form-list-product").unbind('click').click(function(event){
                        $("#form-list-product").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var producto = $('#form-list-product input[id="form-name"]').val(); if(!producto) producto = "todos";
                            var tiempo = $('#form-list-product select[id="form-tiempo"] option:selected').val();
                            var fecha_1 = "todos";
                            var fecha_2 = "todos";
                            switch (tiempo) {
                                case "Específico":
                                    fecha_1 = $('#form-list-product input[id="form-fecha-1"]').val();
                                    fecha_2 = $('#form-list-product input[id="form-fecha-2"]').val();
                                    break;
                            }

                            var cantidad = "{{$registros}}";
                            var registro = "{{ route('list-inventario') }}";
                            var orden = "{{$order}}";
                            
                            var ruta = registro+"/"+cantidad+"/"+producto+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                            
                            if(producto && tiempo){
                                if(tiempo=="Específico"){
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
                    var table = "lista-inventario";
                    var url = "{{ route('delete-inventario') }}";
                    var report_url = "{{ route('report-error') }}";
                    var inventario = new Array();
                    $("#check-lista-inventario .check-data").each(function( index ) {
                        if ($(this).prop('checked') == true){
                            inventario.push($(this).val());
                        }
                    });

                    if(inventario.length > 0){
                        swal({
                            title: "Eliminar registros",
                            text: "¿Esta seguro de eliminar los productos seleccionados?",
                            icon: "warning",
                            buttons: ["Cancelar","Aceptar"],
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                ajaxDelete(inventario,url,table,report_url);
                            }
                        });
                    }
                    break;
                case "registros":
                    var producto = "{{ $producto_name }}"; if(!producto) producto = "todos";
                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = $('select[id="num-register-lista-inventario"] option:selected').val();
                    var registro = "{{ route('list-inventario') }}";
                    var orden = "{{$order}}";

                    var ruta = registro+"/"+cantidad+"/"+producto+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
                    location.href = ruta;
                    break;
                case "refresh":
                    var registro = "{{ route('list-inventario') }}";
                    location.href = registro;
                    break;
                case "print":
                    var producto = "{{ $producto_name }}"; if(!producto) producto = "todos";
                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var registro = "{{ route('pdf-list-inventario') }}";
                    var ruta = registro+"/"+producto+"/"+tiempo+"/"+fecha_1+"/"+fecha_2;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var producto = "{{ $producto_name }}"; if(!producto) producto = "todos";
                    var tiempo = "{{ $tiempo }}"; if(!tiempo) tiempo = "todos";
                    var fecha_1 = "{{ $fecha_1 }}"; if(!fecha_1) fecha_1 = "todos";
                    var fecha_2 = "{{ $fecha_2 }}"; if(!fecha_2) fecha_2 = "todos";

                    var cantidad = "{{$registros}}";
                    var registro = "{{ route('list-inventario') }}";
                    var orden = e;

                    var ruta = registro+"/"+cantidad+"/"+producto+"/"+tiempo+"/"+fecha_1+"/"+fecha_2+"/"+orden;
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
                @if(isset($id))
                    @foreach ($id as $value)
                        <h5>{{$value}}</h5>
                    @endforeach
                @endif
                <h3 class="text-center alert alert-danger">{{ session('status') }}</h3>
            </div>
        @endif

        @php
            if($tiempo)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(
                "table-id" => "lista-inventario",
                "title" => "Presione sobre el producto del inventario para editar sus valores",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "Producto",
                        "bd-name" => "id_producto",
                    ),
                    array(
                        "nombre" => "Precio",
                        "bd-name" => "precio",
                    ),
                    array(
                        "nombre" => "Cantidad",
                        "bd-name" => "cantidad",
                    ),
                    array(
                        "nombre" => "Fecha de Expiración",
                        "bd-name" => "expedicion",
                    ),
                ),
                "content" => array(),
            );

            $inv_danger = array();
            $inv_warning = array();

            $data_content = array(
                "id" => 4,
                "dato-1" => "Naranja Empaquetada",
                "dato-2" => "25.000 Bs",
                "dato-3" => "5",
                "dato-4" => "30-02-2020",
            );

            foreach ($inventario as $product) {
                $data_content["id"] = $product->id;
                $data_content["dato-1"] = $product->producto->nombre;
                $data_content["dato-2"] = number_format($product->precio,2, ",", ".")." Bs";
                $data_content["dato-3"] = $product->cantidad." Kg/Und";
                $data_content["dato-4"] = $product->expedicion ? $product->expedicion : "no posee";

                array_push($data_list["content"],$data_content);
                
                if($product->expedicion){
                    if( strtotime($product->expedicion) - strtotime(date("d-m-Y")) < 3*86400){
                        if( strtotime($product->expedicion) - strtotime(date("d-m-Y")) > 0*86400)
                            array_push($inv_warning,$product->id);
                        else
                            array_push($inv_danger,$product->id);
                    }
                }
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $inventario->links() }}
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
                            "form-id" => "form-list-product",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Parte o Nombre completo del Producto (Opc.)",
                                    "icon" => "fa-info",
                                    "type" => "text",
                                    "id_name" => "form-name",
                                    "form_name" => "nombre",
                                    "placeholder" => "Ingrese el nombre a buscar",
                                    "validate" => "Nombre es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-false",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Fecha de Expiración",
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
                                            "nombre" => "Todas las fechas",
                                        ),
                                        array(
                                            "value" => "sin fecha",
                                            "nombre" => "Sin fecha de Expiración",
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

    <!-- MODAL PARA AGREGAR LA TABLA -->
    <div class="modal fade" id="table-agregar" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="titulo">Agregar un Nuevo Producto al Inventario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php
                        $productos = array();

                        $one_option = array("value" => "id", "nombre" => "Producto X",);
                        foreach ($products as $one) {
                            $one_option["value"] = $one->id;
                            $one_option["nombre"] = $one->nombre;
                            array_push($productos,$one_option);
                        }

                        $data_form = array(
                            "action" => "save-inventario",
                            "title" => "",
                            "form-id" => "form-add-inv",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Producto",
                                    "icon" => "fa-list-alt",
                                    "id_name" => "form-producto",
                                    "form_name" => "id_producto",
                                    "title" => "Selecciona un producto",
                                    "options" => $productos,
                                    "validate" => "Producto es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Precio del Producto (Bs)",
                                    "icon" => "fa-money",
                                    "type" => "text",
                                    "id_name" => "form-price",
                                    "form_name" => "precio",
                                    "placeholder" => "Ingrese el precio del producto",
                                    "validate" => "Precio es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Cantidad del Producto (Kg / Und)",
                                    "icon" => "fa-archive",
                                    "type" => "number",
                                    "id_name" => "form-cantidad",
                                    "form_name" => "cantidad",
                                    "placeholder" => "Ingrese la Cantidad",
                                    "validate" => "Cantidad es requerida",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Fecha de Expiración",
                                    "icon" => "fa-calendar",
                                    "type" => "date",
                                    "id_name" => "form-fecha",
                                    "form_name" => "expedicion",
                                    "placeholder" => "",
                                    "validate" => "Fecha es requerida",
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

    <!-- MODAL PARA EDITAR ALGO DE INVENTARIO -->
    <div class="modal fade" id="table-editar" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="titulo">Editar la Información del Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php
                        $data_form = array(
                            "action" => "edit-inventario",
                            "title" => "",
                            "form-id" => "form-edit-inv",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Producto",
                                    "icon" => "fa-list-alt",
                                    "id_name" => "form-producto",
                                    "form_name" => "id_producto",
                                    "title" => "Selecciona un producto",
                                    "options" => $productos,
                                    "validate" => "Producto es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Precio del Producto (Bs)",
                                    "icon" => "fa-money",
                                    "type" => "text",
                                    "id_name" => "form-price",
                                    "form_name" => "precio",
                                    "placeholder" => "Ingrese el precio del producto",
                                    "validate" => "Precio es requerido",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Cantidad del Producto (Kg / Und)",
                                    "icon" => "fa-archive",
                                    "type" => "text",
                                    "id_name" => "form-cantidad",
                                    "form_name" => "cantidad",
                                    "placeholder" => "Ingrese la Cantidad",
                                    "validate" => "Cantidad es requerida",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Fecha de Expiración",
                                    "icon" => "fa-calendar",
                                    "type" => "date",
                                    "id_name" => "form-fecha",
                                    "form_name" => "expedicion",
                                    "placeholder" => "",
                                    "validate" => "Fecha es requerida",
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
            $("#iv").addClass("active");
            $("#iv").removeClass("icono_head");
            $(".iv").removeClass("icono_color");
        
        //MODAL PARA MODIFICAR LA CANTIDAD
            //Agrego el spinner para que exista
            html  = '<div class="m-auto text-center col-12 py-5" id="edit-spinner">';
            html +=    '<i class="fa fa-5x fa-lg fa-spinner fa-spin" style="color: #028936"></i>';
            html += '</div>';
            $("#form-edit-inv").before(html);
            //Agrego un input al formulario que llevara el id de edicion
            var html = '<input type="hidden" id="edit-suministro" name="inventario-cod" value="id-inventario">';
            $("#form-edit-inv").append(html);

            $(".tr-lista-inventario").click(function() {
                //Elimino el error si existe
                $("#error-edicion").remove();
                $("#form-edit-inv #form-producto").attr("disabled",true);
                var id = $(this).parent().attr("id"); //id del suministro
                var url = "{{ route('info-inventario') }}";
                var form = "form-edit-inv"; //ES EL ID DEL FORMULARIO
                var tipo = "inventario"; //PARA QUE EL AJAX ACOMODE EN SUMINISTRO
                var spinner = "edit-spinner"; //ES EL SPINNER ANTES DEL FORMULARIO

                //FORMATEO EL MODAL Y LO MUESTRO
                $("#"+form).addClass("d-none");
                $("#"+spinner).removeClass("d-none");
                $('#table-editar').modal(true);
                
                //AGREGO LOS PRODUCTOS DE DICHO SUMINISTRO Y FORMATEO EL MODAL
                ajaxEditLogistica(id,url,form,tipo,spinner);
            });

            $("#form-edit-inv #submit-form-edit-inv").click(function() {
                $("#form-edit-inv #form-producto").attr("disabled",false);
            });

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
        
        //BORRO EL TABLE STRIPED PARA PODER PONER BG WARNING Y BG DANGER POR EXPIRACION
            $("table").removeClass("table-striped");
        
        //MUESTRO LOS PRODUCTOS QUE ESTAN CERCA DE EXPIRAR CON EL SOMBRIADO EN LA TABLA
            @if(!empty($inv_danger))
                @foreach($inv_danger as $one)
                    $(".tr-lista-inventario").each(function() {
                        var id = $(this).parent().attr("id");
                        if(id == "{{$one}}")
                            $(this).parent().addClass("alert-danger");
                    });
                @endforeach
            @endif

            @if(!empty($inv_warning))
                @foreach($inv_warning as $one)
                    $(".tr-lista-inventario").each(function() {
                        var id = $(this).parent().attr("id");
                        if(id == "{{$one}}")
                            $(this).parent().addClass("alert-warning");
                    });
                @endforeach
            @endif
        
        //EVENTO QUE GENERA UN SWALERT SI FALTAN PRODUCTOS Y MUESTRO CUALES SON
            @if(session('fallo'))
                var html = 'Faltan los siguientes productos en suministro:';
                    @foreach(session('fallo') as $one)
                        html += '\n{{ $one["nombre"] }}: {{ $one["cantidad"] }} Kg/unidades';
                    @endforeach
                swal({
                    title: "El producto no pudo ser agregado",
                    text: html,
                    icon: 'error',
                    closeOnClickOutside: false,
                    button: 'Aceptar',
                });
            @endif

            @if(session('fallo-edit'))
                var html = 'Faltan los siguientes productos en suministro:';
                    @foreach(session('fallo-edit') as $one)
                        html += '\n{{ $one["nombre"] }}: {{ $one["cantidad"] }} Kg/unidades';
                    @endforeach
                swal({
                    title: "El producto no pudo ser modificado",
                    text: html,
                    icon: 'error',
                    closeOnClickOutside: false,
                    button: 'Aceptar',
                });
            @endif
    </script>
@endsection