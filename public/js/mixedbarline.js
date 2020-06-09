//var interval = setInterval(function(){ printChart(); }, 1000);

$(".mixedlinebarchart-spinner").fadeOut( 1000, function() {
    $(".canvamlbid").each(function(indice,elemento){
        var id = $(this).val();
        var labels = new Array();
        var type;
        /* BAR */
        var bar = new Array();
        var label_bar, bgColor_bar, brColor_bar;
        var data_bar = new Array();
        /* LINE */
        var line = new Array(); 
        var label_line, bgColor_line, brColor_line;
        var data_line = new Array();

        var unique = $(this).attr("name"); //AGARRO EL VALOR UNICO PARA QUE NO ME DUPLIQUE VALORES
        //AQUI YA RECOJO TODO LO QUE ME VIAJA DE VALORES PARA EL CHART
        /* GLOBAL VALUES */
        $(".canvamlblabels"+unique).each(function(indice,elemento){
            labels.push($(this).val());
        });
        $(".canvatypemlbid"+unique).each(function(indice,elemento){
            type = $(this).val();
        });

        /* BAR 1 VALUES */
        $(".canvamlbdata1"+unique).each(function(indice,elemento){
            data_bar.push($(this).val());
        });
        $(".canvamlblabel1"+unique).each(function(indice,elemento){
            label_bar = $(this).val();
        });
        $(".canvamlbbgcolor1"+unique).each(function(indice,elemento){
            bgColor_bar = $(this).val();
        });
        $(".canvamlbbdcolor1"+unique).each(function(indice,elemento){
            brColor_bar = $(this).val();
        });
        bar.push(data_bar,label_bar,bgColor_bar,brColor_bar);

        /* BAR 2 VALUES */
        $(".canvamlbdata2"+unique).each(function(indice,elemento){
            data_line.push($(this).val());
        });
        $(".canvamlblabel2"+unique).each(function(indice,elemento){
            label_line = $(this).val();
        });
        $(".canvamlbbgcolor2"+unique).each(function(indice,elemento){
            bgColor_line = $(this).val();
        });
        $(".canvamlbbdcolor2"+unique).each(function(indice,elemento){
            brColor_line = $(this).val();
        });
        line.push(data_line,label_line,bgColor_line,brColor_line);

        printMixedLineBarChart(id,type,labels,bar,line);
    });
});

var printMixedLineBarChart = (e, type, labels, bar, line) => {
    //clearInterval(interval);
    var canvas = document.getElementById(e);
    Chart.defaults.global.defaultFontSize = 15;

    //ACOMODANDO LOS DATOS PARA EL CHART
    //DATO GLOBAL
    var xAsysLabel = new Array();
    labels.forEach(function (elemento) {
        xAsysLabel.push(elemento);
    });

    //DATOS BAR
    var DataBar = new Array();
    bar[0].forEach(function (elemento) {
        DataBar.push(elemento);
    });

    //DATOS LINE
    var DataLine = new Array();
    line[0].forEach(function (elemento) {
        DataLine.push(elemento);
    });

    if(type=="normal"){
        maxed = Math.ceil(Math.max(...DataLine));
        step = "";
        style = "";
        daxys = false;
        Bar_Axis = "";
        Line_Axis = "";
    }else if (type=="porcentaje"){
        maxed = 1;
        step = 0.20;
        style = {style:'percent'};
        daxys = true;
        Bar_Axis = "y-axis-label1";
        Line_Axis = "y-axis-label2";
    }

    var Dato1 = {
        label: bar[1],//'Label Inferior sobre esta barra',
        data: DataBar,
        backgroundColor: bar[2],
        borderColor: bar[3],
        borderWidth: 2,
        yAxisID: Bar_Axis,
        borderSkipped: "bottom",
        hoverBorderWidth: 3,
    };

    var Dato2 = {
        label: line[1],//'Label Inferior sobre esta barra',
        data: DataLine,
        backgroundColor: line[2],
        borderColor: line[3],
        borderWidth: 2,
        yAxisID: Line_Axis,
        borderSkipped: "left",
        hoverBorderWidth: 3,
        type: "line",
        fill: false,
        lineTension: 0,
        borderDash: [5, 5],
        borderWidth: 4,
        spanGaps: false,
        pointBorderColor: line[3],
        pointBackgroundColor: line[2],
        pointRadius: 5,
        pointHoverRadius: 10,
        pointHitRadius: 30,
        pointBorderWidth: 2,
        pointStyle: 'circle'
    };

    var chartData = {
        labels: xAsysLabel,
        datasets: [Dato2, Dato1]
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
                id: "y-axis-label1", //link los datos de un dataset
                ticks: {
                    backdropColor: 'rgba(255, 255, 255, 1)',
                    display: true, //muestra los valores en el eje
                    beginAtZero: true,
                },
                gridLines: {
                    display: true,
                    drawTicks: true,
                    drawOnChartArea: true,
                 },
                position: "left"
            }, {
                id: "y-axis-label2", //link los datos de un dataset
                ticks: {
                    backdropColor: 'rgba(255, 255, 255, 1)',
                    display: daxys, //muestra los valores en el eje
                    beginAtZero: true,
                    callback: function (value) {
                        return value.toLocaleString('de-DE', style);
                    },
                },
                gridLines: {
                   display: daxys,
                   drawTicks: daxys,
                   drawOnChartArea: false,
                },
                stacked: true,
                position: "right",
            }]
        },
        legend: {
            display: true,
            labels:{
            fontColor: "#333333",
            fontStyle: "normal"
            },
            position: "bottom",
            align: "center", //start o end
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

    var mixedChart = new Chart(canvas, {
        type: 'bar',
        data: chartData,
        options: chartOptions
    });

}