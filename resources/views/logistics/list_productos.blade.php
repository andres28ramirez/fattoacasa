@extends('layouts.principal')

@section('title','Logística · Fatto a Casa')

@section('titulo','FATTO A CASA - PRODUCTOS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-inventario')}}">Inventario</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-suministro') }}">Suministro</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-producto') }}">Portafolio de Productos</a>
        </li>
    </ul>
@endsection

@section('info')
    
    <!-- SCRIPT PARA REDIRECCIÓN BOTONES DE LA TABLA -->
    <script>
        function redirect_table(e){
            switch (e) {
                case "agregar":
                    location.href = "{{ route('agg-producto') }}";
                    break;
                case "filtrar":
                    $('#table-filter').modal(true);
                    //CAPTURAR EVENTO SUBMIT DE FILTRAR INFORMACIÓN
                    $("#submit-form-list-product").unbind('click').click(function(event){
                        $("#form-list-product").on('submit',function(){
                            //Evaluar los valores que me llegan y hacer el location.href
                            var cantidad = "{{$registros}}";
                            var categoria = $('#form-list-product select[id="form-category"] option:selected').val();
                            var producto = $('#form-list-product input[id="form-name"]').val(); if(!producto) producto = "todos";
                            var registro = "{{ route('list-producto') }}";
                            var orden = "{{$order}}";
                            if(categoria)
                                location.href = registro+"/"+cantidad+"/"+categoria+"/"+producto+"/"+orden;
                            return false;
                        });
                    });
                    break;
                case "registros":
                    var cantidad = $('select[id="num-register-lista-productos"] option:selected').val();
                    var categoria = "{{$id_categoria}}"; if(!categoria) categoria = "todos";
                    var producto = "{{$nombre}}"; if(!producto) producto = "todos";
                    var registro = "{{ route('list-producto') }}";
                    var orden = "{{$order}}";
                    location.href = registro+"/"+cantidad+"/"+categoria+"/"+producto+"/"+orden;
                    break;
                case "refresh":
                    var registro = "{{ route('list-producto') }}";
                    location.href = registro;
                    break;
                case "print":
                    var categoria = "{{$id_categoria}}"; if(!categoria) categoria = "todos";
                    var producto = "{{$nombre}}"; if(!producto) producto = "todos";
                    var registro = "{{ route('pdf-list-producto') }}";
                    var ruta = registro+"/"+categoria+"/"+producto;
                    window.open(ruta);
                    break;
                default: //EL DEFAULT ES EL DE ORDENAR
                    var cantidad = "{{$registros}}";
                    var categoria = "{{$id_categoria}}"; if(!categoria) categoria = "todos";
                    var producto = "{{$nombre}}"; if(!producto) producto = "todos";
                    var registro = "{{ route('list-producto') }}";
                    var orden = e;
                    location.href = registro+"/"+cantidad+"/"+categoria+"/"+producto+"/"+orden;
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
            if($id_categoria || $nombre)
                $filtrado = true;
            else
                $filtrado = false;

            $data_list = array(
                "table-id" => "lista-productos",
                "title" => "Presione click sobre la fila para ver la receta del producto",
                "registros" => $registros,
                "filter" => $filtrado,
                "title-click" => $order,
                "titulos" => array(
                    array(
                        "nombre" => "ID Producto",
                        "bd-name" => "id",
                    ),
                    array(
                        "nombre" => "Nombre",
                        "bd-name" => "nombre",
                    ),
                    array(
                        "nombre" => "Descripción",
                        "bd-name" => "descripcion",
                    ),
                    array(
                        "nombre" => "Categoría",
                        "bd-name" => "id_categoria",
                    ),
                ),

                "content" => array(),
            );

            $data_content = array(
                "id" => 1,
                "dato-1" => "1",
                "dato-2" => "Bolsas",
                "dato-3" => "Bolsas de Kilogramo",
                "dato-4" => "Productos Frescos",
            );

            foreach ($products as $product) {
                $data_content["id"] = $product->id;
                $data_content["dato-1"] = $product->id;
                $data_content["dato-2"] = $product->nombre;
                $data_content["dato-3"] = $product->descripcion;
                $data_content["dato-4"] = $product->categoria->nombre;

                array_push($data_list["content"],$data_content);
            }
        @endphp
        @include('includes.general_table',['data'=>$data_list])
        <nav aria-label="..." class="pagination-table">
            {{ $products->links() }}
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
                        $categorias = array ();

                        $one_category = array(
                            "value" => "todos",
                            "nombre" => "Todas las Categorías",
                        );

                        array_push($categorias,$one_category);
                        foreach ($categories as $category) {
                            $one_category["value"] = $category->id;
                            $one_category["nombre"] = $category->nombre;
                            array_push($categorias,$one_category);
                        }

                        $data_form = array(
                            "action" => "",
                            "title" => "",
                            "form-id" => "form-list-product",
                            
                            "form-components" => array(
                                array(
                                    "component-type" => "select",
                                    "label-name" => "Por categoria",
                                    "icon" => "fa-list-alt",
                                    "id_name" => "form-category",
                                    "form_name" => "id_categoria",
                                    "title" => "Selecciona una Categoría o Todas",
                                    "options" => $categorias,
                                    "validate" => "Categoria es requerida",
                                    "bd-error" => "LO QUE SEA",
                                    "requerido" => "req-true",
                                ),
                                array(
                                    "component-type" => "input",
                                    "label-name" => "Parte o Nombre completo del Producto",
                                    "icon" => "fa-info",
                                    "type" => "text",
                                    "id_name" => "form-name",
                                    "form_name" => "nombre",
                                    "placeholder" => "Ingrese el nombre a buscar",
                                    "validate" => "Nombre es requerido",
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
            $("#iv").addClass("active");
            $("#iv").removeClass("icono_head");
            $(".iv").removeClass("icono_color");
        
        //ELIMINAR LOS BOTONES DE AGREGAR-ELIMINAR-FILTRAR-DESCARGAR
            $("#delete-lista-productos").remove();

        //ELIMINAR TODOS LOS CHECK
            $("#th-lista-productos").remove();
            $(".td-lista-productos").remove();

        //MODAL PARA MODIFICAR LA CANTIDAD
            $(".tr-lista-productos").click(function() {
                var id = $(this).parent().attr("id");
                var url = "{{ route('detail-producto') }}";
                location.href = url+"/"+id;
            });

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
@endsection