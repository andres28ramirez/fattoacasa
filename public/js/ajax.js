//AJAX QUE GENERA LA FUNCION DE BORRAR UN REGISTRO DE LA TABLA
const ajaxDelete = (data,url,table,report_url) => {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    $.ajax({
        type: "POST", 
        url: url, 
        data: {'values': JSON.stringify(data)}, 
        dataType: 'json',
    })
    .done(function(response) {
        var obj = response;
        if(obj!="Error"){
            obj.forEach( function(valor, indice, array) {
                $(".tr-"+table).each(function() {
                    var id = $(this).parent().attr("id");
                    if(id == valor){
                        $(this).parent().fadeOut( 500, function() {
                            $(this).remove();
                        });
                    }
                });
            });
            swal({
                title: 'Registros eliminados correctamente!',
                icon: 'success',
                closeOnClickOutside: false,
                button: 'Aceptar',
            });
            $(".swal-button--confirm").addClass('bg-success');
        }
        else{
            swal({
                title: 'Registro no puede ser Eliminado!',
                text: "Debido a que puede poseer datos asociados en [compra, venta, pagos, suministro, despachos]",
                icon: 'warning',
                closeOnClickOutside: false,
                button: 'Aceptar',
            });
            $(".swal-button--confirm").addClass('bg-success');
        }
    })
    .fail( function( jqXHR, textStatus, errorThrown ) {

        if (jqXHR.status === 0) {
            ajaxSwallErrorNotification('Error al eliminar registro. No hay conexión: Verifique la conexión');
            ajaxReportError('Not connect: Verify Network.',report_url);
        } else if (jqXHR.status == 404) {
            ajaxSwallErrorNotification('Error al eliminar registro. Error [404] página no encontrada');
            ajaxReportError('Requested page not found [404]',report_url);
        } else if (jqXHR.status == 500) {
            ajaxSwallErrorNotification('Error al eliminar registro. Error [500] error de servidor');
            ajaxReportError('Internal Server Error [500].',report_url);
        } else if (textStatus === 'parsererror') {
            ajaxSwallErrorNotification('Error al eliminar registro. Error en envio de JSON');
            ajaxReportError('Requested JSON parse failed.',report_url);
        } else if (textStatus === 'timeout') {
            ajaxSwallErrorNotification('Error al eliminar registro. Finalizo el tiempo de conexión intentelo de nuevo');
            ajaxReportError('Time out error.',report_url);
        } else if (textStatus === 'abort') {
            ajaxSwallErrorNotification('Error al eliminar registro. La solicitud fue rechazada intentelo de nuevo');
            ajaxReportError('Ajax request aborted.',report_url);
        } else {
            ajaxSwallErrorNotification('Error al eliminar registro. Error [203] PHP fallo en la solicitud');
            ajaxReportError('Uncaught Error: ' + jqXHR.responseText,report_url);
        }

    })
    .always(function(){
        //alert("finalizo el ajax");
    });
}

//AJAX QUE GRABA EL REPORTE DE ERROR
const ajaxReportError = (message,url) => {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    $.ajax({
        type: "POST", 
        url: url, 
        data: {'values': JSON.stringify(message)}, 
        dataType: 'json',
    })
    .done(function(response) {
        //alert("Error correctamente guardado");
    })
    .fail( function( jqXHR, textStatus, errorThrown ) {
        //alert("Error en la transacción");
    })
    .always(function(){
        //alert("finalizo el ajax");
    });
}

