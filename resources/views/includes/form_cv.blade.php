<style>
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

    .product-buttons:hover {
        background-color: rgba(0,0,0,0.4);
        color: rgba(255,255,255,1);
        -webkit-transition: all 1s ease;
        -moz-transition: all 1s ease;
        -o-transition: all 1s ease;
        -ms-transition: all 1s ease;
        transition: all 1s ease;
    }

    .product-buttons {
        color: #898989;
    }

    .border-right{
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
    }

    .border-left{
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
    }

    /* BOTONES DEL FINAL RESPONSIVE */
    @media (max-width: 450px) {  
        #send-buttons{
            display: block !important;
        }

        #btn-back{
            text-align: center;
        }

        #btn-add{
            text-align: center;
            margin-bottom: 5px;
        }
    }

    /* PRODUCTOS RESPONSIVE */
    @media (max-width: 995px) {  
        #responsive-title{
            display: none;
        }

        #productos-{{ $data['form-id'] }}{
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
        #productos-{{ $data['form-id'] }} .d-separator{
            display: none;
        }

        #productos-{{ $data['form-id'] }} .rl-producto{
            display: none;
        }

        #productos-{{ $data['form-id'] }} .rl-precio{
            display: none;
        }

        #productos-{{ $data['form-id'] }} .rl-cantidad{
            display: none;
        }

        #productos-{{ $data['form-id'] }} .rl-fecha{
            display: none;
        }
    }

    @media (max-width: 575px) {  
        #productos-{{ $data['form-id'] }} .rl-precio{
            margin-top: 5px;
        }

        #productos-{{ $data['form-id'] }} .rl-cantidad{
            margin-top: 5px;
        }

        #productos-{{ $data['form-id'] }} .rl-fecha{
            margin-top: 5px;
        }
    }
</style>

<div class="form-title">
    <span class="form-title2">
        {{ $data['title'] }}
    </span>
</div>

