@extends('layouts.principal')

@section('title','Clientes · Fatto a Casa')

@section('titulo','FATTO A CASA - CLIENTES')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-client')}}">Listado</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('agg-client') }}">Añadir</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('sell-client') }}">Ventas Realizadas</a>
        </li>
    </ul>
@endsection

@section('info')

    <!-- SCRIPT PARA REDIRECCIÓN BOTONES DE LA TABLA -->
    <script>
        function redirect_table(e){
            switch (e) {
                case "agregar":
                    location.href = "{{ route('agg-client') }}";
                    break;
                case "filtrar":
                    $('#table-filter').modal(true);
                    //CAPTURAR EVENTO SUBMIT DE FILTRAR INFORMACIÓN
                    $("#submit-form-list-client").unbind('click').click(function(event){
                        $("#form-list-client").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var cantidad = "{{$registros}}";
                            var zona = $('#form-list-client select[id="form-zone"] option:selected').val();
                            var persona = $('#form-list-client select[id="tipo_cid"] option:selected').val();
                            var registro = "{{ route('list-client') }}";
                            var orden = "{{$order}}";
                            if(zona && persona)
                                location.href = registro+"/"+cantidad+"/"+zona+"/"+persona+"/"+orden;
                            return false;
                        });
                    });
                    break;
                case "eliminar":
                    // aqui reviso los campos que estan en check y tomo su ID
                    var table = "lista-clientes";
                    var url = "{{ route('delete-client') }}";
                    var report_url = "{{ route('report-error') }}";
                    var clientes = new Array();
                    $("#check-lista-clientes .check-data").each(function( index ) {
                        if ($(this).prop('checked') == true){
                            clientes.push($(this).val());
                        }
                    });

                    if(clientes.length > 0){
                        swal({
                            title: "Eliminar registros",
                            text: "¿Esta seguro de eliminar los clientes seleccionados?",
                            icon: "warning",
                            buttons: ["Cancelar","Aceptar"],
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                ajaxDelete(clientes,url,table,report_url);
                            }
                        });
                    }
                    break;
                case "registros":
                    var cantidad = $('select[id="num-register-lista-clientes"] option:selected').val();
                    var zona = "{{$id_zona}}"; if(!zona) zona = "todos";
                    var persona = "{{$persona}}"; if(!persona) persona = "todos";
                    var registro = "{{ route('list-client') }}";
                    var orden = "{{$order}}";
                    location.href = registro+"/"+cantidad+"/"+zona+"/"+persona+"/"+orden;
                    break;
                case "refresh":
                    var registro = "{{ route('list-client') }}";
                    location.href = registro;
                    break;
                case "print":
                    var zona = "{{$id_zona}}"; if(!zona) zona = "todos";
                    var persona = "{{$persona}}"; if(!persona) persona = "todos";

                    var registro = "{{ route('pdf-client') }}";
                    var ruta = registro+"/"+zona+"/"+persona;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var cantidad = "{{$registros}}";
                    var zona = "{{$id_zona}}"; if(!zona) zona = "todos";
                    var persona = "{{$persona}}"; if(!persona) persona = "todos";
                    var registro = "{{ route('list-client') }}";
                    var orden = e;
                    location.href = registro+"/"+cantidad+"/"+zona+"/"+persona+"/"+orden;
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
                <h3 class="text-center alert alert-danger">{{ session('status') }}</h3>
            </div>
        @endif

        @php
            if($id_zona || $persona)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(          
                "table-id" => "lista-clientes",
                "title" => "LISTADO DE CLIENTES",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "Nombre",
                        "bd-name" => "nombre",
                    ),
                    array(
                        "nombre" => "P. Contacto",
                        "bd-name" => "persona_contacto",
                    ),
                    array(
                        "nombre" => "CI/RIF",
                        "bd-name" => "rif_cedula",
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
                        "nombre" => "Zona",
                        "bd-name" => "id_zona",
                    ),
                    array(
                        "nombre" => "Dirección",
                        "bd-name" => "direccion",
                    ),
                ),
                "content" => array(),
            );

            $data_content = array(
                "id" => 1,
                "dato-1" => "Leonardo Guilarte",
                "dato-2" => "Leonardo Guilarte",
                "dato-3" => "268427456",
                "dato-4" => "04120950165",
                "dato-5" => "leomiguel1907@gmail.com",
                "dato-6" => "Porlamar",
                "dato-7" => "Calle San Juan Casa 110-15",
            );

            foreach ($clients as $client) {
                $data_content["id"] = $client->id;
                $data_content["dato-1"] = $client->nombre;
                $data_content["dato-2"] = $client->persona_contacto;
                $data_content["dato-3"] = $client->tipo_cid."".$client->rif_cedula;
                $data_content["dato-4"] = $client->telefono;
                $data_content["dato-5"] = $client->correo;
                $data_content["dato-6"] = $client->zona->nombre;
                $data_content["dato-7"] = $client->direccion;

                array_push($data_list["content"],$data_content);
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $clients->links() }}
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
                            "form-id" => "form-list-client",
                            
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
            $("#ib").addClass("active");
            $("#ib").removeClass("icono_head");
            $(".ib").removeClass("icono_color");

        //REDIRECCIONAR AL DETALLADO DEL CLIENTE
            $(".tr-lista-clientes").click(function() {
                var id = $(this).parent().attr("id");
                var url = "{{ route('detail-client') }}";
                location.href = url+"/"+id;
            });

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();
        
        //ELIMINAR LOS BOTONES DE AGREGAR-ELIMINAR-FILTRAR-DESCARGAR
            $("#delete-lista-clientes").remove();
        
        //ELIMINAR TODOS LOS CHECK
            $("#th-lista-clientes").remove();
            $(".td-lista-clientes").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
@endsection