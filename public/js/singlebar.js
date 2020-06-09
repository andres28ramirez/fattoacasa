//var interval = setInterval(function(){ printChart(); }, 1000);

$(".singlechart-spinner").fadeOut( 1000, function() {
    $(".canvasbid").each(function(indice,elemento){
        var id = $(this).val();
        var data = new Array(); 
        var labels = new Array();
        var type, bgColor, brColor;

        var unique = $(this).attr("name"); //AGARRO EL VALOR UNICO PARA QUE NO ME DUPLIQUE VALORES
        //AQUI YA RECOJO TODO LO QUE ME VIAJA DE VALORES PARA EL CHART
        $(".canvasbdata"+unique).each(function(indice,elemento){
            data.push($(this).val());
        });
        $(".canvasblabels"+unique).each(function(indice,elemento){
            labels.push($(this).val());
        });
        $(".canvatypesbid"+unique).each(function(indice,elemento){
            type = $(this).val();
        });
        $(".canvasbbgcolors"+unique).each(function(indice,elemento){
            bgColor = $(this).val();
        });
        $(".canvasbbdcolors"+unique).each(function(indice,elemento){
            brColor = $(this).val();
        });
        if(type=="top")
            $("#"+id).attr('height', 100);
        printSingleBarChart(id,data,labels,type,bgColor,brColor);

        
    });
    var alto = $('#dashboard-cv').height(); $('#dashboard-agenda').css('max-height', alto);
});

var printSingleBarChart = (e,data,labels,type,colorBg,colorBr) => {
    //clearInterval(interval);
    var canvas = document.getElementById(e);
    Chart.defaults.global.defaultFontSize = 15;
    
    //ACOMODANDO LOS DATOS PARA EL CHART
    var DataChart = new Array();
    var xAsysLabel = new Array();
    var maxed, step, style;

    labels.forEach(function (elemento) {
        xAsysLabel.push(elemento);
    });

    data.forEach(function (elemento) {
        DataChart.push(elemento);
    });

    if(type=="normal"){
        maxed = Math.ceil(Math.max(...DataChart));
        step = "";
        style = "";
    }else if (type=="porcentaje"){
        maxed = 1;
        step = 0.20;
        style = {style:'percent'};
    }else{
        maxed = Math.ceil(Math.max(...DataChart));
        step = "";
        style = "";
    }
    //FIN ACOMODANDO LOS DATOS PARA EL CHART

    var Dato1 = {
        data: DataChart,
        backgroundColor: colorBg,
        borderColor: colorBr,
        borderWidth: 2,
        hoverBorderWidth: 3,
    };

    var chartData = {
        labels: xAsysLabel,
        datasets: [Dato1]
    };

    var chartOptions = {
        scales: {
            xAxes: [{
                barPercentage: 1,
                categoryPercentage: 0.7,
                //position: "top" o "right" o "left" o "bottom"
                ticks: {
                    //beginAtZero: true,
                    //min: respecto a los labels de chartData / The minimum item to display
                    //max: respecto a los labels de chartData / The maximum item to display
                    //stepSize: n settea la escala de cuanto en cuanto los labels
                    //fontColor:
                    //fontFamily:
                    //fontSize:
                    //fontStyle:
                },
                gridLines: {
                    display: true,
                    drawTicks: true,
                    drawOnChartArea: false,
                    backdropColor: 'rgba(255, 255, 255, 1)',
                    backdropPaddingX: 2,
                    backdropPaddingY: 2,
                }
            }],
            yAxes: [{
                display: true,
                barPercentage: 1,
                categoryPercentage: 0.8,
                ticks: {
                   display: true,
                   padding: 10,
                   fontStyle: "normal",
                   max: maxed,
                   stepSize: step,
                   callback: function (value) {
                        return value.toLocaleString('de-DE', style);
                    },
                },
                gridLines: {
                   display: true,
                   drawTicks: true,
                   drawOnChartArea: true,
                },
                stacked: true,
             }],
        },
        legend: {
            display: false,
            position: "bottom",
            align: "center",
            rtl: false, //muestra el label a la izquierda si esta true
            label:{
                //usePointStyle: true
                //fontSize: n
                //fontFamily: ""
                //fontStyle: ""
                //fontColor: ""
                //boxWidth: n
                //padding: n
            }
        },
        animation: {
            animateRotate: true,
            animateScale: true
        },
        responsive: true,
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0
            }
        }
    };

    var barChart = new Chart(canvas, {
        type: 'bar',
        data: chartData,
        options: chartOptions
    });

}