<div class="container-forms p-0" style="padding-top: 20px;">
    <div class="container px-4">
        <form method="POST" action="{{ $data['action'] ? route($data['action']) : '' }}" class="validate-form" id="{{ $data['form-id'] }}">
            @csrf

            <!-- INFORMACION DE LA COMPRA O VENTA -->
            <div class="form-row justify-content-center p-t-20">
                @foreach ($data['form-components'] as $input)
                    <div class="form-group col-md-6" id="input-{{ $input['id_name'] }}">
                        <label>{{$input['label-name']}}:</label>
                        <div class="input-group validate-input" data-validate="{{ $input['validate'] }}">
                            <div class="d-flex">
                                <div class="m-auto form-control icon-box text-center">
                                    <i class="fa {{ $input['icon'] }} fa-lg m-auto"></i>
                                </div>
                            </div>
                            @if($input['component-type']=="select")
                                <select name="{{ $input['form_name'] }}" 
                                        id="{{ $input['id_name'] }}" 
                                        class="form-control input100 {{ $input['requerido'] }} {{ $errors->has($input['form_name']) ? ' is-invalid' : '' }}" 
                                        style="height: calc(2.19rem + 10px)" >
                                    <option selected value="">{{$input['title']}}</option>
                                    @foreach ($input['options'] as $option)
                                        <option value="{{ $option['value'] }}">{{ $option["nombre"] }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @if($input['component-type']=="input")
                                <input 
                                    class="form-control input100 {{ $input['requerido'] }} {{ $errors->has($input['form_name']) ? ' is-invalid' : '' }}" 
                                    style="height: calc(2.19rem + 10px)"
                                    type="{{ $input['type'] }}" 
                                    id="{{ $input['id_name'] }}"
                                    name="{{ $input['form_name'] }}" 
                                    placeholder="{{ $input['placeholder'] }}"
                                    step = "any"
                                    onblur="check_fecha('{{$input['id_name']}}')"
                                >
                            @endif
                            <span class="focus-input100"></span>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="my-3" style="border-bottom: 1.5px grey solid;"></div>

            <!-- INFORMACIÓN DEL PAGO -->
            <div class="form-row justify-content-center">
                <label class="col-12 font-weight-bold">Información de Pago (Opcional <span style="color:red">*</span>):</label>
                @foreach ($data['form-pago'] as $input)
                    <div class="form-group col-md-6" id="input-{{ $input['id_name'] }}">
                        <label>{{$input['label-name']}} <span style="color:red">*</span> :</label>
                        <div class="input-group validate-input" data-validate="{{ $input['validate'] }}">
                            <div class="d-flex">
                                <div class="m-auto form-control icon-box text-center">
                                    <i class="fa {{ $input['icon'] }} fa-lg m-auto"></i>
                                </div>
                            </div>
                            @if($input['component-type']=="select")
                                <select name="{{ $input['form_name'] }}" 
                                        id="{{ $input['id_name'] }}" 
                                        class="form-control input100 {{ $input['requerido'] }} {{ $errors->has($input['form_name']) ? ' is-invalid' : '' }}" 
                                        style="height: calc(2.19rem + 10px)" >
                                    <option selected value="">{{$input['title']}}</option>
                                    @foreach ($input['options'] as $option)
                                        <option value="{{ $option['value'] }}">{{ $option["nombre"] }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @if($input['component-type']=="input")
                                <input 
                                    class="form-control input100 {{ $input['requerido'] }} {{ $errors->has($input['form_name']) ? ' is-invalid' : '' }}" 
                                    style="height: calc(2.19rem + 10px)"
                                    type="{{ $input['type'] }}" 
                                    id="{{ $input['id_name'] }}"
                                    name="{{ $input['form_name'] }}" 
                                    placeholder="{{ $input['placeholder'] }}"
                                    step = "any"
                                    onblur="check_fecha('{{$input['id_name']}}')"
                                >
                            @endif
                            <span class="focus-input100"></span>
                            @if ($errors->has($input['form_name']))
                                <span class="invalid-feedback" role="alert">
                                    <strong>El campo ya se encuentra registrado</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="my-3" style="border-bottom: 1.5px grey solid;"></div>

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
                        <div class="col">
                            <!-- FECHA DE EXPIRACIÓN -->
                            <strong>Caducidad:</strong>
                        </div> 
                    </div>                        
                </div>
                <div class="col-1"><!-- ES UN SEPARADOR PARA LAS OPCIONES DE LOS BOTONES --></div>

                <!-- APARTADO DE LOS PRODUCTOS UNO A UNO -->
                <div id="productos-{{ $data['form-id'] }}" class="col-12">
                    <div class="form-group col-12 row justify-content-center" id="1">
                        <div class="col-lg p-0"> <!-- PRODUCTO -->
                            <div class="input-group validate-input" data-validate="Producto es requerido">
                                <label class="rl-producto align-self-center col-lg-2 col-md-3 col-sm-3"><strong>Producto:</strong></label>
                                <select name="form-producto-1" 
                                        id="form-producto-1" 
                                        class="form-control input100 border-right req-true" 
                                        style="height: calc(2.19rem + 10px)" >
                                    @foreach($data['form-products'] as $product)
                                        <option id="algo" value="{{ $product['value'] }}">{{ $product['nombre'] }}</option>
                                    @endforeach
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
                                    id="form-price-1"
                                    name="form-price-1" 
                                    placeholder="Ingrese el precio del producto"
                                    min='0.001'
                                    step="any"
                                    value="0.001"
                                    onchange="precio(1)"
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
                                    id="form-cantidad-1"
                                    name="form-cantidad-1" 
                                    placeholder="Ingrese la cantidad del producto"
                                    min='0.001'
                                    step="any"
                                    value="0.001"
                                    onchange="precio(1)"
                                >
                            </div>
                        </div>
                        <div class="col-lg p-0"> <!-- FECHA DE EXPIRACIÓN -->
                            <div class="input-group validate-input" data-validate="Fecha es requerida">
                                <label class="rl-fecha align-self-center col-lg-2 col-md-3 col-sm-3"><strong>Expiración:</strong></label>
                                <input 
                                    class="form-control border-left req-true form-cantidad" 
                                    style="height: calc(2.19rem + 10px)"
                                    type="date" 
                                    id="form-expiracion-1"
                                    name="form-expiracion-1" 
                                    placeholder="Ingrese la fecha de expiracion"
                                    min='0'
                                    step="any"
                                    value="{{ date('Y-m-d') }}"
                                >
                            </div>
                        </div>
                        <div class="d-flex p-0 iva-button"> <!-- CHECK PARA CONTAR EL IVA -->
                            <div class="form-check m-auto">
                                <label class="form-check-label" for="form-expiracion-1">IVA</label>
                                <input type="checkbox" class="d-block m-auto" id="form-iva-1" name="form-iva-1" onchange="precio(1)">
                            </div>
                        </div>
                        <!-- BOTON PARA BORRAR EL PRODUCTO DEL FORM -->
                        <div class="col-1 d-flex p-0 delete-button"> 
                            <!-- <button class="btn product-buttons m-auto" onclick="borrar(1)">
                                <i class="fa fa-trash font-weight-bold"></i>
                            </button> -->
                        </div>
                        <div style="border-bottom: 1px solid #C3CAD6;" class="w-100 my-3 d-separator"></div>
                    </div>
                </div>

                <div class="form-group col-12"> <!-- BOTON PARA AGREGAR NUEVO PRODUCTO -->
                    <div id="agregar-{{ $data['form-id'] }}" style="cursor: pointer; width: fit-content">
                        <i class="fa fa-plus-square fa-lg m-auto"></i>
                        <a>Agregar Producto...</a>
                    </div>
                </div>
            </div>
            
            <!-- TOTAL DE LA VENTA O COMPRA -->
            <div class="form-row mt-2">
                <div class="col-12">
                    <div class="bordes">
                        <div>Total Base: <span id="total-base">0</span> Bs</div>
                        <div>IVA: <span id="IVA">0</span> Bs</div>
                        <input type="hidden" id="total-input" name="monto">
                        <div class="bordes2">TOTAL: <span id="total-todo">0</span> Bs</div>
                    </div>
                </div>
            </div> 
        </form>
        <input type="hidden" id="" class="id-form" value="{{ $data['form-id'] }}">
    </div>

    <!-- BOTONES DE AGREGAR O ECHAR PARA ATRAS -->
    <div id="send-buttons" class="justify-content-end mt-2 p-2" style="display: flex; background-color: #e9ecef">
        <div class="" id="btn-add">
            <button class="btn form-buttons px-5" id="form-add">Agregar</button>
        </div>

        <div class="mx-2" id="btn-back">
            <button class="btn form-buttons px-5" id="form-back" onclick="retroceder()">Volver</button>
        </div>
    </div>
</div>

<script src="{{ asset('js/form_validate.js') }}"></script>
<script>
    //ELIMINO EL SOMBRIADO DEL FORMULARIO
    $(".container-forms").css("box-shadow","none");

    //EVENTO DE CLICK DE AGREGAR COMPRA O VENTA
    (function ($) {
        "use strict";
        var form_id = "{{ $data['form-id'] }}"; 

        //ACOMODANDO PARA QUE EJECUTE LA VALIDACION
        $("#form-add").click(function(event){
            $("#"+form_id).submit();
        });
    })(jQuery);

    //EVENTO PARA AGREGAR NUEVOS PRODUCTOS A LA COMPRA O VENTA
    var agregar = "agregar-{{ $data['form-id'] }}";
    var productos = "productos-{{ $data['form-id'] }}";
    var cantidad = 2;

    $("#"+agregar).click(function() {
        html = "<div class='form-group col-12 row justify-content-center' id="+cantidad+">";
        html +=    "<div class='col-lg p-0'>";
        html +=        "<div class='input-group validate-input' data-validate='Producto es requerido'>";
        html +=            "<label class='rl-producto align-self-center col-lg-2 col-md-3 col-sm-3'><strong>Producto:</strong></label>";
        html +=            "<select name='form-producto-"+cantidad+"' id='form-producto-"+cantidad+"' "; 
        html +=                   "class='form-control input100 border-right req-true' "; 
        html +=                   "style='height: calc(2.19rem + 10px)' >";
                        @foreach($data['form-products'] as $product)
        html +=                    "<option value='{{ $product['value'] }}'>{{ $product['nombre'] }}</option>";
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
        html +=    "<div class='col-lg p-0'>";
        html +=        "<div class='input-group validate-input' data-validate='Fecha es requerida'>";
        html +=            "<label class='rl-fecha align-self-center col-lg-2 col-md-3 col-sm-3'><strong>Expiración:</strong></label>";
        html +=            "<input class='form-control input100 border-left req-true' "; 
        html +=                "style='height: calc(2.19rem + 10px)' type='date' "; 
        html +=                "id='form-expiracion-"+cantidad+"' name='form-expiracion-"+cantidad+"'"; 
        html +=                " placeholder='Ingrese la fecha de expiracion' ";
        html +=                "min='0' step='any' value='{{ date('Y-m-d') }}'>";
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

    //EVENTO QUE ME MODIFICA EL PRECIO DE COMPRA O VENTA
    const precio = () => {
        var price = 0;
        var iva = 0;
        for (i = 1; i <= cantidad-1; i++) {
            //REVISO SI EXISTE EL ELEMENTO
            if ( $('#form-price-'+i).length ) {
                valor = $('#form-price-'+i).val();
                if(valor < 0){
                    valor = 0.001;
                    $('#form-price-'+i).val("0.001");
                }
                precio_producto =  parseFloat(valor);

                valor = $('#form-cantidad-'+i).val();
                if(valor < 0){
                    valor = 0.001;
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
        $("#total-base").text(Math.round(price*100)/100);
        $("#IVA").text(Math.round(iva*100)/100);
        $("#total-todo").text(Math.round((price+iva)*100)/100);

        //SE LO AGREGO AL INPUT DEL TOTAL
        $("#total-input").val(Math.round((price+iva)*100)/100);
    }

    //EVENTO QUE VALIDA LAS FECHAS DE PAGO , VENTA Y COMPRA
        //Fecha Actual
        var f = new Date();
        var month = (f.getMonth() +1); 
        month > 9 ? "" : month = "0"+month;
        var fecha = f.getFullYear() + "-" + month + "-" + f.getDate();
        
        const check_fecha = (id) => {
            var alerta = false;

            //Fecha del Pago
            if(id == "form-fecha-pago"){
                if(Date.parse($("#"+id).val()) > Date.parse(fecha)) {
                    $("#"+id).val(fecha);
                    alerta = true;
                    mensaje = "La fecha de pago debe ser menor al día actual!";
                }

                //Valida que la fecha de pago no se puede sobrepasar a la de venta por debajo
                if($("#form-fecha").val()){
                    if(Date.parse($("#"+id).val()) < Date.parse($("#form-fecha").val())) {
                        $("#"+id).val($("#form-fecha").val());
                        alerta = true;
                        mensaje = "La fecha de pago no puede ser menor que la fecha de venta/compra!";
                    }
                }
            }

            //Fecha de la Venta o compra
            if(id == "form-fecha"){
                if(Date.parse($("#"+id).val()) > Date.parse(fecha)) {
                    $("#"+id).val(fecha);
                    alerta = true;
                    mensaje = "La fecha de venta/compra debe ser menor al día actual!";
                }

                if($("#form-fecha-pago").val()){
                    if(Date.parse($("#"+id).val()) > Date.parse($("#form-fecha-pago").val())) {
                        $("#"+id).val($("#form-fecha").val());
                        alerta = true;
                        mensaje = "La fecha de venta/compra no puede ser mayor que la fecha de pago!";
                    }
                }
            }
            
            if(alerta){
                swal({
                    title: mensaje,
                    icon: 'warning',
                    closeOnClickOutside: false,
                    button: 'Aceptar',
                });
                $(".swal-button").addClass("bg-secondary");
            }
        }
</script>







