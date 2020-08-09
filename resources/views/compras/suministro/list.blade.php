@extends('layouts.principal')

@section('title','Compras · Fatto a Casa')

@section('titulo','PROVEEDORES DE LA EMPRESA')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-compras')}}">Listado de Compras</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('suministros')}}">Suministros</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('cpp')}}">Cuentas por Pagar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('cp')}}">Pagos Realizados</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('discard-compras')}}">Compras Descartadas</a>
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
                    $("#submit-form-list-provider").unbind('click').click(function(event){
                        $("#form-list-provider").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var cantidad = "{{$registros}}";
                            var zona = $('#form-list-provider select[id="form-zone"] option:selected').val();
                            var persona = $('#form-list-provider select[id="tipo_cid"] option:selected').val();
                            var registro = "{{ route('suministros') }}";
                            var orden = "{{$order}}";
                            if(zona && persona)
                                location.href = registro+"/"+cantidad+"/"+zona+"/"+persona+"/"+orden;
                            return false;
                        });
                    });
                    break;
                case "registros":
                    var cantidad = $('select[id="num-register-compras-sum"] option:selected').val();
                    var zona = "{{$id_zona}}"; if(!zona) zona = "todos";
                    var persona = "{{$persona}}"; if(!persona) persona = "todos";
                    var registro = "{{ route('suministros') }}";
                    var orden = "{{$order}}";
                    location.href = registro+"/"+cantidad+"/"+zona+"/"+persona+"/"+orden;
                    break;
                case "refresh":
                    var registro = "{{ route('suministros') }}";
                    location.href = registro;
                    break;
                case "print":
                    var zona = "{{$id_zona}}"; if(!zona) zona = "todos";
                    var persona = "{{$persona}}"; if(!persona) persona = "todos";
                    var registro = "{{ route('pdf-suministros') }}";
                    var ruta = registro+"/"+zona+"/"+persona;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var cantidad = "{{$registros}}";
                    var zona = "{{$id_zona}}"; if(!zona) zona = "todos";
                    var persona = "{{$persona}}"; if(!persona) persona = "todos";
                    var registro = "{{ route('suministros') }}";
                    var orden = e;
                    location.href = registro+"/"+cantidad+"/"+zona+"/"+persona+"/"+orden;
                    break;
            }
        }
    </script>

    <div class="row justify-content-center my-3 px-2">
        @php
            if($id_zona || $persona)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(
                
                "table-id" => "compras-sum",
                "title" => "Información de Proveedores, presione sobre la fila para ver al detalle sus productos y precios",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "Proveedor",
                        "bd-name" => "nombre",
                    ),
                    array(
                        "nombre" => "Zona",
                        "bd-name" => "id_zona",
                    ),
                    array(
                        "nombre" => "Dirección",
                        "bd-name" => "direccion",
                    ),
                    array(
                        "nombre" => "Teléfono",
                        "bd-name" => "telefono",
                    ),
                    array(
                        "nombre" => "Correo",
                        "bd-name" => "correo",
                    ),
                    array(
                        "nombre" => "CI/RIF",
                        "bd-name" => "rif_cedula",
                    ),
                ),

                "content" => array(),
            );

            $data_provider = array(
                "id" => 1,
                "dato-1" => "Leonardo Guilarte",
                "dato-2" => "Porlamar",
                "dato-3" => "Calle San Juan Casa 110-15",
                "dato-4" => "04120950165",
                "dato-5" => "leomiguel1907@gmail.com",
                "dato-6" => "268427456",
            );

            foreach ($providers as $provider) {
                $data_provider["id"] = $provider->id;
                $data_provider["dato-1"] = $provider->nombre;
                $data_provider["dato-2"] = $provider->zona->nombre;
                $data_provider["dato-3"] = $provider->direccion;
                $data_provider["dato-4"] = $provider->telefono;
                $data_provider["dato-5"] = $provider->correo;
                $data_provider["dato-6"] = $provider->tipo_cid."".$provider->rif_cedula;

                array_push($data_list["content"],$data_provider);
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $providers->links() }}
        </nav>
    </div>

    <!-- MODAL PARA VER EL DETALLADO DE PRODUCTOS -->
    <div class="modal fade" id="listado-productos" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="titulo">Listado de Productos del Proveedor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="m-auto text-center col-12 py-5" id="productos-spinner">
                        <i class="fa fa-5x fa-lg fa-spinner fa-spin" style="color: #028936"></i>
                    </div>
                    <!-- INFORMACIÓN PRODUCTO A PRODUCTO -->
                    <div class="form-row justify-content-center" id="div-product-data">
                        <div class="col-12 p-2" >
                            <div class="input-group row justify-content-center">
                                <div class="col-6">
                                    <!-- PRODUCTO -->
                                    <strong>Producto:</strong>
                                </div>
                                <div class="col">
                                    <!-- PRECIO -->
                                    <strong>Precio:</strong>
                                </div>
                            </div>                          
                        </div>
                        
                        <div id="form-product-data" class="col-12">
                        </div>  
                    </div>
                </div>
            </div>
        </div>
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

                        $zonas = array ();
                        $one_zone = array(
                            "value"=>"todos",
                            "nombre"=>"Todas las zonas"
                        );
                        array_push($zonas,$one_zone);
                        foreach ($zones as $zone) {
                            $one_zone["value"] = $zone->id;
                            $one_zone["nombre"] = $zone->nombre;
                            array_push($zonas,$one_zone);
                        }

                        $data_form = array(
                            "action" => "",
                            "title" => "",
                            "form-id" => "form-list-provider",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Zona",
                                    "icon" => "fa-map-pin",
                                    "id_name" => "form-zone",
                                    "form_name" => "id_zona",
                                    "title" => "Selecciona una zona",
                                    "options" => $zonas,
                                    "validate" => "Zona es requerida",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Tipo de Proveedor",
                                    "icon" => "fa-book",
                                    "id_name" => "tipo_cid",
                                    "form_name" => "tipo_cid",
                                    "title" => "Selecciona un Tipo",
                                    "options" => array(
                                        array(
                                            "value" => "todos",
                                            "nombre" => "Todos los tipos de Proveedores",
                                        ),
                                        array(
                                            "value" => "V -",
                                            "nombre" => "Persona Natural (Venezolano)",
                                        ),
                                        array(
                                            "value" => "E -",
                                            "nombre" => "Persona Natural (Extranjero)",
                                        ),
                                        array(
                                            "value" => "J -",
                                            "nombre" => "Personalidad Jurídica",
                                        ),
                                    ),
                                    "validate" => "Tipo es requerido",
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

@endsection

@section('scripts')
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#ic").addClass("active");
            $("#ic").removeClass("icono_head");
            $(".ic").removeClass("icono_color");

        //ELIMINAR LOS BOTONES DE AGREGAR-ELIMINAR-FILTRAR-DESCARGAR
            $("#delete-compras-sum").remove();
            $("#add-compras-sum").remove();

        //ELIMINAR TODOS LOS CHECK
            $("#th-compras-sum").remove();
            $(".td-compras-sum").remove();

        //ABRIR MODAL PARA VER DETALLADO DE PRODUCTOS
            $(".tr-compras-sum").click(function() {
                var id = $(this).parent().attr("id"); //ID DEL VALOR QUE VAMOS A AGREGAR
                var url = "{{ route('prov-products') }}";
                var form = "div-product-data"; //ES EL DIV DE LOS PROUCTOS
                var form_data = "form-product-data"; //ES EL DIV DONDE ESTA EL APARTADO DE CADA PRODUCTO
                var spinner = "productos-spinner"; //ES EL SPINNER ANTES DEL MOSTRAR LOS PRODUCTOS

                //FORMATEO EL MODAL Y LO MUESTRO
                $("#"+form).addClass("d-none");
                $("#"+form_data).empty(); //BORRO EL CONTENIDO DONDE SALE LOS PRODUCTOS
                $("#"+spinner).removeClass("d-none");
                $('#listado-productos').modal(true);
                
                //AGREGO LOS PRODUCTOS DE DICHA COMPRA O VENTA Y FORMATEO EL MODAL
                ajaxDetailSuministroProducts(id,url,form,form_data,spinner);
            });

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");

        //EVENTO PARA EL CORREO DE CONTACTAR A PROVEEDOR
            $(".email-send").click(function() {
                var correo = $(this).children("input").val();
                alert(correo);
            });
    </script>
@endsection