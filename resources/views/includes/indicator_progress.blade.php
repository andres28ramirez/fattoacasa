<div class="rounded border">
    <div class="card-header py-3 d-flex">
        <h6 class="my-auto font-weight-bold" style="color: #333333; letter-spacing: 1px">{{ $totals['texto'] }}</h6>
        <div class="my-auto ml-auto dropdown text-right">
            @if($totals['filtrar'])
            <a href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-2x fa fa-angle-down text-muted fa-lg"></i>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                @if($totals['filtrar'])
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#progress-filter{{ $totals['indicator'] }}">Filtrar</a>
                @endif
                <a class="dropdown-item" href="#">Descargar</a>
            </div>
            @endif
        </div>
    </div>
    <div class="card-body px-1 col-12" id="indicator-top-{{ $totals['indicator'] }}" style="overflow: auto">
        
        @foreach($data as $datos)
            @php
                $percentaje = ($datos['cantidad']*100)/$totals['total'];
            @endphp
            <h4 class="small font-weight-bold ">{{ $datos['text'] }} <span class="float-right">{{ $datos['cantidad'] }}</span></h4>
            <div class="progress mb-4">
                <div 
                    class="progress-bar progress-bar-striped progress-bar-animated" 
                    role="progressbar" 
                    style="width: {{ $percentaje }}%; background-color: {{ $totals['bgColorProduct'] }}" 
                    aria-valuenow="{{ $percentaje }}" 
                    aria-valuemin="0" 
                    aria-valuemax="{{ $totals['total'] }}"
                >
                    @if($totals['porcentaje'])
                        {{ $percentaje }}%
                    @else
                        {{ $datos['cantidad'] }}
                    @endif
                </div>
            </div>
        @endforeach

        <h4 class="small font-weight-bold">{{ $totals['total_text'] }} <span class="float-right">{{ $totals['total'] }}</span></h4>
        <div class="progress">
            <div 
                class="progress-bar progress-bar-striped progress-bar-animated" 
                role="progressbar" 
                style="width: 100%; background-color: {{ $totals['bgColorTotal'] }}" 
                aria-valuenow="{{ $totals['total'] }}" 
                aria-valuemin="0" 
                aria-valuemax="{{ $totals['total'] }}"
            >
                @if($totals['porcentaje'])
                    {{ "100%" }}
                @else
                    {{ $totals['total'] }}
                @endif
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA FILTRAR EL GRÁFICO -->
<div class="modal fade" id="progress-filter{{ $totals['indicator'] }}" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="titulo">Filtrar Gráfico</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('includes.general_form',['data'=>$data_form])
            </div>
        </div>
    </div>
</div>