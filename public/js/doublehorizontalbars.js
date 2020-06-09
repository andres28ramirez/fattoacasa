//var interval = setInterval(function(){ printChart(); }, 1000);
$(".horizontalchart-spinner").fadeOut( 1000, function() {
   $(".canvadhbid").each(function(indice,elemento){
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
      $(".canvahdblabels"+unique).each(function(indice,elemento){
         labels.push($(this).val());
      });

      /* BAR 1 VALUES */
      $(".canvahdbdata1"+unique).each(function(indice,elemento){
         data_1.push($(this).val());
      });
      $(".canvahdblabel1"+unique).each(function(indice,elemento){
         label_1 = $(this).val();
      });
      $(".canvahdbbgcolor1"+unique).each(function(indice,elemento){
         bgColor_1 = $(this).val();
      });
      $(".canvahdbbdcolor1"+unique).each(function(indice,elemento){
         brColor_1 = $(this).val();
      });
      bar_1.push(data_1,label_1,bgColor_1,brColor_1);

      /* BAR 2 VALUES */
      $(".canvahdbdata2"+unique).each(function(indice,elemento){
         data_2.push($(this).val());
      });
      $(".canvahdblabel2"+unique).each(function(indice,elemento){
         label_2 = $(this).val();
      });
      $(".canvahdbbgcolor2"+unique).each(function(indice,elemento){
         bgColor_2 = $(this).val();
      });
      $(".canvahdbbdcolor2"+unique).each(function(indice,elemento){
         brColor_2 = $(this).val();
      });
      bar_2.push(data_2,label_2,bgColor_2,brColor_2);

      printHorizontalDoubleBarChart(id,labels,bar_1,bar_2);
   });

   var alto = $('#indicator-es').height(); $('#dashboard-agenda').css('max-height', alto);
});

var printHorizontalDoubleBarChart = (e, chart_labels, bar_1, bar_2) => {
    //clearInterval(interval);
   var canvas = document.getElementById(e);
   Chart.defaults.global.defaultFontSize = 15;
   
   //ACOMODANDO LOS DATOS PARA EL CHART
   //DATO GLOBAL
   var xAsysLabel = new Array();
   chart_labels.forEach(function (elemento) {
      xAsysLabel.push(elemento);
   });

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

   var chartOptions = {
      layout: {
         padding: {
            left: 5,
            right: 5,
            top: 0,
            bottom: 0
         }
      },
      scales: {
         yAxes: [{
            display: true,
            barPercentage: 1,
            categoryPercentage: 0.8,
            ticks: {
               display: true,
               padding: 10,
               fontStyle: "bold",
            },
            gridLines: {
               display: false,
               drawTicks: true,
               drawOnChartArea: false,
            },
            stacked: true,
         }],
         xAxes: [{
            stacked: true,
            barPercentage: 1,
            categoryPercentage: 0.7,
            ticks: {
               callback: function(t, i) {
                  return t < 0 ? Math.abs(t) : t;
               },
               padding: 4, //padding entre el valor del asys y el grafico
               fontStyle: "normal",
            },
            gridLines: {
               display: true,
               drawTicks: true,
               drawOnChartArea: true,
            },
            display: true,
         }]
      },
      tooltips: {
         callbacks: {
            label: function(t, d) {
               var datasetLabel = d.datasets[t.datasetIndex].label;
               var xLabel = Math.abs(t.xLabel);
               return datasetLabel + ': ' + xLabel;
            }
         }
      },
      responsive: true,
      //maintainAspectRatio: false,
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
         animateScale: true,
         animateRotate: true
      },
   };

   var Salidas = {
      label: bar_1[1],
      data: DataBar_1,
      backgroundColor: bar_1[2],
      borderColor: bar_1[3],
      borderWidth: 2,
      hoverBorderWidth: 3,
   };

   var Entradas = {
      label: bar_2[1],
      data: DataBar_2,
      backgroundColor: bar_2[2],
      borderColor: bar_2[3],
      borderWidth: 2,
      hoverBorderWidth: 3,
   };

   var chartData = {
      labels: xAsysLabel,
      datasets: [Salidas, Entradas],
   };

   var DoubleHorizontalCHart = new Chart(canvas, {
      type: 'horizontalBar',
      data: chartData,
      options: chartOptions
   });
}