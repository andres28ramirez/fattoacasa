@extends('layouts.principal')

@section('title','Finanzas · Fatto a Casa')

@section('titulo','FATTO A CASA - NÓMINA')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-ingresos')}}">Ingresos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-egresos') }}">Egresos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-gasto-costo') }}">Gastos y Costos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-nomina') }}">Nómina</a>
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
                $workers = array ();

                $one_row = array(
                    "value" => 1,
                    "nombre" => "Andres",
                );

                foreach ($trabajadores as $row) {
                    $one_row["value"] = $row->id;
                    $one_row["nombre"] = $row->nombre." ".$row->apellido;
                    array_push($workers,$one_row);
                }

                $data_form = array(
                    "action" => "save-nomina",
                    "title" => "AGREGAR UN PAGO DE NOMINA",
                    "form-id" => "form-nomina",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "select",
                            "label-name" => "Empleado",
                            "icon" => "fa-user",
                            "id_name" => "form-empleado",
                            "form_name" => "id_trabajador",
                            "title" => "Selecciona una opción",
                            "options" => $workers,
                            "validate" => "Empleado es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Fecha correspondiente en Nómina",
                            "icon" => "fa-calendar",
                            "type" => "month",
                            "id_name" => "form-fecha",
                            "form_name" => "mes",
                            "placeholder" => "Ingrese la fecha de Nómina",
                            "validate" => "Fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Fecha del Pago",
                            "icon" => "fa-calendar-o",
                            "type" => "date",
                            "id_name" => "form-fecha-pago",
                            "form_name" => "fecha_pago",
                            "placeholder" => "Ingrese la fecha del pago",
                            "validate" => "Fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Monto Pagado",
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
                            "component-type" => "select",
                            "label-name" => "Banco del Pago",
                            "icon" => "fa-home",
                            "id_name" => "form-banco",
                            "form_name" => "banco",
                            "title" => "Selecciona una opción",
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
                            "label-name" => "Referencia Bancaria",
                            "icon" => "fa-clipboard",
                            "type" => "text",
                            "id_name" => "form-referencia",
                            "form_name" => "referencia",
                            "placeholder" => "Ingrese la referencia",
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
@endsection

@section('scripts')
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#if").addClass("active");
            $("#if").removeClass("icono_head");
            $(".if").removeClass("icono_color");
    </script>
@endsection