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
    <div class="row justify-content-center my-3 px-2">
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $data_form = array(
                    "action" => "save-worker",
                    "title" => "AGREGAR UN NUEVO EMPLEADO",
                    "form-id" => "form-worker",
                    
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
                        ),
                    ),
                );
            @endphp
            @include('includes.general_form',['data'=>$data_form])
        </div>
    </div>
@endsection

@section('scripts')
@endsection