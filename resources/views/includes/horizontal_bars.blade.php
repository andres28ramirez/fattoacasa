<style>
    @media (max-width: 860px) {  
        #horizontalchart{{ $data['canva'] }}{
            height: 50px;
        }
    }

    @media (max-width: 550px) {  
        #horizontalchart{{ $data['canva'] }}{
            height: 40px;
        }
    }

    @media (max-width: 500px) {  
        #horizontalchart{{ $data['canva'] }}{
            height: 30px;
        }
    }

    @media (max-width: 400px) {  
        #horizontalchart{{ $data['canva'] }}{
            height: 20px;
        }
    }
</style>

<div class="rounded" id="indicator-es">
    <div class="card-header py-3 d-flex">
        <h6 class="my-auto font-weight-bold float-left" style="color: #333333; letter-spacing: 1px">{{ $data['texto'] }}</h6>
        <div class="my-auto ml-auto dropdown text-right" id="horizontalbars-filter">
            <a href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-2x fa fa-gear text-muted fa-lg"></i>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                <a class="dropdown-item" href="#">Filtrar</a>
                <a class="dropdown-item download-indicator" type="horizontalchart{{ $data['canva'] }}" style="cursor: pointer">Descargar</a>
            </div>
        </div>
    </div>
    <div class="card-body px-1 text-center col-12 d-flex" id="canva-indicator-{{ $data['canva'] }}" title="{{ $data['texto'] }}" name="{{ $data['texto'] }}:">
        <div class="m-auto text-center col-12 horizontalchart-spinner">
            <i class="fa fa-5x fa-lg fa-spinner fa-spin" style="color: #028936"></i>
        </div>
        <canvas id="horizontalchart{{ $data['canva'] }}" height="{{ $data['chartHeight'] }}"></canvas>
        <input type="hidden" name="{{ $data['canva'] }}" class="canvadhbid" value="horizontalchart{{ $data['canva'] }}">

        <!-- VALORES QUE RECOJO PARA EL CHART -->
            @foreach ($data['labels'] as $labels)
                <input type="hidden" class="canvahdblabels{{ $data['canva'] }}" value="{{ $labels }}">
            @endforeach

        <!-- BAR 1 -->
            <input type="hidden" class="canvahdblabel1{{ $data['canva'] }}" value="{{ $data['bar-label-1'] }}">
            @foreach ($data['bar-datos-1'] as $datos)
                <input type="hidden" class="canvahdbdata1{{ $data['canva'] }}" value="{{ $datos }}">
            @endforeach
            <input type="hidden" class="canvahdbbgcolor1{{ $data['canva'] }}" value="{{ $data['bar-bgcolors-1'] }}">
            <input type="hidden" class="canvahdbbdcolor1{{ $data['canva'] }}" value="{{ $data['bar-brcolors-1'] }}">

            <!-- BAR 2 -->
            <input type="hidden" class="canvahdblabel2{{ $data['canva'] }}" value="{{ $data['bar-label-2'] }}">
            @foreach ($data['bar-datos-2'] as $datos)
                <input type="hidden" class="canvahdbdata2{{ $data['canva'] }}" value="{{ $datos }}">
            @endforeach
            <input type="hidden" class="canvahdbbgcolor2{{ $data['canva'] }}" value="{{ $data['bar-bgcolors-2'] }}">
            <input type="hidden" class="canvahdbbdcolor2{{ $data['canva'] }}" value="{{ $data['bar-brcolors-2'] }}">
    </div>
</div>