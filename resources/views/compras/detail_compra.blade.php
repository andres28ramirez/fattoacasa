@extends('layouts.principal')

@section('title','Compras · Fatto a Casa')

@section('titulo','FATTO A CASA - COMPRAS')

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
            <a class="nav-link text-secondary" href="{{ route('cp')}}">Cuentas Pagadas</a>
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
        INFORMACIÓN DE COMPRA - CÓDIGO ({{ strtoupper($compra->id) }})
    </div>

    <div class="border">
        <!-- APARTADO INFORMACIÓN DE LA IZQUIERDA -->
        @php
            $proveedores = array ();

            $one_provider = array(
                "value" => 1,
                "nombre" => "Categoria Nombre",
            );

            foreach ($providers as $person) {
                $one_provider["value"] = $person->id;
                $one_provider["nombre"] = $person->nombre;
                array_push($proveedores,$one_provider);
            }

            $data_form = array(
                "action" => "edit-compra",
                "title" => "",
                "form-id" => "form-edit-compra",
                "edit-id" => $compra->id,
            
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
                        "value" => $compra->id_proveedor,
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
                        "value" => $compra->fecha,
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
                        "value" => $compra->credito,
                    ),
                    array(
                        "component-type" => "select",
                        "label-name" => "Banco del Pago",
                        "icon" => "fa-home",
                        "id_name" => "form-banco",
                        "form_name" => "banco",
                        "title" => "Selecciona un Banco",
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
                        "requerido" => $compra->pago ? "req-true" : "req-false",
                        "value" => $compra->pago ? $compra->pago->banco : "",
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Fecha del Pago",
                        "icon" => "fa-calendar",
                        "type" => "date",
                        "id_name" => "form-fecha-pago",
                        "form_name" => "fecha_pago",
                        "placeholder" => "Ingresa la Fecha del pago",
                        "validate" => "Fecha es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => $compra->pago ? "req-true" : "req-false",
                        "value" => $compra->pago ? $compra->pago->fecha_pago : "",
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
                        "requerido" => $compra->pago ? "req-true" : "req-false",
                        "value" => $compra->pago ? $compra->pago->referencia : "",
                    ),
                ),
            );
        @endphp
        
        <!-- APARTADO INFORMACION DE LA DERECHA -->
        @php
            $products = array();

            $one_product = array(
                array(
                    "name" => "Naranja",
                    "cantidad" => "5",
                    "precio" => "15.6",
                ),
            );

            foreach ($productos as $pro) {
                $one_product["name"] = $pro->producto->nombre;
                $one_product["cantidad"] = $pro->cantidad;
                $one_product["precio"] = $pro->precio;
                array_push($products,$one_product);
            }

            $data_list = array(
                array(
                    "table-id" => "lista-proveedor",
                    "icon" => "fa-list-alt",
                    "type" => "inline-info",
                    "title" => "Información del Proveedor",
                    "information" => array(
                        array(
                            "label" => "Nombre", 
                            "dato" => $compra->proveedor->nombre,
                        ),
                        array(
                            "label" => "Zona", 
                            "dato" => $compra->proveedor->zona->nombre,
                        ),
                        array(
                            "label" => "Dirección", 
                            "dato" => $compra->proveedor->direccion,
                        ),
                        array(
                            "label" => "Teléfono", 
                            "dato" => $compra->proveedor->telefono,
                        ),
                        array(
                            "label" => "Correo", 
                            "dato" => $compra->proveedor->correo,
                        ),
                    ),
                    "productos" => $products,
                ),
            );

            $title = "Datos de la Compra";
        @endphp
        @include('includes.general_detail',['data'=>$data_form, 'data_list'=>$data_list, 'title'=>$title])
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/form_validate.js') }}"></script>
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#ic").addClass("active");
            $("#ic").removeClass("icono_head");
            $(".ic").removeClass("icono_color");
        
        //VOLVER A LA VISTA QUE ESTABAMOS ANTES
            const retroceder = () => {
                location.href = "{{ url()->previous() }}";
            }
    </script>
@endsection