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
        INFORMACIÓN DE PAGO DE NÓMINA - {{ strtoupper($nomina->trabajador->nombre." ".$nomina->trabajador->apellido) }}
    </div>

    <div class="border">
        <!-- APARTADO INFORMACION DE LA IZQUIERDA -->
        @php
            $workers = array ();

            $one_row = array(
                "value" => 1,
                "nombre" => "Trabajador Nombre",
            );

            foreach ($trabajadores as $worker) {
                $one_row["value"] = $worker->id;
                $one_row["nombre"] = $worker->nombre." ".$worker->apellido;
                array_push($workers,$one_row);
            }

            $data_form = array(
                "action" => "edit-nomina",
                "title" => "",
                "form-id" => "form-nomina",
                "edit-id" => $nomina->id,
                
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
                        "value" => $nomina->id_trabajador,
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
                        "value" => gmdate("Y-m",strtotime($nomina->mes)),
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
                        "value" => $nomina->pago->fecha_pago,
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
                        "value" => $nomina->monto,
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
                        "value" => $nomina->pago->banco,
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
                        "value" => $nomina->pago->referencia,
                    ),
                ),
            );
        @endphp

        <!-- APARTADO INFORMACION DE LA DERECHA -->
        @php
            $datos = array(
                array(
                    "label" => "Nombre del Empleado", 
                    "dato" => $nomina->trabajador->nombre." ".$nomina->trabajador->apellido,
                ),
                array(
                    "label" => "Cédula", 
                    "dato" => $nomina->trabajador->cedula,
                ),
                array(
                    "label" => "Teléfono", 
                    "dato" => $nomina->trabajador->telefono,
                ),
                array(
                    "label" => "Tipo de Empleado", 
                    "dato" => $nomina->trabajador->tipo,
                ),
                array(
                    "label" => "Banco del Empleado", 
                    "dato" => $nomina->trabajador->banco,
                ),
                array(
                    "label" => "Número de Cuenta", 
                    "dato" => $nomina->trabajador->num_cuenta,
                ),
            );

            $data_list = array(
                array(
                    "table-id" => "lista-nomina",
                    "icon" => "fa-user",
                    "type" => "inline-info",
                    "title" => "Información del Empleado",
                    "information" => $datos,
                ),
            );

            $title = "Datos del Pago de Nómina";
        @endphp
        @include('includes.general_detail',['data'=>$data_form, 'data_list'=>$data_list, 'title'=>$title])
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/form_validate.js') }}"></script>
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#if").addClass("active");
            $("#if").removeClass("icono_head");
            $(".if").removeClass("icono_color");
        
        //VOLVER A LA VISTA QUE ESTABAMOS ANTES
            const retroceder = () => {
                location.href = "{{ url()->previous() }}";
            }
    </script>
@endsection