//FUNCION AJAX QUE SE EJECUTA AL CAMBIAR EL RECETARIO DE PRODUCTO
const ajaxUpdateReceta = (id_producto,id_ingrediente,cantidad,url) => {
    var retorno;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    $.ajax({
        async: false,
        type: "POST", 
        url: url, 
        data: {
            'id_producto': JSON.stringify(id_producto),
            'id_ingrediente': JSON.stringify(id_ingrediente),
            'cantidad': JSON.stringify(cantidad),
        }, 
        dataType: 'json',
    })
    .done(function(response) {
        //true que siga el proceso de update en form_validate
        retorno = true;
    })
    .fail( function( jqXHR, textStatus, errorThrown ) {
        
        if (jqXHR.status === 0) {
            ajaxSwallErrorNotification('Error al editar producto. No hay conexión: Verifique la conexión');
        } else if (jqXHR.status == 404) {
            ajaxSwallErrorNotification('Error al editar producto. Error [404] página no encontrada');
        } else if (jqXHR.status == 500) {
            ajaxSwallErrorNotification('Error al editar producto. Error [500] error de servidor');
        } else if (textStatus === 'parsererror') {
            ajaxSwallErrorNotification('Error al editar producto. Error en envio de JSON');
        } else if (textStatus === 'timeout') {
            ajaxSwallErrorNotification('Error al editar producto. Finalizo el tiempo de conexión intentelo de nuevo');
        } else if (textStatus === 'abort') {
            ajaxSwallErrorNotification('Error al editar producto. La solicitud fue rechazada intentelo de nuevo');
        } else {
            ajaxSwallErrorNotification('Error al editar producto. Error [203] PHP fallo en la solicitud');
        }
        //false que se corte y pare el proceso de update en form_validate
        retorno = false;
    })
    .always(function(){
        //alert("finalizo el ajax");
    });

    return retorno; 
}

//FUNCION AJAX QUE ME ACOMODO LOS PRODUCTOS PARA AGREGAR DESPERDICIO
const ajaxDesperdicio = (id,url,form,form_data,spinner) => {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    $.ajax({
        type: "POST", 
        url: url, 
        data: {'values': JSON.stringify(id)}, 
        dataType: 'json',
    })
    .done(function(response) {
        var obj = response;
        if(obj){
            $("#"+spinner).addClass("d-none");
            $("#"+form).removeClass("d-none");
            var cantidad = 1;
            obj.forEach( function(valor, indice, array) {
                var html;
                html  = '<div class="form-group col-12 row justify-content-center" id="'+cantidad+'">';
                html +=     '<input type="hidden" id="producto-id-'+cantidad+'" value="'+valor["id"]+'">';
                html +=     '<div class="col-lg p-0">'; //Producto
                html +=         '<div class="input-group validate-input" data-validate="Producto es requerido">';
                html +=             '<label class="rl-producto align-self-center col-5"><strong>Producto:</strong></label>';
                html +=             '<input class="form-control border-right req-true" style="height: calc(2.19rem + 10px)" ';
                html +=                 'type="text" id="form-producto-'+cantidad+'" name="form-producto-'+cantidad+'" ';
                html +=                  'value="'+valor["nombre"]+'" readonly>';
                html +=         '</div>';
                html +=     '</div>';
                html +=     '<div class="col-lg p-0">'; //CANTIDAD
                html +=         '<div class="input-group validate-input" data-validate="Cantidad es requerida">';
                html +=             '<label class="rl-cantidad align-self-center col-5"><strong>Cantidad:</strong></label>';
                html +=             '<input class="form-control border-right border-left req-true" style="height: calc(2.19rem + 10px)" ';
                html +=                 'type="text" id="form-cantidad-'+cantidad+'" name="form-cantiidad-'+cantidad+'" ';
                html +=                 'value="'+valor["cantidad"]+'" readonly>';
                html +=         '</div>';
                html +=     '</div>';
                html +=     '<div class="col-lg p-0">'; //DESPERDICIO
                html +=         '<div class="input-group validate-input" data-validate="Cantidad es requerida">';
                html +=             '<label class="rl-desperdicio align-self-center col-5"><strong>Desperdicio:</strong></label>';
                html +=             '<input class="form-control border-left req-true" style="height: calc(2.19rem + 10px)" ';
                html +=                 'type="number" id="form-desperdicio-'+cantidad+'" name="form-desperdicio-'+cantidad+'" '; 
                html +=                 'placeholder="Ingrese la cantidad de desperdicio" min="0" max="55" '; 
                html +=                 'step="any" value="'+valor["desperdicio"]+'" onchange="verificar('+cantidad+')">';
                html +=         '</div>';
                html +=     '</div>';
                html +=     '<div style="border-bottom: 1px solid #C3CAD6;" class="w-100 my-3 d-separator"></div>';
                html += '</div>';
                $("#"+form_data).append(html);
                cantidad++;
            });
            var html = '<input type="hidden" id="cantidad-productos-desperdicio" value="'+(cantidad-1)+'">';
            $("#"+form_data).append(html);
        }
        else{
            $("#"+spinner).addClass("d-none");
            $("#"+form).removeClass("d-none");
            var html;
            html = '<div class="text-center alert alert-danger">Error interno con el servidor. Porfavor intentelo mas tarde</div>';
            $("#"+form_data).append(html);
        }
    })
    .fail( function( jqXHR, textStatus, errorThrown ) {
        var mensaje;

        if (jqXHR.status === 0) {
            mensaje= 'Error al recibir los datos de la compra. No hay conexión: Verifique la conexión';
        } else if (jqXHR.status == 404) {
            mensaje= 'Error al recibir los datos de la compra. Error [404] página no encontrada';
        } else if (jqXHR.status == 500) {
            mensaje= 'Error al recibir los datos de la compra. Error [500] error de servidor';
        } else if (textStatus === 'parsererror') {
            mensaje= 'Error al recibir los datos de la compra. Error en envio de JSON';
        } else if (textStatus === 'timeout') {
            mensaje= 'Error al recibir los datos de la compra. Finalizo el tiempo de conexión intentelo de nuevo';
        } else if (textStatus === 'abort') {
            mensaje= 'Error al recibir los datos de la compra. La solicitud fue rechazada intentelo de nuevo';
        } else {
            mensaje= 'Error al recibir los datos de la compra. Error [203] PHP fallo en la solicitud';
        }

        $("#"+spinner).addClass("d-none");
        $("#"+form).removeClass("d-none");
        var html;
        html = '<div class="text-center alert alert-danger">'+mensaje+'<br>Porfavor Intentelo mas tarde</div>';
        $("#"+form_data).append(html);

    })
    .always(function(){
        //alert("finalizo el ajax");
    });
}

