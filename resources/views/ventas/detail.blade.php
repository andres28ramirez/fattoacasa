@extends('layouts.principal')

@section('title','Ventas · Fatto a Casa')

@section('titulo','FATTO A CASA - DETALLADO DE VENTA')

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

        .superior-buttons:hover {
            background-color: rgba(0,0,0,0.4);
            color: rgba(255,255,255,1);
            -webkit-transition: all 1s ease;
            -moz-transition: all 1s ease;
            -o-transition: all 1s ease;
            -ms-transition: all 1s ease;
            transition: all 1s ease;
        }

        .superior-buttons {
            color: #898989;
        }

        .border-detail {
            border-top: 1px solid #ced4da;
            border-left: 1px solid #ced4da;
        }

        .form-buttons:hover {
            background-color: rgba(0,0,0,0.6);
            color: rgba(255,255,255,1);
            -webkit-transition: all 1s ease;
            -moz-transition: all 1s ease;
            -o-transition: all 1s ease;
            -ms-transition: all 1s ease;
            transition: all 1s ease;
        }

        .form-buttons {
            color: rgba(255,255,255,1);
            background-color: rgba(0,0,0,0.3);
        }

        /* PRODUCTOS RESPONSIVE */
        @media (max-width: 995px) {  
            #responsive-title{
                display: none;
            }

            #productos-form-orden{
                margin-top: 10px;
            }

            .delete-button{
                margin-left: 5px;
                margin-top: 10px;
            }

            .iva-button{
                margin-top: 10px;
            }
        }

        @media (min-width: 996px) {  
            #productos-form-orden .d-separator{
                display: none;
            }

            #productos-form-orden .rl-producto{
                display: none;
            }

            #productos-form-orden .rl-precio{
                display: none;
            }

            #productos-form-orden .rl-cantidad{
                display: none;
            }

            #productos-form-orden .rl-fecha{
                display: none;
            }
        }

        @media (max-width: 575px) {  
            #productos-form-orden .rl-precio{
                margin-top: 5px;
            }

            #productos-form-orden .rl-cantidad{
                margin-top: 5px;
            }

            #productos-form-orden .rl-fecha{
                margin-top: 5px;
            }
        }
    </style>

    @if(session('message'))
        <h3 class="text-center alert alert-success">{{ session('message') }}</h3>
    @endif

    @if(session('status'))
        <h3 class="text-center alert alert-danger">{{ session('status') }}</h3>
    @endif
    
    <div class="text-left py-2 col-12 m-auto row justify-content-between" style="font-size: 1.2em; border-bottom: 1px solid black">
        <div class='col-lg-6 col-md-6 col-sm-6 col-12 d-flex my-auto'>
            INFORMACIÓN DE VENTA - CÓDIGO ({{ strtoupper($venta->id) }})
        </div>

        <div class='col-lg-6 col-md-6 col-sm-6 col-12 text-sm-right'>
            <button id="print" class="btn superior-buttons" onClick="imprimir({{$venta->id}})">
                <i class="fa fa-print font-weight-bold"></i> Descargar
            </button>
        </div>
    </div>

    <div class="border">
        <!-- APARTADO INFORMACION DE LA IZQUIERDA -->
        @php
            $clientes = array ();

            $one_client = array(
                "value" => 1,
                "nombre" => "Cliente Nombre",
            );

            foreach ($clients as $client) {
                $one_client["value"] = $client->id;
                $one_client["nombre"] = $client->nombre;
                array_push($clientes,$one_client);
            }

            $data_form = array(
                "action" => "edit-venta",
                "title" => "",
                "form-id" => "form-edit-venta",
                "edit-id" => $venta->id,
            
                "form-components" => array(
                    array(
                        "component-type" => "select",
                        "label-name" => "Cliente",
                        "icon" => "fa-book",
                        "type" => "text",
                        "id_name" => "form-cliente",
                        "form_name" => "id_cliente",
                        "title" => "Selecciona un cliente",
                        "options" => $clientes,
                        "validate" => "Cliente es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $venta->id_cliente,
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
                        "value" => $venta->fecha,
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
                        "value" => $venta->credito,
                    ),
                    array(
                        "component-type" => "textarea",
                        "label-name" => "Nota de Venta",
                        "icon" => "fa-note",
                        "type" => "text",
                        "id_name" => "form-nota",
                        "form_name" => "nota",
                        "placeholder" => "Ingresa la nota de venta",
                        "validate" => "Nota es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $venta->nota,
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
                        "requerido" => $venta->pago ? "req-true" : "req-false",
                        "value" => $venta->pago ? $venta->pago->banco : "",
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
                        "requerido" => $venta->pago ? "req-true" : "req-false",
                        "value" => $venta->pago ? $venta->pago->fecha_pago : "",
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Nota de Pago",
                        "icon" => "fa-money",
                        "type" => "text",
                        "id_name" => "form-nota-pago",
                        "form_name" => "nota_pago",
                        "placeholder" => "Ingresa la nota del pago",
                        "validate" => "Nota es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => $venta->pago ? "req-true" : "req-false",
                        "value" => $venta->pago ? $venta->pago->nota_pago : "",
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
                        "value" => $venta->pago ? $venta->pago->referencia : "",
                    ),
                ),
            );
        @endphp
        
        <!-- APARTADO INFORMACION DE LA DERECHA -->
        @php
            $products = array();

            $one_product = array(
                array("name","cantidad","precio"),
            );

            foreach ($productos as $pro) {
                $one_product["name"] = $pro->producto->nombre;
                $one_product["cantidad"] = $pro->cantidad;
                $one_product["precio"] = $pro->precio;
                array_push($products,$one_product);
            }

            $data_list = array(
                array(
                    "table-id" => "lista-cliente",
                    "icon" => "fa-list-alt",
                    "type" => "inline-info",
                    "title" => "Información del Cliente",
                    "information" => array(
                        array(
                            "label" => "Nombre", 
                            "dato" => $venta->cliente->nombre,
                        ),
                        array(
                            "label" => "Zona", 
                            "dato" => $venta->cliente->zona->nombre,
                        ),
                        array(
                            "label" => "Dirección", 
                            "dato" => $venta->cliente->direccion,
                        ),
                        array(
                            "label" => "Teléfono", 
                            "dato" => $venta->cliente->telefono,
                        ),
                        array(
                            "label" => "Correo", 
                            "dato" => $venta->cliente->correo,
                        ),
                    ),
                    "productos" => $products,
                ),
            );

            $title = "Datos de la Venta";
        @endphp
        @include('includes.general_detail',['data'=>$data_form, 'data_list'=>$data_list, 'title'=>$title])
    </div>
    
    <!-- MODAL PARA EDITAR LA ORDEN -->
    <div class="modal fade" id="table-orden" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="titulo">Productos de la Orden de Venta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('update-orden-venta') }}" class="validate-form" id="form-orden">
                        @csrf
                        <!-- INFORMACIÓN DE LOS PRODUCTOS E LA COMPRA O VENTA -->
                        <div class="form-row justify-content-center">
                            <div class="col-11 p-2" id="responsive-title">
                                <div class="input-group row justify-content-center">
                                    <div class="col">
                                        <!-- PRODUCTO -->
                                        <strong>Producto:</strong>
                                    </div>
                                    <div class="col">
                                        <!-- CANTIDAD -->
                                        <strong>Precio:</strong>
                                    </div>
                                    <div class="col">
                                        <!-- PRECIO -->
                                        <strong>Cantidad (Kg/Und):</strong>
                                    </div>
                                </div>                        
                            </div>
                            <div class="col-1"><!-- ES UN SEPARADOR PARA LAS OPCIONES DE LOS BOTONES --></div>

                            <!-- APARTADO DE LOS PRODUCTOS UNO A UNO -->
                            @php $cantidad=0; @endphp
                            <div id="productos-form-orden" class="col-12">
                                @foreach($productos as $pro)
                                @php $cantidad++; @endphp
                                <div class="form-group col-12 row justify-content-center" id="{{ $cantidad }}">
                                    <div class="col-lg p-0"> <!-- PRODUCTO -->
                                        <div class="input-group validate-input" data-validate="Producto es requerido">
                                            <label class="rl-producto align-self-center col-lg-2 col-md-3 col-sm-3"><strong>Producto:</strong></label>
                                            <select name="form-producto-{{ $cantidad }}" 
                                                    id="form-producto-{{ $cantidad }}" 
                                                    class="form-control input100 border-right req-true" 
                                                    style="height: calc(2.19rem + 10px)" >
                                                    <option value="{{ $pro->producto->id }}">{{ $pro->producto->nombre }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg p-0"> <!-- PRECIO -->
                                        <div class="input-group validate-input" data-validate="Precio es requerido">
                                            <label class="rl-precio align-self-center col-lg-2 col-md-3 col-sm-3"><strong>Precio:</strong></label>
                                            <input 
                                                class="form-control border-left border-right req-true precio-producto" 
                                                style="height: calc(2.19rem + 10px)"
                                                type="number" 
                                                id="form-price-{{ $cantidad }}"
                                                name="form-price-{{ $cantidad }}" 
                                                placeholder="Ingrese el precio del producto"
                                                min='0.001'
                                                step="any"
                                                value="{{ $pro->precio }}"
                                                onchange="precio({{ $cantidad }})"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-lg p-0"> <!-- CANTIDAD -->
                                        <div class="input-group validate-input" data-validate="Cantidad es requerida">
                                            <label class="rl-cantidad align-self-center col-lg-2 col-md-3 col-sm-3"><strong>Cantidad (Kg/Und):</strong></label>
                                            <input 
                                                class="form-control border-left border-right req-true" 
                                                style="height: calc(2.19rem + 10px)"
                                                type="number" 
                                                id="form-cantidad-{{ $cantidad }}"
                                                name="form-cantidad-{{ $cantidad }}" 
                                                placeholder="Ingrese la cantidad del producto"
                                                min='0.001'
                                                step="any"
                                                value="{{ $pro->cantidad }}"
                                                onchange="precio({{ $cantidad }})"
                                            >
                                        </div>
                                    </div>
                                    <div class="d-flex p-0 iva-button"> <!-- CHECK PARA CONTAR EL IVA -->
                                        <div class="form-check m-auto">
                                            <label class="form-check-label" for="form-expiracion-{{ $cantidad }}">IVA</label>
                                            <input type="checkbox" class="d-block m-auto" id="form-iva-{{ $cantidad }}" name="form-iva-{{ $cantidad }}" onchange="precio({{ $cantidad }})">
                                        </div>
                                    </div>
                                    <!-- BOTON PARA BORRAR EL PRODUCTO DEL FORM -->
                                    <div class="col-1 d-flex p-0 delete-button">
                                        @if($cantidad!=1)
                                        <button class="btn product-buttons m-auto" onclick="borrar({{ $cantidad }})">
                                            <i class="fa fa-trash font-weight-bold"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <div style="border-bottom: 1px solid #C3CAD6;" class="w-100 my-3 d-separator"></div>
                                </div>
                                @endforeach
                                <input type="hidden" name="vc_id" value="{{$venta->id}}">
                                <input type="hidden" id="vc_monto" name="vc_monto" value="{{$venta->monto}}">
                            </div>

                            <div class="form-group col-12"> <!-- BOTON PARA AGREGAR NUEVO PRODUCTO -->
                                <div id="agregar-form-orden" style="cursor: pointer; width: fit-content">
                                    <i class="fa fa-plus-square fa-lg m-auto"></i>
                                    <a>Agregar Producto...</a>
                                </div>
                            </div>
                        </div>

                        <div id="send-buttons" class="justify-content-end mt-2 p-2" style="display: flex; background-color: #e9ecef">
                            <div class="" id="btn-add">
                                <button class="btn form-buttons px-5" id="form-add">Editar</button>
                            </div>

                            <div class="mx-2" id="btn-back">
                                <button class="btn form-buttons px-5" id="form-back" onclick="retroceder()">Volver</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/form_validate.js') }}"></script>
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#ip").addClass("active");
            $("#ip").removeClass("icono_head");
            $(".ip").removeClass("icono_color");
        
        //VOLVER A LA VISTA QUE ESTABAMOS ANTES
            const retroceder = () => {
                location.href = "{{ url()->previous() }}";
            }

        //EVENTO DE IMPRIMIR
            const imprimir = (id) =>{
                var registro = "{{ route('pdf-detail-venta') }}";
                var ruta = registro+"/"+id;
                window.open(ruta);
            }
        
        //EVENTO PARA AGREGAR NUEVOS PRODUCTOS A LA COMPRA O VENTA
            var agregar = "agregar-form-orden";
            var productos = "productos-form-orden";
            var cantidad = {{$cantidad+1}};

            $("#"+agregar).click(function() {
                html = "<div class='form-group col-12 row justify-content-center' id="+cantidad+">";
                html +=    "<div class='col-lg p-0'>";
                html +=        "<div class='input-group validate-input' data-validate='Producto es requerido'>";
                html +=            "<label class='rl-producto align-self-center col-lg-2 col-md-3 col-sm-3'><strong>Producto:</strong></label>";
                html +=            "<select name='form-producto-"+cantidad+"' id='form-producto-"+cantidad+"' "; 
                html +=                   "class='form-control input100 border-right req-true' "; 
                html +=                   "style='height: calc(2.19rem + 10px)' >";
                                @foreach($orden_productos as $pro)
                html +=                    "<option value='{{ $pro->id }}'>{{ $pro->nombre }}</option>";
                                @endforeach
                html +=            "</select>";
                html +=       "</div>";
                html +=    "</div>";
                html +=    "<div class='col-lg p-0'>";
                html +=        "<div class='input-group validate-input' data-validate='Precio es requerido'>";
                html +=            "<label class='rl-precio align-self-center col-lg-2 col-md-3 col-sm-3'><strong>Precio:</strong></label>";
                html +=            "<input class='form-control input100 border-left border-right req-true precio-producto' "; 
                html +=                "style='height: calc(2.19rem + 10px)' type='number'"; 
                html +=                "id='form-price-"+cantidad+"' name='form-price-"+cantidad+"'"; 
                html +=                " placeholder='Ingrese el precio del producto' ";
                html +=                "min='0.001' step='any' value='0.001' onchange='precio("+cantidad+")'>";
                html +=        "</div>";
                html +=    "</div>";
                html +=    "<div class='col-lg p-0'>";
                html +=        "<div class='input-group validate-input' data-validate='Cantidad es requerida'>";
                html +=            "<label class='rl-cantidad align-self-center col-lg-2 col-md-3 col-sm-3'><strong>Cantidad (Kg/Und):</strong></label>";
                html +=            "<input class='form-control input100 border-left border-right req-true cantidad-producto' ";
                html +=                "style='height: calc(2.19rem + 10px)' type='number' "; 
                html +=                "id='form-cantidad-"+cantidad+"' name='form-cantidad-"+cantidad+"'"; 
                html +=                " placeholder='Ingrese la cantidad del producto' ";
                html +=                "min='0.001' step='any' value='0.001' onchange='precio("+cantidad+")'>";
                html +=        "</div>";
                html +=    "</div>";
                html +=    "<div class='d-flex p-0'>";
                html +=        "<div class='form-check m-auto'>";
                html +=            "<label class='form-check-label' for='form-expiracion-"+cantidad+"'>IVA</label>";
                html +=            "<input type='checkbox' class='d-block m-auto' id='form-iva-"+cantidad+"' name='form-iva-"+cantidad+"' onchange='precio("+cantidad+")'>";
                html +=        "</div>";
                html +=    "</div>";
                html +=    "<div class='col-1 d-flex p-0 delete-button'>"; 
                html +=        "<button class='btn product-buttons m-auto' onclick='borrar("+cantidad+")'>";
                html +=            "<i class='fa fa-trash font-weight-bold'></i>";
                html +=        "</button>";
                html +=    "</div>";
                html +=    '<div style="border-bottom: 1px solid #C3CAD6;" class="w-100 my-3 d-separator"></div>';
                html +="</div>";

                $("#"+productos).append(html);
                cantidad++;
            });

            function borrar(codigo){
                $("#"+codigo).remove();
                precio();
            }

            const precio = () => {
                var price = 0;
                var iva = 0;
                for (i = 1; i <= cantidad-1; i++) {
                    //REVISO SI EXISTE EL ELEMENTO
                    if ( $('#form-price-'+i).length ) {
                        valor = $('#form-price-'+i).val();
                        if(valor < 0){
                            valor = 0;
                            $('#form-price-'+i).val("0.001");
                        }
                        precio_producto =  parseFloat(valor);

                        valor = $('#form-cantidad-'+i).val();
                        if(valor < 0){
                            valor = 0;
                            $('#form-cantidad-'+i).val("0.001");
                        }
                        cantidad_producto = parseFloat(valor);
                        
                        price += precio_producto * cantidad_producto;

                        if($('#form-iva-'+i).prop('checked'))
                            iva += precio_producto * cantidad_producto * 0.16;
                    }
                }

                ajustar(price,iva);
            }

            const ajustar = (price,iva) => {
                //SE LO AGREGO AL INPUT DEL TOTAL
                $("#vc_monto").val(Math.round((price+iva)*100)/100);
            }
    </script>
@endsection