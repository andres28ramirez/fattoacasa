<style>
    @media (max-width: 550px) {  
        #singlehorizontalchart{{ $data['canva'] }}{
            height: 40px;
        }
    }

    @media (max-width: 500px) {  
        #singlehorizontalchart{{ $data['canva'] }}{
            height: 30px;
        }
    }

    @media (max-width: 400px) {  
        #singlehorizontalchart{{ $data['canva'] }}{
            height: 20px;
        }
    }
</style>

<div class="rounded">
    <div class="card-header py-3 d-flex">
        <h6 class="my-auto font-weight-bold float-left" style="color: #333333; letter-spacing: 1px">{{ $data['texto'] }}</h6>
        <div class="my-auto ml-auto dropdown text-right">
            <a href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-2x fa fa-gear text-muted fa-lg"></i>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                <a class="dropdown-item download-indicator" type="singlehorizontalchart{{ $data['canva'] }}" style="cursor: pointer">Descargar</a>
            </div>
        </div>
    </div>
    <div class="card-body px-1 text-center col-12 d-flex" id="canva-indicator-{{ $data['canva'] }}" title="{{ $data['texto'] }}" name="{{ $data['texto'] }}:">
        <div class="m-auto text-center col-12 singlehorizontalchart-spinner">
            <i class="fa fa-5x fa-lg fa-spinner fa-spin" style="color: #028936"></i>
        </div>
        <canvas id="singlehorizontalchart{{ $data['canva'] }}"></canvas>
        <input type="hidden" name="{{ $data['canva'] }}" class="canvashbid" value="singlehorizontalchart{{ $data['canva'] }}">
        
        <!-- VALORES QUE RECOJO PARA EL CHART -->
        <input type="hidden" class="canvatypeshbid{{ $data['canva'] }}" value="{{ $data['tipo'] }}">
        <input type="hidden" class="canvashbbgcolors{{ $data['canva'] }}" value="{{ $data['bgcolors'] }}">
        <input type="hidden" class="canvashbbdcolors{{ $data['canva'] }}" value="{{ $data['brcolors'] }}">
        @foreach ($data['datos'] as $datos)
            <input type="hidden" class="canvashbdata{{ $data['canva'] }}" value="{{ $datos }}">
        @endforeach
        @foreach ($data['labels'] as $labels)
            <input type="hidden" class="canvashblabels{{ $data['canva'] }}" value="{{ $labels }}">
        @endforeach
    </div>
</div>