//FUNCION AJAX QUE GUARDA EL CONTENIDO DE DESPERDICIO QUE HAYAMOS ENVIADO
const ajaxUpdateDesperdicio = (id,url,report_url) => {

    var productos = new Array();
    var elementos = $("#cantidad-productos-desperdicio").val();
    for (i = 1; i <= elementos; i++) {
        var datos = new Object();
        datos.id_producto = parseInt($("#producto-id-"+i).val(), 16);
        datos.cantidad = parseFloat($("#form-desperdicio-"+i).val());
        productos.push(datos);
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    $.ajax({
        type: "POST", 
        url: url, 
        data: {
            'id': JSON.stringify(id),
            'products': JSON.stringify(productos),
        }, 
        dataType: 'json',
    })
    .done(function(response) {
        var result = response;
        if(result){
            swal({
                title: 'Desperdicio Editado Correctamente',
                icon: 'success',
                closeOnClickOutside: false,
                button: 'Aceptar',
            });
            $(".swal-button--confirm").addClass('bg-success');
        }
        else{
            swal({
                title: 'Desperdicio no pudo ser editado, intentelo mas tarde.',
                icon: 'error',
                closeOnClickOutside: false,
                button: 'Aceptar',
            });
            $(".swal-button--confirm").addClass('bg-success');
        }
    })
    .fail( function( jqXHR, textStatus, errorThrown ) {
        
        if (jqXHR.status === 0) {
            ajaxSwallErrorNotification('Error al editar desperdicio. No hay conexión: Verifique la conexión');
            ajaxReportError('Editar Desperdicio - Not connect: Verify Network.',report_url);
        } else if (jqXHR.status == 404) {
            ajaxSwallErrorNotification('Error al editar desperdicio. Error [404] página no encontrada');
            ajaxReportError('Editar Desperdicio - Requested page not found [404]',report_url);
        } else if (jqXHR.status == 500) {
            ajaxSwallErrorNotification('Error al editar desperdicio. Error [500] error de servidor');
            ajaxReportError('Editar Desperdicio - Internal Server Error [500].',report_url);
        } else if (textStatus === 'parsererror') {
            ajaxSwallErrorNotification('Error al editar desperdicio. Error en envio de JSON');
            ajaxReportError('Editar Desperdicio - Requested JSON parse failed.',report_url);
        } else if (textStatus === 'timeout') {
            ajaxSwallErrorNotification('Error al editar desperdicio. Finalizo el tiempo de conexión intentelo de nuevo');
            ajaxReportError('Editar Desperdicio - Time out error.',report_url);
        } else if (textStatus === 'abort') {
            ajaxSwallErrorNotification('Error al editar desperdicio. La solicitud fue rechazada intentelo de nuevo');
            ajaxReportError('Editar Desperdicio - Ajax request aborted.',report_url);
        } else {
            ajaxSwallErrorNotification('Error al editar desperdicio. Error [203] PHP fallo en la solicitud');
            ajaxReportError('Editar Desperdicio - Uncaught Error: ' + jqXHR.responseText,report_url);
        }

    })
    .always(function(){
        //alert("finalizo el ajax");
    });
}

//FUNCION AJAX QUE ME ACOMODO LOS PRODUCTOS DE LA VENTA O COMPRA
const ajaxDetailProducts = (id,url,form,form_data,spinner) => {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    $.ajax({
        type: "POST", 
        url: url, 
        data: {'values': JSON.stringify(id)}, 
        dataType: 'json',
    })
    .done(function(response) {
        var obj = response;
        if(obj){
            $("#"+spinner).addClass("d-none");
            $("#"+form).removeClass("d-none");
            obj.forEach( function(valor, indice, array) {
                var html;
                html  = '<div class="form-group form-control col-12 p-0" style="height: calc(2.19rem + 10px)">';
                html +=    '<div class="input-group row justify-content-center">';
                html +=        '<div class="d-flex col-6" style="border-right: 1px solid #ced4da; overflow: hidden">';
                                    //PRODUCTO
                html +=            '<div class="my-auto icon-box text-center bg-transparent" style="border: 0px; border-radius: 0px">';
                html +=                '<span>'+valor["nombre"]+'</span>';
                html +=            '</div>';
                html +=        '</div>';
                html +=        '<div class="d-flex col" style="border-right: 1px solid #ced4da; overflow: hidden">';
                                    //CANTIDAD
                html +=            '<div class="my-auto icon-box text-center bg-transparent" style="border: 0px; border-radius: 0px">';
                html +=                '<span>'+valor["cantidad"]+'</span>';
                html +=            '</div>';
                html +=        '</div>';
                html +=        '<div class="d-flex col">';
                                    //PRECIO
                html +=            '<div class="my-auto icon-box text-center bg-transparent" style="border: 0px; border-radius: 0px">';
                html +=                '<span>'+valor["precio"]+' Bs</span>';
                html +=            '</div>';
                html +=        '</div>';
                html +=    '</div>';                          
                html += '</div>';
                $("#"+form_data).append(html);
            });
            $("#"+form_data).append(html);
        }
        else{
            $("#"+spinner).addClass("d-none");
            $("#"+form).removeClass("d-none");
            var html;
            html = '<div class="text-center alert alert-danger">Error interno con el servidor. Porfavor intentelo mas tarde</div>';
            $("#"+form_data).append(html);
        }
    })
    .fail( function( jqXHR, textStatus, errorThrown ) {
        var mensaje;

        if (jqXHR.status === 0) {
            mensaje= 'Error al recibir los datos de la compra. No hay conexión: Verifique la conexión';
        } else if (jqXHR.status == 404) {
            mensaje= 'Error al recibir los datos de la compra. Error [404] página no encontrada';
        } else if (jqXHR.status == 500) {
            mensaje= 'Error al recibir los datos de la compra. Error [500] error de servidor';
        } else if (textStatus === 'parsererror') {
            mensaje= 'Error al recibir los datos de la compra. Error en envio de JSON';
        } else if (textStatus === 'timeout') {
            mensaje= 'Error al recibir los datos de la compra. Finalizo el tiempo de conexión intentelo de nuevo';
        } else if (textStatus === 'abort') {
            mensaje= 'Error al recibir los datos de la compra. La solicitud fue rechazada intentelo de nuevo';
        } else {
            mensaje= 'Error al recibir los datos de la compra. Error [203] PHP fallo en la solicitud';
        }

        $("#"+spinner).addClass("d-none");
        $("#"+form).removeClass("d-none");
        var html;
        html = '<div class="text-center alert alert-danger">'+mensaje+'<br>Porfavor Intentelo mas tarde</div>';
        $("#"+form_data).append(html);

    })
    .always(function(){
        //alert("finalizo el ajax");
    });
}

//FUNCION AJAX QUE ME ACOMODA LOS PRODUCTOS DE LOS SUMINISTROS POR PROVEEDOR
const ajaxDetailSuministroProducts = (id,url,form,form_data,spinner) => {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    $.ajax({
        type: "POST", 
        url: url, 
        data: {'values': JSON.stringify(id)}, 
        dataType: 'json',
    })
    .done(function(response) {
        var obj = response;
        if(obj){
            $("#"+spinner).addClass("d-none");
            $("#"+form).removeClass("d-none");
            obj.forEach( function(valor, indice, array) {
                var html;
                html  = '<div class="form-group form-control col-12 p-0" style="height: calc(2.19rem + 10px)">';
                html +=    '<div class="input-group row justify-content-center">';
                html +=        '<div class="d-flex col-6" style="border-right: 1px solid #ced4da; overflow: hidden">';
                                    //PRODUCTO
                html +=            '<div class="my-auto icon-box text-center bg-transparent" style="border: 0px; border-radius: 0px">';
                html +=                '<span>'+valor["nombre"]+'</span>';
                html +=            '</div>';
                html +=        '</div>';
                html +=        '<div class="d-flex col">';
                                    //PRECIO
                html +=            '<div class="my-auto icon-box text-center bg-transparent" style="border: 0px; border-radius: 0px">';
                html +=                '<span>'+valor["precio"]+' Bs</span>';
                html +=            '</div>';
                html +=        '</div>';
                html +=    '</div>';                          
                html += '</div>';
                $("#"+form_data).append(html);
            });
            if(obj.length<=0){
                html = '<div class="text-center alert alert-warning">No se encuentran productos registrados</div>';
            }
            $("#"+form_data).append(html);
        }
        else{
            $("#"+spinner).addClass("d-none");
            $("#"+form).removeClass("d-none");
            var html;
            html = '<div class="text-center alert alert-danger">Error interno con el servidor. Porfavor intentelo mas tarde</div>';
            $("#"+form_data).append(html);
        }
    })
    .fail( function( jqXHR, textStatus, errorThrown ) {
        var mensaje;

        if (jqXHR.status === 0) {
            mensaje= 'Error al recibir los datos de la compra. No hay conexión: Verifique la conexión';
        } else if (jqXHR.status == 404) {
            mensaje= 'Error al recibir los datos de la compra. Error [404] página no encontrada';
        } else if (jqXHR.status == 500) {
            mensaje= 'Error al recibir los datos de la compra. Error [500] error de servidor';
        } else if (textStatus === 'parsererror') {
            mensaje= 'Error al recibir los datos de la compra. Error en envio de JSON';
        } else if (textStatus === 'timeout') {
            mensaje= 'Error al recibir los datos de la compra. Finalizo el tiempo de conexión intentelo de nuevo';
        } else if (textStatus === 'abort') {
            mensaje= 'Error al recibir los datos de la compra. La solicitud fue rechazada intentelo de nuevo';
        } else {
            mensaje= 'Error al recibir los datos de la compra. Error [203] PHP fallo en la solicitud';
        }

        $("#"+spinner).addClass("d-none");
        $("#"+form).removeClass("d-none");
        var html;
        html = '<div class="text-center alert alert-danger">'+mensaje+'<br>Porfavor Intentelo mas tarde</div>';
        $("#"+form_data).append(html);

    })
    .always(function(){
        //alert("finalizo el ajax");
    });
}

//FUNCION AJAX QUE ACOMODA LA EDICION DE LOS PRODUCTOS EN SUMINISTRO O INVENTARIO
const ajaxEditLogistica = (id,url,form,tipo,spinner) => {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    $.ajax({
        type: "POST", 
        url: url, 
        data: {'values': JSON.stringify(id)}, 
        dataType: 'json',
    })
    .done(function(response) {
        var valores = response;
        if(valores){
            $("#"+spinner).addClass("d-none");
            $("#"+form).removeClass("d-none");

            //Setteo el formulario
            $("#"+form+" #form-producto option[value="+ valores["id_producto"] +"]").attr("selected",true);
            $("#"+form+" #form-price").val(valores["precio"]);
            $("#"+form+" #form-cantidad").val(valores["cantidad"]);
            $("#"+form+" #form-fecha").val(valores["expedicion"]);
            if(tipo=="suministro"){
                //Setteo el proveedor
                $("#"+form+" #form-proveedor option[value="+ valores["id_proveedor"] +"]").attr("selected",true);
            }
            //Setteo el id del suministro a editar
            $("#edit-suministro").val(id);
        }
        else{
            $("#"+spinner).addClass("d-none");
            var html;
            html = '<div class="text-center alert alert-danger" id="error-edicion">Error interno con el servidor. Porfavor intentelo mas tarde</div>';
            $("#"+form).before(html);
        }
    })
    .fail( function( jqXHR, textStatus, errorThrown ) {
        var mensaje;

        if (jqXHR.status === 0) {
            mensaje= 'Error al recibir los datos. No hay conexión: Verifique la conexión';
        } else if (jqXHR.status == 404) {
            mensaje= 'Error al recibir los datos. Error [404] página no encontrada';
        } else if (jqXHR.status == 500) {
            mensaje= 'Error al recibir los datos. Error [500] error de servidor';
        } else if (textStatus === 'parsererror') {
            mensaje= 'Error al recibir los datos. Error en envio de JSON';
        } else if (textStatus === 'timeout') {
            mensaje= 'Error al recibir los datos. Finalizo el tiempo de conexión intentelo de nuevo';
        } else if (textStatus === 'abort') {
            mensaje= 'Error al recibir los datos. La solicitud fue rechazada intentelo de nuevo';
        } else {
            mensaje= 'Error al recibir los datos. Error [203] PHP fallo en la solicitud';
        }

        $("#"+spinner).addClass("d-none");
        var html;
        html = '<div class="text-center alert alert-danger" id="error-edicion">'+mensaje+'<br>Porfavor Intentelo mas tarde</div>';
        $("#"+form).before(html);

    })
    .always(function(){
        //alert("finalizo el ajax");
    });
}

//FUNCION QUE MUESTRA EL MENSAJE DE ERROR
const ajaxSwallErrorNotification = (message) => {
    swal({
        title: message,
        icon: 'error',
        closeOnClickOutside: false,
        button: 'Aceptar',
    });
}

//FUNCION AJAX QUE FILTRA LOS CHARTS EN ESTADITICAS
const ajaxFilterCharts = (id,url,CanvaInfo,BarColors1,BarColors2,tipo,busqueda) => {
    //FORMATEO EL DIV DEL CANVA
    $("#"+CanvaInfo[0]).empty();
    var html = '<div class="m-auto text-center col-12 '+CanvaInfo[2]+'">';
        html +=   '<i class="fa fa-5x fa-lg fa-spinner fa-spin" style="color: #028936"></i>';
        html +='</div>';
        html +='<canvas id="'+CanvaInfo[1]+'"></canvas>';
    $("#"+CanvaInfo[0]).append(html);
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    $.ajax({
        type: "POST", 
        url: url, 
        data: {
            'id': JSON.stringify(id),
            'busqueda': JSON.stringify(busqueda),
        }, 
        dataType: 'json',
    })
    .done(function(response) {
        var obj = response;
        if(obj){
            //PARA QUITAR EL SPINNER
            $("."+CanvaInfo[2]).addClass('d-none');
            switch (tipo) {
                case 'singlelinechart':
                    var labels = obj[0];
                    var data = obj[1];
                    printSingleLineChart(CanvaInfo[1],data,labels,"",BarColors1[0],BarColors1[1]);
                    $('#'+CanvaInfo[1]).height(CanvaInfo[3]); 
                    break;

                case 'doublebarschart':
                    var labels = obj[0]; var label_1 = obj[1]; var label_2 = obj[2];
                    var data_1 = obj[3];
                    var data_2 = obj[4];
                    console.log(data_1); console.log(data_2);
                    /* BAR 1 */
                    var bar_1 = new Array();
                    bar_1.push(data_1,label_1,BarColors1[0],BarColors1[1]);
                    /* BAR 2 */
                    var bar_2 = new Array(); 
                    bar_2.push(data_2,label_2,BarColors2[0],BarColors2[1]);
                    printDoubleBarChart(CanvaInfo[1],labels,bar_1,bar_2);
                    break;

                case 'singlebarchart':
                    var labels = obj[0];
                    var data = obj[1];
                    printSingleBarChart(CanvaInfo[1],data,labels,"",BarColors1[0],BarColors1[1]);
                    $('#'+CanvaInfo[1]).height(CanvaInfo[3]); 
                    break;

                case 'mixedbarline':
                    var labels = obj[0]; var label_1 = obj[1]; var label_2 = obj[2];
                    var data_1 = obj[3];
                    var data_2 = obj[4];
                    console.log(data_1); console.log(data_2);
                    /* BAR 1 */
                    var bar = new Array();
                    bar.push(data_1,label_1,BarColors1[0],BarColors1[1]);
                    /* BAR 2 */
                    var line = new Array(); 
                    line.push(data_2,label_2,BarColors2[0],BarColors2[1]);
                    printMixedLineBarChart(CanvaInfo[1],CanvaInfo[4],labels,bar,line);
                    break;

                case 'piechart':
                    var labels = obj[0];
                    var data = obj[1];
                    printPieChart(CanvaInfo[1],data,labels,BarColors1,BarColors2);
                    $('#'+CanvaInfo[1]).height(CanvaInfo[3]); 
                    break;
            }
        }
        else{
            $("."+CanvaInfo[2]).fadeOut(1000, function() {
                var html = '<div class="text-center alert alert-danger">Error interno con el servidor. Porfavor intentelo mas tarde</div>';
                $("#"+CanvaInfo[0]).append(html);
            });
        }
    })
    .fail( function( jqXHR, textStatus, errorThrown ) {
        var mensaje;

        if (jqXHR.status === 0) {
            mensaje= 'Error al recibir los datos del indicador. No hay conexión: Verifique la conexión';
        } else if (jqXHR.status == 404) {
            mensaje= 'Error al recibir los datos del indicador. Error [404] página no encontrada';
        } else if (jqXHR.status == 500) {
            mensaje= 'Error al recibir los datos del indicador. Error [500] error de servidor';
        } else if (textStatus === 'parsererror') {
            mensaje= 'Error al recibir los datos del indicador. Error en envio de JSON';
        } else if (textStatus === 'timeout') {
            mensaje= 'Error al recibir los datos del indicador. Finalizo el tiempo de conexión intentelo de nuevo';
        } else if (textStatus === 'abort') {
            mensaje= 'Error al recibir los datos del indicador. La solicitud fue rechazada intentelo de nuevo';
        } else {
            mensaje= 'Error al recibir los datos del indicador. Error [203] PHP fallo en la solicitud';
        }

        $("."+CanvaInfo[2]).fadeOut(1000, function() {
            var html = '<div class="text-center alert alert-danger">'+mensaje+'<br>Porfavor Intentelo mas tarde</div>';
            $("#"+CanvaInfo[0]).append(html);
        });
    })
    .always(function(){
        //alert("finalizo el ajax");
    });
}