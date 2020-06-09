@extends('layouts.principal')

@section('title','Indicadores · Fatto a Casa')

@section('titulo','INDICADORES - COMPRAS')

@section('tabs')
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('ind-logistica') }}">Logística</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('ind-venta') }}">Ventas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('ind-compra') }}">Compras</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('ind-client') }}">Clientes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('ind-finanza') }}">Finanzas</a>
        </li>
    </ul>
@endsection

@section('info')
    <!-- PRIMER BLOQUE DE INDICADOR -->
    <div class="row justify-content-center my-2 px-2">
        <!-- GRAFICO DE LINEAS EGRESOS GENERADOS POR COMPRAS -->
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $data_singleline = array(
                    "texto" => "DINERO GASTADO EN COMPRAS",
                    "canva" => "c1",
                    "labels" => array("ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sep.", "oct.", "nov.", "dic."),
                    "datos" => $c1_data,
                    "bgcolors" => "rgba(255,129,0,0.7)",
                    "brcolors" => "rgba(128,66,5,0.8)",
                    "chartHeight" => "80px"
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-buy-c1",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "select",
                            "label-name" => "Periodo a Evaluar",
                            "icon" => "fa-leanpub",
                            "id_name" => "form-periodo",
                            "form_name" => "form-periodo",
                            "title" => "Selecciona un periodo",
                            "options" => array(
                                array(
                                    "value" => "Mensual",
                                    "nombre" => "Mensual",
                                ),
                                array(
                                    "value" => "Días",
                                    "nombre" => "Días de la Semana",
                                ),
                            ),
                            "validate" => "Periodo es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "select",
                            "label-name" => "Tiempo a Evaluar",
                            "icon" => "fa-hourglass-half",
                            "id_name" => "form-tiempo",
                            "form_name" => "form-tiempo",
                            "title" => "Selecciona un tiempo",
                            "options" => array(
                                array(
                                    "value" => "Año",
                                    "nombre" => "Año",
                                ),
                                array(
                                    "value" => "Específico",
                                    "nombre" => "Específico",
                                ),
                                array(
                                    "value" => "Todos",
                                    "nombre" => "Todas las Fechas",
                                ),
                            ),
                            "validate" => "Tiempo a evaluar es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Selecionar Año",
                            "icon" => "fa-calendar-o",
                            "type" => "number",
                            "id_name" => "form-año",
                            "form_name" => "form-año",
                            "placeholder" => "Ingrese el año deseado",
                            "validate" => "Año es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Primera fecha",
                            "icon" => "fa-calendar",
                            "type" => "month",
                            "id_name" => "form-fecha-1",
                            "form_name" => "form-fecha-1",
                            "placeholder" => "",
                            "validate" => "Primera fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Segunda fecha",
                            "icon" => "fa-calendar",
                            "type" => "month",
                            "id_name" => "form-fecha-2",
                            "form_name" => "form-fecha-2",
                            "placeholder" => "",
                            "validate" => "Segunda fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                    ),
                );
            @endphp
            @include('includes.single_line',['data'=>$data_singleline, 'data_form'=>$data_form])
        </div>
    </div>

    <!-- LOS 2 BLOQUES siguientes -->
    <div class="row justify-content-center my-3 px-2">
        <!-- COMPRAS TOTALES POR MES -->
        <div class="col-lg-6 col-md-10 col-sm-12">
            @php
                $colorBg = array(
                    "rgba(255,129,0,0.15)",
                    "rgba(255,129,0,0.20)",
                    "rgba(255,129,0,0.25)",
                    "rgba(255,129,0,0.30)",
                    "rgba(255,129,0,0.35)",
                    "rgba(255,129,0,0.40)",
                    "rgba(255,129,0,0.45)",
                    "rgba(255,129,0,0.50)",
                    "rgba(255,129,0,0.55)",
                    "rgba(255,129,0,0.60)",
                    "rgba(255,129,0,0.65)",
                    "rgba(255,129,0,0.70)"
                );

                $data_pie = array(
                    "texto" => "COMPRAS TOTALES",
                    "canva" => "c2",
                    "labels" => array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio.", 
                                        "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"),
                    "datos" => $c2_data,
                    "bgcolors" => $colorBg,
                    "brcolors" => "rgba(128,66,5,0.8)"
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-buy-c2",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "select",
                            "label-name" => "Periodo a Evaluar",
                            "icon" => "fa-leanpub",
                            "id_name" => "form-periodo",
                            "form_name" => "form-periodo",
                            "title" => "Selecciona un periodo",
                            "options" => array(
                                array(
                                    "value" => "Mensual",
                                    "nombre" => "Mensual",
                                ),
                                array(
                                    "value" => "Días",
                                    "nombre" => "Días de la Semana",
                                ),
                            ),
                            "validate" => "Periodo es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "select",
                            "label-name" => "Tiempo a Evaluar",
                            "icon" => "fa-hourglass-half",
                            "id_name" => "form-tiempo",
                            "form_name" => "form-tiempo",
                            "title" => "Selecciona un tiempo",
                            "options" => array(
                                array(
                                    "value" => "Año",
                                    "nombre" => "Año",
                                ),
                                array(
                                    "value" => "Específico",
                                    "nombre" => "Específico",
                                ),
                                array(
                                    "value" => "Todos",
                                    "nombre" => "Todas las Fechas",
                                ),
                            ),
                            "validate" => "Tiempo a evaluar es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Selecionar Año",
                            "icon" => "fa-calendar-o",
                            "type" => "number",
                            "id_name" => "form-año",
                            "form_name" => "form-año",
                            "placeholder" => "Ingrese el año deseado",
                            "validate" => "Año es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Primera fecha",
                            "icon" => "fa-calendar",
                            "type" => "month",
                            "id_name" => "form-fecha-1",
                            "form_name" => "form-fecha-1",
                            "placeholder" => "",
                            "validate" => "Primera fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Segunda fecha",
                            "icon" => "fa-calendar",
                            "type" => "month",
                            "id_name" => "form-fecha-2",
                            "form_name" => "form-fecha-2",
                            "placeholder" => "",
                            "validate" => "Segunda fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                    ),
                );
            @endphp
            @include('includes.pie',['data'=>$data_pie, 'data_form'=>$data_form])
        </div>

        <!-- TOTALES DE VALORES EN GENERAL DE COMPRAS -->
        <div class="col-lg-6 col-md-10 col-sm-12 row">
            @php
                $indicator_totals = array(
                    array(
                        "color-header" => "#014A1D",
                        "color-inside" => "#028936",
                        "cantidad" => $compra_promedio ? round($compra_promedio->promedio, 2)." Bs" : "0 Bs",
                        "text" => "VALOR PROMEDIO DE COMPRA",
                        "figure" => "fa-product-hunt",
                        "col" => "col-lg-12"
                    ) ,
                    array(
                        "color-header" => "#80170B",
                        "color-inside" => "#FF3017",
                        "cantidad" => $pendientes ? $pendientes->total : 0,
                        "text" => "CUENTAS POR PAGAR",
                        "figure" => "fa-money",
                        "col" => "col-lg-6"
                    ),
                    array(
                        "color-header" => "#BF6000",
                        "color-inside" => "#FF8100",
                        "cantidad" => round($pagos_dias)." días",
                        "text" => "PLAZO MEDIO DE PAGO",
                        "figure" => "fa-hourglass-half",
                        "col" => "col-lg-6"
                    )
                );
            @endphp
            @foreach ($indicator_totals as $totals)
                @include('includes.indicator_totals',['indicador'=>$totals])
            @endforeach
        </div>
    </div>

    <!-- LOS 2 BLOQUES siguientes -->
    <div class="row justify-content-center my-3 px-2">
        <!-- CALIDAD GLOBAL POR PROVEEDOR -->
        <div class="col-lg-6 col-md-10 col-sm-12">
            @php
                
                $data_singlebar = array(
                    "texto" => "TOP PROVEEDORES - CALIDAD GLOBAL",
                    "canva" => "c3",
                    "labels" => $c3_labels,
                    "datos" => $c3_data,
                    "tipo" => "porcentaje",
                    "bgcolors" => "rgba(255,48,23,0.7)",
                    "brcolors" => "rgba(128,23,11,0.8)"
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-buy-c3",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "select",
                            "label-name" => "Tiempo a Evaluar",
                            "icon" => "fa-hourglass-half",
                            "id_name" => "form-tiempo",
                            "form_name" => "form-tiempo",
                            "title" => "Selecciona un tiempo",
                            "options" => array(
                                array(
                                    "value" => "Año",
                                    "nombre" => "Año",
                                ),
                                array(
                                    "value" => "Específico",
                                    "nombre" => "Específico",
                                ),
                                array(
                                    "value" => "Todos",
                                    "nombre" => "Todas las Fechas",
                                ),
                            ),
                            "validate" => "Tiempo a evaluar es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Selecionar Año",
                            "icon" => "fa-calendar-o",
                            "type" => "number",
                            "id_name" => "form-año",
                            "form_name" => "form-año",
                            "placeholder" => "Ingrese el año deseado",
                            "validate" => "Año es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Primera fecha",
                            "icon" => "fa-calendar",
                            "type" => "month",
                            "id_name" => "form-fecha-1",
                            "form_name" => "form-fecha-1",
                            "placeholder" => "",
                            "validate" => "Primera fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Segunda fecha",
                            "icon" => "fa-calendar",
                            "type" => "month",
                            "id_name" => "form-fecha-2",
                            "form_name" => "form-fecha-2",
                            "placeholder" => "",
                            "validate" => "Segunda fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                    ),
                );
            @endphp
            @include('includes.single_bar',['data'=>$data_singlebar, 'data_formulario'=>$data_form])
        </div>

        <!-- CALIDAD ESPECIFICA POR PRODUCTO -->
        <div class="col-lg-6 col-md-10 col-sm-12">
            @php
                $data_singlebar = array(
                    "texto" => "TOP PROVEEDORES - CALIDAD (".strtoupper($titulo_chart).")",
                    "canva" => "c4",
                    "labels" => $c4_labels,
                    "datos" => $c4_data,
                    "tipo" => "porcentaje",
                    "bgcolors" => "rgba(29,163,101,0.7)",
                    "brcolors" => "rgba(25,61,47,0.8)"
                );

                $productos = array ();

                $one_content = array(
                    "value" => "ID",
                    "nombre" => "Nombre de Producto",
                );

                foreach ($products as $pro) {
                    $one_content["value"] = $pro->id;
                    $one_content["nombre"] = $pro->nombre;
                    array_push($productos,$one_content);
                }

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-buy-c4",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "select",
                            "label-name" => "Tiempo a Evaluar",
                            "icon" => "fa-hourglass-half",
                            "id_name" => "form-tiempo",
                            "form_name" => "form-tiempo",
                            "title" => "Selecciona un tiempo",
                            "options" => array(
                                array(
                                    "value" => "Año",
                                    "nombre" => "Año",
                                ),
                                array(
                                    "value" => "Específico",
                                    "nombre" => "Específico",
                                ),
                                array(
                                    "value" => "Todos",
                                    "nombre" => "Todas las Fechas",
                                ),
                            ),
                            "validate" => "Tiempo a evaluar es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "select",
                            "label-name" => "Producto",
                            "icon" => "fa-list-alt",
                            "id_name" => "form-producto",
                            "form_name" => "form-producto",
                            "title" => "Selecciona un producto",
                            "options" => $productos,
                            "validate" => "Producto es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Selecionar Año",
                            "icon" => "fa-calendar-o",
                            "type" => "number",
                            "id_name" => "form-año",
                            "form_name" => "form-año",
                            "placeholder" => "Ingrese el año deseado",
                            "validate" => "Año es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Primera fecha",
                            "icon" => "fa-calendar",
                            "type" => "month",
                            "id_name" => "form-fecha-1",
                            "form_name" => "form-fecha-1",
                            "placeholder" => "",
                            "validate" => "Primera fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                        array(
                            "component-type" => "input",
                            "label-name" => "Segunda fecha",
                            "icon" => "fa-calendar",
                            "type" => "month",
                            "id_name" => "form-fecha-2",
                            "form_name" => "form-fecha-2",
                            "placeholder" => "",
                            "validate" => "Segunda fecha es requerida",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-false",
                        ),
                    ),
                );
            @endphp
            @include('includes.single_bar',['data'=>$data_singlebar, 'data_formulario'=>$data_form])
        </div>
    </div>
    <input type="hidden" id="url-filter" value="{{ route('filter-compras') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script src="{{ asset('js/ajaxIndicators.js') }}"></script>
    <script src="{{ asset('js/indicatorsDownload.js') }}"></script>
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#iu").addClass("active");
            $("#iu").removeClass("icono_head");
            $(".iu").removeClass("icono_color");
        
        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
    <script type="text/javascript" src="{{ asset('js/pie.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/singlebar.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/singleline.js') }}"></script>
@endsection

