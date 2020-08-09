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
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $categorias = array ();

                $one_category = array(
                    "value" => 1,
                    "nombre" => "Prod.Fresco",
                );

                foreach ($categories as $category) {
                    $one_category["value"] = $category->id;
                    $one_category["nombre"] = $category->nombre;
                    array_push($categorias,$one_category);
                }

                $data_products = array();

                $one_product = array(
                    "value" => 1,
                    "nombre" => "Producto Nombre",
                );

                foreach ($products as $product) {
                    $one_product["value"] = $product->id;
                    $one_product["nombre"] = $product->nombre;
                    array_push($data_products,$one_product);
                }

                $data_form = array(
                    "action" => "save-producto",
                    "title" => "AGREGAR UN NUEVO PRODUCTO",
                    "form-id" => "form-producto",
                    "type" => "agregar",
                    
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
                        ),
                    ),
                );
            @endphp
            @include('includes.general_form',['data'=>$data_form])
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
        $("#iv").addClass("active");
        $("#iv").removeClass("icono_head");
        $(".iv").removeClass("icono_color");
    </script>
@endsection