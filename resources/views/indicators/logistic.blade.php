@extends('layouts.principal')

@section('title','Indicadores · Fatto a Casa')

@section('titulo','INDICADORES - LOGÍSTICA')

@section('tabs')
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('ind-logistica') }}">Logística</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('ind-venta') }}">Ventas</a>
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

    <!-- INDICADOR ENTRADAS - SALIDAS -->
    <div class="row justify-content-center my-2 px-2">
        <!-- GRAFICO DE BARRAS SOBRE ENTRADAS -->
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $c1_labels = array();
                $c1_salidas = array();
                $c1_entradas = array();
                foreach($entradas as $row){
                    array_push($c1_labels,$row->producto->nombre);
                    array_push($c1_entradas,$row->cantidad);
                    $grabo = false;
                    foreach($salidas as $col){
                        if($col["id_producto"] == $row->id_producto){
                            array_push($c1_salidas,$col["cantidad"]*-1); 
                            $grabo=true;
                        }
                    }
                    $grabo ? "" : array_push($c1_salidas,0);
                }

                $data_doublehorizontalbars = array(
                    "texto" => "TOP 5 PRODUCTOS (ENTRADAS - SALIDAS) DE INVENTARIO",
                    "canva" => "c1",
                    "chartHeight" => "70px",
                    "labels" => $c1_labels,
                    "bar-label-1" => "Salidas",
                    "bar-datos-1" => $c1_salidas,
                    "bar-bgcolors-1" => "rgba(255,129,0,0.7)",
                    "bar-brcolors-1" => "rgba(128,66,5,0.8)",
                    "bar-label-2" => "Entradas",
                    "bar-datos-2" => $c1_entradas,
                    "bar-bgcolors-2" => "rgba(29,163,101,0.7)",
                    "bar-brcolors-2" => "rgba(25,61,47,0.8)",
                );
            @endphp
            @include('includes.horizontal_bars',['data'=>$data_doublehorizontalbars])
        </div>
    </div>

    <!-- LOS 2 BLOQUES siguientes -->
    <div class="row justify-content-center my-3 px-2">
        <!-- TOTALES DE VALORES EN GENERAL DE DESPACHOS Y VENTAS -->
        <div class="col-lg-6 col-md-10 col-sm-12 row">
            @php
                $indicator_totals = array(
                    array(
                        "color-header" => "#BF6000",
                        "color-inside" => "#FF8100",
                        "cantidad" => $sales ? round($sales->cantidad) : 0,
                        "text" => "INVENTARIO VENDIDO",
                        "figure" => "fa-dropbox",
                        "col" => "col-lg-12"
                    ) ,
                    array(
                        "color-header" => "#80170B",
                        "color-inside" => "#FF3017",
                        "cantidad" => $despachos_f ? count($despachos_f) : 0,
                        "text" => "DESPACHOS REALIZADOS",
                        "figure" => "fa-check-square-o",
                        "col" => "col-lg-6"
                    ),
                    array(
                        "color-header" => "#014A1D",
                        "color-inside" => "#028936",
                        "cantidad" => $despachos_p ? count($despachos_p) : 0,
                        "text" => "DESPACHOS PENDIENTES",
                        "figure" => "fa-times-rectangle",
                        "col" => "col-lg-6"
                    )
                );
            @endphp
            @foreach ($indicator_totals as $totals)
                @include('includes.indicator_totals',['indicador'=>$totals])
            @endforeach
        </div>

        <!-- DESPACHOS REALIZADOS YA ABARCADO (MES, SEMANA, DIAS, ETC) -->
        <div class="col-lg-6 col-md-10 col-sm-12">
            @php
                $c2_data = array();
                for($i = 1; $i <= 12; $i++){
                    $grabar = true;
                    foreach($despachos as $row){
                        if($i == $row->mes){
                            array_push($c2_data,$row->total); $grabar=false;
                        }
                    }
                    $grabar ? array_push($c2_data,0) : "";
                }

                $data_singlebar = array(
                    "texto" => "TOTAL DESPACHOS REALIZADOS",
                    "canva" => "c2",
                    "labels" => array("ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sep.", "oct.", "nov.", "dic."),
                    "datos" => $c2_data,
                    "tipo" => "normal",
                    "bgcolors" => "rgba(255,48,23,0.7)",
                    "brcolors" => "rgba(128,23,11,0.8)"
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-logistic-c2",
                    
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
            @include('includes.single_bar',['data'=>$data_singlebar, 'data_formulario'=>$data_form])
        </div>
    </div>

    <!-- LOS 2 BLOQUES siguientes -->
    <div class="row justify-content-center my-3 px-2">
        <!-- TOTALES DE ENTRADAS Y SALIDAS GENERAL POR DETERMINADOS TIEMPOS -->
        <div class="col-lg-6 col-md-10 col-sm-12">
            @php
                $c3_entradas = array();
                $c3_salidas = array();
                for($i = 0; $i < 7; $i++){
                    $grabar_entradas = true;
                    $grabar_salidas = true;
                    
                    foreach($dia_entradas as $row){
                        if($i == $row->dia){
                            array_push($c3_entradas,$row->cantidad); $grabar_entradas=false;
                        }
                    }

                    foreach($dia_salidas as $row){
                        if($i == $row->dia){
                            array_push($c3_salidas,$row->cantidad); $grabar_salidas=false;
                        }
                    }

                    $grabar_entradas ? array_push($c3_entradas,0) : "";
                    $grabar_salidas ? array_push($c3_salidas,0) : "";
                }

                $data_doublebar = array(
                    "texto" => "TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "canva" => "c3",
                    "labels" => array("dom.", "lun.", "mar.", "mie.", "jue.", "vie.", "sab."),
                    "bar-label-1" => "Entradas",
                    "bar-datos-1" => $c3_entradas,
                    "bar-bgcolors-1" => "rgba(29,163,101,0.7)",
                    "bar-brcolors-1" => "rgba(25,61,47,0.8)",
                    "bar-label-2" => "Salidas",
                    "bar-datos-2" => $c3_salidas,
                    "bar-bgcolors-2" => "rgba(255,129,0,0.7)",
                    "bar-brcolors-2" => "rgba(128,66,5,0.8)",
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-logistic-c3",
                    
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
            @include('includes.double_bars',['data'=>$data_doublebar, 'data_form'=>$data_form])
        </div>

        <!-- ROTACIÓN DE INVENTARIO (GENERAL, PRODUCTOS FRESCOS Y TRANSFORMADOS) -->
        <div class="col-lg-6 col-md-10 col-sm-12">
            @php
                $data_singlebar = array(
                    "texto" => "ROTACIÓN DE INVENTARIO - N# VECES EN GLOBAL",
                    "canva" => "c4",
                    "labels" => array('Total', 'Prod. Frescos', 'Prod. Procesados'),
                    "datos" => array($rotacion_total, $rotacion_fresco, $rotacion_procesado),
                    "tipo" => "normal",
                    "bgcolors" => "rgba(255,129,0,0.7)",
                    "brcolors" => "rgba(128,66,5,0.8)"
                );
            @endphp
            @include('includes.single_bar',['data'=>$data_singlebar])
        </div>
    </div>

    <!-- ULTIMO BLOQUE DE INDICADOR -->
    <div class="row justify-content-center my-2 px-2">
        <!-- GRAFICO DE LINEAS SOBRE VENTA DE INVENTARIO -->
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $c5_data = array();
                for($i = 1; $i <= 12; $i++){
                    $grabar = true;
                    foreach($ventas as $row){
                        if($i == $row->mes){
                            array_push($c5_data,$row->cantidad); $grabar=false;
                        }
                    }
                    $grabar ? array_push($c5_data,0) : "";
                }

                $data_singleline = array(
                    "texto" => "INVENTARIO VENDIDO",
                    "canva" => "c5",
                    "labels" => array("ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sep.", "oct.", "nov.", "dic."),
                    "datos" => $c5_data,
                    "bgcolors" => "rgba(29,163,101,0.7)",
                    "brcolors" => "rgba(25,61,47,0.8)",
                    "chartHeight" => "70px"
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-logistic-c5",
                    
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
    <input type="hidden" id="url-filter" value="{{ route('filter-logistica') }}">
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
    <script type="text/javascript" src="{{ asset('js/doublehorizontalbars.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/doublebars.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/singlebar.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/singleline.js') }}"></script>
@endsection

