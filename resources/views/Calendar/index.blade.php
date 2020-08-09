@extends('layouts.principal')

@section('title','Calendario · Fatto a Casa')

@section('titulo','VER - CALENDARIO')

@section('tabs')
    <ul class="nav nav-tabs">
        <li class="nav-item">
        <a class="nav-link text-dark active font-weight-bold" href=" {{ url('/Calendario')}}">Calendario</a>
        </li>
    </ul>
@endsection

@section('CalendarScripts')

    <link href="{{ asset('fullcalendar/core/main.css') }}" rel="stylesheet">
    <link href="{{ asset('fullcalendar/daygrid/main.css') }}" rel="stylesheet">
    <link href="{{ asset('fullcalendar/list/main.css') }}" rel="stylesheet">
    <link href="{{ asset('fullcalendar/timegrid/main.css') }}" rel="stylesheet">

    <script type="text/javascript" src="{{ asset('fullcalendar/core/main.js') }}"></script>
    <script type="text/javascript" src="{{ asset('fullcalendar/interaction/main.js') }}"></script>
    <script type="text/javascript" src="{{ asset('fullcalendar/daygrid/main.js') }}"></script>
    <script type="text/javascript" src="{{ asset('fullcalendar/list/main.js') }}"></script>
    <script type="text/javascript" src="{{ asset('fullcalendar/timegrid/main.js') }}"></script>

    <!-- Funcionalidades y uso de Fullcalendar-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
           
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [ 'dayGrid', 'interaction','timeGrid', 'list',],

                //themeSystem: 'standard',
                //defaultView: 'timeGridDay',
                header:{
                    left:'prev,next today MiBoton',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
                },
  
                customButtons:{
                    MiBoton:{
                        text:"Agregar",
                        click:function(){
                            limpiarFormulario();
                            $('#exampleModal').modal('toggle');
                            $("#btnAgregar").show();
                            $("#btnModificar").hide();
                            $("#btnEliminar").hide();
                            $(".id").hide();
                            $("#txtFecha").removeAttr('disabled');
                            $('#txtCliente').hide();
                            $('#txtProveedor').hide();
                            $('#txtempleado').hide();
                            $('#Clientebtn').show();
                            $('#Proveedorbtn').show();
                            $('#Empleadobtn').show();
                            $('.l1').hide();
                            $('.l2').hide();
                            $('.l3').hide();
                        }
                    }
                },

                buttonText:{
                    today:    'Hoy',
                    month:    'Mes',
                    week:     'Semana',
                    day:      'Día',
                    list:     'Lista',
                },
                
                allDayText: 'Todo el día',
                
                dateClick: function(info){
                    limpiarFormulario();
                    $('#txtFecha').val(info.dateStr);
                    $('#exampleModal').modal();
                    $("#btnAgregar").show();
                    $("#btnModificar").hide();
                    $("#btnEliminar").hide();
                    $(".id").hide();
                    $("#txtFecha").prop( "disabled", true );
                    $('#Clientebtn').show();
                    $('#Proveedorbtn').show();
                    $('#Empleadobtn').show();
                    $('#txtCliente').hide();
                    $('#txtProveedor').hide();
                    $('#txtempleado').hide();
                    $('.l1').hide();
                    $('.l2').hide();
                    $('.l3').hide();
                },
  
                eventClick:function(info){
                    $('.validate-form .input100').each(function(){
                        hideValidate(this);
                    });
                    $("#btnAgregar").hide();
                    $("#btnModificar").show();
                    $("#btnEliminar").show();
                    $(".id").hide();
                    $("#txtFecha").removeAttr('disabled');
                    $('#Clientebtn').show();
                    $('#Proveedorbtn').show();
                    $('#Empleadobtn').show();
                    $('#txtCliente').hide();
                    $('#txtProveedor').hide();
                    $('#txtempleado').hide();
                    $('.l1').hide();
                    $('.l2').hide();
                    $('.l3').hide();
                    
                    console.log(info);
                    console.log(info.event.title);
                    console.log(info.event.start);
                    console.log(info.event.end);
                    console.log(info.event.backgroundColor);
                    console.log(info.event.extendedProps.descripcion);
                    
                    cliente = info.event.extendedProps.cliente_id;
                    proveedor = info.event.extendedProps.proveedor_id;
                    trabajador = info.event.extendedProps.trabajador_id;
                    console.log(cliente);
                    console.log(proveedor);
                    console.log(trabajador);

                    $('#txtID').val(info.event.id);
                    $('#txtTitulo').val(info.event.title);
                    $('#txtColor').val(info.event.backgroundColor);
                    $('#txtDescripcion').val(info.event.extendedProps.descripcion);

                    mes = (info.event.start.getMonth()+1);
                    dia = (info.event.start.getDate());
                    anio = (info.event.start.getFullYear());
                    
                    mes = (mes<10)?"0"+mes:mes;
                    dia = (dia<10)?"0"+dia:dia;
                    
                    minutos=info.event.start.getMinutes();
                    hora=info.event.start.getHours();
                    minutos = (minutos<10)?"0"+minutos:minutos;
                    hora = (hora<10)?"0"+hora:hora;
                    hora = (hora+":"+minutos);
                    $('#txtFecha').val(anio+"-"+mes+"-"+dia);
                    $('#txtHora').val(hora);

                    if(cliente){
                        $('#txtCliente').show();
                        $('.l1').show();
                        $("#txtCliente option[value='"+ cliente +"']").attr("selected",true);
                    }
                    else if(proveedor){
                        $('#txtProveedor').show();
                        $('.l2').show();
                        $("#txtProveedor option[value='"+ proveedor +"']").attr("selected",true);
                    }
                    else if(trabajador){
                        $('#txtempleado').show();
                        $('.l3').show();
                        $("#txtempleado option[value='"+ trabajador +"']").attr("selected",true);
                    }

                    $('#exampleModal').modal();
                },
          
                events: "{{ url('/Calendario/show')}}"
            });

            calendar.setOption('locale', 'es');    
            calendar.render();

            $('#btnAgregar').click(function(){
                if(validar_calendario()){
                    objEvento = recolectarDatosGUI("POST");
                    EnviarInformacion('', objEvento);
                }
            });   

            $('#btnEliminar').click(function(){
                swal({
                    title: "Eliminar Evento",
                    text: "¿Esta seguro de eliminar el evento seleccionado?",
                    icon: "warning",
                    buttons: ["Cancelar","Aceptar"],
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        objEvento=recolectarDatosGUI("DELETE");
                        EnviarInformacion('/'+$('#txtID').val(), objEvento);
                    }
                });
            });

            $('#btnModificar').click(function(){
                if(validar_calendario()){
                    objEvento = recolectarDatosGUI("PATCH");
                    EnviarInformacion('/'+$('#txtID').val(), objEvento);
                }
            });
       
            function recolectarDatosGUI(method){
                nuevoEvento={
                    id: $('#txtID').val(),
                    title: $('#txtTitulo').val(),
                    descripcion: $('#txtDescripcion').val(),
                    start: $('#txtFecha').val()+" "+$('#txtHora').val(),
                    end: $('#txtFecha').val()+" "+$('#txtHora').val(),
                    color: $('#txtColor').val(),
                    cliente_id: $('#txtCliente').val(),
                    proveedor_id: $('#txtProveedor').val(),
                    trabajador_id: $('#txtempleado').val(),
                    activo: '0',
                    '_token':$("meta[name='csrf-token']").attr("content"),
                    '_method': method
                }
                return(nuevoEvento);
            }

            function EnviarInformacion(accion, objEvento, metodo){
                $.ajax(
                    {
                        type: "POST",
                        url:"{{ url('/Calendario') }}"+accion,
                        data: objEvento,
                        success: function(msg){
                            console.log(msg);
                            $('#exampleModal').modal('toggle');
                            calendar.refetchEvents();
                        },
                        error: function(){ 
                            alert("Hay un error");
                        },
                    }
                );
            }

            function limpiarFormulario()
            {
                $('#txtID').val(""),
                $('#txtTitulo').val(""),
                $('#txtDescripcion').val(""),
                $('#txtFecha').val("")
                $('#txtHora').val("07:00")
                $('#txtColor').val("");
                $('#txtCliente').val("");
                $('#txtProveedor').val("");
                $('#txtempleado').val("");
                $('.validate-form .input100').each(function(){
                    hideValidate(this);
                });
            }
  
        });
    </script>

    <!-- Este script es para manejar los clientes/proveedores/empleados
         y sus respectivos iconos -->
    <script>
        $(document).ready(function(){

            function reset(){
                $("#btnAgregar").removeAttr("disabled");

                $('#txtCliente').removeClass('req-true'); 
                $('#txtCliente').addClass('req-false');
                $('#txtCliente').prop('selectedIndex',0);

                $('#txtProveedor').removeClass('req-true'); 
                $('#txtProveedor').addClass('req-false');
                $('#txtProveedor').prop('selectedIndex',0);
                
                $('#txtempleado').removeClass('req-true'); 
                $('#txtempleado').addClass('req-false');
                $('#txtempleado').prop('selectedIndex',0);
            }

            $("#Clientebtn").click(function(){
                //$('#Clientebtn').hide();
                //$('#Proveedorbtn').hide();
                //$('#Empleadobtn').hide();
                reset();
                $('#txtCliente').show();
                $('#txtCliente').removeClass('req-false'); $('#txtCliente').addClass('req-true');
                $('.l1').show();
                $('.l2').hide();
                $('.l3').hide();
            });

            $("#Proveedorbtn").click(function(){
                //$('#Clientebtn').hide();
                //$('#Proveedorbtn').hide();
                //$('#Empleadobtn').hide();
                reset();
                $('#txtProveedor').show();
                $('#txtProveedor').removeClass('req-false'); $('#txtProveedor').addClass('req-true');
                $('.l1').hide();
                $('.l2').show();
                $('.l3').hide();
            });

            $("#Empleadobtn").click(function(){
                //$('#Clientebtn').hide();
                //$('#Proveedorbtn').hide();
                //$('#Empleadobtn').hide();
                reset();
                $('#txtempleado').show();
                $('#txtempleado').removeClass('req-false'); $('#txtempleado').addClass('req-true');
                $('.l1').hide();
                $('.l2').hide();
                $('.l3').show();
            });

            //CAMBIAR EL TEXTO DE LOS BOTONES DE MES-DIA-SEMANA-LISTADO
            /* $(".fc-dayGridMonth-button").text("mes");
            $(".fc-timeGridWeek-button ").text("semanal");
            $(".fc-timeGridDay-button ").text("diario");
            $(".fc-listMonth-button").text("listado"); */
            $('.fc-axis fc-widget-content').text('Todo el día');

        });
    </script>
