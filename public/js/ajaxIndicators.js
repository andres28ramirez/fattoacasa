var urlFilter = $("#url-filter").val();

const periodo = (chart) => {
    return $('#'+chart+' select[id="form-periodo"] option:selected').val();
}

const tiempo = (chart) => {
    return $('#'+chart+' select[id="form-tiempo"] option:selected').val();
}

const fecha_1 = (chart) => {
    var fecha = $('#'+chart+' input[id="form-fecha-1"]').val();
    return fecha;
}

const fecha_2 = (chart) => {
    var fecha = $('#'+chart+' input[id="form-fecha-2"]').val();
    return fecha;
}

const ayo = (chart) => {
    var fecha = $('#'+chart+' input[id="form-año"]').val();
    return fecha;
}

//CAPTURAR EVENTO SUBMIT DE FILTRAR INFORMACIÓN
//INDICADORES DE LOGISTICA
    $("#submit-form-logistic-c5").one('click',function(event){
        $("#form-logistic-c5").on('submit',function(){
            var filtrar = false;
            if(periodo("form-logistic-c5") && tiempo("form-logistic-c5")){
                if(tiempo("form-logistic-c5")=="Específico"){
                    if(!(Date.parse(fecha_2("form-logistic-c5")) < Date.parse(fecha_1("form-logistic-c5"))) && fecha_1("form-logistic-c5") && fecha_2("form-logistic-c5"))
                        filtrar = true;
                }
                else if(tiempo("form-logistic-c5")=="Año"){
                    ayo("form-logistic-c5") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-logistic-c5"),tiempo("form-logistic-c5"),fecha_1("form-logistic-c5"),fecha_2("form-logistic-c5"),ayo("form-logistic-c5"));
                //INFORMACIÓN DEL CANVA
                var tipo = "singlelinechart";
                var height = $("#canva-indicator-c5").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c5","singlelinechartc5","singlelinechart-spinner",height);
                var BarColors1 = new Array(); BarColors1.push("rgba(29,163,101,0.7)","rgba(25,61,47,0.8)");
                var BarColors2 = new Array(); BarColors2.push("","");
                ajaxFilterCharts("c5",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

    $("#submit-form-logistic-c3").one('click',function(event){
        $("#form-logistic-c3").on('submit',function(){
            var filtrar = false;
            if(periodo("form-logistic-c3") && tiempo("form-logistic-c3")){
                if(tiempo("form-logistic-c3")=="Específico"){
                    if(!(Date.parse(fecha_2("form-logistic-c3")) < Date.parse(fecha_1("form-logistic-c3"))) && fecha_1("form-logistic-c3") && fecha_2("form-logistic-c3"))
                        filtrar = true;
                }
                else if(tiempo("form-logistic-c3")=="Año"){
                    ayo("form-logistic-c3") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-logistic-c3"),tiempo("form-logistic-c3"),fecha_1("form-logistic-c3"),fecha_2("form-logistic-c3"),ayo("form-logistic-c3"));
                //INFORMACIÓN DEL CANVA
                var tipo = "doublebarschart";
                var height = $("#canva-indicator-c3").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c3","doublechartc3","doublechart-spinner",height);
                var BarColors1 = new Array(); BarColors1.push("rgba(29,163,101,0.7)","rgba(25,61,47,0.8)");
                var BarColors2 = new Array(); BarColors2.push("rgba(255,129,0,0.7)","rgba(128,66,5,0.8)");
                ajaxFilterCharts("c3",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

    $("#submit-form-logistic-c2").one('click',function(event){
        $("#form-logistic-c2").on('submit',function(){
            var filtrar = false;
            if(periodo("form-logistic-c2") && tiempo("form-logistic-c2")){
                if(tiempo("form-logistic-c2")=="Específico"){
                    if(!(Date.parse(fecha_2("form-logistic-c2")) < Date.parse(fecha_1("form-logistic-c2"))) && fecha_1("form-logistic-c2") && fecha_2("form-logistic-c2"))
                        filtrar = true;
                }
                else if(tiempo("form-logistic-c2")=="Año"){
                    ayo("form-logistic-c2") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-logistic-c2"),tiempo("form-logistic-c2"),fecha_1("form-logistic-c2"),fecha_2("form-logistic-c2"),ayo("form-logistic-c2"));
                //INFORMACIÓN DEL CANVA
                var tipo = "singlebarchart";
                var height = $("#canva-indicator-c2").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c2","singlechartc2","singlechart-spinner",height);
                var BarColors1 = new Array(); BarColors1.push("rgba(255,48,23,0.7)","rgba(128,23,11,0.8)");
                var BarColors2 = new Array(); BarColors2.push("","");
                ajaxFilterCharts("c2",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

//INDICADORES DE VENTAS
    $("#submit-form-sell-c1").one('click',function(event){
        $("#form-sell-c1").on('submit',function(){
            var filtrar = false;
            if(periodo("form-sell-c1") && tiempo("form-sell-c1")){
                if(tiempo("form-sell-c1")=="Específico"){
                    if(!(Date.parse(fecha_2("form-sell-c1")) < Date.parse(fecha_1("form-sell-c1"))) && fecha_1("form-sell-c1") && fecha_2("form-sell-c1"))
                        filtrar = true;
                }
                else if(tiempo("form-sell-c1")=="Año"){
                    ayo("form-sell-c1") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-sell-c1"),tiempo("form-sell-c1"),fecha_1("form-sell-c1"),fecha_2("form-sell-c1"),ayo("form-sell-c1"));
                //INFORMACIÓN DEL CANVA
                var tipo = "mixedbarline";
                var height = $("#canva-indicator-c1").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT - TIPO
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c1","mixedlinebarchartc1","mixedlinebarchart-spinner",height,"porcentaje");
                var BarColors1 = new Array(); BarColors1.push("rgba(29,163,101,0.7)","rgba(25,61,47,0.8)");
                var BarColors2 = new Array(); BarColors2.push("rgba(255,48,23,0.7)","rgba(128,23,11,0.8)");
                ajaxFilterCharts("c1",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

    $("#submit-form-sell-c2").one('click',function(event){
        $("#form-sell-c2").on('submit',function(){
            var filtrar = false;
            if(periodo("form-sell-c2") && tiempo("form-sell-c2")){
                if(tiempo("form-sell-c2")=="Específico"){
                    if(!(Date.parse(fecha_2("form-sell-c2")) < Date.parse(fecha_1("form-sell-c2"))) && fecha_1("form-sell-c2") && fecha_2("form-sell-c2"))
                        filtrar = true;
                }
                else if(tiempo("form-sell-c2")=="Año"){
                    ayo("form-sell-c2") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-sell-c2"),tiempo("form-sell-c2"),fecha_1("form-sell-c2"),fecha_2("form-sell-c2"),ayo("form-sell-c2"));
                //INFORMACIÓN DEL CANVA
                var tipo = "singlelinechart";
                var height = $("#canva-indicator-c2").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c2","singlelinechartc2","singlelinechart-spinner",height);
                var BarColors1 = new Array(); BarColors1.push("rgba(255,48,23,0.7)","rgba(128,23,11,0.8)");
                var BarColors2 = new Array(); BarColors2.push("","");
                ajaxFilterCharts("c2",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

    $("#submit-form-sell-c3").one('click',function(event){
        $("#form-sell-c3").on('submit',function(){
            var filtrar = false;
            if(periodo("form-sell-c3") && tiempo("form-sell-c3")){
                if(tiempo("form-sell-c3")=="Específico"){
                    if(!(Date.parse(fecha_2("form-sell-c3")) < Date.parse(fecha_1("form-sell-c3"))) && fecha_1("form-sell-c3") && fecha_2("form-sell-c3"))
                        filtrar = true;
                }
                else if(tiempo("form-sell-c3")=="Año"){
                    ayo("form-sell-c3") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-sell-c3"),tiempo("form-sell-c3"),fecha_1("form-sell-c3"),fecha_2("form-sell-c3"),ayo("form-sell-c3"));
                //INFORMACIÓN DEL CANVA
                var tipo = "mixedbarline";
                var height = $("#canva-indicator-c3").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT - TIPO
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c3","mixedlinebarchartc3","mixedlinebarchart-spinner",height,"normal");
                var BarColors1 = new Array(); BarColors1.push("rgba(255,129,0,0.7)","rgba(128,66,5,0.8)");
                var BarColors2 = new Array(); BarColors2.push("rgba(29,163,101,0.7)","rgba(25,61,47,0.8)");
                ajaxFilterCharts("c3",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

//INDICADORES DE COMPRAS
    $("#submit-form-buy-c1").one('click',function(event){
        $("#form-buy-c1").on('submit',function(){
            var filtrar = false;
            if(periodo("form-buy-c1") && tiempo("form-buy-c1")){
                if(tiempo("form-buy-c1")=="Específico"){
                    if(!(Date.parse(fecha_2("form-buy-c1")) < Date.parse(fecha_1("form-buy-c1"))) && fecha_1("form-buy-c1") && fecha_2("form-buy-c1"))
                        filtrar = true;
                }
                else if(tiempo("form-buy-c1")=="Año"){
                    ayo("form-buy-c1") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-buy-c1"),tiempo("form-buy-c1"),fecha_1("form-buy-c1"),fecha_2("form-buy-c1"),ayo("form-buy-c1"));
                //INFORMACIÓN DEL CANVA
                var tipo = "singlelinechart";
                var height = $("#canva-indicator-c1").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c1","singlelinechartc1","singlelinechart-spinner",height);
                var BarColors1 = new Array(); BarColors1.push("rgba(255,129,0,0.7)","rgba(128,66,5,0.8)");
                var BarColors2 = new Array(); BarColors2.push("","");
                ajaxFilterCharts("c1",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

    $("#submit-form-buy-c2").one('click',function(event){
        $("#form-buy-c2").on('submit',function(){
            var filtrar = false;
            if(periodo("form-buy-c2") && tiempo("form-buy-c2")){
                if(tiempo("form-buy-c2")=="Específico"){
                    if(!(Date.parse(fecha_2("form-buy-c2")) < Date.parse(fecha_1("form-buy-c2"))) && fecha_1("form-buy-c2") && fecha_2("form-buy-c2"))
                        filtrar = true;
                }
                else if(tiempo("form-buy-c2")=="Año"){
                    ayo("form-buy-c2") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-buy-c2"),tiempo("form-buy-c2"),fecha_1("form-buy-c2"),fecha_2("form-buy-c2"),ayo("form-buy-c2"));
                //INFORMACIÓN DEL CANVA
                var tipo = "piechart";
                var height = $("#canva-indicator-c2").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c2","piechartc2","piechart-spinner",height);
                var BarColors1 = new Array(); 
                BarColors1.push("rgba(255,129,0,0.15)", "rgba(255,129,0,0.20)", "rgba(255,129,0,0.25)","rgba(255,129,0,0.30)",
                                "rgba(255,129,0,0.35)", "rgba(255,129,0,0.40)", "rgba(255,129,0,0.45)", "rgba(255,129,0,0.50)",
                                "rgba(255,129,0,0.55)", "rgba(255,129,0,0.60)", "rgba(255,129,0,0.65)", "rgba(255,129,0,0.70)");
                var BgColor = "rgba(128,66,5,0.8)";
                ajaxFilterCharts("c2",urlFilter,CanvaInfo,BarColors1,BgColor,tipo,busqueda);
            }
            return false;
        });
    });

    $("#submit-form-buy-c3").one('click',function(event){
        $("#form-buy-c3").on('submit',function(){
            var filtrar = false;
            if(tiempo("form-buy-c3")){
                if(tiempo("form-buy-c3")=="Específico"){
                    if(!(Date.parse(fecha_2("form-buy-c3")) < Date.parse(fecha_1("form-buy-c3"))) && fecha_1("form-buy-c3") && fecha_2("form-buy-c3"))
                        filtrar = true;
                }
                else if(tiempo("form-buy-c3")=="Año"){
                    ayo("form-buy-c3") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(tiempo("form-buy-c3"),fecha_1("form-buy-c3"),fecha_2("form-buy-c3"),ayo("form-buy-c3"));
                //INFORMACIÓN DEL CANVA
                var tipo = "singlebarchart";
                var height = $("#canva-indicator-c3").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c3","singlechartc3","singlechart-spinner",height);
                var BarColors1 = new Array(); BarColors1.push("rgba(255,48,23,0.7)","rgba(128,23,11,0.8)");
                var BarColors2 = new Array(); BarColors2.push("","");
                ajaxFilterCharts("c3",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

    $("#submit-form-buy-c4").one('click',function(event){
        $("#form-buy-c4").on('submit',function(){
            var filtrar = false;
            var producto = $('#form-buy-c4 select[id="form-producto"] option:selected').val();
            var producto_nombre = $('#form-buy-c4 select[id="form-producto"] option:selected').text();

            if(tiempo("form-buy-c4") && producto){
                if(tiempo("form-buy-c4")=="Específico"){
                    if(!(Date.parse(fecha_2("form-buy-c4")) < Date.parse(fecha_1("form-buy-c4"))) && fecha_1("form-buy-c4") && fecha_2("form-buy-c4"))
                        filtrar = true;
                }
                else if(tiempo("form-buy-c4")=="Año"){
                    ayo("form-buy-c4") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //ACOMODO EL TEXTO DEL FILTRO
                $("#singlechart-title-c4").text("TOP PROVEEDORES - CALIDAD ("+producto_nombre.toUpperCase()+")");
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(tiempo("form-buy-c4"),fecha_1("form-buy-c4"),fecha_2("form-buy-c4"),ayo("form-buy-c4"),producto);
                //INFORMACIÓN DEL CANVA
                var tipo = "singlebarchart";
                var height = $("#canva-indicator-c4").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c4","singlechartc4","singlechart-spinner",height);
                var BarColors1 = new Array(); BarColors1.push("rgba(29,163,101,0.7)","rgba(25,61,47,0.8)");
                var BarColors2 = new Array(); BarColors2.push("","");
                ajaxFilterCharts("c4",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

//INDICADORES DE CLIENTES
    $("#submit-form-client-c1").one('click',function(event){
        $("#form-client-c1").on('submit',function(){
            var filtrar = false;

            if(tiempo("form-client-c1")){
                if(tiempo("form-client-c1")=="Específico"){
                    if(!(Date.parse(fecha_2("form-client-c1")) < Date.parse(fecha_1("form-client-c1"))) && fecha_1("form-client-c1") && fecha_2("form-client-c1"))
                        filtrar = true;
                }
                else if(tiempo("form-client-c1")=="Año"){
                    ayo("form-client-c1") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-client-c1"),tiempo("form-client-c1"),fecha_1("form-client-c1"),fecha_2("form-client-c1"),ayo("form-client-c1"));
                //INFORMACIÓN DEL CANVA
                var tipo = "singlebarchart";
                var height = $("#canva-indicator-c1").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c1","singlechartc1","singlechart-spinner",height);
                var BarColors1 = new Array(); BarColors1.push("rgba(29,163,101,0.7)","rgba(25,61,47,0.8)");
                var BarColors2 = new Array(); BarColors2.push("","");
                ajaxFilterCharts("c1",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

    $("#submit-form-client-c3").one('click',function(event){
        $("#form-client-c3").on('submit',function(){
            var filtrar = false;
            var cliente = $('#form-client-c3 select[id="form-client"] option:selected').val();
            var cliente_nombre = $('#form-client-c3 select[id="form-client"] option:selected').text();

            if(periodo("form-client-c3") && tiempo("form-client-c3") && cliente){
                if(tiempo("form-client-c3")=="Específico"){
                    if(!(Date.parse(fecha_2("form-client-c3")) < Date.parse(fecha_1("form-client-c3"))) && fecha_1("form-client-c3") && fecha_2("form-client-c3"))
                        filtrar = true;
                }
                else if(tiempo("form-client-c3")=="Año"){
                    ayo("form-client-c3") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //ACOMODO EL TEXTO DEL FILTRO
                $("#piechart-title-c3").text("TOP PROVEEDORES - CALIDAD ("+cliente_nombre.toUpperCase()+")");
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-client-c3"),tiempo("form-client-c3"),fecha_1("form-client-c3"),fecha_2("form-client-c3"),ayo("form-client-c3"),cliente);
                //INFORMACIÓN DEL CANVA
                var tipo = "piechart";
                var height = $("#canva-indicator-c3").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c3","piechartc3","piechart-spinner",height);
                var BarColors1 = new Array(); 
                BarColors1.push("rgba(255,48,23,0.15)", "rgba(255,48,23,0.20)", "rgba(255,48,23,0.25)","rgba(255,48,23,0.30)",
                                "rgba(255,48,23,0.35)", "rgba(255,48,23,0.40)", "rgba(255,48,23,0.45)", "rgba(255,48,23,0.50)",
                                "rgba(255,48,23,0.55)", "rgba(255,48,23,0.60)", "rgba(255,48,23,0.65)", "rgba(255,48,23,0.70)");
                var BgColor = "rgba(128,23,11,0.8)";
                ajaxFilterCharts("c3",urlFilter,CanvaInfo,BarColors1,BgColor,tipo,busqueda);
            }
            return false;
        });
    });

//INDICADORES DE FINANZAS
    $("#submit-form-finance-c1").one('click',function(event){
        $("#form-finance-c1").on('submit',function(){
            var filtrar = false;
            if(periodo("form-finance-c1") && tiempo("form-finance-c1")){
                if(tiempo("form-finance-c1")=="Específico"){
                    if(!(Date.parse(fecha_2("form-finance-c1")) < Date.parse(fecha_1("form-finance-c1"))) && fecha_1("form-finance-c1") && fecha_2("form-finance-c1"))
                        filtrar = true;
                }
                else if(tiempo("form-finance-c1")=="Año"){
                    ayo("form-finance-c1") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-finance-c1"),tiempo("form-finance-c1"),fecha_1("form-finance-c1"),fecha_2("form-finance-c1"),ayo("form-finance-c1"));
                //INFORMACIÓN DEL CANVA
                var tipo = "mixedbarline";
                var height = $("#canva-indicator-c1").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT - TIPO
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c1","mixedlinebarchartc1","mixedlinebarchart-spinner",height,"porcentaje");
                var BarColors1 = new Array(); BarColors1.push("rgba(255,129,0,0.7)","rgba(128,66,5,0.8)");
                var BarColors2 = new Array(); BarColors2.push("rgba(29,163,101,0.7)","rgba(25,61,47,0.8)");
                ajaxFilterCharts("c1",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

    $("#submit-form-finance-c2").one('click',function(event){
        $("#form-finance-c2").on('submit',function(){
            var filtrar = false;
            if(periodo("form-finance-c2") && tiempo("form-finance-c2")){
                if(tiempo("form-finance-c2")=="Específico"){
                    if(!(Date.parse(fecha_2("form-finance-c2")) < Date.parse(fecha_1("form-finance-c2"))) && fecha_1("form-finance-c2") && fecha_2("form-finance-c2"))
                        filtrar = true;
                }
                else if(tiempo("form-finance-c2")=="Año"){
                    ayo("form-finance-c2") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-finance-c2"),tiempo("form-finance-c2"),fecha_1("form-finance-c2"),fecha_2("form-finance-c2"),ayo("form-finance-c2"));
                //INFORMACIÓN DEL CANVA
                var tipo = "singlelinechart";
                var height = $("#canva-indicator-c2").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c2","singlelinechartc2","singlelinechart-spinner",height);
                var BarColors1 = new Array(); BarColors1.push("rgba(255,48,23,0.7)","rgba(128,23,11,0.8)");
                var BarColors2 = new Array(); BarColors2.push("","");
                ajaxFilterCharts("c2",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

    $("#submit-form-finance-c3").one('click',function(event){
        $("#form-finance-c3").on('submit',function(){
            var filtrar = false;
            if(periodo("form-finance-c3") && tiempo("form-finance-c3")){
                if(tiempo("form-finance-c3")=="Específico"){
                    if(!(Date.parse(fecha_2("form-finance-c3")) < Date.parse(fecha_1("form-finance-c3"))) && fecha_1("form-finance-c3") && fecha_2("form-finance-c3"))
                        filtrar = true;
                }
                else if(tiempo("form-finance-c3")=="Año"){
                    ayo("form-finance-c3") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-finance-c3"),tiempo("form-finance-c3"),fecha_1("form-finance-c3"),fecha_2("form-finance-c3"),ayo("form-finance-c3"));
                //INFORMACIÓN DEL CANVA
                var tipo = "singlelinechart";
                var height = $("#canva-indicator-c3").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER - HEIGHT
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c3","singlelinechartc3","singlelinechart-spinner",height);
                var BarColors1 = new Array(); BarColors1.push("rgba(255,129,0,0.7)","rgba(128,66,5,0.8)");
                var BarColors2 = new Array(); BarColors2.push("","");
                ajaxFilterCharts("c3",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });

//INDICADORES DE HOME
    $("#submit-form-home-c1").one('click',function(event){
        $("#form-home-c1").on('submit',function(){
            var filtrar = false;
            if(periodo("form-home-c1") && tiempo("form-home-c1")){
                if(tiempo("form-home-c1")=="Específico"){
                    if(!(Date.parse(fecha_2("form-home-c1")) < Date.parse(fecha_1("form-home-c1"))) && fecha_1("form-home-c1") && fecha_2("form-home-c1"))
                        filtrar = true;
                }
                else if(tiempo("form-home-c1")=="Año"){
                    ayo("form-home-c1") ? filtrar = true : "";
                }
                else{
                    filtrar = true;
                }
            }

            if(filtrar){
                //BUSQUEDA PARA FILTRAR
                var busqueda = new Array(); 
                busqueda.push(periodo("form-home-c1"),tiempo("form-home-c1"),fecha_1("form-home-c1"),fecha_2("form-home-c1"),ayo("form-home-c1"));
                //INFORMACIÓN DEL CANVA
                var tipo = "doublebarschart";
                var height = $("#canva-indicator-c1").attr("height");
                //DIV QUE ENGLOBA TODO - NOMBRE DEL CANVA - NOMBRE DEL DIV DEL SPINNER
                var CanvaInfo = new Array(); CanvaInfo.push("canva-indicator-c1","doublechartc1","doublechart-spinner",height);
                var BarColors1 = new Array(); BarColors1.push("rgba(29,163,101,0.7)","rgba(25,61,47,0.8)");
                var BarColors2 = new Array(); BarColors2.push("rgba(255,129,0,0.7)","rgba(128,66,5,0.8)");
                ajaxFilterCharts("c1",urlFilter,CanvaInfo,BarColors1,BarColors2,tipo,busqueda);
            }
            return false;
        });
    });