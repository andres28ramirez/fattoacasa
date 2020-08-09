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
                

                    <form class="login100-form validate-form" method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="wrap-input100 validate-input m-b-26" data-validate="Es requerido ingresar un correo">
                            <span class="label-input100">{{ __('Dirección de correo') }}</span>
                            <input id="email" type="email" class="input-100 form-control @error('email') is-invalid @enderror" name="email"  value="{{ $email ?? old('email') }}" autocomplete="email" autofocus readonly>
                            @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{-- $message --}}El token para resetar la contraseña ha expirado.</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="wrap-input100 validate-input m-b-26" data-validate="Es requerido ingresar la nueva contraseña">
                            <span class="label-input100">{{ __('Contraseña') }}</span>
                            <input id="password" type="password" class="input-100 form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{-- $message --}}La confirmación de contraseña no coincide.</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="wrap-input100 validate-input m-b-26" data-validate="Es requerido confirmar la nueva contraseña">
                            <span class="label-input100">{{ __('Confirmar contraseña') }}</span>
                            <input id="password-confirm" type="password" class="input-100 form-control" name="password_confirmation" autocomplete="new-password">
                        </div>

                        <div class="container-login100-form-btn">
                            <button class="login100-form-btn" type="submit">
                                {{ __('Resetear contraseña') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
