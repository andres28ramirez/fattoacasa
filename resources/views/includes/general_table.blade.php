<style>
    .table-buttons:hover {
        background-color: rgba(0,0,0,0.4);
        color: rgba(255,255,255,1);
        -webkit-transition: all 1s ease;
        -moz-transition: all 1s ease;
        -o-transition: all 1s ease;
        -ms-transition: all 1s ease;
        transition: all 1s ease;
    }

    .table-buttons {
        color: #898989;
    }
</style>

<div class="col-12 row justify-content-between">
    <span id="name-{{ $data['table-id'] }}" class="mb-2 col-12" style="font-size: 1.1em">{{ $data['title'] }}</span>

    <div class='col-lg-6 col-md-6 col-sm-6 col-12 d-flex num-reg-table'>
        <h6 class='text-muted float-left my-auto mr-1'>NÚMERO DE REGISTROS:</h6>
        <strong class='text-dark float-left my-auto mr-1'>
            <select id="num-register-{{ $data['table-id'] }}" class="form-control" style="width: fit-content">
                <option value="10" {{ "10" == $data['registros'] ? "selected" : "" }}>10</option>
                <option value="15" {{ "15" == $data['registros'] ? "selected" : "" }}>15</option>
                <option value="20" {{ "20" == $data['registros'] ? "selected" : "" }}>20</option>
                <option value="25" {{ "25" == $data['registros'] ? "selected" : "" }}>25</option>
                <option value="30" {{ "30" == $data['registros'] ? "selected" : "" }}>30</option>
                <option value="35" {{ "35" == $data['registros'] ? "selected" : "" }}>35</option>
            </select>
        </strong>
        <span class="text-dark float-left my-auto" id="register-send-{{ $data['table-id'] }}">
            <button class="btn table-buttons" onClick="redirect_table('registros')">Enviar</button>
        </span>
        @if($data['filter'])
            <span class="text-dark float-left my-auto ml-1">
                <button class="btn table-buttons" onClick="redirect_table('refresh')">Limpiar Filtrado</button>
            </span>
        @endif
    </div>

    <!-- BOTONES PARA AGREGAR / FILTRAR / BORRAR -->
    <div class='col-lg-6 col-md-6 col-sm-6 col-12 text-sm-right button-table-options'>
        <button id="add-{{ $data['table-id'] }}" class="btn table-buttons" onClick="redirect_table('agregar')">
            <i class="fa fa-plus font-weight-bold"></i>
        </button>
        <button id="filter-{{ $data['table-id'] }}" class="btn table-buttons" onClick="redirect_table('filtrar')">
            <i class="fa fa-filter font-weight-bold"></i>
        </button>
        <button id="delete-{{ $data['table-id'] }}" class="btn table-buttons" onClick="redirect_table('eliminar')">
            <i class="fa fa-trash font-weight-bold"></i>
        </button>
        <button id="print-{{ $data['table-id'] }}" class="btn table-buttons" onClick="redirect_table('print')">
            <i class="fa fa-print font-weight-bold"></i>
        </button>
    </div>
</div>

