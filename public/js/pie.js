//var interval = setInterval(function(){ printChart(); }, 1000);

$(".piechart-spinner").fadeOut( 1000, function() {
    $(".canvapieid").each(function(indice,elemento){
        var id = $(this).val();
        var data = new Array(); 
        var labels = new Array();
        var bgColor = new Array(); 
        var brColor;

        var unique = $(this).attr("name"); //AGARRO EL VALOR UNICO PARA QUE NO ME DUPLIQUE VALORES
        //AQUI YA RECOJO TODO LO QUE ME VIAJA DE VALORES PARA EL CHART
        $(".canvapiedata"+unique).each(function(indice,elemento){
            data.push($(this).val());
        });
        $(".canvapielabels"+unique).each(function(indice,elemento){
            labels.push($(this).val());
        });
        $(".canvapiebgcolors"+unique).each(function(indice,elemento){
            bgColor.push($(this).val());
        });
        $(".canvapiebdcolors"+unique).each(function(indice,elemento){
            brColor = $(this).val();
        });
        printPieChart(id,data,labels,bgColor,brColor);
    });
});

var printPieChart = (e,data,labels,colorBg,colorBr) => {
    //clearInterval(interval);
    var canvas = document.getElementById(e);
    Chart.defaults.global.defaultFontSize = 15;
    
    //ACOMODANDO LOS DATOS PARA EL CHART
    var DataChart = new Array();
    var xAsysLabel = new Array();
    var bgColors = new Array();

    labels.forEach(function (elemento) {
        xAsysLabel.push(elemento);
    });

    data.forEach(function (elemento) {
        DataChart.push(elemento);
    });

    colorBg.forEach(function (elemento) {
        bgColors.push(elemento);
    });
    //FIN ACOMODANDO LOS DATOS PARA EL CHART

    var Dato1 = {
        data: DataChart,
        backgroundColor: bgColors,
        borderColor: colorBr,
        borderWidth: 2,
        hoverBorderWidth: 3,
        borderAlign: 'inner',
    };

    var chartData = {
        labels: xAsysLabel,
        datasets: [Dato1]
    };

    var chartOptions = {
        scales: {
            xAxes: [{
                display: false,
            }],
            yAxes: [{
                display: false,
             }],
        },
        title: {
            display: false,
            text: 'Compras Totales'
        },
        legend: {
            display: true,
            position: "right",
            align: "center",
            rtl: false, //muestra el label a la izquierda si esta true
            label:{
                usePointStyle: true,
                fontSize: 5,
                //fontFamily: ""
                //fontStyle: ""
                //fontColor: ""
                boxWidth: 1,
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

    var pieChart = new Chart(canvas, {
        type: 'pie',
        data: chartData,
        options: chartOptions
    });

}