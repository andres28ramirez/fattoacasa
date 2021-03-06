<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <link rel="SHORTCUT ICON" href="{{ asset('img/logo.png') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jspdf.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/sweetalert.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/Chart.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('font/font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/utils.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navigation.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toolbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/form-styles.css') }}" rel="stylesheet">

    <!-- Archivos para el uso de FullCalendar, para el calendario-->
    @yield('CalendarScripts')

    <!-- Script de la barra de navegación -->
    <script>
	    $(document).ready(function() {
	        $(".dropdown-item").hover(function() {
	      	    $(this).css('background-color', 'rgba(0,0,0,0.1)');
	        }, function() {
	      	    $(this).css('background-color', 'white');
	        });

	        $(".icono_head").hover(function() {
	      	    var id = $(this).attr('id');
	      	    $("."+id).removeClass('icono_color');
	        }, function() {
	      	    var id = $(this).attr('id');
	      	    $("."+id).addClass('icono_color');
	        });

	        $(".logo-imagen").click(function(event) {
	      	    location.href = "{{ url('/') }}";
            });

            //SCRIPT DE LA BARRA DE NAVEGACION CUANDO EXISTE RESPONSIVIDAD
            $(".menu_navegacion").click(function(event) {
                $(".nav-side-menu").fadeIn(500);
                $("#fade").removeClass('d-none');
            });

            $("#cerrar_menu").click(function(event) {
                $(".nav-side-menu").fadeOut(500);
                $("#fade").addClass('d-none');
            });
	    });
        
        function redirect(e){
            switch (e) {
                case "compras":
                    location.href = "{{ route('list-compras') }}";
                    break;
                case "ventas":
                    location.href = "{{ route('list-ventas') }}";
                    break;
                case "clientes":
                    location.href = "{{ route('list-client') }}";
                    break;
                case "proveedores":
                    location.href = "{{ route('list-prov') }}";
                    break;
                case "logistica":
                    location.href = "{{ route('list-inventario') }}";
                    break;
                case "finanzas":
                    location.href = "{{ route('list-ingresos') }}";
                    break;
                case "indicadores":
                    location.href = "{{ route('ind-logistica') }}";
                    break;
                case "calendario":
                    location.href = "{{ url('/Calendario') }}";
                    break;
                case "configuracion":
                    location.href = "{{ route('perfil') }}";
                    break;
                case "cerrar_sesion":
                    location.href = "{{ route('logout') }}";
                    break;
                default:
                    location.href = "{{ url('/') }}";
                    break;
            }
        }
    </script>
      
    <style>
        #fade{
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 11
		}
    </style>

