<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">   
    <title>Document</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="SHORTCUT ICON" href="{{ asset('img/logo.png') }}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

<style>
    @font-face {
        font-family: Poppins-Regular;
        src: url('../font/poppins/Poppins-Regular.ttf'); 
    }
    
    @font-face {
        font-family: Poppins-Medium;
        src: url('../font/poppins/Poppins-Medium.ttf'); 
    }
    
    @font-face {
        font-family: Poppins-Bold;
        src: url('../font/poppins/Poppins-Bold.ttf'); 
    }
    
    @font-face {
        font-family: Poppins-SemiBold;
        src: url('../font/poppins/Poppins-SemiBold.ttf'); 
    }

    .clearfix:after {
        content: "";
        display: table;
        clear: both;
    }

    a {
        color: #5D6975;
        text-decoration: underline;
    }

    body {
        position: relative;
        margin: 0 auto; 
        color: #001028;
        background: #FFFFFF; 
        font-family: Arial, sans-serif; 
        font-size: 16px; 
        font-family: Arial;
    }

    header {
        padding: 10px 0;
        margin-bottom: 30px;
    }

    #logo {
        margin-bottom: 20px;
        padding-bottom: 20px;
    }

    #logo img {
        height: 90px;
        width: 90px;
        float: left;
    }

    #project {
        float: left;
    }

    #project span {
        color: #5D6975;
        text-align: right;
        width: 52px;
        margin-right: 10px;
        display: inline-block;
        font-size: 0.8em;
    }

    #company {
        height: 90px;
        float: left;
        padding-top: 10px;
        padding-left: 10px;
    }

    #project div,
    #company div {
        white-space: nowrap;        
    }

    h1 {
        /*border-top: 1px solid  #495057;
        border-bottom: 1px solid  #495057;*/
        color: #495057;
        font-size: 2.4em;
        line-height: 1.4em;
        margin-top: 20px;
        font-weight: normal;
        font-style: bold;
        text-align: center;
        margin: 0 0 -20px 0;
        /*background-color: #f9f6f1;*/
    }

  
    .presentation{
        padding-left:10px;
        font-weight: bold;
    }
    #notices .notice {
        color: #5D6975;
        font-size: 1.2em;
        padding-bottom: 10px;
        text-align: center;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }

    footer {
        color: #5D6975;
        width: 100%;
        height: 30px;
        position: absolute;
        bottom: 0;
        border-top: 1px solid #C1CED9;
        padding: 8px 0;
        text-align: center;
    }
</style>

</head>
<body>
    <header class="clearfix">
        <div id="logo">
            <!--<img src="{{ asset('img/logo.png') }}">-->
            <div id="company">
                <div>Fatto a Casa C.A</div>
                <div>Calle Tamare, quinta Lina el Marques<br /> Caracas, Venezuela</div>
                <div>+58-212-237-7847</div>
                <div><a href="mailto:Infofattoacasa@gmail.com">Infofattoacasa@gmail.com</a></div>
                <br>
            </div>
        </div>
        <h1 style="clear: both">Aviso de cuenta por pagar</h1>
    </header>
    <main>


        <div id="notices">
            <div class="presentation">La familia Fatto a Casa le extiende un cordial saludo, <br> Sr {{$venta->cliente->nombre}}</div>

            <div class="notice">Nos comunicamos con usted para informarle que el credito  de ({{$venta->credito}} días) <br>
                de su compra, realizada el {{$venta->fecha}} por el monto de {{$venta->monto}} Bs<br>
                esta por finalizar, por favor comuniquese con nosotros lo antes posible. <br>
                Saludos y Gracias de antemano.
            </div>
        </div>
    </main>
    
</body>
</html>