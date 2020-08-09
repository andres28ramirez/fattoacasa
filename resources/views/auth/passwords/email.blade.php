<!DOCTYPE html>
<html lang="en">
<head>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
    
        <title>Fatto a Casa</title>
        <link rel="SHORTCUT ICON" href="{{ asset('img/logo.png') }}">
    
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    
        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        <link href="{{ asset('font/font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet">
    
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/login.css') }}" rel="stylesheet">
        <link href="{{ asset('css/utils.css') }}" rel="stylesheet">
    
    </head>
</head>
<body>
    <div class="app">
        <div class="limiter">
            <div class="row justify-content-center container-login100" style="background-image: url({{ asset('img/bg-login.jpg') }})">

                <div class="wrap-login100">
                    <div class="login100-form-title" style="background-image: url({{ asset('img/bg-up-login.jpg') }})">
                        <span class="login100-form-title-1">
                            {{ __('Resetear contraseña') }}
                        </span>
                    </div>

                    <form class="login100-form validate-form" method="POST" action="{{ route('password.email') }}">
                        @csrf

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{-- session('status') --}}
                                Hemos enviado un correo con el link para resetear la contraseña!
                            </div>
                        @endif

                        @if(session('message'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('message') }}
                            </div>
                        @endif

                        <div class="wrap-input100 validate-input m-b-26" data-validate="Es requerido ingresar un correo">
                            <span class="label-input100">{{ __('Dirección de correo') }}</span>
                            <input id="email" type="email" class="input100 form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ "Este correo no se encuentra registrado" }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="flex-sb-m w-full p-b-30">
                            <div>
                                <a href="{{ route('login') }}" class="txt1">
                                    Iniciar Sesión
                                </a>
                            </div>
                        </div>

                        <div class="container-login100-form-btn">
                            <button class="login100-form-btn" type="submit">
                                {{ __('Enviar link de reseteo de contraseña') }}
                            </button>
                        </div>
                    </form>
			    </div>
            </div>
        </div>
    </div>
</body>
</html>

<script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>