@endsection

@section('info')
    <style>
        /* BOTONES DEL MODAL */
        @media (max-width: 442px) {  
            #Clientebtn-div{
                flex-basis: unset;
            }

            #Empleadobtn-div, #Proveedorbtn-div{
                margin-top: 10px;
                flex-basis: unset;
            }

            #Proveedorbtn, #Empleadobtn, #Clientebtn{
                width: 80%;
            }
        }

        /* BOTONES DE LA PANTALLA PRINCIPAL QUE FILTRA EL CALENDARIO */
        @media (max-width: 1000px) {  
            #calendar{
                font-size: 10px;
            }
        }

        @media (max-width: 600px) {  
            #calendar{
                font-size: 8px;
            }
        }

        @media (max-width: 500px) {  
            #calendar{
                font-size: 7px;
            }
        }

        @media (max-width: 450px) {  
            #calendar{
                font-size: 6px;
            }
        }

        @media (max-width: 400px) {  
            #calendar{
                font-size: 5px;
            }
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col"></div>
            <div class="col-12"> 
                <div id="calendar"></div> 
            </div>
            <div class="col"></div>
        </div>
    </div>

    <!-- Modal -->
    <!--<option style="background-color:red; color:white; font-weight:bold;" value="">Eliminar Cliente<option>-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content" >

                <div class="modal-header" style="background-color:#F6FAF5; border-bottom: 2px rgba(2,137,54,0.6) solid;">
                    <h5 class="modal-title" style="margin:0 auto; font-weight:bold; color:#028936;font-size:1.4rem;" id="exampleModalLabel">CALENDARIO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row validate-form" id="calendar-modal">
                        <div class="id">
                            ID: <br>
                            <input type="text" name="txtID" id="txtID">
                        </div>

                        <div class="form-group col-md-12">
                            <label>Tipo de evento:</label> 
                            <div class="input-group validate-input" data-validate="El evento es requerido">
                                <div class="d-flex">
                                    <div class="m-auto form-control icon-box text-center">
                                        <i class="fa fa-users fa-lg m-auto"></i>
                                    </div>
                                </div>
                                <select class="form-control input100 req-true" style="font-weight:bold;height: calc(2.19rem + 10px)" name="txtTitulo" id="txtTitulo" required>
                                    <option selected value="">Selecciona el evento</option>
                                    <option value="Reunión">Reunión</option>
                                    <option value="Despacho">Despacho</option>
                                    <option value="Compra">Compra</option>
                                    <option value="Venta">Venta</option>
                                    <option value="Viaje">Viaje</option>
                                    <option value="Pago de Nómina">Pago de Nómina</option>
                                    <option value="Vacaciones">Vacaciones</option>
                                    <option value="Otra">Otra</option>
                                </select>     
                            </div>
                        </div>

                        <div class="form-group col-md-8">
                            <label>Fecha:</label>
                            <div class="input-group validate-input" data-validate="La fecha es requerida">
                                <div class="d-flex">
                                    <div class="m-auto form-control icon-box text-center">
                                        <i class="fa fa-calendar-check-o fa-lg m-auto"></i>
                                    </div>
                                </div>
                                <input required type="date" style="height: calc(2.19rem + 10px)" class="form-control input100 req-true" name="txtFecha" id="txtFecha">
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label >Hora:</label>
                            <div class="input-group validate-input" data-validate="La hora es requerida">
                                <div class="d-flex">
                                    <div class="m-auto form-control icon-box text-center">
                                        <i class="fa fa-calendar-check-o fa-lg m-auto"></i>
                                    </div>
                                </div>
                                <input type="time" style="height: calc(2.19rem + 10px)" min="07:00" step="60" max="19:00" class="form-control input100 req-true" name="txtHora" id="txtHora">
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Descripción:</label>
                            <textarea  class="form-control" name="txtDescripcion" id="txtDescripcion" rows="3" required></textarea>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="container">
                                <div class="row justify-content-center">
                                    <div class="col text-center" id="Clientebtn-div">
                                        <button id="Clientebtn" class="btn btn-secondary">Clientes</button>
                                    </div>
                                    <div class="col text-center" id="Empleadobtn-div">
                                        <button  id="Empleadobtn" class="btn btn-secondary">Empleados</button>
                                    </div>
                                    <div class="col text-center" id="Proveedorbtn-div">
                                        <button id="Proveedorbtn" class="btn btn-secondary">Proveedores</button>   
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="form-group col-md-12 content-center l1">
                            <div class="input-group validate-input" data-validate="La persona es requerida">
                                <div class="d-flex">
                                    <div class="m-auto form-control icon-box text-center">
                                        <i class="fa fa-users fa-lg m-auto"></i>
                                    </div>
                                </div>
                                <select class="form-control input100 req-false" style="font-weight:bold;height: calc(2.19rem + 10px);" name="txtCliente" id="txtCliente">
                                    <option selected value="">Listado de Clientes</option>
                                    @foreach ($clients as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-12 content-center l2">
                            <div class="input-group validate-input" data-validate="La persona es requerida">
                                <div class="d-flex">
                                    <div class="m-auto form-control icon-box text-center">
                                        <i class="fa fa-users fa-lg m-auto"></i>
                                    </div>
                                </div>
                                <select class="form-control input100 req-false" style="font-weight:bold;height: calc(2.19rem + 10px)" name="txtProveedor" id="txtProveedor">
                                    <option selected value="">Listado de Proveedores</option>
                                    @foreach ($providers as $prov)
                                        <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                                    @endforeach
                                </select> 
                            </div>
                        </div>

                        <div class="form-group col-md-12 content-center l3">
                            <div class="input-group validate-input" data-validate="La persona es requerida">
                                <div class="d-flex">
                                    <div class="m-auto form-control icon-box text-center">
                                        <i class="fa fa-users fa-lg m-auto"></i>
                                    </div>
                                </div>
                                <select class="form-control input100 req-false" style="font-weight:bold;height: calc(2.19rem + 10px)" name="txtempleado" id="txtempleado">
                                    <option selected  value="">Listado de Empleados</option>
                                    @foreach ($employers as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->nombre }}</option>
                                    @endforeach
                                </select> 
                            </div>
                        </div>

                        <div class="form-group col-md-12 mt-2">
                            <label>Color del Evento:</label> 
                            <div class="input-group validate-input" data-validate="El color es requerido">
                                <div class="d-flex">
                                    <div class="m-auto form-control icon-box text-center">
                                        <i class="fa fa-pencil-square-o fa-lg m-auto"></i>
                                    </div>
                                </div>
                                <select required class="form-control input100 req-true" style="font-weight:bold;height: calc(2.19rem + 10px)" name="txtColor" id="txtColor">
                                    <option selected value="">Selecciona un color</option>
                                    <option style="color:#0000FF;" value="#0000FF">Azul</option>
                                    <option style="color:#FF8000;" value="#FF8000">Naranja</option>
                                    <option style="color:#00FFFF;" value="#00FFFF">Aqua</option>
                                    <option style="color:#FF0000 ;" value="#FF0000 ">Rojo</option>
                                    <option style="color:#3ADF00;" value="#3ADF00">Verde</option>
                                    <option style="color:#800080;" value="#800080">Morado</option>
                                    <option style="color:#FF0080;" value="#FF0080">Fucsia</option>
                                    <option style="color:#FFFF00;" value="#FFFF00">Amarillo</option>
                                </select>     
                            </div>
                        </div>                   
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="btnAgregar" class="btn btn-success" disabled>Agregar</button>
                    <button  id="btnModificar" class="btn btn-warning">Modificar</button>
                    <button id="btnEliminar" class="btn btn-danger">Borrar</button>
                    <button id="btnCancelar" data-dismiss="modal" class="btn btn-secondary">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    //ACOMODO LA BARRA DE NAVEGACION
    $("#icon").addClass("active");
    $("#icon").removeClass("icono_head");
    $(".icon").removeClass("icono_color");

    //EVENTOS PARA VALIDAR EL MODAL DEL CALENDARIO
        $('.input100').each(function(){
            $(this).on('blur', function(){
                if($(this).val().trim() != "") {
                    $(this).addClass('has-val');
                }
                else {
                    $(this).removeClass('has-val');
                }
            })    
        })

        var input = $('#calendar-modal .input100');
        console.log(input);

        //ESTO REVISO INPUT POR INPUT Y CUANDO YA TODOS PASEN LA VALIDACIÓN SE HABILITA EL ENVIO DE CALENDARIO
        function validar_calendario(){
            var check = true;
            for(var i=0; i<input.length; i++) {
                if(validate(input[i]) == false){
                    showValidate(input[i]);
                    check=false;
                }
            }

            return check;
        }

        //FUNCIÓN QUE CARGA EN CADA INPUT EL EVENTO DE BORRAR LA ALERTA
        $('.validate-form .input100').each(function(){
            $(this).focus(function(){
                hideValidate(this);
            });
        });

        //FUNCIONES QUE EJECUTAN CADA VALIDACIÓN DE ARRIBA Y PONE O QUITA ALERTAS
        function validate (input) {
            //REVISO SI ES REQUERIDO O NO
            if($(input).hasClass("req-true")){
                //REVISO SI ESTA VACIO PRIMERO
                if($(input).val().trim() == ''){
                    return false;
                }
            }
            else{//AQUI YA NO ES REQUERIDO
                //REVISO SI ESTA VACIO PRIMERO POR LO QUE SI PUEDE PASAR
                if($(input).val().trim() == ''){
                    return true;
                }
            }
        }

        function showValidate(input) {
            var thisAlert = $(input).parent();
            $(thisAlert).addClass('alert-validate');
        }

        function hideValidate(input) {
            var thisAlert = $(input).parent();
            $(thisAlert).removeClass('alert-validate');
        }
</script>
@endsection