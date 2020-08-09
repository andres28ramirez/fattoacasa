@extends('layouts.principal')

@section('title','Indicadores · Fatto a Casa')

@section('titulo','INDICADORES - CLIENTES')

@section('tabs')
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('ind-logistica') }}">Logística</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('ind-venta') }}">Ventas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('ind-compra') }}">Compras</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('ind-client') }}">Clientes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('ind-finanza') }}">Finanzas</a>
        </li>
    </ul>
@endsection

@section('info')

    <!-- INDICAOR TOTALS EN CENTRO -->
    <div class="row justify-content-center my-2 px-2">
        @php
            $indicator_totals = array(
                array(
                    "color" => "#028936",
                    "cantidad" => round($porcentaje_desertores,2)."%",
                    "text" => "TASA DE DESERCIÓN",
                    "figure" => "fa-frown-o"
                ) ,
                array(
                    "color" => "#245743",
                    "cantidad" => round($porcentaje_leales,2)."%",
                    "text" => "TASA DE LEALTAD",
                    "figure" => "fa-smile-o"
                ) ,
                array(
                    "color" => "#FF3017",
                    "cantidad" => $repeticion ? $repeticion->total : 0,
                    "text" => "REPETICIÓN DE COMPRA",
                    "figure" => "fa-shopping-cart"
                ) ,
                array(
                    "color" => "#FF8100",
                    "cantidad" => count($clients),
                    "text" => "TOTAL DE CLIENTES",
                    "figure" => "fa-users"
                ) 
            );
        @endphp
        
        @foreach ($indicator_totals as $totals)
            @include('includes.totals_block',['indicador'=>$totals])
        @endforeach
    </div>

    <!-- INDICADOR CLIENTES CAPTADOS -->
    <div class="row justify-content-center my-2 px-2">
        <!-- GRAFICO DE BARRAS SOBRE CLIENTES -->
        <div class="col-lg col-md-10 col-sm-12">
            @php
                $data_singlebar = array(
                    "texto" => "CLIENTES CAPTADOS",
                    "canva" => "c1",
                    "labels" => array("ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sep.", "oct.", "nov.", "dic."),
                    "datos" => $c1_data,
                    "tipo" => "top",
                    "bgcolors" => "rgba(29,163,101,0.70)",
                    "brcolors" => "rgba(25,61,47,0.8)"
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-client-c1",
                    
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
        <!-- TOP 5 RENTABILIDAD DE CLIENTES -->
        <div class="col-lg-6 col-md-10 col-sm-12">
            @php
                $data_singlebar = array(
                    "texto" => "TOP ".count($c2_data)." - RENTABILIDAD DE CLIENTES",
                    "canva" => "c2",
                    "labels" => $c2_labels,
                    "datos" => $c2_data,
                    "tipo" => "porcentaje",
                    "bgcolors" => "rgba(255,129,0,0.70)",
                    "brcolors" => "rgba(128,66,5,0.8)"
                );
            @endphp
            @include('includes.single_horizontalbar',['data'=>$data_singlebar])
        </div>

        <!-- PRODUCTOS COMPRADOS GENERALMENTE POR UN CLIENTE -->
        <div class="col-lg-6 col-md-10 col-sm-12">
            @php
                $colorBg = array(
                    "rgba(255,48,23,0.15)",
                    "rgba(255,48,23,0.20)",
                    "rgba(255,48,23,0.25)",
                    "rgba(255,48,23,0.30)",
                    "rgba(255,48,23,0.35)",
                    "rgba(255,48,23,0.40)",
                    "rgba(255,48,23,0.45)",
                    "rgba(255,48,23,0.50)",
                    "rgba(255,48,23,0.55)",
                    "rgba(255,48,23,0.60)",
                    "rgba(255,48,23,0.65)",
                    "rgba(255,48,23,0.70)"
                );

                $data_pie = array(
                    "texto" => "PRODUCTOS COMPRADOS - ".strtoupper($titulo_chart),
                    "canva" => "c3",
                    "labels" => array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio.", 
                                        "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"),
                    "datos" => $c3_data,
                    "bgcolors" => $colorBg,
                    "brcolors" => "rgba(128,23,11,0.8)"
                );

                $clientes = array ();

                $one_content = array(
                    "value" => "ID",
                    "nombre" => "Nombre de Cliente",
                );

                foreach ($clients as $row) {
                    $one_content["value"] = $row->id;
                    $one_content["nombre"] = $row->nombre;
                    array_push($clientes,$one_content);
                }

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-client-c3",
                    
                    "form-components" => array(
                        array(
                            "component-type" => "select",
                            "label-name" => "Cliente",
                            "icon" => "fa-user",
                            "id_name" => "form-client",
                            "form_name" => "form-client",
                            "title" => "Seleccione un cliente",
                            "options" => $clientes,
                            "validate" => "Cliente es requerido",
                            "bd-error" => "LO QUE SEA",
                            "requerido" => "req-true",
                        ),
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
    </div>
    <input type="hidden" id="url-filter" value="{{ route('filter-clientes') }}">
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
    <script type="text/javascript" src="{{ asset('js/singlehorizontalbar.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/pie.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/singlebar.js') }}"></script>
@endsection

