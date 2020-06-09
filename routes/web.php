<?php

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home_2');

/* FILTRAR EL INDICADOR QUE APARECE EN EL HOME */
Route::post('filter-home', 'HomeController@ajaxindicator')->name('filter-home');

Route::group(['prefix'=>'user'],function(){
    Route::get('perfil', 'UsuarioController@showPerfil')->name('perfil');
    Route::post('editar-perfil', 'UsuarioController@updatePerfil')->name('edit-profile');

    /* USUARIOS */
    Route::get('usuarios/{registros?}/{tipo?}/{name?}/{username?}/{orden?}', 'UsuarioController@showUsers')->name('list-users');
    Route::get('agregar-usuario', 'UsuarioController@createUser')->name('agg-user');
    Route::get('pdf-list-users/{tipo?}/{name?}/{username?}', 'UsuarioController@downloadUsuario')->name('pdf-list-users');
    Route::get('detail-usuario/{id?}', 'UsuarioController@detailUser')->name('detail-user');

    Route::post('almacenar-usuario', 'UsuarioController@saveUser')->name('save-user');
    Route::post('editar-usuario', 'UsuarioController@updateUser')->name('edit-user');
    Route::post('borrar-usuario', 'UsuarioController@deleteUser')->name('delete-user');

    /* EMPLEADOS */
    Route::get('empleados/{registros?}/{tipo?}/{nombre?}/{banco?}/{orden?}', 'UsuarioController@showWorkers')->name('list-workers');
    Route::get('agregar-empleado', 'UsuarioController@createWorker')->name('agg-worker');
    Route::get('pdf-list-workers/{tipo?}/{nombre?}/{banco?}', 'UsuarioController@downloadTrabajador')->name('pdf-list-workers');
    Route::get('detail-empleado/{id?}', 'UsuarioController@detailWorker')->name('detail-worker');

    Route::post('almacenar-worker', 'UsuarioController@saveWorker')->name('save-worker');
    Route::post('editar-worker', 'UsuarioController@updateWorker')->name('edit-worker');
    Route::post('borrar-worker', 'UsuarioController@deleteWorker')->name('delete-worker');

    /* REPORTES */
    Route::get('reportes/{registros?}/{nombre?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'UsuarioController@showReports')->name('list-reports');
    Route::post('borrar-reporte', 'UsuarioController@deleteReport')->name('delete-report');

    /* REPORTES DEL SISTEMA */
    Route::get('reportes-sistema/{registros?}/{nombre?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'UsuarioController@showSystem')->name('list-system');
    Route::post('borrar-incidencia', 'UsuarioController@deleteIncidencia')->name('delete-incidencia');

    /* RESTAURACIÓN */
    Route::get('backups', 'UsuarioController@showBackups')->name('list-backups');
    Route::get('backups/create', 'UsuarioController@createBackup')->name('create-backup');
    Route::get('backups/download/{file_name}', 'UsuarioController@downloadBackup')->name('download-backup');
    Route::get('backups/delete/{file_name}', 'UsuarioController@deleteBackup')->name('delete-backup');
});

Route::group(['prefix'=>'indicadores'],function(){
    Route::get('logística', 'IndicadorController@showlogistic')->name('ind-logistica');
    Route::get('ventas', 'IndicadorController@showsell')->name('ind-venta');
    Route::get('compras', 'IndicadorController@showbuy')->name('ind-compra');
    Route::get('clientes', 'IndicadorController@showclient')->name('ind-client');
    Route::get('proveedores', 'IndicadorController@showprovider')->name('ind-proveedor');
    Route::get('finanzas', 'IndicadorController@showfinance')->name('ind-finanza');

    //Filtros por modulo
    Route::post('filter-logistica', 'IndicadorController@ajaxlogistic')->name('filter-logistica');
    Route::post('filter-ventas', 'IndicadorController@ajaxsells')->name('filter-ventas');
    Route::post('filter-compras', 'IndicadorController@ajaxbuys')->name('filter-compras');
    Route::post('filter-clientes', 'IndicadorController@ajaxclients')->name('filter-clientes');
    Route::post('filter-finance', 'IndicadorController@ajaxfinance')->name('filter-finance');
});

Route::group(['prefix'=>'clientes'],function(){
    Route::get('lista/{registros?}/{id_zona?}/{persona?}/{orden?}', 'ClienteController@index')->name('list-client');
    Route::get('ventas-cliente/{registros?}/{id?}/{proveedor?}/{estado?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'ClienteController@sell')->name('sell-client');
    Route::get('detail-cliente/{id?}', 'ClienteController@show')->name('detail-client');
    Route::post('detail-venta-client', 'ClienteController@detailVentaProducts')->name('products-client-venta');
    Route::get('agregar-cliente', 'ClienteController@create')->name('agg-client');

    Route::get('pdf-client/{id_zona?}/{persona?}', 'ClienteController@downloadCliente')->name('pdf-client');
    Route::get('pdf-sell-client/{id?}/{proveedor?}/{estado?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'ClienteController@downloadVenta')->name('pdf-sell-client');

    Route::post('almacenar-cliente', 'ClienteController@save')->name('save-client');
    Route::post('editar-cliente', 'ClienteController@update')->name('edit-client');
    Route::post('borrar-cliente', 'ClienteController@delete')->name('delete-client');
});

Route::group(['prefix'=>'proveedores'],function(){
    Route::get('lista/{registros?}/{id_zona?}/{persona?}/{orden?}', 'ProveedorController@index')->name('list-prov');
    Route::get('compras-proveedor/{registros?}/{id?}/{proveedor?}/{estado?}/{tiempo?}/{ayo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'ProveedorController@buys')->name('buy-prov');
    Route::get('detail-proveedor/{id?}', 'ProveedorController@show')->name('detail-prov');
    Route::post('detail-compra-prov', 'ProveedorController@detailCompraProducts')->name('products-prov-compra');
    Route::get('agregar-proveedor', 'ProveedorController@create')->name('agg-prov');
    
    Route::get('pdf-prov/{id_zona?}/{persona?}', 'ProveedorController@downloadProveedor')->name('pdf-prov');
    Route::get('pdf-buy-prov/{id?}/{proveedor?}/{estado?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'ProveedorController@downloadCompra')->name('pdf-buy-prov');

    Route::post('almacenar-proveedor', 'ProveedorController@save')->name('save-prov');
    Route::post('editar-proveedor', 'ProveedorController@update')->name('edit-prov');
    Route::post('borrar-proveedor', 'ProveedorController@delete')->name('delete-prov');
});

Route::group(['prefix'=>'finanzas'],function(){
    /* INGRESOS */
    Route::get('ingresos/{registros?}/{id?}/{cliente?}/{referencia?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'FinanzaController@showIngresos')->name('list-ingresos');
    Route::get('pdf-list-ingresos/{id?}/{cliente?}/{referencia?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'FinanzaController@downloadIngreso')->name('pdf-list-ingresos');

    /* EGRESOS */
    Route::get('egresos/{registros?}/{tipo?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'FinanzaController@showEgresos')->name('list-egresos');
    Route::get('pdf-list-egresos/{tipo?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'FinanzaController@downloadEgreso')->name('pdf-list-egresos');

    /* GASTOS-COSTOS */
    Route::get('gastos-costos/{registros?}/{tipo?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'FinanzaController@showGastoCosto')->name('list-gasto-costo');
    Route::get('agregar-gasto-costo', 'FinanzaController@createCostoGasto')->name('agg-gasto-costo');
    Route::get('detail-gasto-costo/{id?}', 'FinanzaController@detailGastoCosto')->name('detail-gasto-costo');
    Route::get('pdf-list-gasto-costo/{tipo?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'FinanzaController@downloadGastoCosto')->name('pdf-list-gasto-costo');

    Route::post('almacenar-gasto-costo', 'FinanzaController@saveGastoCosto')->name('save-gasto-costo');
    Route::post('editar-gasto-costo', 'FinanzaController@updateGastoCosto')->name('edit-gasto-costo');
    Route::post('eliminar-gasto-costo', 'FinanzaController@deleteGastoCosto')->name('delete-gasto-costo');

    /* NOMINA */
    Route::get('nómina/{registros?}/{empleado?}/{tiempo?}/{ayo?}/{mes?}/{orden?}', 'FinanzaController@showNomina')->name('list-nomina');
    Route::get('agregar-nomina', 'FinanzaController@createNomina')->name('agg-nomina');
    Route::get('detail-nomina/{id?}', 'FinanzaController@detailNomina')->name('detail-nomina');
    Route::get('pdf-list-nomina/{empleado?}/{tiempo?}/{ayo?}/{mes?}', 'FinanzaController@downloadNomina')->name('pdf-list-nomina');

    Route::post('almacenar-nomina', 'FinanzaController@saveNomina')->name('save-nomina');
    Route::post('editar-nomina', 'FinanzaController@updateNomina')->name('edit-nomina');
    Route::post('eliminar-nomina', 'FinanzaController@deleteNomina')->name('delete-nomina');

    /* PAGOS */
    Route::get('pagos/{registros?}/{tipo?}/{referencia?}/{banco?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'FinanzaController@showPagos')->name('finance-pagos');
    Route::get('detail-pago/{id?}', 'FinanzaController@detailPagos')->name('detail-pago');
    Route::post('editar-pago', 'FinanzaController@updatePago')->name('edit-pago-finance');
    Route::get('pdf-finance-pagos/{tipo?}/{referencia?}/{banco?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'FinanzaController@downloadPago')->name('pdf-finance-pagos');

    /* FILTRAR LOS INDICADORES QUE SALEN EN LA SECCION DE EGRESOS E INGRESOS */
    Route::post('filter-indicators', 'FinanzaController@ajaxfinance')->name('filter-indicators');
});

Route::group(['prefix'=>'logistica'],function(){
    /* INVENTARIO */
    Route::get('inventario/{registros?}/{producto_name?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'LogisticaController@showInventario')->name('list-inventario');
    Route::post('detail-inventario-info', 'LogisticaController@detailInventarioInfo')->name('info-inventario');
    Route::get('pdf-list-inventario/{producto_name?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'LogisticaController@downloadInventario')->name('pdf-list-inventario');

    Route::post('almacenar-inventario', 'LogisticaController@saveInventario')->name('save-inventario');
    Route::post('editar-inventario', 'LogisticaController@updateInventario')->name('edit-inventario');
    Route::post('eliminar-inventario', 'LogisticaController@deleteInventario')->name('delete-inventario');

    /* SUMINISTRO */
    Route::get('suministro/{registros?}/{id?}/{proveedor?}/{producto_name?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'LogisticaController@showSuministro')->name('list-suministro');
    Route::post('detail-suministro-info', 'LogisticaController@detailSuministroInfo')->name('info-suministro');
    Route::get('pdf-list-suministro/{id?}/{proveedor?}/{producto_name?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'LogisticaController@downloadSuministro')->name('pdf-list-suministro');
    
    Route::post('almacenar-suministro', 'LogisticaController@saveSuministro')->name('save-suministro');
    Route::post('editar-suministro', 'LogisticaController@updateSuministro')->name('edit-suministro');
    Route::post('eliminar-suministro', 'LogisticaController@deleteSuministro')->name('delete-suministro');

    /* PORTAFOLIO DE PRODUCTOS */
    Route::get('producto/{registros?}/{id_categoria?}/{nombre?}/{orden?}', 'LogisticaController@showProducto')->name('list-producto');
    Route::get('agregar-producto', 'LogisticaController@createProducto')->name('agg-producto');
    Route::get('detail-producto/{id?}', 'LogisticaController@detailProducto')->name('detail-producto');
    Route::get('pdf-list-producto/{id_categoria?}/{nombre?}', 'LogisticaController@downloadProducto')->name('pdf-list-producto');
    
    Route::post('almacenar-producto', 'LogisticaController@saveProducto')->name('save-producto');
    Route::post('editar-producto', 'LogisticaController@updateProducto')->name('edit-producto');
    Route::post('editar-receta', 'LogisticaController@updateReceta')->name('edit-receta');
});

Route::group(['prefix'=>'compras'],function(){
    /* COMPRA REALIZADA */
    Route::get('lista/{registros?}/{id?}/{proveedor?}/{estado?}/{tiempo?}/{ayo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'CompraController@showCompras')->name('list-compras');
    Route::get('agregar-compra', 'CompraController@createCompra')->name('agg-compra');
    Route::get('detail-compra/{id?}', 'CompraController@detailCompra')->name('detail-compra');
    Route::post('detail-compra-products', 'CompraController@detailCompraProducts')->name('products-compra');
    Route::get('pdf-compras/{id?}/{proveedor?}/{estado?}/{tiempo?}/{ayo?}/{fecha_1?}/{fecha_2?}', 'CompraController@downloadCompra')->name('pdf-compras');

    Route::post('almacenar-compra', 'CompraController@saveCompra')->name('save-compra');
    Route::post('almacenar-pago', 'CompraController@savePago')->name('save-compra-pago');
    Route::post('almacenar-desperdicio', 'CompraController@saveDesperdicio')->name('save-desperdicio');
    Route::post('editar-compra', 'CompraController@updateCompra')->name('edit-compra');
    Route::post('eliminar-compra', 'CompraController@deleteCompra')->name('delete-compras');

    /* SUMINISTRO - PRECIO DE PROVEEDORES ETC */
    Route::get('suministros/{registros?}/{id_zona?}/{persona?}/{orden?}', 'CompraController@showSuministros')->name('suministros');
    Route::post('detail-suministro-prov', 'CompraController@detailSuministroProducts')->name('prov-products');
    Route::get('pdf-suministros/{id_zona?}/{persona?}', 'CompraController@downloadSuministro')->name('pdf-suministros');
    
    /* CUENTAS POR PAGAR */
    Route::get('cuentas-por-pagar/{registros?}/{id?}/{proveedor?}/{estado?}/{tiempo?}/{ayo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'CompraController@showCuentasPP')->name('cpp');
    Route::get('pdf-cpp/{id?}/{proveedor?}/{tiempo?}/{ayo?}/{fecha_1?}/{fecha_2?}', 'CompraController@downloadCuentasPP')->name('pdf-cpp');
    
    /* PAGOS REALIZADOS */
    Route::get('cuentas-pagadas/{registros?}/{id?}/{referencia?}/{banco?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'CompraController@showCuentasPagadas')->name('cp');
    Route::get('detail-pago/{id?}', 'CompraController@detailPago')->name('compra-pago');
    Route::post('editar-pago', 'CompraController@updatePago')->name('edit-pago');
    Route::get('pdf-compra-pago/{id?}/{referencia?}/{banco?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'CompraController@downloadPago')->name('pdf-compra-pago');
});

Route::group(['prefix'=>'ventas'],function(){
    /* VENTA REALIZADA */
    Route::get('lista/{registros?}/{id?}/{proveedor?}/{estado?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'VentaController@showVentas')->name('list-ventas');
    Route::get('agregar-venta', 'VentaController@createVenta')->name('agg-venta');
    Route::get('detail-venta/{id?}', 'VentaController@detailVenta')->name('detail-venta');
    Route::get('pdf-ventas/{id?}/{proveedor?}/{estado?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'VentaController@downloadVenta')->name('pdf-ventas');

    Route::post('almacenar-venta', 'VentaController@saveVenta')->name('save-venta');
    Route::post('almacenar-pago', 'VentaController@savePago')->name('save-venta-pago');
    Route::post('editar-venta', 'VentaController@updateVenta')->name('edit-venta');
    Route::post('eliminar-venta', 'VentaController@deleteVenta')->name('delete-ventas');

    /* PEDIDOS SIN DESPACHO */
    Route::get('lista-pedidos/{registros?}/{id?}/{proveedor?}/{estado?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'VentaController@showPedidos')->name('list-pedidos');
    Route::get('pdf-pedidos/{id?}/{proveedor?}/{estado?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'VentaController@downloadPedido')->name('pdf-pedidos');
    Route::post('almacenar-despacho', 'VentaController@saveDespacho')->name('save-despacho');

    /* DESPACHOS */
    Route::get('lista-despachos/{registros?}/{id?}/{persona?}/{despachador?}/{estado?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'VentaController@showDespachos')->name('list-despachos');
    Route::get('detail-despacho/{id?}', 'VentaController@detailDespacho')->name('detail-despacho');
    Route::get('pdf-despachos/{id?}/{persona?}/{despachador?}/{estado?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'VentaController@downloadDespacho')->name('pdf-despachos');
    Route::get('pdf-detail-despacho/{id?}', 'VentaController@downloadDetailDespacho')->name('pdf-detail-despacho');

    Route::post('editar-despacho', 'VentaController@updateDespacho')->name('edit-despacho');
    Route::post('eliminar-despacho', 'VentaController@deleteDespacho')->name('delete-despacho');

    /* CUENTAS POR COBRAR */
    Route::get('cuentas-por-cobrar/{registros?}/{id?}/{cliente?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'VentaController@showCuentas')->name('list-cuentas');
    Route::get('pdf-ventas-cuentas/{id?}/{cliente?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'VentaController@downloadCuenta')->name('pdf-ventas-cuentas');

    /* PAGOS REALIZADOS */
    Route::get('pagos-recibidos/{registros?}/{id?}/{referencia?}/{banco?}/{tiempo?}/{fecha_1?}/{fecha_2?}/{orden?}', 'VentaController@showPagos')->name('list-pagos');
    Route::get('detail-pago/{id?}', 'VentaController@detailPago')->name('venta-pago');
    Route::get('pdf-ventas-pago/{id?}/{referencia?}/{banco?}/{tiempo?}/{fecha_1?}/{fecha_2?}', 'VentaController@downloadPago')->name('pdf-ventas-pago');
    Route::post('editar-pago', 'VentaController@updatePago')->name('edit-pago-venta');
});

Route::group(['prefix'=>'reporte'],function(){
    Route::post('error-sistema', 'ReporteController@saveError')->name('report-error');
    Route::get('pdf', 'ReporteController@index')->name('descargar');
});

//RUTAS PARA EL CALENDARIO

//Route::get('/Calendario', 'CalendarioController@index')->name('calendario');

//Route::get('Calendario', 'CalendarioController@index')->name('calendario');

Route::resource('Calendario', 'CalendarioController');

//RUTA PARA EL ENVIO DE CORREOS
Route::post('mail/send/{id}', 'MailController@send');

//Para probar la vista del correo
Route::view('/mail', 'mails.mail');