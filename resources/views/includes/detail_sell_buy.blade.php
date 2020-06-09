<div class="modal fade" id="{{ $data['modal-id'] }}" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="titulo">{{ $data['title'] }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m-auto text-center col-12 py-5" id="productos-spinner">
                    <i class="fa fa-5x fa-lg fa-spinner fa-spin" style="color: #028936"></i>
                </div>
                <!-- INFORMACIÓN PRODUCTO A PRODUCTO -->
                <div class="form-row justify-content-center" id="div-product-data">
                    <div class="col-12 p-2" >
                        <div class="input-group row justify-content-center">
                            <div class="col-6">
                                <!-- PRODUCTO -->
                                <strong>Producto:</strong>
                            </div>
                            <div class="col">
                                <!-- CANTIDAD -->
                                <strong>Cantidad (Kg/Und):</strong>
                            </div>
                            <div class="col">
                                <!-- PRECIO -->
                                <strong>Precio:</strong>
                            </div>
                        </div>                          
                    </div>
                    
                    <div id="form-product-data" class="col-12">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>