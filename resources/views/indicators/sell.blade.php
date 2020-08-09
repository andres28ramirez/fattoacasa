@extends('layouts.principal')

@section('title','Indicadores · Fatto a Casa')

@section('titulo','INDICADORES - VENTAS')

@section('tabs')
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('ind-logistica') }}">Logística</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('ind-venta') }}">Ventas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('ind-compra') }}">Compras</a>
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

    <!-- INDICADOR DE AUMENTO DE VENTAS Y EL TOTAL EN CANTIDAD -->
    <div class="row justify-content-center my-2 px-2">
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $data_doublehorizontalbars = array(
                    "texto" => "COMPARACIÓN AUMENTO DE VENTAS",
                    "canva" => "c1",
                    "chartHeight" => "100px",
                    "type" => "porcentaje",
                    "labels" => array("ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sep.", "oct.", "nov.", "dic."),
                    "bar-label" => "Num. Ventas",
                    "bar-datos" => $bar_data,
                    "bar-bgcolors" => "rgba(29,163,101,0.7)",
                    "bar-brcolors" => "rgba(25,61,47,0.8)",
                    "line-label" => "Aum. Ventas",
                    "line-datos" => $bar_line,
                    "line-bgcolors" => "rgba(255,48,23,0.7)",
                    "line-brcolors" => "rgba(128,23,11,0.8)",
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-sell-c1",
                    
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
            @include('includes.mixed_barline',['data'=>$data_doublehorizontalbars, 'data_form'=>$data_form])
        </div>
    </div>

    <!-- BLOQUE DE TOTALES -->
    <div class="row justify-content-center my-3 px-2">
        <!-- TOTALES DE VALORES EN GENERAL DE DESPACHOS Y VENTAS -->
        <div class="col-lg row">
            @php
                $indicator_totals = array(
                    array(
                        "color-header" => "#BF6000",
                        "color-inside" => "#FF8100",
                        "cantidad" => $venta_promedio ? number_format($venta_promedio->promedio,2, ",", ".")." Bs" : "0 Bs",
                        "text" => "VALOR PROMEDIO DE VENTA",
                        "figure" => "fa-product-hunt",
                        "col" => "col-lg"
                    ),
                    array(
                        "color-header" => "#014A1D",
                        "color-inside" => "#028936",
                        "cantidad" => round($pagos_dias)." días",
                        "text" => "PLAZO MEDIO DE PAGOS",
                        "figure" => "fa-hourglass-half",
                        "col" => "col-lg"
                    ),
                    array(
                        "color-header" => "#80170B",
                        "color-inside" => "#FF3017",
                        "cantidad" => $pendientes ? $pendientes->total : 0,
                        "text" => "COBROS PENDIENTES",
                        "figure" => "fa-archive",
                        "col" => "col-lg"
                    ),
                    array(
                        "color-header" => "#193D2F",
                        "color-inside" => "#29634C",
                        "cantidad" => $pagos ? $pagos->total : 0,
                        "text" => "PAGOS RECIBIDOS",
                        "figure" => "fa-money",
                        "col" => "col-lg"
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
        <!-- TOP 10 PRODUCTOS VENDIDOS -->
        <div class="col-lg-5 col-md-10 col-sm-12">
            @php
                $DataIndicator = array(
                    "texto" => "TOP ".count($top10_products)." PRODUCTOS VENDIDOS",
                    "indicator" => "c2",
                    "total" => $total_top10,
                    "total_text" => "Global Productos Vendidos",
                    "bgColorTotal" => "#028936",
                    "bgColorProduct" => "#FF3017",
                    "filtrar" => false,
                    "porcentaje" => true
                );

                $DataTop = $top10_products;
            @endphp
            @include('includes.indicator_progress', ['data'=>$DataTop, 'totals'=>$DataIndicator])
        </div>

        <!-- TOTAL VENTAS REALIZADAS -->
        <div class="col-lg-7 col-md-10 col-sm-12">
            @php
                $data_singleline = array(
                    "texto" => "TOTAL VENTAS REALIZADAS",
                    "canva" => "c2",
                    "labels" => array("ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sep.", "oct.", "nov.", "dic."),
                    "datos" => $bar_data,
                    "bgcolors" => "rgba(255,48,23,0.7)",
                    "brcolors" => "rgba(128,23,11,0.8)",
                    "chartHeight" => "150px"
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-sell-c2",
                    
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

    <!-- RENTABILIDAD - BENEFICIO DE VENTA -->
    <div class="row justify-content-center my-2 px-2">
        <div class="col-lg col-md-10 col-sm-12">
            <!-- INGRESOS DE VENTAS - EGRESOS DE COMPRAS POR CADA MES Y SACO EL BENEFICIO -->
            <!-- BAR ES EL INGRESO Y LA LINE ES EL DE BENEFICIO -->
            @php
                $data_doublehorizontalbars = array(
                    "texto" => "RENTABILIDAD DE VENTAS",
                    "canva" => "c3",
                    "chartHeight" => "100px",
                    "type" => "normal",
                    "labels" => array("ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sep.", "oct.", "nov.", "dic."),
                    "bar-label" => "Ingresos Ven.",
                    "bar-datos" => $c3_bar_data,
                    "bar-bgcolors" => "rgba(255,129,0,0.7)",
                    "bar-brcolors" => "rgba(128,66,5,0.8)",
                    "line-label" => "Beneficios Ven.",
                    "line-datos" => $c3_bar_line,
                    "line-bgcolors" => "rgba(29,163,101,0.7)",
                    "line-brcolors" => "rgba(25,61,47,0.8)",
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-sell-c3",
                    
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
            @include('includes.mixed_barline',['data'=>$data_doublehorizontalbars, 'data_form'=>$data_form])
        </div>
    </div>
    <input type="hidden" id="url-filter" value="{{ route('filter-ventas') }}">
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

        //BORRAR EL BOTON DE FILTRAR DE ALGUNOS INDICADORES
            $("#horizontalbars-filter").remove();

        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");
    </script>
    <script type="text/javascript" src="{{ asset('js/mixedbarline.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/doublehorizontalbars.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/doublebars.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/singlebar.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/singleline.js') }}"></script>
@endsection

