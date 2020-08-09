@extends('layouts.principal')

@section('title','Finanzas · Fatto a Casa')

@section('titulo','FATTO A CASA - PAGOS')

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
            <a class="nav-link text-secondary" href="{{ route('list-nomina') }}">Personal</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('finance-pagos') }}">Pagos</a>
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
        INFORMACIÓN DE PAGO - CÓDIGO ({{ strtoupper($pago->id) }})
    </div>

    <div class="border">
        <!-- RECORRIENDO LA COLECCION DEL PAGO PARA SABER QUE TIENE Y SETTEAR VALORES -->
        @php
            foreach($pago->compra as $fila){
                $monto = $fila->monto;
                $tipo = "Compra";
                $codigo = $fila->id;
                $proveedor = $fila->proveedor->nombre;
                $fecha = $fila->fecha;

                $products = array();

                $one_product = array(
                    array(
                        "name" => "Naranja",
                        "cantidad" => "5",
                        "precio" => "15.6",
                    ),
                );

                foreach ($fila->orden_producto as $pro) {
                    $one_product["name"] = $pro->producto->nombre;
                    $one_product["cantidad"] = $pro->cantidad;
                    $one_product["precio"] = $pro->precio;
                    array_push($products,$one_product);
                }
            }

            foreach($pago->venta as $fila){
                $monto = $fila->monto;
                $tipo = "Venta";
                $codigo = $fila->id;
                $cliente = $fila->cliente->nombre;
                $fecha = $fila->fecha;
                $products = array();

                $one_product = array(
                    array(
                        "name" => "Naranja",
                        "cantidad" => "5",
                        "precio" => "15.6",
                    ),
                );

                foreach ($fila->order_producto as $pro) {
                    $one_product["name"] = $pro->producto->nombre;
                    $one_product["cantidad"] = $pro->cantidad;
                    $one_product["precio"] = $pro->precio;
                    array_push($products,$one_product);
                }
            }

            foreach($pago->nomina as $fila){
                $monto = $fila->monto;
                $tipo = "Nómina";
                $codigo = $fila->id;
                $trabajador = $fila->trabajador->nombre." ".$fila->trabajador->apellido;
                $fecha = gmdate("Y-m",strtotime($fila->mes));
                setlocale(LC_TIME, "spanish"); 
                $mes = strftime("%B", strtotime($fila->mes));
                $products = null;
            }
        @endphp

        <!-- APARTADO INFORMACION DE LA IZQUIERDA -->
        @php
            $data_form = array(
                "action" => "edit-pago-finance",
                "title" => "",
                "form-id" => "form-pago",
                "edit-id" => $pago->id,
                
                "form-components" => array(
                    array(
                        "component-type" => "select",
                        "label-name" => "Banco",
                        "icon" => "fa-home",
                        "id_name" => "form-banco",
                        "form_name" => "banco",
                        "title" => "Selecciona un Banco",
                        "options" => array(
                            array(
                                "value" => "Otro",
                                "nombre" => "Otro",
                            ),
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
                        "value" => $pago->banco,
                    ),
                    array(
                        "component-type" => "textarea",
                        "label-name" => "Nota de Pago",
                        "icon" => "fa-money",
                        "type" => "text",
                        "id_name" => "form-nota-pago",
                        "form_name" => "nota_pago",
                        "placeholder" => "Ingresa la nota del pago",
                        "validate" => "Nota es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $pago->nota_pago,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Referencia",
                        "icon" => "fa-th-list",
                        "type" => "text",
                        "id_name" => "form-referencia",
                        "form_name" => "referencia",
                        "placeholder" => "Ingrese la referencia bancaria",
                        "validate" => "Referencia es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-false",
                        "value" => $pago->referencia,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Fecha del Pago",
                        "icon" => "fa-calendar",
                        "type" => "date",
                        "id_name" => "form-fecha-pago",
                        "form_name" => "fecha_pago",
                        "placeholder" => "Ingrese la fecha del pago",
                        "validate" => "Fecha es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $pago->fecha_pago,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Monto del Pago en Bs",
                        "icon" => "fa-money",
                        "type" => "text",
                        "id_name" => "form-price",
                        "form_name" => "monto",
                        "placeholder" => "Ingrese el Monto",
                        "validate" => "Monto es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $monto,
                    ),
                ),
            );
        @endphp

        <!-- APARTADO INFORMACION DE LA DERECHA -->
        @php
            switch ($tipo) {
                case "Compra":
                    $title = "Información de Compra";
                    $datos = array(
                        array(
                            "label" => "Código de Compra", 
                            "dato" => $codigo,
                        ),
                        array(
                            "label" => "Proveedor", 
                            "dato" => $proveedor,
                        ),
                        array(
                            "label" => "Fecha de Compra", 
                            "dato" => $fecha,
                        ),
                    );
                    break;
                case "Venta":
                    $title = "Información de Venta";
                    $datos = array(
                        array(
                            "label" => "Código de Venta", 
                            "dato" => $codigo,
                        ),
                        array(
                            "label" => "Cliente", 
                            "dato" => $cliente,
                        ),
                        array(
                            "label" => "Fecha de Venta", 
                            "dato" => $fecha,
                        ),
                    );
                    break;
            }

            $data_list = array(
                array(
                    "table-id" => "lista-pago",
                    "icon" => "fa-archive",
                    "type" => "inline-info",
                    "title" => $title,
                    "information" => $datos,
                    "productos" => $products,
                ),
            );

            $title = "Datos del Pago";
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

        //BORRAR EL .INPUT100 DE LOS QUE NO PUEDEN SER EDITADOS 
            $("#form-tipo").removeClass("input100");
            $("#form-price").removeClass("input100");
        
        //ELIMINAR OPCION DE EDITAR ORDEN
            $("#editar-orden-button").css("visibility", "hidden ");
    </script>
@endsection