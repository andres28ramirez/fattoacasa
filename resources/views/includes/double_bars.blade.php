<div class="rounded" id="dashboard-cv">
    <div class="card-header py-3 d-flex">
        <h6 class="my-auto font-weight-bold float-left" style="color: #333333; letter-spacing: 1px">{{ $data['texto'] }}</h6>
        <div class="my-auto ml-auto dropdown text-right">
            <a href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-2x fa fa-gear text-muted fa-lg"></i>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#double-bar-filter{{ $data['canva'] }}">Filtrar</a>
                <a class="dropdown-item download-indicator" type="doublechart{{ $data['canva'] }}" style="cursor: pointer">Descargar</a>
            </div>
        </div>
    </div>
    <div class="card-body px-1 text-center col-12 d-flex" id="canva-indicator-{{ $data['canva'] }}" title="{{ $data['texto'] }}" name="{{ $data['texto'] }}:">
        <div class="m-auto text-center col-12 doublechart-spinner">
            <i class="fa fa-5x fa-lg fa-spinner fa-spin" style="color: #028936"></i>
        </div>
        <canvas id="doublechart{{ $data['canva'] }}"></canvas>
        <input type="hidden" name="{{ $data['canva'] }}" class="canvadbid" value="doublechart{{ $data['canva'] }}">

        <!-- VALORES QUE RECOJO PARA EL CHART -->
        @foreach ($data['labels'] as $labels)
            <input type="hidden" class="canvadblabels{{ $data['canva'] }}" value="{{ $labels }}">
        @endforeach

        <!-- BAR 1 -->
        <input type="hidden" class="canvadblabel1{{ $data['canva'] }}" value="{{ $data['bar-label-1'] }}">
        @foreach ($data['bar-datos-1'] as $datos)
            <input type="hidden" class="canvadbdata1{{ $data['canva'] }}" value="{{ $datos }}">
        @endforeach
        <input type="hidden" class="canvadbbgcolor1{{ $data['canva'] }}" value="{{ $data['bar-bgcolors-1'] }}">
        <input type="hidden" class="canvadbbdcolor1{{ $data['canva'] }}" value="{{ $data['bar-brcolors-1'] }}">

        <!-- BAR 2 -->
        <input type="hidden" class="canvadblabel2{{ $data['canva'] }}" value="{{ $data['bar-label-2'] }}">
        @foreach ($data['bar-datos-2'] as $datos)
            <input type="hidden" class="canvadbdata2{{ $data['canva'] }}" value="{{ $datos }}">
        @endforeach
        <input type="hidden" class="canvadbbgcolor2{{ $data['canva'] }}" value="{{ $data['bar-bgcolors-2'] }}">
        <input type="hidden" class="canvadbbdcolor2{{ $data['canva'] }}" value="{{ $data['bar-brcolors-2'] }}">
    </div>
</div>

<!-- MODAL PARA FILTRAR EL GRÁFICO -->
<div class="modal fade" id="double-bar-filter{{ $data['canva'] }}" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
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