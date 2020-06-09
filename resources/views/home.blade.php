@extends('layouts.principal')

@section('title','Home · Fatto a Casa')

@section('titulo','FATTO A CASA')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card">
        <div class="card-body" style="background-color: rgba(255,255,255,0.4)">    

    <!-- LOS 4 BLOQUES DE ARRIBA -->
    <div class="row justify-content-center my-2 px-2">
        @php
            $indicator_totals = array(
                array(
                    "color" => "#028936",
                    "cantidad" => $clientes ? $clientes->total : 0,
                    "text" => "CLIENTES",
                    "figure" => "fa-users"
                ) ,
                array(
                    "color" => "#245743",
                    "cantidad" => $inventarios ? round($inventarios->total,2) : 0,
                    "text" => "INVENTARIO",
                    "figure" => "fa-dropbox"
                ) ,
                array(
                    "color" => "#FF3017",
                    "cantidad" => $proveedores ? $proveedores->total : 0,
                    "text" => "PROVEEDORES",
                    "figure" => "fa-briefcase"
                ) ,
                array(
                    "color" => "#FF8100",
                    "cantidad" => $suministros ? round($suministros->total,2) : 0,
                    "text" => "ALMACEN",
                    "figure" => "fa-clipboard"
                ) 
            );
        @endphp
        
        @foreach ($indicator_totals as $totals)
            @include('includes.totals_block',['indicador'=>$totals])
        @endforeach
    </div>
    
    <!-- LOS 2 BLOQUES DEL MEDIO -->
    <div class="row justify-content-center my-3 px-2">
        <!-- GRAFICO DE BARRAS SOBRE VENTAS -->
        <div class="col-lg-9 col-md-10 col-sm-12 mt-2">
            @php
                $data_doublebar = array(
                    "texto" => "COMPARACIÓN VENTAS - COMPRAS",
                    "canva" => "c1",
                    "labels" => array("ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sep.", "oct.", "nov.", "dic."),
                    "bar-label-1" => "Compras",
                    "bar-datos-1" => $compras_data,
                    "bar-bgcolors-1" => "rgba(29,163,101,0.7)",
                    "bar-brcolors-1" => "rgba(25,61,47,0.8)",
                    "bar-label-2" => "Ventas",
                    "bar-datos-2" => $ventas_data,
                    "bar-bgcolors-2" => "rgba(255,129,0,0.7)",
                    "bar-brcolors-2" => "rgba(128,66,5,0.8)",
                );

                $data_form = array(
                    "action" => "",
                    "title" => "FILTRO TOTALES DE ENTRADAS - SALIDAS DE INVENTARIO",
                    "form-id" => "form-home-c1",
                    
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

        <!-- AGENDA TAREAS POR HACER -->
        <!--<div class="d-flex bg-primary col-lg-3 col-md-10 col-sm-12 mt-2" style="overflow: auto" id="dashboard-agenda">
            <div class="m-auto text-white font-weight-bold">
                AGENDA<br>AGENDA<br>AGENDA<br>AGENDA<br>AGENDA<br>AGENDA<br>AGENDA<br>AGENDA<br>AGENDA<br>AGENDA<br>
            </div>
        </div>-->
        <div class="col-lg-3 col-md-10 col-sm-12 mt-2">
            <div class="m-auto font-weight-bold">
                @php
                    $data_list = array(
                        "table-id" => "agenda",
                        "title" => "Agenda",
                        "registros" => 1,
                        "filter" => false,
                        "title-click" => false,
                        "titulos" => array(
                            array(
                                "nombre" => "Fecha",
                                "bd-name" => "start",
                            ),
                            array(
                                "nombre" => "Título",
                                "bd-name" => "title",
                            ),
                        ),
                        "content" => array(),
                    );
                    
                    foreach ($agendas as $agenda) {
                    $data_content["id"] = $agenda->id;
                    $data_content["dato-1"] = $agenda->start;
                    $data_content["dato-2"] = $agenda->title;
                    $data_content["dato-3"] = $agenda->title;
                    $data_content["dato-4"] = $agenda->description;

                    array_push($data_list["content"],$data_content);
                }
                @endphp
                @include('includes.general_table',['data'=>$data_list])
            </div>
        </div>
    </div>

    @if(Auth::user()->permiso_venta)
    <!-- LOS 2 BLOQUES DE ABAJO -->
    <div class="row justify-content-center my-3 px-2">
        <!-- TOP 5 DESPACHOS POR HACER -->
        <div class="col-lg-6 col-md-10 col-sm-12 mt-2">
            <div class="m-auto font-weight-bold">
            @php
                $data_list = array(
                    "table-id" => "despacho-cliente",
                    "title" => $despachos ? "Ultimos ".count($despachos)." despachos pendientes:" : "Despachos pendientes:",
                    "registros" => 1,
                    "filter" => false,
                    "title-click" => false,
                    "titulos" => array(
                        array(
                            "nombre" => "Código",
                            "bd-name" => "nombre",
                        ),
                        array(
                            "nombre" => "Cliente",
                            "bd-name" => "rif_cedula",
                        ),
                        array(
                            "nombre" => "Fecha de Venta",
                            "bd-name" => "telefono",
                        ),
                        array(
                            "nombre" => "Fecha de Despacho",
                            "bd-name" => "correo",
                        ),
                    ),
                    "content" => array(),
                );

                $data_content = array(
                    "id" => 3,
                    "dato-1" => "3",
                    "dato-2" => "Excelsior Gama",
                    "dato-3" => "28-06-1996",
                    "dato-4" => "01-07-1996",
                );

                foreach ($despachos as $despacho) {
                    $data_content["id"] = $despacho->id;
                    $data_content["dato-1"] = $despacho->id;
                    $data_content["dato-2"] = $despacho->venta->cliente->nombre;
                    $data_content["dato-3"] = $despacho->venta->fecha;
                    $data_content["dato-4"] = $despacho->fecha;

                    array_push($data_list["content"],$data_content);
                }
            @endphp
            @include('includes.general_table',['data'=>$data_list])
            </div>
        </div>

        <!-- TOP 5 CUENTAS POR COBRAR PENDIENTES -->
        <div class="col-lg-6 col-md-10 col-sm-12 mt-2">
            <div class="m-auto font-weight-bold">
            @php
                $data_list = array(
                    "table-id" => "venta-pagar",
                    "title" => $cuentas ? "Ultimas ".count($cuentas)." cuentas por cobrar pendientes:" : "Cuentas por cobrar pendientes:",
                    "registros" => 1,
                    "filter" => false,
                    "title-click" => false,
                    "titulos" => array(
                        array(
                            "nombre" => "Cliente",
                            "bd-name" => "id_cliente",
                        ),
                        array(
                            "nombre" => "Monto",
                            "bd-name" => "monto",
                        ),
                        array(
                            "nombre" => "Fecha",
                            "bd-name" => "fecha",
                        ),
                        array(
                            "nombre" => "Credito",
                            "bd-name" => "credito",
                        ),
                        array(
                            "nombre" => "Correo",
                            "bd-name" => "Contactar",
                        ),
                    ),
                    "content" => array(),
                );

                $data_content = array(
                    "id" => 1,
                    "dato-1" => "Excelsior Gama",
                    "dato-2" => "25869,123 Bs",
                    "dato-3" => "28-06-1996",
                    "dato-4" => "30 días",
                    "contact-5" => "andresramirez2025@gmail.com",
                );

                foreach ($cuentas as $sell) {
                    $data_content["id"] = $sell->id;
                    $data_content["dato-1"] = $sell->cliente->nombre;
                    $data_content["dato-2"] = $sell->monto." Bs";
                    $data_content["dato-3"] = $sell->fecha;
                    $data_content["dato-4"] = $sell->credito." días";
                    $data_content["contact-5"] = $sell->cliente->correo;

                    array_push($data_list["content"],$data_content);
                }
            @endphp
            @include('includes.general_table',['data'=>$data_list])
            </div>
        </div>
    </div>
    @endif

        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="url-filter" value="{{ route('filter-home') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script src="{{ asset('js/ajaxIndicators.js') }}"></script>
    <script src="{{ asset('js/indicatorsDownload.js') }}"></script>
    <script>
        //BORRO EL TITULO DE LOS FORMULARIOS DE FILTRADO
            $(".form-title").remove();

        //ELIMINO EL SOMBRIADO DEL FORMULARIO Y LOS BORDES
            $(".container-forms").css("border","0px");
            $(".container-forms").css("box-shadow","none");

        //BORRAR TODOS LOS BOTONES DE LA TABLA DE DESPACHOS Y CUENTAS POR COBRAR
            $(".num-reg-table").remove();
            $(".button-table-options").remove();

        //BORRAR LA PAGINACIÓN
            $(".pagination-table").remove();

        //ELIMINAR LOS CHECK BUTTONS
            $("#th-venta-pagar").remove();
            $(".td-venta-pagar").remove();

            $("#th-agenda").remove();
            $(".td-agenda").remove();

            $("#th-despacho-cliente").remove();
            $(".td-despacho-cliente").remove();

        //REDIRECCIÓN A SUS RESPECTIVAS VISTAS
            //CUENTAS POR PAGAR
            $(".tr-venta-pagar").click(function() {
                location.href = "{{ route('list-cuentas') }}";
            });

            //Agenda/Calendario
            $(".tr-agenda").click(function() {
                location.href = "{{ url('Calendario') }}";
            });
            //DESPACHOS
            $(".tr-despacho-cliente").click(function() {
                location.href = "{{ route('list-despachos') }}";
            });
    </script>
    <script type="text/javascript" src="{{ asset('js/doublebars.js') }}"></script>
@endsection

