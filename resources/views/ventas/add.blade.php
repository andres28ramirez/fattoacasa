@extends('layouts.principal')

@section('title','Ventas · Fatto a Casa')

@section('titulo','FATTO A CASA - AÑADIR VENTA')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-ventas')}}">Listado de Ventas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-pedidos') }}">Pedidos sin Despacho</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-despachos')}}">Despachos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-cuentas')}}">Cuentas por Cobrar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-pagos')}}">Pagos Recibidos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('discard-ventas')}}">Ventas Descartadas</a>
        </li>
    </ul>
@endsection

@section('info')
    <div class="row justify-content-center my-3 px-2">
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $clientes = array ();

                $one_cliente = array(
                    "value" => 1,
                    "nombre" => "Andres",
                );

                foreach ($clients as $cliente) {
                    $one_cliente["value"] = $cliente->id;
                    $one_cliente["nombre"] = $cliente->nombre;
                    array_push($clientes,$one_cliente);
                }

                $data_form = array(
                    "action" => "save-venta",
                    "title" => "AGREGAR VENTA REALIZADA",
                    "form-id" => "form-venta",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "select",
                            "label-name" => "Cliente",
                            "icon" => "fa-book",
                            "type" => "text",
                            "id_name" => "form-cliente",
                            "form_name" => "id_cliente",
                            "title" => "Selecciona un Cliente",
                            "options" => $clientes,
                            "validate" => "Cliente es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Fecha de Venta",
                            "icon" => "fa-calendar",
                            "type" => "date",
                            "id_name" => "form-fecha",
                            "form_name" => "fecha",
                            "placeholder" => "Ingresa la Fecha de la venta",
                            "validate" => "Fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Días de Crédito de la Venta",
                            "icon" => "fa-calendar-o",
                            "type" => "number",
                            "id_name" => "form-credit",
                            "form_name" => "credito",
                            "placeholder" => "Ingresa la duración del crédito",
                            "validate" => "Crédito es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Nota de Venta",
                            "icon" => "fa-pencil",
                            "type" => "text",
                            "id_name" => "form-nota",
                            "form_name" => "nota",
                            "placeholder" => "Ingrese la nota de venta",
                            "validate" => "Nota es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                    ),

                    "form-pago" => array(
                        array(
                            "component-type" => "select",
                            "label-name" => "Banco del Pago",
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
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Fecha",
                            "icon" => "fa-calendar",
                            "type" => "date",
                            "id_name" => "form-fecha-pago",
                            "form_name" => "fecha_pago",
                            "placeholder" => "Ingresa la Fecha del pago",
                            "validate" => "Fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Núm. Referencia del Pago",
                            "icon" => "fa-money",
                            "type" => "text",
                            "id_name" => "form-referencia",
                            "form_name" => "referencia",
                            "placeholder" => "Ingresa la referencia del pago",
                            "validate" => "Referencia es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Nota de Pago",
                            "icon" => "fa-bookmark",
                            "type" => "text",
                            "id_name" => "form-nota-pago",
                            "form_name" => "nota_pago",
                            "placeholder" => "Ingrese la nota de pago",
                            "validate" => "Nota es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                    ),

                    "form-products" => array(),
                );

                $one_product = array(
                    "value" => 1,
                    "nombre" => "Producto Nombre",
                );

                foreach ($products as $product) {
                    $one_product["value"] = $product->producto->id;
                    $one_product["nombre"] = $product->producto->nombre;
                    array_push($data_form["form-products"],$one_product);
                }
            @endphp
            @include('includes.form_cv',['data'=>$data_form])
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#ip").addClass("active");
            $("#ip").removeClass("icono_head");
            $(".ip").removeClass("icono_color");

            const retroceder = () => {
                location.href = "{{ url()->previous() }}";
            }

        //EVENTO QUE GENERA UN SWALERT SI FALTAN PRODUCTOS Y MUESTRO CUALES SON
            @if(session('fallo'))
                var html = 'Faltan los siguientes productos en inventario:';
                    @foreach(session('fallo') as $one)
                        html += '\n{{ $one["nombre"] }}: {{ $one["cantidad"] }} Kg/unidades';
                    @endforeach
                swal({
                    title: "La venta no pudo ser agregada",
                    text: html,
                    icon: 'error',
                    closeOnClickOutside: false,
                    button: 'Aceptar',
                });
            @endif
    </script>
@endsection