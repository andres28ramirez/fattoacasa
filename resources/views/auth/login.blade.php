<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
<body>
    <div class="app">
        <div class="limiter">
            <div class="row justify-content-center container-login100" style="background-image: url({{ asset('img/bg-login.jpg') }})">

                <div class="wrap-login100">
                    <div class="login100-form-title" style="background-image: url({{ asset('img/bg-up-login.jpg') }})">
                        <span class="login100-form-title-1">
                            {{ __('Iniciar Sesión') }}
                        </span>
                    </div>

                    <form class="login100-form validate-form" method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                        @csrf

                        <div class="wrap-input100 validate-input m-b-26" data-validate="Usuario es requerido">
                            <span class="label-input100">{{ __('Usuario') }}</span>
                            <input id="username" type="text" placeholder="Ingresar Usuario" class="input100 form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" autofocus>
                            <span class="focus-input100"></span>
                            @if ($errors->has('username'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ "El usuario o la contraseña no concuerdan" }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="wrap-input100 validate-input m-b-18" data-validate="Contraseña es requerida">
                            <span class="label-input100">{{ __('Contraseña') }}</span>
                            <input id="password" type="password" placeholder="Ingresar Contraseña" class="input100 form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">
                            <span class="focus-input100"></span>
                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ "Error de Contraseña" }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="flex-sb-m w-full p-b-30">
                            <div>
                                <a href="{{ route('password.request') }}" class="txt1">
                                    Recuperar Contraseña...
                                </a>
                            </div>
                        </div>

                        <div class="container-login100-form-btn">
                            <button class="login100-form-btn" type="submit">
                                {{ __('Ingresar') }}
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

