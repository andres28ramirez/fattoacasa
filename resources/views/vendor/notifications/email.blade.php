@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('Hello!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Saludos!'),<br>
{{-- config('app.name') --}}
Fatto a Casa
@endif

{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
<div style="text-align: center; color:gray">
    Fatto a Casa C.A<br>
    Calle Tamare, quinta Lina el Marques <br>
    Caracas, Venezuela <br>
    +58-212-237-7847 <br>
    Infofattoacasa@gmail.com <br>
</div>
    
@endslot
@endisset
@endcomponent
