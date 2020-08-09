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
                    "title" => "AGREGAR UN PAGO GLOBAL DE PERSONAL",
                    "form-id" => "form-nomina",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "input",
                            "label-name" => "Fecha de Pago al Personal",
                            "icon" => "fa-calendar",
                            "type" => "month",
                            "id_name" => "form-fecha-pago",
                            "form_name" => "mes",
                            "placeholder" => "Ingrese la fecha de Pago",
                            "validate" => "Fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
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