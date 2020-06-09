<div class="{{ $indicador['col'] }} col-mg-6 col-sm-6 text-center p-0 m-0 d-flex row" style="min-height: 100px; background-color: {{ $indicador['color-inside'] }}">
    <div class="col-12 p-0">
        <div class="card-header py-3 d-flex" style="background-color: {{ $indicador['color-header'] }}">
            <h6 class="my-auto font-weight-bold float-left" style="color: white; letter-spacing: 1px">{{ $indicador['text'] }}</h6>
        </div>
    </div>
    <div class="col-12 p-0">
        <div class="card-body px-1 text-center d-flex">
            <div class="col-12 my-auto text-center">
                <i class="fa fa-4x {{ $indicador['figure'] }} fa-lg ic text-white"></i>
                <span class="text-white font-weight-bold fs-25 ml-1" style="letter-spacing: 1px">{{ $indicador['cantidad'] }}</span>
            </div>
        </div>
    </div>
</div>