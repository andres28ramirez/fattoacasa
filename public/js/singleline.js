//var interval = setInterval(function(){ printChart(); }, 1000);

$(".singlelinechart-spinner").fadeOut( 1000, function() {
    $(".canvaslid").each(function(indice,elemento){
        var id = $(this).val();
        var data = new Array(); 
        var labels = new Array();
        var type, bgColor, brColor;

        var unique = $(this).attr("name"); //AGARRO EL VALOR UNICO PARA QUE NO ME DUPLIQUE VALORES
        //AQUI YA RECOJO TODO LO QUE ME VIAJA DE VALORES PARA EL CHART
        $(".canvasldata"+unique).each(function(indice,elemento){
            data.push($(this).val());
        });
        $(".canvasllabels"+unique).each(function(indice,elemento){
            labels.push($(this).val());
        });
        $(".canvaslbgcolors"+unique).each(function(indice,elemento){
            bgColor = $(this).val();
        });
        $(".canvaslbdcolors"+unique).each(function(indice,elemento){
            brColor = $(this).val();
        });
        printSingleLineChart(id,data,labels,type,bgColor,brColor);

        var alto = $('#canva-indicator-'+unique).height(); 
        $('#indicator-top-'+unique).css('max-height', alto);
    });
});

var printSingleLineChart = (e,data,labels,type,colorBg,colorBr) => {
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
    }
    //FIN ACOMODANDO LOS DATOS PARA EL CHART

    var Dato1 = {
        data: DataChart,
        lineTension: 0, //es la curvatura que tiene la recta
        backgroundColor: colorBg, //background por debajo de la linea
        borderColor: colorBr, //color del borde o como tal de la linea
        borderDash: [5, 5], //pone en guinoes o linea recta pues la linea
        fill: false, //como llenar el area bajo la linea (true) y si pasa tiene start, end, origin o por numero n
        borderWidth: 4, //ancho de la linea
        //showLine false no muestra la linea
        spanGaps: false, //false corta la linea si hay un valor en cero o NaN si es true no le para
        pointBorderColor: colorBr, //bordercolor del punto
        pointBackgroundColor: colorBg, //backgroundcolor dle punto
        pointRadius: 5, //radio o tamaño del punto
        pointHoverRadius: 10, //tamaño del punto al hacerle hover
        pointHitRadius: 30, //tamaño del mismo al darle click
        pointBorderWidth: 2, //ancho del punto
        pointStyle: 'circle' //circle, cross, dash, line, rect, rectrounded, star y triangle
        //xAxisID y yAxisID si usamos lineas multiples
        //ESTILOS DE HOVER EN GENERAL
        //hoverBackgroundColor
        //hoverBorderColor
        //hoverBorderDash number[]
        //hoverBorderWidth
        //pointHoverBackgroundColor
        //pointHoverBorderColor
        //pointHoverBorderWidth
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

    var lineChart = new Chart(canvas, {
        type: 'line',
        data: chartData,
        options: chartOptions
    });

}