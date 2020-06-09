//var interval = setInterval(function(){ printChart(); }, 1000);

$(".doublechart-spinner").fadeOut( 1000, function() {
    $(".canvadbid").each(function(indice,elemento){
        var id = $(this).val();
        var labels = new Array();
        /* BAR 1 */
        var bar_1 = new Array();
        var label_1, bgColor_1, brColor_1;
        var data_1 = new Array();
        /* BAR 2 */
        var bar_2 = new Array(); 
        var label_2, bgColor_2, brColor_2;
        var data_2 = new Array();

        var unique = $(this).attr("name"); //AGARRO EL VALOR UNICO PARA QUE NO ME DUPLIQUE VALORES
        //AQUI YA RECOJO TODO LO QUE ME VIAJA DE VALORES PARA EL CHART
        /* GLOBAL VALUES */
        $(".canvadblabels"+unique).each(function(indice,elemento){
            labels.push($(this).val());
        });

        /* BAR 1 VALUES */
        $(".canvadbdata1"+unique).each(function(indice,elemento){
            data_1.push($(this).val());
        });
        $(".canvadblabel1"+unique).each(function(indice,elemento){
            label_1 = $(this).val();
        });
        $(".canvadbbgcolor1"+unique).each(function(indice,elemento){
            bgColor_1 = $(this).val();
        });
        $(".canvadbbdcolor1"+unique).each(function(indice,elemento){
            brColor_1 = $(this).val();
        });
        bar_1.push(data_1,label_1,bgColor_1,brColor_1);

        /* BAR 2 VALUES */
        $(".canvadbdata2"+unique).each(function(indice,elemento){
            data_2.push($(this).val());
        });
        $(".canvadblabel2"+unique).each(function(indice,elemento){
            label_2 = $(this).val();
        });
        $(".canvadbbgcolor2"+unique).each(function(indice,elemento){
            bgColor_2 = $(this).val();
        });
        $(".canvadbbdcolor2"+unique).each(function(indice,elemento){
            brColor_2 = $(this).val();
        });
        bar_2.push(data_2,label_2,bgColor_2,brColor_2);

        printDoubleBarChart(id,labels,bar_1,bar_2);
    });
    var alto = $('#dashboard-cv').height(); $('#dashboard-agenda').css('max-height', alto);
});

var printDoubleBarChart = (e, labels, bar_1, bar_2) => {
    //clearInterval(interval);
    var canvas = document.getElementById(e);
    Chart.defaults.global.defaultFontSize = 15;

    //ACOMODANDO LOS DATOS PARA EL CHART
    //DATO GLOBAL
    var xAsysLabel = new Array();
    labels.forEach(function (elemento) {
        xAsysLabel.push(elemento);
    });

    console.log(bar_1);
    console.log(bar_2);

    //DATOS BAR 1
    var DataBar_1 = new Array();
    bar_1[0].forEach(function (elemento) {
        DataBar_1.push(elemento);
    });

    //DATOS BAR 2
    var DataBar_2 = new Array();
    bar_2[0].forEach(function (elemento) {
        DataBar_2.push(elemento);
    });

    var Dato1 = {
        label: bar_1[1],//'Label Inferior sobre esta barra',
        data: DataBar_1,
        backgroundColor: bar_1[2],
        borderColor: bar_1[3],
        borderWidth: 2,
        //yAxisID: "y-axis-label1",
        borderSkipped: "left",
        hoverBorderWidth: 3,
    };

    var Dato2 = {
        label: bar_2[1],//'Label Inferior sobre esta barra',
        data: DataBar_2,
        backgroundColor: bar_2[2],
        borderColor: bar_2[3],
        borderWidth: 2,
        //yAxisID: "y-axis-label2",
        borderSkipped: "left",
        hoverBorderWidth: 3,
    };

    var chartData = {
        labels: xAsysLabel,
        datasets: [Dato1, Dato2]
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
            /* yAxes: [{
                id: "y-axis-label1", //link los datos de un dataset
                ticks: {
                    backdropColor: 'rgba(255, 255, 255, 1)',
                    display: true, //muestra los valores en el eje
                },
                position: "left"
            }, {
                id: "y-axis-label2", //link los datos de un dataset
                ticks: {
                    backdropColor: 'rgba(255, 255, 255, 1)',
                    display: true, //muestra los valores en el eje
                },
                position: "right"
            }] */
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

    var barChart = new Chart(canvas, {
        type: 'bar',
        data: chartData,
        options: chartOptions
    });

}