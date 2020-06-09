@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-12">
                <ul class="nav nav-tabs">
                    @yield('tabs')
                </ul>
            </div>

            <div class="card">
                <div class="card-body" style="background-color: rgba(255,255,255,0.4)">
                    @yield('info')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
