<style>
    /* BOTONES DEL FINAL RESPONSIVE */
    @media (max-width: 450px) {  
        #edit-buttons{
            display: block !important;
        }

        #btn-back{
            text-align: center;
        }

        #btn-edit{
            text-align: center;
            margin-bottom: 5px;
        }
    }

    /* RESPONSIVE RIGHT INFORMATION - DETALLADO DE PRODUCTOS*/
    @media (max-width: 1435px) {  
        #detail-detail-products, .detail-product-info, .detail-inline-info{
            font-size: 10px;
        }
    }

    @media (max-width: 1275px) {  
        #detail-detail-products{
            font-size: 8px;
        }
    }

    @media (max-width: 1100px) {  
        #detail-detail-products, .detail-product-info, .detail-inline-info{
            font-size: 12px;
        }
    }

    @media (max-width: 700px) {  
        .detail-product-info{
            font-size: 10px;
        }
    }

    @media (max-width: 576px) {  
        .detail-product-info{
            font-size: 12px;
        }

        .detail-product-info-space{
            border-right: 0 !important;
        }

        .detail-product-info-box{
            height: calc(7.7rem + 10px) !important;
        }

        #detail-detail-products{
            display: none;
        }
    }

    @media (min-width: 577px) {  
        .detail-product-info .rl-producto{
            display: none;
        }

        .detail-product-info .rl-precio{
            display: none;
        }

        .detail-product-info .rl-cantidad{
            display: none;
        }
    }

    @media (max-width: 500px) {  
        #detail-detail-products, .detail-product-info, .detail-inline-info{
            font-size: 10px;
        }

        .right-title{
            font-size: 12px !important;
        }
    }

    /* RESPONSIVIDAD DEL RECETARIO */
    @media (max-width: 1370px) {  
        .recetario-text{
            font-size: 10px;
        }
    }

    @media (max-width: 1200px) {  
        .recetario-text{
            font-size: 12px;
        }

        .margin-cantidad, .margin-delete{
            margin-top: 10px;
        }
    }

    @media (min-width: 1200px) {  
        .recetario-separator{
            display: none;
        }
    }
    
</style>

