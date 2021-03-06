<?php
require_once('../ApiFacturacion/ado/clsEmisor.php');
require_once('../ApiFacturacion/ado/clsCompartido.php');

$objEmisor = new clsEmisor();
$listado_emisores = $objEmisor->consultarListaEmisores();

$objCompartido = new clsCompartido();
$monedas = $objCompartido->listarMonedas();
$comprobantes = $objCompartido->listarComprobantesCodigo('01');
$documentos = $objCompartido->listarTipoDocumentoCodigo('6');

?>

<div class="col-12 mt-2">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-shopping-cart"></i> FACTURA ELECTRÓNICA</h3>
        </div>

        <div class="card-body">
            <form id="frmVenta" name="frmVenta" submit="return false">
                <input type="hidden" name="accion" id="accion" value="GUARDAR_VENTA">
                <div class="col-12">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label>Facturar por</label>
                                <select name="idemisor" id="idemisor" class="form-control">
                                    <?php while($fila = $listado_emisores->fetch(PDO::FETCH_NAMED)){ ?>
                                            <option value="<?php echo $fila['id'];?>"><?php echo $fila['razon_social'];?></option>
                                        <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Fecha</label>
                                <input type="date" class="form-control" name="fecha_emision" id="fecha_emision" value="<?php echo date('Y-m-d') ?>">
                            </div>

                            <div class="form-group">
                                <label>Moneda</label>
                                <select class="form-control" name="moneda" id="moneda">
                                <?php while($fila = $monedas->fetch(PDO::FETCH_NAMED)){ ?>
                                        <option value="<?php echo $fila['codigo'];?>"><?php echo $fila['descripcion'];?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Forma de pago</label>
                                <select name="forma_pago" id="forma_pago" class="form-control">
                                    <option value="Contado">Contado</option>
                                    <option value="Credito">Crédito</option>
                                </select>
                            </div>

                            <div id="div_monto_pendiente">

                            </div>
                        </div>

                        <div class="col-4">
                            <div class="form-group">
                                <label>Tipo Comprobante</label>
                                <select class="form-control" name="tipocomp" id="tipocomp" onchange="ConsultarSerie()">
                                <?php while($fila = $comprobantes->fetch(PDO::FETCH_NAMED)){ ?>
                                        <option value="<?php echo $fila['codigo'];?>"><?php echo $fila['descripcion'];?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Serie</label>
                                <select name="idserie" id="idserie" class="form-control" onchange="ConsultarCorrelativo()">

                                </select>
                            </div>

                            <div class="form-group">
                                <label>Correlativo</label>
                                <input type="number" class="form-control" name="correlativo" id="correlativo" readonly>
                            </div>

                            <div class="form-group">
                                <label>Cuotas</label>
                                <div class="input-group">
                                    <input type="number" name="cuotas" id="cuotas" min="1" class="form-control">
                                    <div class="input-group-addon">
                                        <button type="button" class="btn btn-default" onclick="GenerarCuotas()">
                                            <li class="fa fa-plus" title="Generar Cuotas"></li>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="form-group">
                                <label>Tipo Documento</label>
                                <select name="tipodoc" id="tipodoc" class="form-control">
                                <?php while($fila = $documentos->fetch(PDO::FETCH_NAMED)){ ?>
                                        <option value="<?php echo $fila['codigo'];?>"><?php echo $fila['descripcion'];?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>RUC</label>
                                <div class="input-group">
                                    <input type="text" name="nrodoc" id="nrodoc" class="form-control">
                                    <div class="input-group-addon">
                                        <button type="button" class="btn btn-default" onclick="ObtenerDatosRUC()">
                                            <li class="fa fa-search" title="Buscar.."></li>
                                        </button>
                                    </div>
                                </div>                               
                            </div>

                            <div class="form-group">
                                    <label>Nombre/Razón Social</label>
                                    <input type="text" class="form-control" name="razon_social" id="razon_social">
                                </div>
                                
                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" class="form-control" name="direccion" id="direccion">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="row">
                                <div class="col-4">
                                    <table class="table table-bordered table-hover table-sm">
                                        <thead class="text-center">
                                            <th>Cuota</th>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                        </thead>
                                        <tbody id="div_cuotas">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="producto" id="producto" placeholder="Buscar producto..">
                                <div class="input-group-addon">
                                    <button type="button" class="btn btn-default" onclick="BuscarProducto()">
                                        <li class="fa fa-search"></li>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div >
                                    <table class="table table-bordered table-hover table-sm">
                                        <thead class="text-center">
                                            <th>Codigo</th>
                                            <th>Nombre</th>
                                            <th>Precio</th>
                                            <th>Cant</th>
                                            <th>
                                                <button type="button" class="btn btn-info"> +</button>
                                            </th>
                                        </thead>
                                        <tbody id="div_productos">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="col-12" id="div_carrito">

                            </div>
                            <div>
                                <button type="button" class="btn btn-primary" onclick="GuardarVenta()">
                                    <i class="fa fa-save"></i> Guardar
                                </button>
                                <button type="button" class="btn btn-danger" onclick="CancelarVenta()">
                                    <i class="fa fa-trash-alt"></i> Cancelar
                                </button>
                            </div>

                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

</div>


<script>

    $('#tipocomp').val('01'); //valor por defecto para factura
    ConsultarSerie();

    function GenerarCuotas(){
        listado = '';
        cuotas = $('#cuotas').val()
        for (let i = 1; i <= cuotas; i++) {
            listado = listado + '<tr><td><input class="form-control input-sm" name="txtCuota' + i +'" type="text" value="Cuota ' + i + '" readonly/></td>'
                        + '<td><input class="form-control input-sm" name="txtFecha' + i +'" type="date"/></td>'
                        + '<td><input class="form-control input-sm" name="txtMonto' + i +'" type="number"/></td></tr>';
        }

        $('#div_cuotas').html(listado);

        if (cuotas > 0) {
            monto_pendiente = '<div class="form-group"><label>Monto pendiente</label><input class="form-control" type="number" name="monto_pendiente" id="monto_pendiente" value="0.0" /></div>';
            $('#div_monto_pendiente').html(monto_pendiente);
        }
    }

    function ConsultarSerie(){
        $.ajax({
            method: "POST",
            url: "apifacturacion/controlador/controlador.php",
            data:{
                "accion": "LISTAR_SERIES",
                "tipocomp": $('#tipocomp').val()
            }
        })
        .done(function(text){
            json = JSON.parse(text);            
            series = json.series;
            options = '';
            for(i=0;i<series.length;i++){
            	options = options + '<option value="'+series[i].id+'">'+series[i].serie+'</option>';
            }
            $("#idserie").html(options);
            ConsultarCorrelativo();
        }
        )
    }
    

    function ConsultarCorrelativo(){
        $.ajax({
            method: "POST",
            url: "apifacturacion/controlador/controlador.php",
            data:{
                "accion": "OBTENER_CORRELATIVO",
                "idserie": $('#idserie').val()
            }
        })
        .done(function(correlativo){
            $('#correlativo').val(correlativo);
        }
        )
    }

    function ObtenerDatosEmpresa(){
        tipodoc = $('#tipodoc').val();
        if (tipodoc == 1) {
            ObtenerDatosDNI();
        }else if(tipodoc == 6){
            ObtenerDatosRUC();
        }
    }

    function ObtenerDatosDNI(){
        $.ajax({
            method: "POST",
            url: "apifacturacion/controlador/controlador.php",
            data:{
                "accion": "CONSULTA_DNI",
                "dni": $('#nrodoc').val()
            }
        })
        .done(function(text){
            json = JSON.parse(text);
            $('#razon_social').val(json.result.Nombre + ' ' + json.result.Paterno + ' ' + json.result.Materno);
            $('#direccion').val('');
        }
        )
    }

    function ObtenerDatosRUC(){
        $.ajax({
            method: "POST",
            url: "apifacturacion/controlador/controlador.php",
            data:{
                "accion": "CONSULTA_RUC",
                "ruc": $('#nrodoc').val()
            }
        })
        .done(function(text){
            json = JSON.parse(text);
            $('#razon_social').val(json.result.razon_social)
            $('#direccion').val('');
        }
        )
    }

    function BuscarProducto(){
        $.ajax({
            method: "POST",
            url: "apifacturacion/controlador/controlador.php",
            data:{
                "accion": "BUSCAR_PRODUCTO",
                "filtro": $('#producto').val()
            }
        })
        .done(function(resultado){
            json = JSON.parse(resultado);            
            productos = json.productos;
            listado = '';
            for(i=0;i<productos.length;i++){
            	listado = listado + '<tr><td>'+productos[i].codigo+'</td><td>'+productos[i].nombre+'</td><td>'+productos[i].precio+'</td><td><input class="form-control input-sm" id="txtCantidad'+productos[i].codigo+'" value="1" type="number" min="1" /></td><td><button type="button" class="btn btn-primary btn-sm" onclick="AgregarCarrito('+productos[i].codigo+')"> + </button></td></tr>';
            }
            $("#div_productos").html(listado);
            
        }
        )
    }

    function AgregarCarrito(codigo){
        $.ajax({
            method: "POST",
            url: "apifacturacion/controlador/controlador.php",
            data:{
                "accion": "ADD_PRODUCTO",
                "codigo": codigo,
                "cantidad": $('#txtCantidad' + codigo).val()
            }
        })
        .done(function(resultado){
            $('#div_carrito').html(resultado);
        }
        );
    }

    function CancelarVenta(){
        $.ajax({
            method: "POST",
            url: "apifacturacion/controlador/controlador.php",
            data:{
                "accion": "CANCELAR_CARRITO"
            }
        })
        .done(function(resultado){
            $('#div_carrito').html(resultado);
        }
        );
    }

    function GuardarVenta(){
        var datax = $('#frmVenta').serializeArray();

        $.ajax({
            method: "POST",
            url: "apifacturacion/controlador/controlador.php",
            data: datax
        })
        .done(function(resultado){
            $('#div_carrito').html(resultado);
        }
        );
    }

</script>