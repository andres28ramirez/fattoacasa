
(function ($) {
    "use strict";

    /*==================================================================
    [ Focus Contact2 ]*/
    $('.input100').each(function(){
        $(this).on('blur', function(){
            if($(this).val().trim() != "") {
                $(this).addClass('has-val');
            }
            else {
                $(this).removeClass('has-val');
            }
        })    
    })

    /*==================================================================
    [ Validate ]*/
    var id; //ID DEL FORMULARIO

    //EVALUO QUE FORMULARIO ESTOY VALIDANDO Y LE HAGO SU PROPIA VALIDACIÓN ASI SEA GENERAL
    $(".id-form").each(function(indice,elemento){
        id = $(this).val();
    });

    var input = $('#'+id+' .input100');
    console.log(input);
    //ESTO REVISO INPUT POR INPUT Y CUANDO YA TODOS PASEN LA VALIDACIÓN SE HABILITA EL SUBMIT
    $('#'+id).on('submit',function(){
        var check = true;
        for(var i=0; i<input.length; i++) {
            if(validate(input[i]) == false){
                showValidate(input[i]);
                check=false;
            }
        }

        //VERIFICO SI EXISTE EL ESPACIO DE RECETARIO PARA QUE EJECUTE UN AJAX
        if ( check && $('#recetario-form-edit-producto').length > 0 ) {
            var url = $("#ruta-receta").val();
            var id_producto = $("#model-id-edit").val();
            //vacio el recetario antes de hacerle un update
            ajaxUpdateReceta(id_producto,0,0,url);
            for (var i = 0; i < 20; i++) {
                if(!check)
                    break;
                if ( $("#form-producto-"+i).length ){
                    var id_ingrediente = $('select[id="form-producto-'+i+'"] option:selected').val();
                    var cantidad = $('input[id="form-cantidad-'+i+'"]').val();
                    check = ajaxUpdateReceta(id_producto,id_ingrediente,cantidad,url);
                }
            }
        }
        
        return check;
    });

    //FUNCIÓN QUE CARGA EN CADA INPUT EL EVENTO DE BORRAR LA ALERTA
    $('.validate-form .input100').each(function(){
        $(this).focus(function(){
           hideValidate(this);
        });
    });

    //VALIDACIÓN POR CADA TIPO DE INPUT - SELECT
    function validate_email(input){
        if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
            //SE ACOMODA EL DATA VALIDATE PARA LA ALERTA
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Formato correo requerido (@)")
            return false;
        }
    }

    function validate_cid(input){
        if($(input).val().trim().match(/^([0-9])*$/) == null) {
            //SE ACOMODA EL DATA VALIDATE PARA LA ALERTA
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Formato númerico requerido")
            return false;
        }

        if($(input).val().length < 7){
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Debe llevar mínimo 7 cifras")
            return false;
        }
    }

    function validate_phone(input){
        if($(input).val().trim().match(/^([0-9\-\+])*$/) == null) {
            //SE ACOMODA EL DATA VALIDATE PARA LA ALERTA
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Formato teléfonico requerido")
            return false;
        }
    }

    function validate_cuenta(input){
        if($(input).val().trim().match(/^([0-9\-])*$/) == null) {
            //SE ACOMODA EL DATA VALIDATE PARA LA ALERTA
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Solo se permiten caracteres númericos y '-'")
            return false;
        }
    }

    function validate_number(input){
        if($(input).val().trim().match(/^([0-9\.])*$/) == null) {
            //SE ACOMODA EL DATA VALIDATE PARA LA ALERTA
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Formato númerico requerido")
            return false;
        }
    }

    function validate_direction(input){
        if($(input).val().length < 7){
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Debe llevar mínimo 7 caracteres")
            return false;
        }
    }

    function validate_price(input){
        if($(input).val().trim().match(/^([0-9\.])*$/) == null) {
            //SE ACOMODA EL DATA VALIDATE PARA LA ALERTA
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Formato númerico requerido");
            return false;
        }

        if($(input).val().length <= 0){
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Precio debe ser mayor a 0")
            return false;
        }
    }

    function validate_cantidad(input){
        if($(input).val().trim().match(/^([0-9\.])*$/) == null) {
            //SE ACOMODA EL DATA VALIDATE PARA LA ALERTA
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Formato númerico requerido");
            return false;
        }

        if($(input).val().length <= 0){
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Cantidad debe ser mayor a 0")
            return false;
        }
    }

    function validate_password(input){
        if($(input).val().length < 6){
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Debe llevar mínimo 6 caracteres")
            return false;
        }
    }

    function validate_re_password(input){
        if($(input).val().length < 6){
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","Debe llevar mínimo 6 caracteres")
            return false;
        }

        if($(input).val().trim() != $("#form-password").val().trim()) {
            //SE ACOMODA EL DATA VALIDATE PARA LA ALERTA
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","La contraseña no coincide");
            return false;
        }
    }

    function validate_fecha_2(input){
        if(Date.parse($(input).val()) < Date.parse($("#"+id+" #form-fecha-1").val())) {
            //SE ACOMODA EL DATA VALIDATE PARA LA ALERTA
            var inputDiv = $(input).parent();
            $(inputDiv).attr("data-validate","La fecha debe ser mayor o igual");
            return false;
        }
    }

    //FUNCIONES QUE EJECUTAN CADA VALIDACIÓN DE ARRIBA Y PONE O QUITA ALERTAS
    function validate (input) {

        //REVISO SI ES REQUERIDO O NO
        if($(input).hasClass("req-true")){
            //REVISO SI ESTA VACIO PRIMERO
            if($(input).val().trim() == ''){
                return false;
            }

            //VALIDO CADA DATO QUE NECESITA ALGUNA CANTIDAD EN ESPECIFICO
            if($(input).attr('type') == 'email' || $(input).attr('id') == 'form-email') {
                return validate_email(input);
            }
            else if($(input).attr('id') == 'form-cid' || $(input).attr('id') == 'form-cedula'){
                return validate_cid(input);
            }
            else if($(input).attr('id') == 'form-phone'){
                return validate_phone(input);
            }
            else if($(input).attr('id') == 'form-cuenta'){
                return validate_cuenta(input);
            }
            else if($(input).attr('type') == 'number' || $(input).attr('id') == 'form-num'){
                return validate_number(input);
            }
            else if($(input).attr('id') == 'form-direction'){
                return validate_direction(input);
            }
            else if($(input).attr('id') == 'form-price'){
                return validate_price(input);
            }
            else if($(input).attr('id') == 'form-cantidad'){
                return validate_cantidad(input);
            }
            else if($(input).hasClass('form-cantidad')){
                return validate_cantidad(input);
            }
            else if($(input).attr('id') == 'form-password'){
                return validate_password(input);
            }
            else if($(input).attr('id') == 'form-re-password'){
                return validate_re_password(input);
            }
            else if($(input).attr('id') == 'form-fecha-2'){
                return validate_fecha_2(input);
            }
        }
        else{//AQUI YA NO ES REQUERIDO
            //REVISO SI ESTA VACIO PRIMERO POR LO QUE SI PUEDE PASAR
            if($(input).val().trim() == ''){
                return true;
            }

            //VALIDO CADA DATO QUE NECESITA ALGUNA CANTIDAD EN ESPECIFICO
            if($(input).attr('type') == 'email' || $(input).attr('id') == 'form-email') {
                return validate_email(input);
            }
            else if($(input).attr('id') == 'form-cid'){
                return validate_cid(input);
            }
            else if($(input).attr('id') == 'form-phone'){
                return validate_phone(input);
            }
            else if($(input).attr('id') == 'form-cuenta'){
                return validate_cuenta(input);
            }
            else if($(input).attr('type') == 'number' || $(input).attr('id') == 'form-num'){
                return validate_number(input);
            }
            else if($(input).attr('id') == 'form-direction'){
                return validate_direction(input);
            }
            else if($(input).attr('id') == 'form-price'){
                return validate_price(input);
            }
            else if($(input).attr('id') == 'form-cantidad'){
                return validate_cantidad(input);
            }
            else if($(input).attr('id') == 'form-password'){
                return validate_password(input);
            }
            else if($(input).attr('id') == 'form-re-password'){
                return validate_re_password(input);
            }
            else if($(input).attr('id') == 'form-fecha-2'){
                return validate_fecha_2(input);
            }
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();
        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();
        $(thisAlert).removeClass('alert-validate');
    }

    /*==================================================================
    [ Hide - Show ]*/
    //EVENTO PARA MOSTRAR LOS INPUTS O SELECT DE LOS FORM INDICADORES
    $('#'+id+" #input-form-año").hide();
    $('#'+id+" #input-form-fecha-1").hide();
    $('#'+id+" #input-form-fecha-2").hide();
    $('#'+id+" #input-form-mes").hide();

    $('#'+id+" #form-tiempo").change(function() {
        var value = $(this).val();
        switch(value) {
            case "Año":
                $('#'+id+" #input-form-año").show(500); /**/ $('#'+id+" #form-año").addClass("req-true");
                $('#'+id+" #input-form-mes").hide(500); /**/ $('#'+id+" #form-mes").removeClass("req-true");
                $('#'+id+" #input-form-fecha-1").hide(500); /**/ $('#'+id+" #form-fecha-1").removeClass("req-true");
                $('#'+id+" #input-form-fecha-2").hide(500); /**/ $('#'+id+" #form-fecha-2").removeClass("req-true");
              break;
            case "Mes":
                $('#'+id+" #input-form-año").hide(500); /**/ $('#'+id+" #form-año").removeClass("req-true");
                $('#'+id+" #input-form-mes").show(500); /**/ $('#'+id+" #form-mes").addClass("req-true");
                $('#'+id+" #input-form-fecha-1").hide(500); /**/ $('#'+id+" #form-fecha-1").removeClass("req-true");
                $('#'+id+" #input-form-fecha-2").hide(500); /**/ $('#'+id+" #form-fecha-2").removeClass("req-true");
              break;
            case "Específico":
                $('#'+id+" #input-form-año").hide(500); /**/ $('#'+id+" #form-año").removeClass("req-true");
                $('#'+id+" #input-form-mes").hide(500); /**/ $('#'+id+" #form-mes").removeClass("req-true");
                $('#'+id+" #input-form-fecha-1").show(500); /**/ $('#'+id+" #form-fecha-1").addClass("req-true");
                $('#'+id+" #input-form-fecha-2").show(500); /**/ $('#'+id+" #form-fecha-2").addClass("req-true");
              break;
            case "Todos los años":
                $('#'+id+" #input-form-año").hide(500); /**/ $('#'+id+" #form-año").removeClass("req-true");
                $('#'+id+" #input-form-mes").hide(500); /**/ $('#'+id+" #form-mes").removeClass("req-true");
                $('#'+id+" #input-form-fecha-1").hide(500); /**/ $('#'+id+" #form-fecha-1").removeClass("req-true");
                $('#'+id+" #input-form-fecha-2").hide(500); /**/ $('#'+id+" #form-fecha-2").removeClass("req-true");
              break;
            default:
                $('#'+id+" #input-form-año").hide(500); /**/ $('#'+id+" #form-año").removeClass("req-true");
                $('#'+id+" #input-form-mes").hide(500); /**/ $('#'+id+" #form-mes").removeClass("req-true");
                $('#'+id+" #input-form-fecha-1").hide(500); /**/ $('#'+id+" #form-fecha-1").removeClass("req-true");
                $('#'+id+" #input-form-fecha-2").hide(500); /**/ $('#'+id+" #form-fecha-2").removeClass("req-true");
              break;
        }
    });

})(jQuery);