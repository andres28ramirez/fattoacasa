@extends('layouts.app')

@section('content')
<style>
    @media (max-width: 650px) {
        #menu-options .nav{
            display: block;
            width: 100%;
        }

        #menu-responsive{
            display: none;
        }

        #menuCSS3{
            display: flex;
        }
    }

    #menuCSS3 ul {
		display: none;
		padding: 0;
		margin: 0;
		list-style: none;
	}
    
    #menuCSS3 a {
		display: block;
		text-decoration: none;
	}

	#menuCSS3 ul li ul {
		display: none;
	}
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-12" id="menu-options">
                <div id="menu-responsive">
                    <ul class="nav nav-tabs">
                        @yield('tabs')
                    </ul>
                </div>

                <div id="menuCSS3">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a id="dropdown-li" class="nav-link font-weight-bold text-white" style="background-color: rgba(0,0,0,0.4);">
                                Opciones
                                <i class="fa fa-caret-down"></i>
                            </a>
                            <ul id="ul-options">
                                @yield('tabs')
                            </ul>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="card">
                <div class="card-body" style="background-color: rgba(255,255,255,0.4)">
                    @yield('info')
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $( "#dropdown-li" ).click(function() {
        if($("#ul-options").css('display') == 'block')
           $("#ul-options").slideUp(500);
        else
            $("#ul-options").slideDown(500);
    });
</script>
@endsection

