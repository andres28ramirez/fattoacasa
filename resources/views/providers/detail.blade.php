@extends('layouts.principal')

@section('title','Proveedores · Fatto a Casa')

@section('titulo','DETALLADO - PROVEEDOR')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-prov')}}">Listado</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('agg-prov') }}">Añadir</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('buy-prov') }}">Compras Realizadas</a>
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

        .border-detail {
            border-top: 1px solid #ced4da;
            border-left: 1px solid #ced4da;
        }
    </style>

    @if(session('message'))
        <h3 class="text-center alert alert-success">{{ session('message') }}</h3>
    @endif

    @if(session('status'))
        <h3 class="text-center alert alert-danger">{{ session('status') }}</h3>
    @endif

    <div class="text-left py-2" style="font-size: 1.2em; border-bottom: 1px solid black">
        INFORMACIÓN DE {{ strtoupper($provider->nombre) }}
    </div>

    <div class="border">
        <!-- APARTADO INFORMACIÓN DE LA IZQUIERDA -->
        @php
            $zonas = array ();

            $one_zone = array(
                "value" => 1,
                "nombre" => "Hatillo",
            );

            foreach ($zones as $zone) {
                $one_zone["value"] = $zone->id;
                $one_zone["nombre"] = $zone->nombre;
                array_push($zonas,$one_zone);
            }

            $data_form = array(
                "action" => "edit-prov",
                "title" => "",
                "form-id" => "form-provider",
                "edit-id" => $provider->id,
                
                "form-components" => array(
                    array(
                        "component-type" => "input",
                        "label-name" => "Nombre",
                        "icon" => "fa-users",
                        "type" => "text",
                        "id_name" => "form-user",
                        "form_name" => "nombre",
                        "placeholder" => "Ingrese el nombre del proveedor",
                        "validate" => "Nombre es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $provider->nombre,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Persona Contacto",
                        "icon" => "fa-user",
                        "type" => "text",
                        "id_name" => "form-person",
                        "form_name" => "persona_contacto",
                        "placeholder" => "Ingrese la persona a contactar",
                        "validate" => "Contacto es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $provider->persona_contacto,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "CI/RIF",
                        "icon" => "fa-id-card",
                        "type" => "text",
                        "id_name" => "form-cid",
                        "form_name" => "rif_cedula",
                        "placeholder" => "Ingrese la cédula o RIF",
                        "validate" => "CI/RIF es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $provider->rif_cedula,
                        "value_tipo" => $provider->tipo_cid,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Teléfono",
                        "icon" => "fa-phone",
                        "type" => "text",
                        "id_name" => "form-phone",
                        "form_name" => "telefono",
                        "placeholder" => "Ingrese el teléfono",
                        "validate" => "Teléfono es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $provider->telefono,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Correo",
                        "icon" => "fa-at",
                        "type" => "text",
                        "id_name" => "form-email",
                        "form_name" => "correo",
                        "placeholder" => "Ingrese el correo",
                        "validate" => "Correo es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $provider->correo,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Dirección",
                        "icon" => "fa-map-marker",
                        "type" => "text",
                        "id_name" => "form-direction",
                        "form_name" => "direccion",
                        "placeholder" => "Ingrese la dirección",
                        "validate" => "Dirección es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $provider->direccion,
                    ),
                    array(
                        "component-type" => "select",
                        "label-name" => "Zona",
                        "icon" => "fa-map-pin",
                        "id_name" => "form-zone",
                        "form_name" => "id_zona",
                        "title" => "Selecciona una zona",
                        "options" => $zonas,
                        "validate" => "Zona es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $provider->id_zona,
                    ),
                ),
            );
        @endphp
        
        <!-- APARTADO INFORMACION DE LA DERECHA -->
        @php
            $data_table_1 = array();
            $data = array("id", "dato-1", "dato-2");
            foreach ($suministro as $product) {
                $data["id"] = $product->id;
                $data["dato-1"] = $product->producto->nombre;
                $data["dato-2"] = $product->precio;
                array_push($data_table_1,$data);
            }

            $data_table_2 = array();
            $data = array("id", "dato-1", "dato-2", "dato-3", "agenda-4");
            foreach ($eventos as $reunion) {
                $data["id"] = $reunion->id;
                $data["dato-1"] = $reunion->start;
                $data["dato-2"] = $reunion->descripcion;
                $data["dato-3"] = $reunion->title;
                $data["agenda-4"] = $reunion->activo;
                array_push($data_table_2,$data);
            }

            $data_list = array(
                array(
                    "table-id" => "lista-productos",
                    "title" => "Productos que ofrece el Proveedor",
                    "hide-options" => true,
                    "icon" => "fa-shopping-basket",
                    "type" => "table",
                    "titulos" => array(
                        "Producto",
                        "Precio",
                    ),
                    "content" => $data_table_1,
                ),
                array(
                    "table-id" => "agenda-proveedores",
                    "title" => "Agenda con el Proveedor",
                    "hide-options" => true,
                    "icon" => "fa-calendar",
                    "type" => "table",
                    "titulos" => array(
                        "Fecha",
                        "Descripción",
                        "Tipo",
                        "Estado",
                    ),
                    "content" => $data_table_2,
                ),
            );

            $title = "Datos del Proveedor";
        @endphp
        @include('includes.general_detail',['data'=>$data_form, 'data_list'=>$data_list, 'title'=>$title])
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/form_validate.js') }}"></script>
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#ich").addClass("active");
            $("#ich").removeClass("icono_head");
            $(".ich").removeClass("icono_color");
        
        //VOLVER A LA VISTA QUE ESTABAMOS ANTES
            const retroceder = () => {
                location.href = "{{ url()->previous() }}";
            }
    </script>
@endsection