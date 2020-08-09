<style>
    @media (max-width: 860px) {  
        #singlelinechart{{ $data['canva'] }}{
            height: 60px;
        }
    }

    @media (max-width: 550px) {  
        #singlelinechart{{ $data['canva'] }}{
            height: 60px;
        }
    }

    @media (max-width: 500px) {  
        #singlelinechart{{ $data['canva'] }}{
            height: 60px;
        }
    }

    @media (max-width: 400px) {  
        #singlelinechart{{ $data['canva'] }}{
            height: 30px;
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
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#single-line-filter{{ $data['canva'] }}">Filtrar</a>
                <a class="dropdown-item download-indicator" type="singlelinechart{{ $data['canva'] }}" style="cursor: pointer">Descargar</a>
            </div>
        </div>
    </div>
    <div class="card-body px-1 text-center col-12 d-flex" id="canva-indicator-{{ $data['canva'] }}"  title="{{ $data['texto'] }}" name="{{ $data['texto'] }}:">
        <div class="m-auto text-center col-12 singlelinechart-spinner">
            <i class="fa fa-5x fa-lg fa-spinner fa-spin" style="color: #028936"></i>
        </div>
        <canvas id="singlelinechart{{ $data['canva'] }}" height="{{ $data['chartHeight'] }}"></canvas>
        <input type="hidden" name="{{ $data['canva'] }}" class="canvaslid" value="singlelinechart{{ $data['canva'] }}">
        
        <!-- VALORES QUE RECOJO PARA EL CHART -->
        <input type="hidden" class="canvaslbgcolors{{ $data['canva'] }}" value="{{ $data['bgcolors'] }}">
        <input type="hidden" class="canvaslbdcolors{{ $data['canva'] }}" value="{{ $data['brcolors'] }}">
        @foreach ($data['datos'] as $datos)
            <input type="hidden" class="canvasldata{{ $data['canva'] }}" value="{{ $datos }}">
        @endforeach
        @foreach ($data['labels'] as $labels)
            <input type="hidden" class="canvasllabels{{ $data['canva'] }}" value="{{ $labels }}">
        @endforeach
    </div>
</div>

<!-- MODAL PARA FILTRAR EL GRÁFICO -->
<div class="modal fade" id="single-line-filter{{ $data['canva'] }}" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
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