$(".download-indicator").click(function() {
    var canva = $(this).attr("type");
    var titulo = $('#'+canva).parent().attr("title");
    var f = new Date();
    var fecha = f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear();
    var name = $('#'+canva).parent().attr("name");
    var usuario = $('#user-text-name').text();

    var logo = new Image();
    logo.src = "http://localhost/fatto-a-casa/public/img/logo.png";
    logo.onload;
    console.log(logo);

    // get size of report page
    var reportPageHeight = $('#'+canva).height();
    var reportPageWidth = $('#'+canva).width();

    // create a new canvas object that we will populate with all other canvas objects
    var pdfCanvas = $('<canvas />').attr({
        id: "canvaspdf",
        width: reportPageWidth,
        height: reportPageHeight
    });

    // keep track canvas position
    var pdfctx = $(pdfCanvas)[0].getContext('2d');
    var pdfctxX = 0;
    var pdfctxY = 0;
    var buffer = 100;

    // for each chart.js chart
    $("#"+canva).each(function(index) {
        // get the chart height/width
        var canvasHeight = $(this).height();
        var canvasWidth = $(this).width();

        // draw the chart into the new canvas
        pdfctx.drawImage($(this)[0], pdfctxX, pdfctxY, canvasWidth, canvasHeight);
        pdfctxX += canvasWidth + buffer;

        // our report page is in a grid pattern so replicate that in the new canvas
        if (index % 2 === 1) {
            pdfctxX = 0;
            pdfctxY += canvasHeight + buffer;
        }
    });

//INSTRUCTIONS
     
    {/*
        orientation: 'p' o 'l',   (portrait o landscape)
        unit: 'mm' o 'pt' o 'cm' o 'm' o 'in' o 'px',   (measurement unit)
        format: 'a4',  (formato de la pagina) a4 esta por default
        putOnlyUsedFonts:true, (Only put fonts into the PDF, which were used) o false o true
        floatPrecision: 16 // or "smart", default is 16 (no especifica)

        ##########################################################################################
        AGREGAR METODOS AL PDF
        addFont() -> agrega fuentes sus opciones son:
                    postScriptName	(PDF specification full name for the font.)
                    id	(PDF-document-instance-specific label assinged to the font.)
                    fontStyle	(Style of the Font.)
                    encoding	(tipo Object) su fuunción es = Encoding_name-to-Font_metrics_object mapping.
        
        addPage(format, orientation) → {jsPDF} -> lo mismo de arriba al settear el objeto PDF

        beginFormObject(x, y, width, height, matrix) → {jsPDF}
        [Starts a new pdf form object, which means that all consequent draw calls target a new independent object until 
        endFormObject is called. The created object can be referenced and drawn later using doFormObject. Nested form objects 
        are possible. x, y, width, height set the bounding box that is used to clip the content.]

        close() → {jsPDF} [Close the current path. The PDF "h" operator.]

        comment(text) → {jsPDF} [Inserts a debug comment into the generated pdf.]

        curveTo(x1, y1, x2, y2, x3, y3) → {jsPDF} 
        [Append a cubic Bézier curve to the current path. The curve shall extend from the current point to the point (x3, y3), 
            using (x1, y1) and (x2, y2) as Bézier control points. The new current point shall be (x3, x3).]
        
        deletePage(targetPage) → {jsPDF} -> targetPage	number of page

        doFormObject(key, matrix) → key	(The key to the form object) y matrix (The matrix applied before drawing the form object.)
        [Draws the specified form object by referencing to the respective pdf XObject created with API.beginFormObject and 
            endFormObject. The location is determined by matrix.]
        
        endFormObject(key) [Completes and saves the form object.] key => The key by which this form object can be referenced

        getCharSpace() → {number} Get global value of CharSpace

        getDrawColor() → {string} Gets the stroke color (HEX) for upcoming elements

        getFont() → {Object} Gets text font face, variant for upcoming text elements

        getFontList() → {Object} Returns an object - a tree of fontName to fontStyle relationships available to active PDF document

        getFontSize() → {number}

        getTextColor() → {string}

        insertPage(beforePage) → {jsPDF}

        line(x1, y1, x2, y2, style) → {jsPDF} = Draw a line on the current page.
        style -> 'S' [default] - stroke, 'F' - fill, and 'DF' (or 'FD')

        lineTo(x, y) → {jsPDF} Append a straight line segment from the current point to the point (x, y). The PDF "l" operator.

        lstext(text, x, y, spacing) → {jsPDF} Letter spacing method to print text with gaps

        output(type, options) → {jsPDF} Generates the PDF document.
        type = A string identifying one of the possible output types. Possible values are 'arraybuffer', 'blob', 'bloburi'/'bloburl', 
        'datauristring'/'dataurlstring', 'datauri'/'dataurl', 'dataurlnewwindow', 'pdfobjectnewwindow', 'pdfjsnewwindow'.
        options = An object providing some additional signalling to PDF generator. Possible options are 'filename'

        save(filename, options) → {jsPDF}
        Saves as PDF document. An alias of jsPDF.output('save', 'filename.pdf'). Uses FileSaver.js-method saveAs.

        setFont(fontName, fontStyle) → {jsPDF}

        setFontSize(size) → {jsPDF}

        setFontStyle(style) → {jsPDF}

        setLineWidth(width) → {jsPDF}

        setPage(page) → {jsPDF} Adds (and transfers the focus to) new page to the PDF document

        setTextColor(ch1, ch2, ch3, ch4) → {jsPDF} ch1 color value in hexadecimal, example: '#FFFFFF'.

        text(text, x, y, optionsopt, transform) → {jsPDF} Adds text to page
        options: align (left, center, right, justify)
                 baseline (alphabetic, ideographic, bottom, top, middle, hanging)
                 angle (Rotate the text clockwise or counterclockwise. Expects the angle in degree)
                 rotationDirection (0 = clockwise, 1 = counterclockwise)
                 charSpace (0...n)
                 lineHeightFactor (interlineado)
                 maxWidth (0..n)
        */
    }
     
//END INSTRUCTIONS

    // create new pdf and add our new canvas as an image
    //var pdf = new jsPDF('l', 'pt', [reportPageWidth, reportPageHeight]);
    //espaciado de un PDF 792 x 612
    var pdf = new jsPDF('p', 'px', [792 , 612]);
    pdf.addFont('ComicSansMS', 'Comic Sans', 'normal');
    pdf.setFont('Helvetica');
    pdf.setFontSize(12);

    //pdf.line(X1,Y1,X2,Y2);
    pdf.line(30,99,580,99);
    pdf.setFillColor(249,246,241);
    pdf.rect(30, 100, 550, 40, 'F');
    pdf.line(30,140,580,140);
    
    pdf.setFontSize(18);
    pdf.setTextColor(73, 80, 87);
    xOffset = (pdf.internal.pageSize.width / 2) - (pdf.getStringUnitWidth(titulo) * pdf.internal.getFontSize() / 2) + 40;
    pdf.text(titulo, xOffset, 125);
    //pdf.text(20, 152,"Título: "+titulo);

    pdf.setTextColor(0, 0, 0);
    pdf.setFontSize(12);
    pdf.fromHTML( 'Reporte Generadó por: <b>'+usuario+'</b>', 30, 150)
    pdf.fromHTML( 'Fecha de Emisión: <b>'+fecha+'</b>', 30, 165)
    //pdf.text(30, 160,"Reporte Generado por: "+usuario);
    //pdf.text(30, 175,"Fecha de Emisión: "+fecha);
    pdf.setFontSize(15);
    pdf.text(30, 210,"Gráfico del Indicador: ");
    pdf.setDrawColor(93, 105, 117);
    pdf.line(30,212,580,212);
    pdf.setFontSize(12);

    // APARTADO DEL LOGO JUNTO A LA INFORMACIÓN DE LA EMPRESA
        //addImage(imageData, format, x, y, width, height, alias, compression, rotation)
        pdf.addImage(logo, 'PNG', 30, 30, 60, 60);
        pdf.text(100, 40,"Fatto a Casa C.A");
        pdf.text(100, 52,"Calle Tamare, quinta Lina el Marques");
        pdf.text(100, 64,"Caracas, Venezuela");
        pdf.text(100, 76,"+58-212-237-7847");
        pdf.setTextColor(93, 105, 117);
        pdf.text(100, 88,"Infofattoacasa@gmail.com");
    //FIN APARTADO DEL LOGO JUNTO A LA INFORMACIÓN DE LA EMPRESA
    
    //FOOTER
        pdf.setDrawColor(93, 105, 117);
        pdf.line(30,750,580,750);
        var text = "Reporte generadó por computador y es inválido sin la firma y sellado del mismo.";
        xOffset = (pdf.internal.pageSize.width / 2) - (pdf.getStringUnitWidth(text) * pdf.internal.getFontSize() / 2) + 50;
        pdf.text(text, xOffset, 760);
    //ENDFOOTER

    pdf.addImage($(pdfCanvas)[0], 'PNG', 30, 230, 550, reportPageHeight);

    // download the pdf
    pdf.save(name+'.pdf');


    /*VERSION VIEJA
        // create new pdf and add our new canvas as an image
        //var pdf = new jsPDF('l', 'pt', [reportPageWidth, reportPageHeight]);
        //espaciado de un PDF 842 x 595
        var pdf = new jsPDF('l', 'px', [reportPageWidth, reportPageHeight]);
        pdf.addFont('ComicSansMS', 'Comic Sans', 'normal');
        pdf.setFont('Helvetica');
        pdf.setFontSize(15);
        pdf.text(10, 20,"Fecha de Emisión: "+fecha);
        pdf.text(10, 40,"Título: "+titulo);
        pdf.rect(0, 0, reportPageWidth, reportPageHeight, 'S');
        pdf.rect(0, 0, reportPageWidth, 50, 'S'); //CUADRADO PEQUEÑO DEL TITULO
        //pdf.line(25,55,reportPageWidth,55);
        
        pdf.addImage($(pdfCanvas)[0], 'PNG', reportPageWidth/4, reportPageHeight/4);

        // download the pdf
        pdf.save(name+'.pdf');
    */
});