@extends('layouts.principal')

@section('title','Home · Fatto a Casa')

@section('titulo','FATTO A CASA')

@section('content')
<style>
    .info-box {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        border-radius: .25rem;
        background: #fff;
        display: -ms-flexbox;
        display: flex;
        margin-bottom: 1rem;
        min-height: 80px;
        padding: .5rem;
        position: relative;
    }

    .info-box .info-box-icon {
        border-radius: .25rem;
        -ms-flex-align: center;
        align-items: center;
        display: -ms-flexbox;
        display: flex;
        font-size: 1.875rem;
        -ms-flex-pack: center;
        justify-content: center;
        text-align: center;
        width: 70px;
    }

    .info-box .info-box-content {
        -ms-flex: 1;
        flex: 1;
        padding: 5px 10px;
    }

    .info-box .info-box-text, .info-box .progress-description {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .info-box .info-box-number {
        display: block;
        font-weight: 700;
    }

    .products-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .product-list-in-card>.item {
        border-radius: 0;
        border-bottom: 1px solid rgba(0,0,0,.125);
    }

    .products-list>.item {
        border-radius: .25rem;
        background: #fff;
        padding: 10px 0;
    }

    .products-list .product-title {
        font-weight: 600;
    }

    .products-list .product-description {
        color: #6c757d;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

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
                    "text" => "SUMINISTRO",
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
        
        <!-- AGENDA -->
        <div class="col-lg-3 col-md-10 col-sm-12 mt-2 text-white">
            <div class="m-auto font-weight-bold mb-3 text-dark">EVENTOS EN LA AGENDA CON:</div>
            
            <div class="info-box mb-3 tr-agenda" style="background-color: #FF3017; cursor: pointer;">
                <span class="info-box-icon"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Clientes</span>
                    <span class="info-box-number">{{$agenda_cliente}}</span>
                </div>
            </div>

            <div class="info-box mb-3 tr-agenda" style="background-color: #FF8100; cursor: pointer;">
                <span class="info-box-icon"><i class="fa fa-shopping-basket"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Proveedores</span>
                    <span class="info-box-number">{{$agenda_proveedor}}</span>
                </div>
            </div>

            <div class="info-box mb-3 tr-agenda" style="background-color: #245743; cursor: pointer;">
                <span class="info-box-icon"><i class="fa fa-tags"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Trabajadores</span>
                    <span class="info-box-number">{{$agenda_trabajador}}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- LOS BLOQUES DE ABAJO -->
    <div class="row justify-content-center my-3 px-2">
        <!-- TOP 5 DESPACHOS Y CUENTAS POR HACER Y COBRAR -->
        @if(Auth::user()->permiso_venta)
        <div class="{{ Auth::user()->permiso_logistica ? 'col-lg-9' : 'col-lg-12' }} col-md-10 col-sm-12 mt-2">
            <!-- TOP 5 DESPACHOS POR HACER -->
            <div class="card">
                <div class="card-header py-3 border-transparent">
                    <h6 class="my-auto font-weight-bold" style="color: #333333; letter-spacing: 1px">DESPACHOS PENDIENTES</h6>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cliente</th>
                                    <th>Fecha de Despacho</th>
                                    <th>Despachador</th>
                                    <th>Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $existencia = false @endphp
                                @foreach($despachos as $row)
                                    @if($row->venta)
                                        @php $existencia = true @endphp
                                        <tr>
                                            <td><a href="{{ route('detail-despacho', ['id' => $row->id]) }}">{{$row->id}}</a></td>
                                            <td>{{$row->venta->cliente->nombre}}</td>
                                            <td>{{$row->fecha}}</td>
                                            <td>{{$row->trabajador->nombre. " " .$row->trabajador->apellido}}</td>
                                            <td>{{$row->nota}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$existencia)
                                    <tr>
                                        <td colspan="5">No hay ningun despacho pendiente</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <a href="{{ route('list-despachos') }}" class="btn btn-sm btn-secondary float-right">Ver todos los despachos</a>
                </div>
                <!-- /.card-footer -->
            </div>

            <!-- TOP 5 CUENTA POR COBRAR PENDIENTE -->
            <div class="card mt-4">
                <div class="card-header py-3 border-transparent">
                    <h6 class="my-auto font-weight-bold" style="color: #333333; letter-spacing: 1px">ULTIMAS VENTAS CON PAGOS PENDIENTES</h6>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>Venta ID</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php  $existencia=false; @endphp
                                @foreach($cuentas as $row)
                                    @php
                                        $existencia = true; 
                                        if( strtotime($row->fecha."+ ".$row->credito." days") - strtotime(date("d-m-Y")) > 3*86400){
                                            $estado = "Pendiente";
                                            $color = "badge-warning";
                                        }
                                        else{
                                            if( strtotime($row->fecha."+ ".$row->credito." days") - strtotime(date("d-m-Y")) > 0*86400){
                                                $estado = "Por Caducar";
                                                $color = "badge-danger";
                                            }
                                            else{
                                                $estado = "Caducado";
                                                $color = "badge-danger";
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td><a href="{{ route('detail-venta', ['id' => $row->id]) }}">{{$row->id}}</a></td>
                                        <td>{{$row->cliente->nombre}}</td>
                                        <td>{{number_format($row->monto,2, ",", ".")}} Bs</td>
                                        <td>{{$row->fecha}}</td>
                                        <td><span class="badge {{$color}}">{{$estado}}</span></td>
                                    </tr>
                                @endforeach
                                @if(!$existencia)
                                    <tr>
                                        <td colspan="5">No hay ninguna cuenta pendiente</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <a href="{{ route('list-cuentas') }}" class="btn btn-sm btn-secondary float-right">Ver las cuentas por cobrar</a>
                </div>
                <!-- /.card-footer -->
            </div>
        </div>
        @endif

        <!-- INVENTARIO Y SUMINISTRO PROXIMO A QUEDARSE SIN EXITENCIA -->
        @if(Auth::user()->permiso_logistica)
        <div class="{{ Auth::user()->permiso_venta ? 'col-lg-3' : 'col-lg-12' }} col-md-10 col-sm-12 mt-2">
            <!-- INVENTARIO -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title my-auto font-weight-bold" style="color: #333333; letter-spacing: 1px">INVENTARIO CERCA DE INEXISTENCIA</h6>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @php $existencia = false @endphp
                        @foreach($d_inventario as $row)
                            @if($row['cantidad'] < 10)
                            @php $existencia = true @endphp
                            <li class="item">
                                <div class="product-info">
                                    <a class="product-title">{{$row['nombre']}}
                                        <span class="badge {{ $row['cantidad'] == 0 ? 'badge-danger' : 'badge-warning'}} float-right">{{$row['cantidad']}}</span>
                                    </a>
                                    <span class="product-description">{{$row['descripcion']}}</span>
                                </div>
                            </li>
                            @endif
                        @endforeach

                        @if(!$existencia)
                            <li class="p-2">Todos los productos en orden</li>
                        @endif
                    </ul>
                </div>
              <!-- /.card-body -->
                <div class="card-footer text-center">
                    <a href="{{ route('list-inventario') }}" class="uppercase">Ver el Inventario</a>
                </div>
              <!-- /.card-footer -->
            </div>

            <!-- SUMINISTRO -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title my-auto font-weight-bold" style="color: #333333; letter-spacing: 1px">SUMINISTRO CERCA DE INEXISTENCIA</h6>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @php $existencia = false @endphp
                        @foreach($d_suministro as $row)
                            @if($row['cantidad'] < 10)
                            @php $existencia = true @endphp
                            <li class="item">
                                <div class="product-info">
                                    <a class="product-title">{{$row['nombre']}}
                                        <span class="badge {{ $row['cantidad'] == 0 ? 'badge-danger' : 'badge-warning'}} float-right">{{$row['cantidad']}}</span>
                                    </a>
                                    <span class="product-description">{{$row['descripcion']}}</span>
                                </div>
                            </li>
                            @endif
                        @endforeach

                        @if(!$existencia)
                            <li class="p-2">Todos los productos en orden</li>
                        @endif
                    </ul>
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-center">
                    <a href="{{ route('list-suministro') }}" class="uppercase">Ver el Suministro</a>
                </div>
                <!-- /.card-footer -->
            </div>
        </div>
        @endif
    </div>

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

        //REDIRECCIÓN A SUS RESPECTIVAS VISTAS
            //CUENTAS POR PAGAR
            $(".tr-venta-pagar").click(function() {
                location.href = "{{ route('list-cuentas') }}";
            });

            //CALENDARIO
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

