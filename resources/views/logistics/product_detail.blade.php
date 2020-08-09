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

    <style>
        .table-buttons:hover {
            background-color: #028936;
            color: rgba(255,255,255,1);
            -webkit-transition: all 1s ease;
            -moz-transition: all 1s ease;
            -o-transition: all 1s ease;
            -ms-transition: all 1s ease;
            transition: all 1s ease;
        }

        .table-buttons {
            color: white;
            background-color: rgba(2,137,54,0.5);
        }

        .recetario-buttons:hover {
            background-color: rgba(0,0,0,0.4);
            color: rgba(255,255,255,1);
            -webkit-transition: all 1s ease;
            -moz-transition: all 1s ease;
            -o-transition: all 1s ease;
            -ms-transition: all 1s ease;
            transition: all 1s ease;
        }

        .recetario-buttons {
            color: #898989;
        }

        .border-detail {
            border-top: 1px solid #ced4da;
            border-left: 1px solid #ced4da;
        }

        .border-right{
            border-top-right-radius: 0px;
            border-bottom-right-radius: 0px;
        }

        .border-left{
            border-top-left-radius: 0px;
            border-bottom-left-radius: 0px;
        }
    </style>

    @if(session('message'))
        <h3 class="text-center alert alert-success">{{ session('message') }}</h3>
    @endif

    @if(session('status'))
        <h3 class="text-center alert alert-danger">{{ session('status') }}</h3>
    @endif
    
    <div class="text-left py-2" style="font-size: 1.2em; border-bottom: 1px solid black">
        INFORMACIÓN PRODUCTO - {{ strtoupper($product->nombre) }}
    </div>

    <div class="border">
        <!-- APARTADO INFORMACIÓN DE LA IZQUIERDA -->
        @php
            $categorias = array ();

            $one_category = array(
                "value" => 1,
                "nombre" => "Categoria Nombre",
            );

            foreach ($categories as $category) {
                $one_category["value"] = $category->id;
                $one_category["nombre"] = $category->nombre;
                array_push($categorias,$one_category);
            }

            $data_form = array(
                "action" => "edit-producto",
                "title" => "",
                "form-id" => "form-edit-producto",
                "edit-id" => $product->id,
                
                "form-components" => array(
                    array(
                        "component-type" => "input",
                        "label-name" => "Nombre del Producto",
                        "icon" => "fa-shopping-basket",
                        "type" => "text",
                        "id_name" => "form-name",
                        "form_name" => "nombre",
                        "placeholder" => "Ingrese el nombre del producto",
                        "validate" => "Nombre es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $product->nombre,
                    ),
                    array(
                        "component-type" => "textarea",
                        "label-name" => "Descripción del Producto",
                        "icon" => "fa-info",
                        "type" => "text",
                        "id_name" => "form-description",
                        "form_name" => "descripcion",
                        "placeholder" => "Ingrese la descripción del producto",
                        "validate" => "Descripción es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $product->descripcion,
                    ),
                    array(
                        "component-type" => "select",
                        "label-name" => "Categoría del Producto",
                        "icon" => "fa-list-alt",
                        "id_name" => "form-categoria",
                        "form_name" => "id_categoria",
                        "title" => "Selecciona una Categoría",
                        "options" => $categorias,
                        "validate" => "Categoría es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $product->id_categoria,
                    ),
                ),
            );
        @endphp
        
        <!-- APARTADO INFORMACION DE LA DERECHA -->
        @php
            $recetario = array();

            $one_product = array(
                "id" => 1,
                "name" => "Pera",
                "cantidad" => "10.56",
            );

            foreach ($product->receta as $receta) {
                $one_product["id"] = $receta->ingrediente->id;
                $one_product["name"] = $receta->ingrediente->nombre;
                $one_product["cantidad"] = $receta->cantidad;
                array_push($recetario,$one_product);
            }

            $data_list = array(
                array(
                    "table-id" => "lista-recetario",
                    "icon" => "fa-list-alt",
                    "type" => "inline-info",
                    "title" => "",
                    "recetario" => $recetario,
                ),
            );

            $data_products = array();

            $one_product = array(
                "value" => 1,
                "nombre" => "Producto Nombre",
            );

            foreach ($productos as $product) {
                $one_product["value"] = $product->id;
                $one_product["nombre"] = $product->nombre;
                array_push($data_products,$one_product);
            }

            $title = "Datos del Producto";
        @endphp
        @include('includes.general_detail',['data'=>$data_form, 'data_list'=>$data_list, 'title'=>$title])
    </div>
    <input type="hidden" id="ruta-receta" value="{{ route('edit-receta') }}">

@endsection

@section('scripts')
    <script src="{{ asset('js/form_validate.js') }}"></script>
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
        $("#iv").addClass("active");
        $("#iv").removeClass("icono_head");
        $(".iv").removeClass("icono_color");
        
        //VOLVER A LA VISTA QUE ESTABAMOS ANTES
        const retroceder = () => {
            location.href = "{{ url()->previous() }}";
        }
    </script>
@endsection