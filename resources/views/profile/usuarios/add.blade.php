@extends('layouts.principal')

@section('title','Usuarios · Fatto a Casa')

@section('titulo','FATTO A CASA - USUARIOS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('perfil')}}">Perfil</a>
        </li>
    @if(Auth::user()->tipo != "operador")
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-users') }}">Usuarios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-workers') }}">Empleados</a>
        </li>
    @endif
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-reports') }}">Reportes Generados</a>
        </li>
    @if(Auth::user()->tipo != "operador")
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-system') }}">Reportes del Sistema</a>
        </li>
    @endif
    @if(Auth::user()->tipo == "admin")
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-backups') }}">Restauración (Back-Ups)</a>
        </li>
    @endif
    </ul>
@endsection

@section('info')
    <div class="row justify-content-center my-3 px-2">
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $data_form = array(
                    "action" => "save-user",
                    "title" => "AGREGAR UN NUEVO USUARIO",
                    "form-id" => "form-user",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "input",
                            "label-name" => "Nombre",
                            "icon" => "fa-user",
                            "type" => "text",
                            "id_name" => "form-user",
                            "form_name" => "name",
                            "placeholder" => "Ingrese el nombre de la Persona",
                            "validate" => "Nombre es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Nombre de Usuario",
                            "icon" => "fa-id-card",
                            "type" => "text",
                            "id_name" => "form-username",
                            "form_name" => "username",
                            "placeholder" => "Ingrese el nombre de Usuario",
                            "validate" => "CI/RIF es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Contraseña",
                            "icon" => "fa-user-secret",
                            "type" => "text",
                            "id_name" => "form-password",
                            "form_name" => "password",
                            "placeholder" => "Ingrese la contraseña",
                            "validate" => "Contraseña es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Confirmar Contraseña",
                            "icon" => "fa-user-secret",
                            "type" => "text",
                            "id_name" => "form-re-password",
                            "form_name" => "re_password",
                            "placeholder" => "Ingrese la contraseña nuevamente",
                            "validate" => "Confirmación es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Correo",
                            "icon" => "fa-at",
                            "type" => "text",
                            "id_name" => "form-email",
                            "form_name" => "email",
                            "placeholder" => "Ingrese el correo",
                            "validate" => "Correo es requerido",
                            "bd-error" => "El correo ya se encuentra registrado",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "select",
                            "label-name" => "Tipo de Usuario",
                            "icon" => "fa-list-alt",
                            "id_name" => "form-tipo",
                            "form_name" => "tipo",
                            "title" => "Selecciona una Opción",
                            "options" => array(
                                array(
                                    "value" => "admin secundario",
                                    "nombre" => "admin secundario",
                                ),
                                array(
                                    "value" => "operador",
                                    "nombre" => "operador",
                                ),
                            ),
                            "validate" => "Tipo de usuario es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "checkbox",
                            "label-name" => "Permisos de Usuario",
                            "icon" => "fa-user-plus",
                            "id_name" => "form-permisos",
                            "form_name" => "permisos",
                            "validate" => "Permisos son requeridos",
                            "requerido" => "req-false",
                            "check-options" => array(
                                array(
                                    "name" => "check-log",
                                    "label" => "Logìstica",
                                    "value" => false,
                                ),
                                array(
                                    "name" => "check-cv",
                                    "label" => "Compras y Ventas",
                                    "value" => false,
                                ),
                                array(
                                    "name" => "check-fin",
                                    "label" => "Finanzas",
                                    "value" => false,
                                ),
                                array(
                                    "name" => "check-cp",
                                    "label" => "Clientes y Proveedores",
                                    "value" => false,
                                ),
                            ),
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