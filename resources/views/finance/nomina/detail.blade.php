@extends('layouts.principal')

@section('title','Finanzas · Fatto a Casa')

@section('titulo','FATTO A CASA - PERSONAL')

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
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-nomina') }}">Personal</a>
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
        INFORMACIÓN DE PAGO GLOBAL DE PERSONAL
    </div>

    <div class="border">
        <!-- APARTADO INFORMACION DE LA IZQUIERDA -->
        @php
            $data_form = array(
                "action" => "edit-nomina",
                "title" => "",
                "form-id" => "form-nomina",
                "edit-id" => $nomina->id,
                
                "form-components" => array(
                    array(
                        "component-type" => "input",
                        "label-name" => "Fecha del Pago",
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
                        "label-name" => "Monto Pagado en Bs",
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
                ),
            );
        @endphp

        <!-- APARTADO INFORMACION DE LA DERECHA -->
        @php
            setlocale(LC_TIME, "spanish"); 
            $datos = array(
                array(
                    "label" => "Mes del Pago", 
                    "dato" => strftime("%B", strtotime($nomina->mes)),
                ),
                array(
                    "label" => "Año Correspondiente", 
                    "dato" => strftime("%Y", strtotime($nomina->mes)),
                ),
                array(
                    "label" => "Monto", 
                    "dato" => number_format($nomina->monto,2, ",", ".")." Bs",
                ),
            );

            $data_list = array(
                array(
                    "table-id" => "lista-nomina",
                    "icon" => "fa-user",
                    "type" => "inline-info",
                    "title" => "Información del Mes Pagado",
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