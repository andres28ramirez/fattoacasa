@extends('layouts.principal')

@section('title','Compras · Fatto a Casa')

@section('titulo','REALIZAR - COMPRA')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-compras')}}">Listado de Compras</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('suministros')}}">Suministros</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('cpp')}}">Cuentas por Pagar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('cp')}}">Pagos Realizados</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('discard-compras')}}">Compras Descartadas</a>
        </li>
    </ul>
@endsection

@section('info')
    <div class="row justify-content-center my-3 px-2">
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $proveedores = array ();

                $one_proveedor = array(
                    "value" => 1,
                    "nombre" => "Andres",
                );

                foreach ($providers as $person) {
                    $one_proveedor["value"] = $person->id;
                    $one_proveedor["nombre"] = $person->nombre;
                    array_push($proveedores,$one_proveedor);
                }

                $data_form = array(
                    "action" => "save-compra",
                    "title" => "AGREGAR COMPRA REALIZADA",
                    "form-id" => "form-compra",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "select",
                            "label-name" => "Proveedor",
                            "icon" => "fa-book",
                            "type" => "text",
                            "id_name" => "form-proveedor",
                            "form_name" => "id_proveedor",
                            "title" => "Selecciona un proveedor",
                            "options" => $proveedores,
                            "validate" => "Proveedor es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Fecha",
                            "icon" => "fa-calendar",
                            "type" => "date",
                            "id_name" => "form-fecha",
                            "form_name" => "fecha",
                            "placeholder" => "Ingresa la Fecha de la compra",
                            "validate" => "Fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Días de Crédito de la Compra",
                            "icon" => "fa-calendar-o",
                            "type" => "number",
                            "id_name" => "form-credit",
                            "form_name" => "credito",
                            "placeholder" => "Ingresa la duración del crédito",
                            "validate" => "Crédito es requerido",
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
                    $one_product["value"] = $product->id;
                    $one_product["nombre"] = $product->nombre;
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
            $("#ic").addClass("active");
            $("#ic").removeClass("icono_head");
            $(".ic").removeClass("icono_color");

            const retroceder = () => {
                location.href = "{{ url()->previous() }}";
            }
    </script>
@endsection