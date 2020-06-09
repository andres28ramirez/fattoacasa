<div class="form-title">
    <span class="form-title2">
        {{ $data['title'] }}
    </span>
</div>

<div class="container-forms">
    <form method="POST" action="{{ $data['action'] ? route($data['action']) : '' }}" class="validate-form" id="{{ $data['form-id'] }}">
        @csrf

        <div class="form-row justify-content-center">
            @foreach ($data['form-components'] as $input) 
                <div class="form-group col-md-6" id="input-{{ $input['id_name'] }}">
                    <label>{{$input['label-name']}}:</label>
                    <div class="input-group validate-input" data-validate="{{ $input['validate'] }}">
                        @if($input['component-type']!="checkbox")
                            <div class="d-flex">
                                <div class="m-auto form-control icon-box text-center">
                                    <i class="fa {{ $input['icon'] }} fa-lg m-auto"></i>
                                </div>
                            </div>
                        @endif
                        @if($input['component-type']=="input")
                            @if ($input['id_name'] == "form-cid")
                                <strong class='text-dark float-left my-auto'>
                                    <select name="tipo_cid" 
                                            class="form-control icon-box bg-transparent" 
                                            style="width: fit-content; border-radius: 0px">
                                        <option value="V -">V -</option>
                                        <option value="E -">E -</option>
                                        <option value="J -">J -</option>
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
                                value="{{ old($input['form_name']) }}"
                                step = "any"
                            >
                        @elseif($input['component-type']=="select")
                            <select name="{{ $input['form_name'] }}" 
                                    id="{{ $input['id_name'] }}" 
                                    class="form-control input100 {{ $input['requerido'] }} {{ $errors->has($input['form_name']) ? ' is-invalid' : '' }}" 
                                    style="height: calc(2.19rem + 10px)" >
                                <option selected value="">{{$input['title']}}</option>
                                @foreach ($input['options'] as $option)
                                    <option value="{{ $option['value'] }}">{{ $option["nombre"] }}</option>
                                @endforeach
                            </select>
                        @elseif($input['component-type']=="textarea")
                            <textarea 
                                class="form-control input100 {{ $input['requerido'] }}"
                                style="height: calc(2.19rem + 10px)"
                                id="{{ $input['id_name'] }}"
                                name="{{ $input['form_name'] }}"
                                placeholder="{{ $input['placeholder'] }}"
                            ></textarea>
                        @elseif($input['component-type']=="checkbox")
                        <div class="form-control input100 {{ $input['requerido'] }}" style="height: fit-content">
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
                                <strong>El campo ya se encuentra registrado</strong>
                            </span>
                        @endif
                    </div>                          
                </div>  
            @endforeach
        </div>  
        
        @if(isset($data['type']))
            <div class="card-header bg-transparent text-center pb-1 mb-3">
                Recetario del Producto
            </div>

            <div class="form-row justify-content-center">
                <div id="recetario-{{ $data['form-id'] }}" class="col-12">
                    <div class="form-group col-12 row justify-content-center" id="1">
                        <div class="col-6 p-0">
                            <!-- PRODUCTO -->
                            <label>Producto:</label>
                            <div class="input-group validate-input" data-validate="Producto es requerido">
                                <select name="form-producto-1" 
                                        id="form-producto-1" 
                                        class="form-control border-right req-false" 
                                        style="height: calc(2.19rem + 10px)" required>
                                    <option selected value="">Selecciona una Opción</option>
                                    @foreach ($data_products as $product)
                                        <option value="{{ $product['value'] }}">{{ $product["nombre"] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-5 p-0">
                            <!-- CANTIDAD -->
                            <label>Cantidad (Kg / Und):</label>
                            <div class="input-group validate-input" data-validate="Cantidad es requerida">
                                <input 
                                    class="form-control border-left req-false form-cantidad" 
                                    style="height: calc(2.19rem + 10px)"
                                    type="text" 
                                    id="form-cantidad-1"
                                    name="form-cantidad-1" 
                                    placeholder="Ingrese el nombre del producto"
                                    min='0'
                                    step="any"
                                >
                            </div>
                        </div>
                        <div class="col-lg d-flex p-0">
                            <button id="" class="btn table-buttons m-auto" onclick="borrar(1)">
                                <i class="fa fa-trash font-weight-bold"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group col-12">
                    <div id="agregar-{{ $data['form-id'] }}" style="cursor: pointer; width: fit-content">
                        <i class="fa fa-plus-square fa-lg m-auto"></i>
                        <a>Agregar Producto...</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="form-row btn-submit">
            <div class="m-auto"> 
                <button class="form-btn" id="submit-{{ $data['form-id'] }}">Enviar</button>
            </div>
        </div>
        @if(isset($data['add-id']))    
            <input type="hidden" name="id" id="add-id-{{ $data['form-id'] }}" value="{{ $data['add-id'] }}">
        @endif
    </form>
    <input type="hidden" id="" class="id-form" value="{{ $data['form-id'] }}">
</div>

<script src="{{ asset('js/form_validate.js') }}"></script>
<script>
    var agregar = "agregar-{{ $data['form-id'] }}";
    var recetario = "recetario-{{ $data['form-id'] }}";
    var cantidad = 2;
    
    $("#"+agregar).click(function() {
        html = "<div class='form-group col-12 row justify-content-center' id="+cantidad+">";
        html +=   "<div class='col-6 p-0'>";
        html +=       "<label>Producto:</label>";
        html +=        "<div class='input-group validate-input' data-validate='Producto es requerido'>";
        html +=           "<select name='form-producto-"+cantidad+"'"; 
        html +=                   " id='form-producto-"+cantidad+"'"; 
        html +=                   " class='form-control input100 border-right req-false'"; 
        html +=                   " style='height: calc(2.19rem + 10px)' >";
                        @if(isset($data_products))
                            @foreach ($data_products as $product)
        html +=                 "<option value='{{ $product['value'] }}'>{{ $product['nombre'] }}</option>";
                            @endforeach
                        @endif
        html +=            "</select>";
        html +=        "</div>";
        html +=    "</div>";
        html +=    "<div class='col-5 p-0'>";
        html +=        "<label>Cantidad (Kg / Und):</label>";
        html +=        "<div class='input-group validate-input' data-validate='Producto es requerido'>";
        html +=            "<input class='form-control input100 border-left req-false form-cantidad'"; 
        html +=                " style='height: calc(2.19rem + 10px)' type='number'"; 
        html +=                " id='form-cantidad-"+cantidad+"' name='form-cantidad-"+cantidad+"'"; 
        html +=                " value='0' placeholder='Ingrese la cantidad del producto' min='0' step='any'>";
        html +=        "</div>";
        html +=    "</div>";
        html +=    "<div class='col-lg d-flex p-0'>";
        html +=        "<button class='btn table-buttons m-auto' onclick='borrar("+cantidad+")'>";
        html +=            "<i class='fa fa-trash font-weight-bold'></i>";
        html +=        "</button>";
        html +=    "</div>";
        html +="</div>";

        $("#"+recetario).append(html);
        cantidad++;
    });

	function borrar(codigo){
		$("#"+codigo).remove();
	}
</script>