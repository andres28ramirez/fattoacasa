@extends('layouts.principal')

@section('title','Manuales · Fatto a Casa')

@section('titulo','FATTO A CASA - MANUALES')

@section('info')
    <style>
        .cards-section {
            padding: 0px 0;
        }

        .cards-section .title {
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 24px;
            font-weight: 600;
        }

        .cards-section .intro {
            margin: 0 auto;
            max-width: 800px;
            margin-bottom: 30px;
            color: #616670;
        }

        .cards-section .cards-wrapper {
            margin-left: auto;
            margin-right: auto;
        }

        .cards-section .item {
            margin-bottom: 30px;
        }

        .cards-section .item .icon-holder {
            margin-bottom: 15px;
        }

        .cards-section .item .icon {
            font-size: 36px;
        }

        .cards-section .item .title {
            font-size: 16px;
            font-weight: 600;
        }

        .cards-section .item .intro {
            margin-bottom: 15px;
        }

        .cards-section .item-inner {
            padding: 45px 30px;
            background: #fff;
            position: relative;
            border: 1px solid #f0f0f0;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            -ms-border-radius: 4px;
            -o-border-radius: 4px;
            border-radius: 4px;
            -moz-background-clip: padding;
            -webkit-background-clip: padding-box;
            background-clip: padding-box;
            height: 100%;
        }

        .cards-section .item-inner .link {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .cards-section .item-inner:hover {
            background: #f5f5f5;
        }

        .cards-section .item-primary .item-inner {
            border-top: 3px solid #40babd;
        }

        .cards-section .item-primary .item-inner:hover .title {
            color: #2d8284;
        }

        .cards-section .item-primary .icon {
            color: #40babd;
        }

        .cards-section .item-green .item-inner {
            border-top: 3px solid #75c181;
        }

        .cards-section .item-green .item-inner:hover .title {
            color: #48a156;
        }

        .cards-section .item-green .icon {
            color: #75c181;
        }

        .cards-section .item-blue .item-inner {
            border-top: 3px solid #FF3017;
        }

        .cards-section .item-blue .item-inner:hover .title {
            color: #80170B;
        }

        .cards-section .item-blue .icon {
            color: #FF3017;
        }

        .cards-section .item-orange .item-inner {
            border-top: 3px solid #FF8100;
        }

        .cards-section .item-orange .item-inner:hover .title {
            color: #BF6000;
        }

        .cards-section .item-orange .icon {
            color: #FF8100;
        }

        .cards-section .item-red .item-inner {
            border-top: 3px solid #f77b6b;
        }

        .cards-section .item-red .item-inner:hover .title {
            color: #f33a22;
        }

        .cards-section .item-red .icon {
            color: #f77b6b;
        }

        .cards-section .item-pink .item-inner {
            border-top: 3px solid #C47E4D;
        }

        .cards-section .item-pink .item-inner:hover .title {
            color: #C76B31;
        }

        .cards-section .item-pink .icon {
            color: #C47E4D;
        }

        .cards-section .item-purple .item-inner {
            border-top: 3px solid #29634C;
        }

        .cards-section .item-purple .item-inner:hover .title {
            color: #193D2F;
        }

        .cards-section .item-purple .icon {
            color: #29634C;
        }

        @media (max-width: 767.98px) {
            .cards-section .item-inner {
                padding: 30px 15px;
            }
        }
    </style>

    <div class="row justify-content-center my-3 px-2">
        <section class="cards-section text-center">
            <div class="container">
                <h2 class="title">Manuales de Usuario</h2>
                <div class="intro">
                    <p>Presiona sobre el recuadro para abrir el manual de usuario deseado.</p>
                </div><!--//intro-->
                <div id="cards-wrapper" class="cards-wrapper row">
                    @if(Auth::user()->tipo == "admin")
                    <!-- MANUAL DE CONFIGURACIÓN USUARIO ADMIN PRINCIPAL -->
                    <div class="item item-pink col-lg-4 col-6">
                        <div class="item-inner">
                            <div class="icon-holder">
                                <i class="icon fa fa-gears"></i>
                            </div><!--//icon-holder-->
                            <h3 class="title">Configuración</h3>
                            <p class="intro">Perfil, Usuarios, Empleados, Reportes, Respaldos y Reset de Contraseña</p>
                            <a target="_blank" class="link" href="{{asset('manuales/configuracion(ap).pdf')}}"><span></span></a>
                        </div><!--//item-inner-->
                    </div><!--//item-->
                    @endif

                    @if(Auth::user()->tipo == "admin secundario")
                    <!-- MANUAL DE CONFIGURACIÓN USUARIO ADMIN SECUNDARIO -->
                    <div class="item item-pink col-lg-4 col-6">
                        <div class="item-inner">
                            <div class="icon-holder">
                                <i class="icon fa fa-gears"></i>
                            </div><!--//icon-holder-->
                            <h3 class="title">Configuración</h3>
                            <p class="intro">Perfil, Usuarios, Empleados, Reportes y Reset de Contraseña</p>
                            <a target="_blank" class="link" href="{{asset('manuales/configuracion(as).pdf')}}"><span></span></a>
                        </div><!--//item-inner-->
                    </div><!--//item-->
                    @endif

                    @if(Auth::user()->tipo == "operador")
                    <!-- MANUAL DE CONFIGURACIÓN USUARIO OPERADOR -->
                    <div class="item item-pink col-lg-4 col-6">
                        <div class="item-inner">
                            <div class="icon-holder">
                                <i class="icon fa fa-gears"></i>
                            </div><!--//icon-holder-->
                            <h3 class="title">Configuración</h3>
                            <p class="intro">Perfil y Reset de Contraseña</p>
                            <a target="_blank" class="link" href="{{asset('manuales/configuracion(op).pdf')}}"><span></span></a>
                        </div><!--//item-inner-->
                    </div><!--//item-->
                    @endif

                    <!-- MANUAL DE CALENDARIO E INDICADORES -->
                    <div class="item item-pink item-2 col-lg-4 col-6">
                        <div class="item-inner">
                            <div class="icon-holder">
                                <i class="icon fa fa-calendar"></i>   <i class="icon fa fa-pie-chart"></i>
                            </div><!--//icon-holder-->
                            <h3 class="title">Calendario e Indicadores</h3>
                            <p class="intro">Inserción, Edición y Eliminación de Eventos como generar reportes de Indicadores</p>
                            <a target="_blank" class="link" href="{{asset('manuales/Calendario-Indicadores.pdf')}}"><span></span></a>
                        </div><!--//item-inner-->
                    </div><!--//item-->

                    @if(Auth::user()->permiso_cliente)
                    <!-- MANUAL DE CLIENTES Y PROVEEDORES -->
                    <div class="item item-pink col-lg-4 col-6">
                        <div class="item-inner">
                            <div class="icon-holder">
                                <i class="icon fa fa-users"></i>
                            </div><!--//icon-holder-->
                            <h3 class="title">Clientes y Proveedores</h3>
                            <p class="intro">Inserción y Edición de Clientes/Proveedores como sus compras realizadas</p>
                            <a target="_blank" class="link" href="{{asset('manuales/Clientes-Proveedores.pdf')}}"><span></span></a>
                        </div><!--//item-inner-->
                    </div><!--//item-->
                    @endif

                    @if(Auth::user()->permiso_venta)
                    <!-- MANUAL DE COMPRAS Y VENTAS -->
                    <div class="item item-pink col-lg-4 col-6">
                        <div class="item-inner">
                            <div class="icon-holder">
                                <i class="icon fa fa-shopping-basket"></i>
                            </div><!--//icon-holder-->
                            <h3 class="title">Compras y Ventas</h3>
                            <p class="intro">Listado de Compras/Ventas, Edición, Inserción, Eliminado, Pagos y Cuentas pendientes</p>
                            <a target="_blank" class="link" href="{{asset('manuales/Compras-Ventas.pdf')}}"><span></span></a>
                        </div><!--//item-inner-->
                    </div><!--//item-->
                    @endif

                    @if(Auth::user()->permiso_finanzas)
                    <!-- MANUAL DE FINANZAS -->
                    <div class="item item-pink col-lg-4 col-6">
                        <div class="item-inner">
                            <div class="icon-holder">
                                <i class="icon fa fa-money"></i>
                            </div><!--//icon-holder-->
                            <h3 class="title">Finanzas</h3>
                            <p class="intro">Ingresos, Egresos, Gastos y Costos, Personal y Pagos realizados o recibidos</p>
                            <a target="_blank" class="link" href="{{asset('manuales/Finanzas.pdf')}}"><span></span></a>
                        </div><!--//item-inner-->
                    </div><!--//item-->
                    @endif

                    @if(Auth::user()->permiso_logistica)
                    <!-- MANUAL DE LOGISTICA -->
                    <div class="item item-pink col-lg-4 col-6">
                        <div class="item-inner">
                            <div class="icon-holder">
                                <i class="icon fa fa-dropbox"></i>
                            </div><!--//icon-holder-->
                            <h3 class="title">Logística</h3>
                            <p class="intro">Inventario, Suministro y Portafolio de Productos</p>
                            <a target="_blank" class="link" href="{{asset('manuales/Logistica.pdf')}}"><span></span></a>
                        </div><!--//item-inner-->
                    </div><!--//item-->
                    @endif
                </div><!--//cards-->
                
            </div><!--//container-->
        </section><!--//cards-section-->
    </div>
@endsection