<div class="table-responsive col-12 my-1" id="{{ $data['table-id'] }}">
    <input type="hidden" class="get-table-id" value="{{ $data['table-id'] }}">
    <table class="table table-striped table-hover" style="overflow: auto; box-shadow: 0px 1px 6px 2px rgba(0,0,0,0.2);">
        <thead class="thead-success text-white" style="background-color: #707070">
            <tr>
                <th class="text-center" scope="col" id="th-{{ $data['table-id'] }}">
                    <div class="form-check">
                        <input id="check-all-{{ $data['table-id'] }}" type="checkbox" value="" style="cursor: pointer">
                    </div>
                </th>
                @foreach ($data['titulos'] as $titulo)
                    <th class="text-center" scope="col" style="cursor: pointer" onClick="redirect_table('{{ $titulo['bd-name'] }}')">
                        <span>{{ $titulo["nombre"] }}</span>
                        @if(isset($data['title-click']) && $data['title-click']==$titulo["bd-name"])
                            <i class="fa fa-caret-down"></i>
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody class="" id="tbody">
            @foreach($data['content'] as $one)
                <tr class="" style="cursor: pointer" id="{{ $one['id'] }}">
                    <td class="text-center td-{{ $data['table-id'] }}">
                        <div class="form-check" id="check-{{ $data['table-id'] }}" >
                            <input class="check-data" type="checkbox" value="{{ $one['id'] }}" style="cursor: pointer">
                        </div>
                    </td>
                    @for($i = 1; $i <= count($data['titulos']); $i++)
                        @if(isset($one['dato-'.$i]))     
                            <td class="text-center tr-{{ $data['table-id'] }}">
                                {{$one['dato-'.$i]}}
                            </td>
                        @endif
                        <!-- ESTO PARA CUANDO VAYAMOS A AGREGAR COSAS UNICAS EN ALGUNOS LADOS -->
                        @if(isset($one['estado-'.$i]))
                            <td class="text-center">
                                @switch($one['estado-'.$i])
                                    @case("Pendiente")
                                        <span class="badge badge-warning">{{ $one['estado-'.$i] }}</span>
                                        @break

                                    @case("Pagado")
                                        <span class="badge badge-success">{{ $one['estado-'.$i] }}</span>
                                        @break
                                    
                                    @case("Finalizado")
                                        <span class="badge badge-success">{{ $one['estado-'.$i] }}</span>
                                        @break

                                    @default
                                        <span class="badge badge-danger">{{ $one['estado-'.$i] }}</span>
                                @endswitch
                            </td>
                        @elseif(isset($one['cantidad-'.$i]))
                            <td class="text-center tr-{{ $data['table-id'] }}">
                                <span>{{$one['cantidad-'.$i]}}</span> Kg/Und
                            </td>
                        @elseif(isset($one['pdf-'.$i]))
                            <td class="text-center">
                                <i class="fa fa-file-pdf-o fa-lg mr-1"></i>
                                <a href="#">{{$one['pdf-'.$i]}}</a>
                            </td>
                        @elseif(isset($one['opciones-'.$i]))
                            <td class="text-center">
                                @if (isset($one['pago']) && !$one['pago'])
                                    <button id="{{ $one['id'] }}" class="btn btn-secondary pago-add">Pago</button>
                                @endif
                                @if (isset($one['desperdicio']) && $one['desperdicio'])
                                    <button id="{{ $one['id'] }}" class="btn btn-secondary desperdicio-add">Desperdicio</button>
                                @endif
                                @if (isset($one['despacho']) && $one['despacho'])
                                    <button id="{{ $one['id'] }}" class="btn btn-secondary despacho-add">Despacho</button>
                                @endif
                            </td>
                        @elseif(isset($one['contact-'.$i]))
                            <td class="text-center email-send">
                                <input type="hidden" value="{{ $one['contact-'.$i] }}">
                                <i class="fa fa-2x fa-envelope-o fa-lg mr-1"></i>
                            </td>
                        @endif
                    @endfor
                </tr>
            @endforeach
            @if(empty($data['content']))
                <tr>
                    <td colspan="{{ count($data['titulos'])+1 }}">
                        <h5 class="text-center">NO SE ENCUENTRAN REGISTROS O RESULTADOS COINCIDENTES</h5>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- SCRIPT DE TABLE -->
<script>
    (function ($) {
        "use strict";
        var table_id; 

        $(".get-table-id").each(function(indice,elemento){
            table_id = $(this).val();
        });

        //EVENTO QUE EVALUA LOS CHECK DE LA TABLA Y ACOMODA EL ENVIO DE NRO REGISTROS
        $("#register-send-"+table_id).hide();
        $("#num-register-"+table_id).change(function() {
            $("#register-send-"+table_id).show(500);
        });

        //SI LE DOY AL CHECK SUPREMO CAMBIA AL MISMO VALOR TODOS LOS OTROS CHECK
        $("#check-all-"+table_id).on("click", function() {  
            $("#check-"+table_id+" .check-data").prop("checked", this.checked);
        });  

        // aqui revisa si todo esta checked y acomoda el boton de check-all de ser falta  
        $("#check-"+table_id+" .check-data").on("click", function() {  
            if ($("#check-"+table_id+" .check-data").length == $("#check-"+table_id+" .check-data:checked").length) {  
                $("#check-all-"+table_id).prop("checked", true);  
            } else {  
                $("#check-all-"+table_id).prop("checked", false);  
            }  
        });
    })(jQuery);
</script>