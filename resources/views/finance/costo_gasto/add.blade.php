@extends('layouts.principal')

@section('title','Finanzas · Fatto a Casa')

@section('titulo','FATTO A CASA - EGRESOS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-ingresos')}}">Ingresos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-egresos') }}">Egresos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-gasto-costo') }}">Gastos y Costos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-nomina') }}">Personal</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('finance-pagos') }}">Pagos</a>
        </li>
    </ul>
@endsection

@section('info')
    <div class="row justify-content-center my-3 px-2">
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $data_form = array(
                    "action" => "save-gasto-costo",
                    "title" => "AGREGAR UN NUEVO GASTO - COSTO",
                    "form-id" => "form-gasto-costo",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "select",
                            "label-name" => "Tipo de egreso",
                            "icon" => "fa-list-alt",
                            "id_name" => "form-tipo",
                            "form_name" => "tipo",
                            "title" => "Selecciona una opción",
                            "options" => array(
                                array(
                                    "value" => "Gasto",
                                    "nombre" => "Gasto",
                                ),
                                array(
                                    "value" => "Costo",
                                    "nombre" => "Costo",
                                ),
                            ),
                            "validate" => "Tipo es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Fecha",
                            "icon" => "fa-calendar",
                            "type" => "date",
                            "id_name" => "form-fecha-pago",
                            "form_name" => "fecha",
                            "placeholder" => "Ingrese la fecha del egreso",
                            "validate" => "Fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Monto en Bs",
                            "icon" => "fa-money",
                            "type" => "text",
                            "id_name" => "form-price",
                            "form_name" => "monto",
                            "placeholder" => "Ingrese el monto",
                            "validate" => "Monto es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "textarea",
                            "label-name" => "Descripción",
                            "icon" => "fa-info",
                            "type" => "textarea",
                            "id_name" => "form-description",
                            "form_name" => "descripcion",
                            "placeholder" => "Ingrese la descripción",
                            "validate" => "Descripción es requerida",
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
            $("#if").addClass("active");
            $("#if").removeClass("icono_head");
            $(".if").removeClass("icono_color");
    </script>
@endsection