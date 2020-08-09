<style>
    @media (max-width: 800px) {
        .total-block{
            font-size: 12px;
        }
    }
</style>

<!-- BLOQUE ESTADISTICO DEL INICIO -->
    <div class="col-lg col-md-4 col-sm-4 col-xs-6 mx-2 mb-md-2 mb-sm-2 mb-2 text-center container" style="box-shadow: 0px 0px 5px 1px rgba(0,0,0,0.2); min-height: 100px">
        <div class="row justify-content-center h-100 total-block">
            <div class="col-5 h-100 container" style="display: flex; background-color: {{ $indicador['color'] }}">
                <div class="text-center m-auto">
                    <i class="fa fa-3x {{ $indicador['figure'] }} fa-lg ic text-white"></i>
                </div>
            </div>
            <div class="col-7 my-auto total-block-text">
                <span class="d-block text-muted fs-20">{{ $indicador['cantidad'] }}</span>
                <span class="icono_color font-weight-bold" style="letter-spacing: 1px">{{ $indicador['text'] }}</span>
            </div>
        </div>
    </div>
<!-- FIN BLOQUE ESTADISTICO DEL INICIO -->