@extends('layouts.principal')

@section('title','Perfil · Fatto a Casa')

@section('titulo','PERFIL - CONFIGURACIÓN')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('perfil')}}">Perfil</a>
        </li>
    @if(Auth::user()->tipo != "operador")
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-users') }}">Usuarios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-workers') }}">Empleados</a>
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
            <a class="nav-link text-secondary" href="{{ route('list-backups') }}">Restauración (Back-Ups)</a>
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
        INFORMACIÓN DE USUARIO - {{ strtoupper(Auth::user()->name) }}
    </div>

    <div class="border">
        <!-- APARTADO INFORMACION DE LA IZQUIERDA -->
        @php
            $data_form = array(
                "action" => "edit-profile",
                "title" => "",
                "form-id" => "form-profile",
                "edit-id" => Auth::user()->id,
                
                "form-components" => array(
                    array(
                        "component-type" => "input",
                        "label-name" => "Nombre",
                        "icon" => "fa-user",
                        "type" => "text",
                        "id_name" => "form-user",
                        "form_name" => "name",
                        "placeholder" => "Ingrese el nombre de la persona",
                        "validate" => "Nombre es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => Auth::user()->name,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Nombre de Usuario",
                        "icon" => "fa-id-card",
                        "type" => "text",
                        "id_name" => "form-username",
                        "form_name" => "username",
                        "placeholder" => "Ingrese el nombre de usuario",
                        "validate" => "Nombre de usuario es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => Auth::user()->username,
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
                        "value" => Auth::user()->email,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Contraseña",
                        "icon" => "fa-secret",
                        "type" => "password",
                        "id_name" => "form-password",
                        "form_name" => "password",
                        "placeholder" => "Ingrese la contraseña",
                        "validate" => "Contraseña es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => Auth::user()->password,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Confirmar Contraseña",
                        "icon" => "fa-secret",
                        "type" => "password",
                        "id_name" => "form-re-password",
                        "form_name" => "re_password",
                        "placeholder" => "Ingrese la confirmación de contraeña",
                        "validate" => "Confirmación es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => Auth::user()->password,
                    ),
                ),
            );
        @endphp
        
        <!-- APARTADO INFORMACION DE LA DERECHA -->
        @php
            $data_list = array(
                array(
                    "table-id" => "lista-reporte-sistema-total",
                    "icon" => "fa-folder-open-o",
                    "type" => "totals",

                    "color-header" => "#80170B",
                    "color-inside" => "#FF3017",
                    "cantidad" => count($n_sistemas),
                    "text" => "REPORTES DEL SISTEMA",
                    "figure" => "fa-clipboard",
                    "col" => "col-lg-12"
                ),
            );

            $title = "Datos del Perfil";
        @endphp
        @include('includes.general_detail',['data'=>$data_form, 'data_list'=>$data_list, 'title'=>$title])
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/form_validate.js') }}"></script>
    <script>
        $("#form-back").remove();
    </script>
@endsection