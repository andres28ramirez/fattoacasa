@extends('layouts.principal')

@section('title','Ventas · Fatto a Casa')

@section('titulo','FATTO A CASA - DESPACHOS')

@section('tabs')
    <ul class="nav nav-tabs opciones">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-ventas')}}">Listado de Ventas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-pedidos') }}">Pedidos sin Despacho</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-dark active font-weight-bold" href="{{ route('list-despachos')}}">Despachos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-cuentas')}}">Cuentas por Cobrar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('list-pagos')}}">Pagos Recibidos</a>
        </li>
    </ul>
@endsection

@section('info')

    <style>
        .table-buttons:hover {
            background-color: #028936;
            color: rgba(255,255,255,1);
            -webkit-transition: all 1s ease;
            -moz-transition: all 1s ease;
            -o-transition: all 1s ease;
            -ms-transition: all 1s ease;
            transition: all 1s ease;
        }

        .table-buttons {
            color: white;
            background-color: rgba(2,137,54,0.5);
        }

        .superior-buttons:hover {
            background-color: rgba(0,0,0,0.4);
            color: rgba(255,255,255,1);
            -webkit-transition: all 1s ease;
            -moz-transition: all 1s ease;
            -o-transition: all 1s ease;
            -ms-transition: all 1s ease;
            transition: all 1s ease;
        }

        .superior-buttons {
            color: #898989;
        }

        .border-detail {
            border-top: 1px solid #ced4da;
            border-left: 1px solid #ced4da;
        }
    </style>

    @if(session('message'))
        <h3 class="text-center alert alert-success">{{ session('message') }}</h3>
    @endif

    @if(session('status'))
        <h3 class="text-center alert alert-danger">{{ session('status') }}</h3>
    @endif
    
    <div class="text-left py-2 col-12 m-auto row justify-content-between" style="font-size: 1.2em; border-bottom: 1px solid black">
        <div class='col-lg-6 col-md-6 col-sm-6 col-12 d-flex my-auto'>
            INFORMACIÓN DE DESPACHO
        </div>

        <div class='col-lg-6 col-md-6 col-sm-6 col-12 text-sm-right'>
            <button id="print" class="btn superior-buttons" onClick="imprimir({{$despacho->id}})">
                <i class="fa fa-print font-weight-bold"></i> Descargar
            </button>
        </div>
    </div>

    <div class="border">
        <!-- APARTADO INFORMACION DE LA IZQUIERDA -->
        @php
            $workers = array ();

            $one_worker = array(
                "value" => 1,
                "nombre" => "Trabajador Nombre",
            );

            foreach ($trabajadores as $trabajador) {
                $one_worker["value"] = $trabajador->id;
                $one_worker["nombre"] = $trabajador->nombre." ".$trabajador->apellido;
                array_push($workers,$one_worker);
            }

            $data_form = array(
                "action" => "edit-despacho",
                "title" => "",
                "form-id" => "form-despacho",
                "edit-id" => $despacho->id,
                
                "form-components" => array(
                    array(
                        "component-type" => "select",
                        "label-name" => "Empleado que realizara el Despacho",
                        "icon" => "fa-user",
                        "id_name" => "form-empleado",
                        "form_name" => "id_trabajador",
                        "title" => "Selecciona un Empleado",
                        "options" => $workers,
                        "validate" => "Empleado es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $despacho->id_trabajador,
                    ),
                    array(
                        "component-type" => "input",
                        "label-name" => "Fecha del Despacho",
                        "icon" => "fa-calendar",
                        "type" => "date",
                        "id_name" => "form-fecha",
                        "form_name" => "fecha",
                        "placeholder" => "Ingrese la fecha del despacho",
                        "validate" => "Fecha es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $despacho->fecha,
                    ),
                    array(
                        "component-type" => "textarea",
                        "label-name" => "Nota del Despacho",
                        "icon" => "fa-list-alt",
                        "type" => "text",
                        "id_name" => "form-description",
                        "form_name" => "nota",
                        "placeholder" => "Ingrese la nota del despacho",
                        "validate" => "Nota es requerida",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-true",
                        "value" => $despacho->nota,
                    ),
                    array(
                        "component-type" => "select",
                        "label-name" => "Estado del Despacho",
                        "icon" => "fa-book",
                        "id_name" => "form-entregado",
                        "form_name" => "entregado",
                        "title" => "Selecciona una Opción",
                        "options" => array(
                            array(
                                "value" => false,
                                "nombre" => "Pendiente",
                            ),
                            array(
                                "value" => true,
                                "nombre" => "Finalizado",
                            ),
                        ),
                        "validate" => "Estado es requerido",
                        "bd-error" => "LO QUE SEA",
                        "requerido" => "req-false",
                        "value" => $despacho->entregado,
                    ),
                ),
            );
        @endphp

        <!-- APARTADO INFORMACION DE LA DERECHA -->
        @php    
            $title = "Detalles de Venta";
            
            $datos = array(
                array(
                    "label" => "ID-Venta", 
                    "dato" => $despacho->id_venta,
                ),
                array(
                    "label" => "Cliente", 
                    "dato" => $despacho->venta->cliente->nombre,
                ),
                array(
                    "label" => "Fecha de la Venta", 
                    "dato" => $despacho->venta->fecha,
                ),
                array(
                    "label" => "Zona", 
                    "dato" => $despacho->venta->cliente->zona->nombre,
                ),
                array(
                    "label" => "Dirección", 
                    "dato" => $despacho->venta->cliente->direccion,
                ),
            );
            
            $products = array();

            $one_product = array(
                array("name","cantidad","precio"),
            );

            foreach ($productos as $pro) {
                $one_product["name"] = $pro->producto->nombre;
                $one_product["cantidad"] = $pro->cantidad;
                $one_product["precio"] = $pro->precio;
                array_push($products,$one_product);
            }

            $data_list = array(
                array(
                    "table-id" => "lista-pago",
                    "icon" => "fa-shopping-cart",
                    "type" => "inline-info",
                    "title" => $title,
                    "information" => $datos,
                    "productos" => $products,
                ),
            );

            $title = "Datos del Despacho";
        @endphp
        @include('includes.general_detail',['data'=>$data_form, 'data_list'=>$data_list, 'title'=>$title])
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/form_validate.js') }}"></script>
    <script>
        //ACOMODO LA BARRA DE NAVEGACION
            $("#ip").addClass("active");
            $("#ip").removeClass("icono_head");
            $(".ip").removeClass("icono_color");
        
        //VOLVER A LA VISTA QUE ESTABAMOS ANTES
            const retroceder = () => {
                location.href = "{{ url()->previous() }}";
            }

        //BORRAR EL .INPUT100 DE LOS QUE NO PUEDEN SER EDITADOS 
            $("#form-tipo").removeClass("input100");
            $("#form-price").removeClass("input100");

        //EVENTO DE IMPRIMIR
            const imprimir = (id) =>{
                var registro = "{{ route('pdf-detail-despacho') }}";
                var ruta = registro+"/"+id;
                window.open(ruta);
            }
    </script>
@endsection