@extends('layouts.principal')

@section('title','Clientes · Fatto a Casa')

@section('titulo','AGREGAR - CLIENTES')

@section('tabs')
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-client')}}">Listado</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('agg-client') }}">Añadir</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('sell-client') }}">Ventas Realizadas</a>
        </li>
    </ul>
@endsection

@section('info')
    <div class="row justify-content-center my-3 px-2">
        <div class="col-lg col-md-10 col-sm-12">
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
                    "action" => "save-client",
                    "title" => "AGREGAR UN NUEVO CLIENTE",
                    "form-id" => "form-client",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "input",
                            "label-name" => "Nombre",
                            "icon" => "fa-users",
                            "type" => "text",
                            "id_name" => "form-user",
                            "form_name" => "nombre",
                            "placeholder" => "Ingrese el nombre del cliente",
                            "validate" => "Nombre es requerido",
                            "bd-error" => "El nombre ya se encuentra registrado",
                            "requerido" => "req-true",
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
                            "bd-error" => "El teléfono ya se encuentra registrado",
                            "requerido" => "req-true",
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
                            "bd-error" => "El correo ya se encuentra registrado",
                            "requerido" => "req-true",
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
                            "bd-error" => "La dirección ya se encuentra registrada",
                            "requerido" => "req-true",
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
                            "bd-error" => "La zona ya se encuentra registrada",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Persona Contacto",
                            "icon" => "fa-user",
                            "type" => "text",
                            "id_name" => "form-person",
                            "form_name" => "persona_contacto",
                            "placeholder" => "Ingrese la persona representante",
                            "validate" => "Contacto es requerido",
                            "bd-error" => "La persona contacto ya se encuentra registrada",
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
            $("#ib").addClass("active");
            $("#ib").removeClass("icono_head");
            $(".ib").removeClass("icono_color");
    </script>
@endsection