<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Descargar Reporte</title>
    <link rel="SHORTCUT ICON" href="{{ asset('img/logo.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
</head>

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
        font-size: 12px; 
        font-family: Arial;
    }

    header {
        padding: 10px 0;
        margin-bottom: 30px;
    }

    #logo {
        /* text-align: center; */
        margin-bottom: 20px;
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
        vertical-align: middle;
    }

    #project div,
    #company div {
        white-space: nowrap;        
    }

    h1 {
        border-top: 1px solid  #495057;
        border-bottom: 1px solid  #495057;
        color: #495057;
        font-size: 2.4em;
        line-height: 1.4em;
        font-weight: normal;
        font-style: bold;
        text-align: center;
        margin: 0 0 -20px 0;
        background-color: #f9f6f1;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20px;
    }

    table tr:nth-child(2n-1) td {
        background: #F5F5F5;
    }

    table th,
    table td {
        text-align: center;
    }

    table th {
        padding: 5px 20px;
        color: #5D6975;
        border-bottom: 1px solid #C1CED9;
        white-space: nowrap;        
        font-weight: normal;
    }

    table .service,
    table .desc {
        text-align: left;
    }

    table td {
        padding: 20px;
        text-align: center;
    }

    table td.service,
    table td.desc {
        vertical-align: top;
    }

    table td.unit,
    table td.qty,
    table td.total {
        font-size: 1.2em;
    }

    table td.grand {
        border-top: 1px solid #5D6975;;
    }

    #notices .notice {
        color: #5D6975;
        font-size: 1.2em;
        padding-bottom: 10px;
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

<!-- LAYOUT DEL PDF -->
<body>
    <header class="clearfix">
        <div id="logo">
            <img src="{{ asset('img/logo.png') }}">
            <div id="company">
                <div>Fatto a Casa C.A</div>
                <div>Calle Tamare, quinta Lina el Marques<br /> Caracas, Venezuela</div>
                <div>+58-212-237-7847</div>
                <div><a href="mailto:Infofattoacasa@gmail.com">Infofattoacasa@gmail.com</a></div>
            </div>
        </div>
        <h1 style="clear: both">{{$header}}</h1>
    </header>
    <main>
        <div id="notices">
            <div>Parametros de Busqueda:</div>
            <div class="notice">{{$filtro}}.</div>
        </div>
        <table>
            <thead>
                <tr>
                    @foreach($titulos as $name)
                        <th class="">{{$name}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $row)
                    <tr>
                        @for($i = 1; $i <= count($titulos); $i++)
                            @if(isset($row['dato-'.$i]))  
                                <td class="">{{$row['dato-'.$i]}}</td>
                            @endif
                        @endfor
                    </tr>
                @endforeach
            <tr>
                <td colspan="{{count($titulos)-1}}" class="grand total" style="text-align: right">Número de Registros:</td>
                <td class="grand total">{{count($datos)}}</td>
            </tr>
            </tbody>
        </table>
    </main>
    <footer>Reporte generado por computador y es invalido sin la firma y sellado del mismo.</footer>
  </body>
</html>