</head>
<body>
    <div id="fade" class="d-none"></div>
    <div id="app">
     
    <!-- BARRA DE NAVEGACION -->
        <div class="nav-side-menu" id="barra_navegacion">
            <div class="brand py-2">
                <button id="cerrar_menu" type="button" class="eliminar-2 float-left my-auto toggle-btn" aria-label="Close">
				    <span aria-hidden="true">&times;</span>
				</button>
                <div class="m-auto text-center row justify-content-center py-3" style="cursor: pointer">
                    <img src="{{ asset('img/logo.png')}}" class="img-fluid logo-imagen" width="125" height="125"><br>
                </div>
            </div>
            <div class="menu-list">
                <ul id="menu-content" class="menu-content collapse out text-justified d-block">
                    @if(Auth::user()->permiso_compra)
                        <li class="py-2 mx-4 icono_head" id="ic" onClick="redirect('compras')">
                            <a>
                                <i class="fa icono_color fa-shopping-cart fa-lg ic "></i>
                                <i class="ic icono_color fa fa-caret-right fa-lg"></i>
                                <span>COMPRAS</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->permiso_venta)
                        <li class="py-2 mx-4 icono_head" id="ip" onClick="redirect('ventas')">
                            <a>
                                <i class="fa fa-shopping-basket fa-lg ip icono_color"></i>
                                <i class="ip icono_color fa fa-caret-right fa-lg"></i>
                                <span>VENTAS</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->permiso_cliente)
                        <li class="py-2 mx-4 icono_head" id="ib" onClick="redirect('clientes')">
                            <a>
                                <i class="ib icono_color fa fa-users fa-lg"></i>
                                <i class="ib icono_color fa fa-caret-right fa-lg"></i> 
                                <span>CLIENTES</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->permiso_proveedor)
                        <li class="py-2 mx-4 icono_head" id="ich" onClick="redirect('proveedores')">
                            <a>
                                <i class="ich icono_color fa fa-book fa-lg"></i>
                                <i class="ich icono_color fa fa-caret-right fa-lg"></i> 
                                <span>PROVEEDORES</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->permiso_logistica)
                        <li class="py-2 mx-4 icono_head" id="iv" onClick="redirect('logistica')">
                            <a>
                                <i class="iv icono_color fa fa-dropbox fa-lg"></i>
                                <i class="iv icono_color fa fa-caret-right fa-lg"></i> 
                                <span>LOGÍSTICA</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->permiso_finanzas)
                        <li class="py-2 mx-4 icono_head" id="if" onClick="redirect('finanzas')">
                            <a>
                                <i class="if icono_color fa fa-money fa-lg"></i>
                                <i class="if icono_color fa fa-caret-right fa-lg"></i> 
                                <span>FINANZAS</span>
                            </a>
                        </li>
                    @endif
                        <li class="py-2 mx-4 icono_head" id="iu" onClick="redirect('indicadores')">
                            <a>
                                <i class="iu icono_color fa fa-pie-chart fa-lg"></i>
                                <i class="iu icono_color fa fa-caret-right fa-lg"></i> 
                                <span>INDICADORES</span>
                            </a>
                        </li>
                        <li class="py-2 mx-4 icono_head" id="icon" onClick="redirect('calendario')">
                            <a>
                                <i class="icon icono_color fa fa-calendar fa-lg"></i>
                                <i class="icon icono_color fa fa-caret-right fa-lg"></i>
                                <span>CALENDARIO</span>
                            </a>
                        </li>
                        <li class="py-2 mx-4 icono_head responsive_menu" id="icon" onClick="redirect('configuracion')">
                            <a>
                                <i class="icon icono_color fa fa-gear fa-lg"></i>
                                <i class="icon icono_color fa fa-caret-right fa-lg"></i>
                                <span>CONFIGURACIÓN</span>
                            </a>
                        </li>
                        <li class="py-2 mx-4 icono_head responsive_menu" id="icon" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                            <a>
                                <i class="icon icono_color fa fa-sign-out fa-lg"></i>
                                <i class="icon icono_color fa fa-caret-right fa-lg"></i>
                                <span>CERRAR SESIÓN</span>
                            </a>
                        </li>
                </ul>
            </div>
        </div>
    <!-- FIN BARRA DE NAVEGACION -->
    
    <!-- BARRA SUPERIOR -->
        <nav class="navbar navbar-expand-md navbar-light shadow-sm py-3" id="main" style="background-color: #C47E4D">
            <div class="container">
                <div class="mx-3 toggle-btn text-center">
                    <div class="text-white menu_navegacion r-menu" style="cursor: pointer">
                        <i class="fa fa-bars d-block icon-menu"></i>
                        <span class="d-block">Menu</span>
                    </div>
                </div>

                <a class="navbar-brand text-white">
                    @yield('titulo')
                </a>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        @if(Auth::user()->permiso_venta || Auth::user()->permiso_logistica)
                        <!-- Opciones de Notificaciones -->
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link text-white" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <span class="caret">{{Session::get('notificaciones')}}</span><i class="icon text-white fa fa-bell-o fa-lg"></i>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            @if(Auth::user()->permiso_venta && Session::get('cobrar-expirar')>0)
                                <a class="dropdown-item" href="{{ route('list-cuentas') }}">
                                    <span class="caret">{{Session::get('cobrar-expirar')}}</span>
                                    <i class="icon fa fa-exclamation-circle fa-lg mx-1" style="color: #FFD70D;"></i>
                                    <span>Cuentas a Cobrar Proximas a Expirar</span>
                                </a>
                            @endif
                            @if(Auth::user()->permiso_logistica)
                                @if(Session::get('inventario-expirar')>0)
                                <a class="dropdown-item" href="{{ route('list-inventario') }}">
                                    <span class="caret">{{Session::get('inventario-expirar')}}</span>
                                    <i class="icon fa fa-exclamation-circle fa-lg mx-1" style="color: #FFD70D;"></i>
                                    <span>Inventario Proximo a Expirarse</span>
                                </a>
                                @endif
                                @if(Session::get('suministro-expirar')>0)
                                <a class="dropdown-item" href="{{ route('list-suministro') }}" style="border-bottom: 0.5px solid grey">
                                    <span class="caret">{{Session::get('suministro-expirar')}}</span>
                                    <i class="icon fa fa-exclamation-circle fa-lg mx-1" style="color: #FFD70D;"></i>
                                    <span>Suministro Proximo a Expirarse</span>
                                </a>
                                @endif
                            @endif
                            @if(Auth::user()->permiso_venta && Session::get('cobrar-caducar')>0)
                                <a class="dropdown-item" href="{{ route('list-cuentas') }}">
                                    <span class="caret">{{Session::get('cobrar-caducar')}}</span>
                                    <i class="icon text-danger fa fa-exclamation-triangle fa-lg mx-1"></i>
                                    <span>Cuentas a Cobrar Caducadas</span>
                                </a>
                            @endif
                            @if(Auth::user()->permiso_logistica)
                                @if(Session::get('inventario-caducar')>0)
                                <a class="dropdown-item" href="{{ route('list-inventario') }}">
                                    <span class="caret">{{Session::get('inventario-caducar')}}</span>
                                    <i class="icon text-danger fa fa-exclamation-triangle fa-lg mx-1"></i>
                                    <span>Inventarios Expirados</span>
                                </a>
                                @endif
                                @if(Session::get('suministro-caducar')>0)
                                <a class="dropdown-item" href="{{ route('list-suministro') }}">
                                    <span class="caret">{{Session::get('suministro-caducar')}}</span>
                                    <i class="icon text-danger fa fa-exclamation-triangle fa-lg mx-1"></i>
                                    <span>Suministros Expirados</span>
                                </a>
                                @endif
                            @endif

                            @if(Session::get('notificaciones')<=0)
                                <a class="dropdown-item" href="">
                                    <span>Inventario, Cuentas y Suministro en Orden!</span>
                                </a>
                            @endif
                            </div>
                        </li>
                        @endif

                        <!-- Opciones de Usuario -->
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle text-white" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <span class="caret"></span>
                                <span id="user-text-name">{{ Auth::user()->name }}</span> 
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('perfil') }}">
                                    <i class="icon icono_color fa fa-gear fa-lg" style="color: #C47E4D"></i>
                                    <span>{{ __('Configuración') }}</span>
                                </a> 
                                <a class="dropdown-item" href="{{ route('manual-usuario') }}">
                                    <i class="icon icono_color fa fa-file-text fa-lg" style="color: #C47E4D"></i>
                                    <span>{{ __('Manuales de Usuario') }}</span>
                                </a>  
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    <i class="icon icono_color fa fa-sign-out fa-lg" style="color: #C47E4D"></i>
                                    <span>{{ __('Cerrar Sesión') }}</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="toggle-btn text-center">
                    <div class="row justify-content-center header-responsive">
                        <div class="align-self-center text-white menu_navegacion mx-2">
                            <i class="fa fa-bars d-block icon-menu"></i>
                            <span class="d-block">Menu</span>
                        </div>
                        <div class="mx-2">
                            <img src="{{ asset('img/logo-responsive.png')}}" class="img-fluid logo-imagen" width="60" height="60">
                        </div>
                    </div>

                    <img src="{{ asset('img/logo-responsive.png')}}" class="img-fluid logo-imagen r-logo" width="60" height="60">
                </div>
            </div>
        </nav>
    <!-- FIN BARRA SUPERIOR -->

    <!-- CONTENIDO DE CADA OPCION -->
        <main class="py-3" id="main">
            @yield('content')
        </main>
    <!-- FIN CONTENIDO DE CADA OPCION -->

    </div>

    @yield('scripts')
</body>
</html>

