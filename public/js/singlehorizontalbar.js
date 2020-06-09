//var interval = setInterval(function(){ printChart(); }, 1000);

$(".singlehorizontalchart-spinner").fadeOut( 1000, function() {
    $(".canvashbid").each(function(indice,elemento){
        var id = $(this).val();
        var data = new Array(); 
        var labels = new Array();
        var type, bgColor, brColor;

        var unique = $(this).attr("name"); //AGARRO EL VALOR UNICO PARA QUE NO ME DUPLIQUE VALORES
        //AQUI YA RECOJO TODO LO QUE ME VIAJA DE VALORES PARA EL CHART
        $(".canvashbdata"+unique).each(function(indice,elemento){
            data.push($(this).val());
        });
        $(".canvashblabels"+unique).each(function(indice,elemento){
            labels.push($(this).val());
        });
        $(".canvatypeshbid"+unique).each(function(indice,elemento){
            type = $(this).val();
        });
        $(".canvashbbgcolors"+unique).each(function(indice,elemento){
            bgColor = $(this).val();
        });
        $(".canvashbbdcolors"+unique).each(function(indice,elemento){
            brColor = $(this).val();
        });
        printSingleHorizontalBarChart(id,data,labels,type,bgColor,brColor);
    });
});

function printSingleHorizontalBarChart(e,data,labels,type,colorBg,colorBr){
    //clearInterval(interval);
    var canvas = document.getElementById(e);
    Chart.defaults.global.defaultFontSize = 15;
    
    //ACOMODANDO LOS DATOS PARA EL CHART
    var DataChart = new Array();
    var xAsysLabel = new Array();

    labels.forEach(function (elemento) {
        xAsysLabel.push(elemento);
    });

    data.forEach(function (elemento) {
        DataChart.push(elemento);
    });

    if(type=="normal"){
        maxed = Math.max(...DataChart) + 0.5;
        step = "";
        style = "";
    }else if (type=="porcentaje"){
        maxed = 1;
        step = 0.20;
        style = {style:'percent'};
    }

    var Datos = {
        data: DataChart,
        backgroundColor: colorBg,
        borderColor: colorBr,
        borderWidth: 2,
        borderSkipped: "left",
        hoverBorderWidth: 3,
    };

    var chartData = {
        labels: xAsysLabel,
        datasets: [Datos]
    };

    var chartOptions = {
        //maintainAspectRatio: false,
        responsive: true,
        scales: {
            xAxes: [{
                barPercentage: 1,
                categoryPercentage: 0.7,
                ticks: {
                    beginAtZero: true,
                    backdropColor: 'rgba(255, 255, 255, 1)',
                    backdropPaddingX: 2,
                    backdropPaddingY: 2,
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
                display: true,
            }],
            yAxes: [
                {
                    position: 'right',
                    //offset: true,
                    barPercentage: 1,
                    categoryPercentage: 0.8,
                    //position: "top" o "right" o "left" o "bottom"
                    ticks: {
                        beginAtZero: true,
                        //min: respecto a los labels de chartData / The minimum item to display
                        //max: respecto a los labels de chartData / The maximum item to display
                        //stepSize: n settea la escala de cuanto en cuanto los labels
                        //fontColor:
                        //fontFamily:
                        //fontSize:
                        //fontStyle:
                        //maxTicksLimit: 12
                        padding: 4, //padding entre el valor del asys y el grafico
                        fontStyle: "bold",
                    },
                    gridLines: {
                        display: true,
                        drawTicks: false,
                        drawOnChartArea: false,
                    }
                }
            ]
        },
        legend: {
            display: false
        }
    };

    var horizontalChart = new Chart(canvas, {
        type: 'horizontalBar',
        data: chartData,
        options: chartOptions
    });
}