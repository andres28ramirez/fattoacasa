@extends('layouts.principal')

@section('title','Finanzas · Fatto a Casa')

@section('titulo','DETALLADO - GASTOS / COSTOS')

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
            <a class="nav-link text-secondary" href="{{ route('list-nomina') }}">Nómina</a>
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
        INFORMACIÓN DE {{ strtoupper($gastocosto->tipo) }} - CÓDIGO ({{ strtoupper($gastocosto->id) }})
    </div>

    <div class="border">
        <!-- APARTADO INFORMACION DE LA IZQUIERDA -->
        @php
            $data_form = array(
                "action" => "edit-gasto-costo",
                "title" => "",
                "form-id" => "form-gasto-costo",
                "edit-id" => $gastocosto->id,
                
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
                        "value" => $gastocosto->tipo,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Fecha",
                        "icon" => "fa-calendar",
                        "type" => "date",
                        "id_name" => "form-fecha",
                        "form_name" => "fecha",
                        "placeholder" => "Ingrese la fecha del egreso",
                        "validate" => "Fecha es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $gastocosto->fecha,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Monto",
                        "icon" => "fa-money",
                        "type" => "text",
                        "id_name" => "form-price",
                        "form_name" => "monto",
                        "placeholder" => "Ingrese el monto",
                        "validate" => "Monto es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $gastocosto->monto,
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
                        "value" => $gastocosto->descripcion,
                    ),
                ),
            );
        @endphp

        <!-- APARTADO INFORMACION DE LA DERECHA -->
        @php
            $data_list = array(
                array(
                    "table-id" => "lista-costo-total",
                    "icon" => "fa-archive",
                    "type" => "totals",

                    "color-header" => "#BF6000",
                    "color-inside" => "#FF8100",
                    "cantidad" => count($total_costos),
                    "text" => "TOTAL DE COSTOS REALIZADOS",
                    "figure" => "fa-bar-chart",
                    "col" => "col-lg-12"
                ),
                array(
                    "table-id" => "lista-gasto-total",
                    "icon" => "fa-book",
                    "type" => "totals",

                    "color-header" => "#80170B",
                    "color-inside" => "#FF3017",
                    "cantidad" => count($total_gastos),
                    "text" => "TOTAL DE GASTOS REALIZADOS",
                    "figure" => "fa-line-chart",
                    "col" => "col-lg-12"
                ),
            );

            $title = "Datos del Gasto o Costo";
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