<!-- BLOQUE DE LA INFORMACIÓN -->
<div class="row justify-content-center my-2 px-3">
    <!-- APARTADO CON LOS DATOS -->
    <div class="col-lg-6 col-md-10 col-sm-12">
        <div class="border">
            <div class="card-header bg-transparent d-flex mb-2">
                <h6 class="my-auto">{{ $title }}</h6>
                <i id="icon-edit" class="ml-2 my-auto fa fa-pencil text-muted fa-lg" style="cursor: pointer"></i>
            </div>

            <form method="POST" action="{{ $data['action'] ? route($data['action']) : '' }}" class="validate-form" id="{{ $data['form-id'] }}">
                @csrf
                <div class="form-row justify-content-center px-3">
                    @foreach ($data['form-components'] as $input)
                        <div class="form-group col-12" id="input-{{ $input['id_name'] }}">
                            <label id="{{ $input['id_name']=='form-re-password' ? 'label-delete' : '' }}">{{$input['label-name']}}:</label>
                            <div class="input-group validate-input" data-validate="{{ $input['validate'] }}">
                                @if($input['component-type']=="input")
                                    @if ($input['id_name'] == "form-cid")
                                        <strong class='text-dark float-left my-auto'>
                                            <select name="tipo_cid" id="tipo_cid" 
                                                    class="form-control icon-box bg-transparent" 
                                                    style="width: fit-content; border-radius: 0px">
                                                <option value="V -" {{ "V -" == $input['value_tipo'] ? "selected" : "" }}>V -</option>
                                                <option value="E -" {{ "E -" == $input['value_tipo'] ? "selected" : "" }}>E -</option>
                                                <option value="J -" {{ "J -" == $input['value_tipo'] ? "selected" : "" }}>J -</option>
                                            </select>
                                        </strong>
                                    @endif
                                    <input 
                                        class="form-control input100 {{ $input['requerido'] }} {{ $errors->has($input['form_name']) ? ' is-invalid' : '' }}" 
                                        style="height: calc(2.19rem + 10px)"
                                        type="{{ $input['type'] }}" 
                                        id="{{ $input['id_name'] }}"
                                        name="{{ $input['form_name'] }}" 
                                        placeholder="{{ $input['placeholder'] }}"
                                        value="{{ $input['value'] }}"
                                        step = "any"
                                        readonly
                                    >
                                @elseif($input['component-type']=="select")
                                    <select name="{{ $input['form_name'] }}" disabled 
                                            id="{{ $input['id_name'] }}" 
                                            class="form-control input100 {{ $input['requerido'] }} {{ $errors->has($input['form_name']) ? ' is-invalid' : '' }}" 
                                            style="height: calc(2.19rem + 10px)" >
                                            <option value="">Selecciona una Opción</option>
                                        @foreach ($input['options'] as $option)
                                            <option value="{{ $option['value'] }}" 
                                                    {{ $option['value'] == $input['value'] ? "selected" : "" }}>
                                                {{ $option["nombre"] }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif($input['component-type']=="textarea")
                                    <textarea 
                                        class="form-control input100 {{ $input['requerido'] }}"
                                        style="height: calc(2.19rem + 10px)"
                                        id="{{ $input['id_name'] }}"
                                        name="{{ $input['form_name'] }}"
                                        placeholder="{{ $input['placeholder'] }}"
                                        value="{{ $input['value'] }}"
                                        readonly
                                    >{{ $input['value'] }}</textarea>
                                @elseif($input['component-type']=="checkbox")
                                    <div class="form-control input100" style="height: fit-content">
                                    @foreach ($input['check-options'] as $option)
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                    type="checkbox" 
                                                    id="cb-{{ $option['label'] }}" 
                                                    name="{{ $option['name'] }}"
                                                    value="1"
                                                    {{ $option['value'] ? "checked" : "" }}>
                                            <label class="form-check-label" for="cb-{{ $option['label'] }}">
                                                {{ $option["label"] }}
                                            </label>
                                        </div>
                                    @endforeach
                                    </div>
                                @endif
                                <span class="focus-input100"></span>
                                @if ($errors->has($input['form_name']))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>El campo a editar ya posee un valor coincidente en los registros</strong>
                                    </span>
                                @endif
                            </div>                          
                        </div>  
                    @endforeach
                </div>
                <input type="hidden" name="id" id="model-id-edit" value="{{ $data['edit-id'] }}">
            </form>
            <input type="hidden" id="" class="id-form" value="{{ $data['form-id'] }}">
        </div>
    </div>

    <!-- APATADO LATERAL CON LA INFORMACIÓN EXTRA -->
    <div class="col-lg-6 col-md-10 col-sm-12">

        @foreach ($data_list as $component)
            <div class="bg-warning float-left mt-3 p-3 rounded-circle" style="width: fit-content; margin-left: -1.5em">
                <i class="fa fa-lg {{ $component['icon'] }}"></i>
            </div>
            @if($component === end($data_list))
            <div class="border-detail p-3 pl-5" style="border-bottom: 1px solid #ced4da;">
            @else
            <div class="border-detail p-3 pl-5">
            @endif
                <div class="rounded">
                @if(isset($component['title']))    
                    <span id="name-{{ $component['table-id'] }}" class="mb-2 col-12 right-title" style="font-size: 1.1em">{{ $component['title'] }}</span>
                @endif
                @if($component['type'] == "table")
                    <div class="table-responsive col-12" id="{{ $component['table-id'] }}">
                        <input type="hidden" class="get-table-id" value="{{ $component['table-id'] }}">
                        <table class="table table-sm" style="overflow: auto; box-shadow: 0px 1px 6px 2px rgba(0,0,0,0.2);">
                            <thead class="thead-success text-dark" style="background-color: #f8fafc">
                                <tr>
                                    @foreach ($component['titulos'] as $titulo)
                                        <th class="text-center" scope="col" style="cursor: pointer">
                                            <span>{{ $titulo }}</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody class="" id="tbody">
                                @foreach($component['content'] as $info)
                                    <tr id="{{ $info['id'] }}">
                                        @for($i = 1; $i <= count($component['titulos']); $i++)
                                            @if(isset($info['dato-'.$i]))     
                                                <td class="text-center">
                                                    {{$info['dato-'.$i]}}
                                                </td>
                                            @endif
                                            <!-- ESTO PARA CUANDO VAYAMOS A AGREGAR COSAS UNICAS EN ALGUNOS LADOS -->
                                            @if(isset($info['estado-'.$i]))
                                                <td class="text-center">
                                                    <span class="badge 
                                                            {{ $info['estado-'.$i] ? 'badge-success' : 'badge-warning' }}">
                                                        {{ $info['estado-'.$i] ? 'Pagado' : 'Pendiente' }}
                                                    </span>
                                                </td> 
                                            @elseif(isset($info['agenda-'.$i]))
                                                <td class="text-center">
                                                    <span class="badge 
                                                            {{ $info['agenda-'.$i] ? 'badge-success' : 'badge-warning' }}">
                                                        {{ $info['agenda-'.$i] ? 'Finalizado' : 'Pendiente' }}
                                                    </span>
                                                </td> 
                                            @endif
                                        @endfor
                                    </tr>
                                @endforeach
                                @if(empty($component['content']))
                                    <tr>
                                        <td colspan="{{ count($component['titulos']) }}">
                                            <h5 class="text-center">NO SE ENCUENTRAN REGISTROS</h5>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @elseif($component['type'] == "totals")
                    @include('includes.indicator_totals',['indicador'=>$component])
                @elseif($component['type'] == "inline-info")
                    @if(!empty($component['information']))
                        @foreach ($component['information'] as $data)
                        <div class="d-block my-2 detail-inline-info">
                            <label class="font-weight-bold">{{ $data['label'] }}:</label>
                            <span class="d-inline form-control border-0 detail-inline-info" style="height: calc(2.19rem + 10px);">
                                {{ $data['dato'] }}
                            </span>
                        </div>
                        @endforeach
                    @endif
                    
                    @if(!empty($component['productos']))
                    <div class="d-block mt-2">
                        <label class="font-weight-bold">DETALLADO DE PRODUCTOS:</label>
                        <div class="col-12 p-2" id="detail-detail-products">
                            <div class="input-group row justify-content-center">
                                <div class="col-6 d-flex">
                                    <!-- PRODUCTO -->
                                    <strong class="my-auto">Producto:</strong>
                                </div>
                                <div class="col-3">
                                    <!-- CANTIDAD -->
                                    <strong class="my-auto">Cantidad (Kg/Und):</strong>
                                </div>
                                <div class="col-3 d-flex">
                                    <!-- PRECIO -->
                                    <strong class="my-auto">Precio:</strong>
                                </div>
                            </div>                        
                        </div>
                        @foreach ($component['productos'] as $product)
                            <div class="form-group form-control col-12 p-0 detail-product-info-box" style="height: calc(2.19rem + 10px)">
                                <div class="input-group row justify-content-center">
                                    <div class="d-flex col-sm-6 col-12 detail-product-info-space" style="border-right: 1px solid #ced4da; overflow: hidden">
                                    <!-- PRODUCTO -->
                                        <div class="my-auto icon-box text-center bg-transparent detail-product-info" style="border: 0px; border-radius: 0px">
                                            <span><strong class="rl-producto">Producto: </strong>{{ $product['name'] }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex col-sm-3 col-12 detail-product-info-space" style="border-right: 1px solid #ced4da; overflow: hidden">
                                    <!-- CANTIDAD -->
                                        <div class="my-auto icon-box text-center bg-transparent detail-product-info" style="border: 0px; border-radius: 0px">
                                            <span><strong class="rl-cantidad">Cantidad (Kg/Und): </strong>{{ $product['cantidad'] }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex col-sm-3 col-12">
                                    <!-- PRECIO -->
                                        <div class="my-auto icon-box text-center bg-transparent detail-product-info" style="border: 0px; border-radius: 0px">
                                            <span><strong class="rl-precio">Precio: </strong>{{ number_format($product['precio'],2, ",", ".") }} Bs</span>
                                        </div>
                                    </div>
                                </div>                          
                            </div>
                        @endforeach
                        <div class="form-group col-12">
                            <div class="d-none" id="editar-orden-button" style="cursor: pointer; width: fit-content">
                                <i class="fa fa-arrow-circle-right fa-lg m-auto"></i>
                                <a>Editar Orden...</a>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($component['recetario']))
                    <div class="d-block">
                        <label class="font-weight-bold right-title">Recetario del Producto:</label>
                        @php $cantidad = 1; @endphp
                        <div class="form-row justify-content-center">
                            <div id="recetario-{{ $data['form-id'] }}" class="col-12">
                            @foreach ($component['recetario'] as $product)
                                <div class="form-group col-12 row justify-content-center" id="{{ $cantidad }}">
                                    <div class="col-xl-5 col-12 p-0 recetario-text">
                                        <!-- PRODUCTO -->
                                        <label>Producto:</label>
                                        <div class="input-group validate-input" data-validate="Producto es requerido">
                                            <select name="form-producto-{{ $cantidad }}"
                                                    id="form-producto-{{ $cantidad }}" 
                                                    class="form-control border-right req-false" 
                                                    style="height: calc(2.19rem + 10px)" >
                                                <option selected value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                                                @foreach ($data_products as $recet)
                                                    <option value="{{ $recet['value'] }}">{{ $recet["nombre"] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-5 col-12 p-0 recetario-text margin-cantidad">
                                        <!-- CANTIDAD -->
                                        <label>Cantidad (Kg / Und):</label>
                                        <div class="input-group validate-input form-cantidad" data-validate="Producto es requerido">
                                            <input 
                                                class="form-control input100 border-left req-false" 
                                                style="height: calc(2.19rem + 10px)"
                                                type="number" 
                                                id="form-cantidad-{{ $cantidad }}"
                                                name="form-cantidad-{{ $cantidad }}" 
                                                placeholder="Ingrese el nombre del producto"
                                                value="{{ $product['cantidad'] }}"
                                                readonly
                                                min='0'
                                                step="any"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-lg d-flex p-0 margin-delete">
                                        <button id="" class="btn recetario-buttons m-xl-auto mt-xl-0 mt-1" onclick="borrar({{ $cantidad }})">
                                            <i class="fa fa-trash font-weight-bold"></i>
                                        </button>
                                    </div>
                                    <div style="border-bottom: 1px solid #C3CAD6;" class="w-100 my-3 recetario-separator"></div>
                                </div>
                                @php $cantidad++; @endphp
                            @endforeach
                            </div>

                            <div class="form-group col-12">
                                <div class="d-none" id="agregar-{{ $data['form-id'] }}" style="cursor: pointer; width: fit-content">
                                    <i class="fa fa-plus-square fa-lg m-auto"></i>
                                    <a>Agregar Producto...</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                @endif
                </div>
            </div>
        @endforeach

    </div>
</div>

<!-- BOTONES DE EDITAR O ECHAR PARA ATRAS -->
<div id="edit-buttons" class="d-flex justify-content-end mt-2 p-2" style="background-color: #F6FAF5">
    <!-- APARTADO CON LOS DATOS -->
    <div class="" id="btn-edit">
        <button class="btn table-buttons px-5" disabled id="form-edit">Editar</button>
    </div>

    <!-- APATADO LATERAL CON LA INFORMACIÓN EXTRA -->
    <div class="mx-2" id="btn-back">
        <button class="btn table-buttons px-5" id="form-back" onclick="retroceder()">Volver</button>
    </div>
</div>

<script>
    //ELIMINO EL SOMBRIADO DEL FORMULARIO, BORDES Y BOTON SUBMIT
        $(".container-forms").css("border","0px");
        $(".container-forms").css("box-shadow","none");

    //EVENTO DE CLICK DE EDITAR PARA MODIFICAR EL FORMULARIO
    (function ($) {
        "use strict";
        @if(isset($data['form-id'])) var agregar = "agregar-{{ $data['form-id'] }}"; @endif
        var form_id; 

        $(".id-form").each(function(indice,elemento){
            form_id = $(this).val();
        });

        //ACOMODANDO PARA QUE EJECUTE LA VALIDACION
            $("#form-edit").click(function(event){
                //
                $("#"+form_id).submit();
                //EL RECETARIO PRODUCTO ES UN AJAX QUE SE EJECUTA EN FORM-VALIDATE.JS
                //SI TODO VA BIEN DEJA EL CHECK EN TRUE PARA QUE TERMINE DE LANZAR EL ACTION DEL FORM
            });
    
        //BOTON DE EDITAR FORMULARIO DE EDICIÓN
            $("#icon-edit").click(function(event){
                $("#form-edit").removeAttr("disabled");
                $('#'+form_id+' .input100').removeAttr("readonly");
                $('#'+form_id+' .input100').removeAttr("disabled");
                $("#recetario-"+form_id+" .input100").removeAttr("readonly");
                $('#editar-orden-button').removeClass("d-none");
                $('#'+agregar).removeClass("d-none");
                $('#form-re-password').removeClass("d-none");
                $("#label-delete").removeClass("d-none");
            });

        //EVENTO PARA QUITAR LA CONFIRMACIÓN DE CONTRASEÑA SE LLEGA Y LO MUESTRE LUEGO DE DARLE EDITAR
            $("#form-re-password").addClass("d-none");
            $("#label-delete").addClass("d-none");

        //EVENTO CLICK QUE ABRE EL MODAL DE LA ORDEN
            $("#editar-orden-button").click(function(event){
                $('#table-orden').modal(true);
            });
    })(jQuery);

    //EVENTO PARA AGREGAR NUEVOS PRODUCTOS AL RECETARIO
        @if(isset($cantidad))
            var agregar = "agregar-{{ $data['form-id'] }}";
            var recetario = "recetario-{{ $data['form-id'] }}";
            var cantidad = parseInt("{{ $cantidad }}", 16);

            $("#"+agregar).click(function() {
                html = "<div class='form-group col-12 row justify-content-center' id="+cantidad+">";
                html +=   "<div class='col-xl-5 col-12 p-0 recetario-text'>";
                html +=       "<label>Producto:</label>";
                html +=        "<div class='input-group validate-input' data-validate='Producto es requerido'>";
                html +=           "<select name='form-producto-"+cantidad+"'"; 
                html +=                   " id='form-producto-"+cantidad+"'"; 
                html +=                   " class='form-control input100 border-right req-true'"; 
                html +=                   " style='height: calc(2.19rem + 10px)' >";
                                @if(isset($data_products))
                                    @foreach ($data_products as $recet)
                html +=                 "<option value='{{ $recet['value'] }}'>{{ $recet['nombre'] }}</option>";
                                    @endforeach
                                @endif
                html +=            "</select>";
                html +=        "</div>";
                html +=    "</div>";
                html +=    "<div class='col-xl-5 col-12 p-0 recetario-text margin-cantidad'>";
                html +=        "<label>Cantidad (Kg / Und):</label>";
                html +=        "<div class='input-group validate-input' data-validate='Producto es requerido'>";
                html +=            "<input class='form-control input100 border-left req-true form-cantidad'"; 
                html +=                " style='height: calc(2.19rem + 10px)' type='number'"; 
                html +=                " id='form-cantidad-"+cantidad+"' name='form-cantidad-"+cantidad+"'"; 
                html +=                " value='0' placeholder='Ingrese la cantidad del producto' min='0' step='any'>";
                html +=        "</div>";
                html +=    "</div>";
                html +=    "<div class='col-lg d-flex p-0 margin-delete'>";
                html +=        "<button class='btn recetario-buttons m-xl-auto mt-xl-0 mt-1' onclick='borrar("+cantidad+")'>";
                html +=            "<i class='fa fa-trash font-weight-bold'></i>";
                html +=        "</button>";
                html +=    "</div>";
                html +=    "<div style='border-bottom: 1px solid #C3CAD6;' class='w-100 my-3 recetario-separator'></div>";
                html +="</div>";

                $("#"+recetario).append(html);
                cantidad++;
            });

            function borrar(codigo){
                $("#"+codigo).remove();
            }
        @endif
</script>