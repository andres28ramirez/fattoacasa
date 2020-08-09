@extends('layouts.principal')

@section('title','Empleados · Fatto a Casa')

@section('titulo','FATTO A CASA - EMPLEADOS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('perfil')}}">Perfil</a>
        </li>
    @if(Auth::user()->tipo != "operador")
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-users') }}">Usuarios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-workers') }}">Empleados</a>
        </li>
    @endif
        <!-- <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-reports') }}">Reportes Generados</a>
        </li> -->
    @if(Auth::user()->tipo != "operador")
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-system') }}">Reportes del Sistema</a>
        </li>
    @endif
    @if(Auth::user()->tipo == "admin")
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-backups') }}">Restauración y Respaldo</a>
        </li>
    @endif
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
        INFORMACIÓN DEL EMPLEADO {{ strtoupper($worker->nombre." ".$worker->apellido) }}
    </div>

    <div class="border">
        <!-- APARTADO INFORMACION DE LA IZQUIERDA -->
        @php
            $data_form = array(
                "action" => "edit-worker",
                "title" => "",
                "form-id" => "form-edit-worker",
                "edit-id" => $worker->id,
                
                "form-components" => array(
                    array(
                        "component-type" => "input",
                        "label-name" => "Nombre",
                        "icon" => "fa-user",
                        "type" => "text",
                        "id_name" => "form-name",
                        "form_name" => "nombre",
                        "placeholder" => "Ingrese el nombre del Empleado",
                        "validate" => "Nombre es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $worker->nombre,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Apellido",
                        "icon" => "fa-user-plus",
                        "type" => "text",
                        "id_name" => "form-lastname",
                        "form_name" => "apellido",
                        "placeholder" => "Ingrese el apellido del Empleado",
                        "validate" => "Apellido es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $worker->apellido,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "CI/RIF",
                        "icon" => "fa-id-card",
                        "type" => "text",
                        "id_name" => "form-cedula",
                        "form_name" => "cedula",
                        "placeholder" => "Ingrese la cédula de identidad",
                        "validate" => "C.I es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $worker->cedula,
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
                        "value" => $worker->telefono,
                    ),
                    array(
                        "component-type" => "select",
                        "label-name" => "Tipo de Empleado",
                        "icon" => "fa-list-alt",
                        "id_name" => "form-tipo",
                        "form_name" => "tipo",
                        "title" => "Selecciona una Opción",
                        "options" => array(
                            array(
                                "value" => "Administrador",
                                "nombre" => "Administrador",
                            ),
                            array(
                                "value" => "Operador",
                                "nombre" => "Operador",
                            ),
                            array(
                                "value" => "Despachador",
                                "nombre" => "Despachador",
                            ),
                            array(
                                "value" => "Contador",
                                "nombre" => "Contador",
                            ),
                            array(
                                "value" => "Mantenimiento",
                                "nombre" => "Mantenimiento",
                            ),
                        ),
                        "validate" => "Tipo de empleado es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $worker->tipo,
                    ),
                    array(
                        "component-type" => "select",
                        "label-name" => "Banco del Empleado",
                        "icon" => "fa-list-alt",
                        "id_name" => "form-banco",
                        "form_name" => "banco",
                        "title" => "Selecciona una Opción",
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
                        "value" => $worker->banco,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Número de Cuenta",
                        "icon" => "fa-clipboard",
                        "type" => "text",
                        "id_name" => "form-cuenta",
                        "form_name" => "num_cuenta",
                        "placeholder" => "Ingrese el número de cuenta",
                        "validate" => "Nro. de cuenta es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $worker->num_cuenta,
                    ),
                ),
            );
        @endphp
        
        <!-- APARTADO INFORMACION DE LA DERECHA -->
        @php
            $data_table_1 = array();
            $data = array("id", "dato-1", "dato-2", "dato-3", "agenda-4");
            foreach ($eventos as $reunion) {
                $data["id"] = $reunion->id;
                $data["dato-1"] = $reunion->start;
                $data["dato-2"] = $reunion->descripcion;
                $data["dato-3"] = $reunion->title;
                $data["agenda-4"] = $reunion->activo;
                array_push($data_table_1,$data);
            }

            $data_list = array(
                array(
                    "table-id" => "agenda-empleado",
                    "title" => "Agenda del Empleado",
                    "hide-options" => true,
                    "icon" => "fa-calendar",
                    "type" => "table",
                    "titulos" => array(
                        "Fecha",
                        "Descripción",
                        "Tipo",
                        "Estado",
                    ),
                    "content" => $data_table_1,
                ),
            );

            $title = "Datos del Empleado";
        @endphp
        @include('includes.general_detail',['data'=>$data_form, 'data_list'=>$data_list, 'title'=>$title])
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/form_validate.js') }}"></script>
    <script>
        //VOLVER A LA VISTA QUE ESTABAMOS ANTES
            const retroceder = () => {
                location.href = "{{ url()->previous() }}";
            }
    </script>
@endsection