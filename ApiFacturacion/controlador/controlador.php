<?php

require_once('../ado/clsCliente.php');
require_once('../ado/clsCompartido.php');
require_once('../ado/clsEmisor.php');
require_once('../ado/clsNotaCredito.php');
require_once('../ado/clsNotaDebito.php');
require_once('../ado/clsVenta.php');

require_once('../api_Facturacion.php');
require_once('../api_GeneradorXML.php');
require_once('../cantidad_en_letras.php');

$accion = $_POST['accion'];

operaciones($accion);

function operaciones($accion)
{
    $objCompartido = new clsCompartido();
    $objEmisor = new clsEmisor();
    $objCliente = new clsCliente();
    $objVenta = new clsVenta();
    $objNC = new clsNotaCredito();
    $objND = new clsNotaDebito();
    $api = new ApiFacturacion();
    $generadorXML = new GeneradorXML();

    switch ($accion) {
        case 'LISTAR_SERIES':
            $series = $objCompartido->listarSerie($_POST['tipocomp']);
            $series = $series->fetchAll(PDO::FETCH_NAMED);
            $series = array(
                "series" => $series
            );
            echo json_encode($series);
            break;

        case 'OBTENER_CORRELATIVO':
            $serie = $objCompartido->obtenerSerie($_POST['idserie']);
            $serie = $serie->fetch(PDO::FETCH_NAMED);
            $correlativo = $serie['correlativo'] + 1;
            echo $correlativo;
            break;

        case 'CONSULTA_DNI':
            $dni = $_POST['dni'];
            $api = "https://consultaruc.win/api/dni/$dni?format=json";

            $header = array();

            $ch = curl_init();
            //Asignamos valores
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1 );
            curl_setopt($ch, CURLOPT_URL, $api );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            $datos = curl_exec($ch);
            curl_close($ch);
            echo $datos;
            break;

        case 'CONSULTA_RUC':
            $ruc = $_POST['ruc'];
            $api = "https://consultaruc.win/api/ruc/$ruc?format=json";

            $header = array();

            $ch = curl_init();
            //Asignamos valores
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1 );
            curl_setopt($ch, CURLOPT_URL, $api );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            $datos = curl_exec($ch);
            curl_close($ch);
            echo $datos;
            break;

        case 'BUSCAR_PRODUCTO':
            $productos = $objCompartido->listarProducto($_POST['filtro']);
            $productos = $productos->fetchAll(PDO::FETCH_NAMED);
            $productos = array(
                'productos' => $productos
            );
            echo json_encode($productos);
            break;

        case 'ADD_PRODUCTO':

            //CARRITO - INICIO
            $producto = $objCompartido->obtenerProducto($_POST['codigo']);
            $producto = $producto->fetch(PDO::FETCH_NAMED);

            $cantidad_agregar = 1;

            if(isset($_POST['precio'])){
                $producto['precio'] = $_POST['precio'];
            }

            if(isset($_POST['cantidad'])){
                $cantidad_agregar = $_POST['cantidad'];
            }

            session_start();
            if(!isset($_SESSION['carrito'])){
                $_SESSION['carrito'] = array();
            }

            $carrito = $_SESSION['carrito'];

            $item = count($carrito) + 1;
            $cantidad = $cantidad_agregar;
            $existe = false;

            foreach ($carrito as $key => $value) {
                if ($value['codigo'] == $_POST['codigo']) {
                    $item = $key;
                    $existe = true;
                    break;
                }
            }

            if(!$existe){
                $carrito[$item] = array(
                    'codigo'        =>  $producto['codigo'],
                    'nombre'        =>  $producto['nombre'],
                    'precio'        =>  $producto['precio'],
                    'unidad'        =>  $producto['unidad'],
                    'codigoafectacion'  =>  $producto['codigoafectacion'],
                    'cantidad'      =>  $cantidad
                );
            }else{
                $carrito[$item]['cantidad'] = $carrito[$item]['cantidad'] + $cantidad_agregar;
            }

            $_SESSION['carrito'] = $carrito;
            //CARRITO - FIN

            //-------------- INICIO DE CALCULO DE TOTALES -------//
            $op_gravadas=0.00;
            $op_exoneradas=0.00;
            $op_inafectas=0.00;
            $igv=0.0;
            $igv_porcentaje=0.18;

            foreach ($carrito as $K => $v) {
                if($v['codigoafectacion']=='10'){
                    $op_gravadas = $op_gravadas+$v['precio']*$v['cantidad'];
                }

                if($v['codigoafectacion']=='20'){
                    $op_exoneradas = $op_exoneradas+$v['precio']*$v['cantidad'];
                }

                if($v['codigoafectacion']=='30'){
                    $op_inafectas = $op_inafectas+$v['precio']*$v['cantidad'];
                }												
            }

            $igv = $op_gravadas * $igv_porcentaje;

            $total = $op_gravadas + $op_exoneradas + $op_inafectas + $igv;

            //----- FIN DEL CALCULO DE TOTALES --------//

            //------ INICIO DE LA TABLITA DEL CARRITO ---- //

            echo "<table class='table table-bordered table-hover'>";
            echo "<tr>";
            echo "<th>ITEM</th><th>CANT</th><th>UND</th><th>PRODUCTO</th><th>VU</th><th>SUBT</th>";
            echo "</tr>";
            foreach($carrito as $k=>$v){
                echo "<tr>";
                echo "<td>".$k."</td><td>".$v['cantidad']."</td><td>".$v['unidad']."</td><td>".$v['nombre']."</td><td>".$v['precio']."</td><td>".($v['precio']*$v['cantidad'])."</td>";
                echo "</tr>";
            }

            echo "<tr><td colspan='5' align='right'>OP. GRAVADAS</td><td>".$op_gravadas."</td></tr>";
            echo "<tr><td colspan='5' align='right'>IGV(18%)</td><td>".$igv."</td></tr>";			
            echo "<tr><td colspan='5' align='right'>OP. EXONERADAS</td><td>".$op_exoneradas."</td></tr>";
            echo "<tr><td colspan='5' align='right'>OP. INAFECTAS</td><td>".$op_inafectas."</td></tr>";						
            echo "<tr><td colspan='5' align='right'><b>TOTAL</b></td><td><b>".$total."</b></td></tr>";		
            echo "</table>";
            //------------ FIN DE LA TABLITA DEL CARRITO ------//

            break;
  
        case 'CANCELAR_CARRITO':
            session_start();
            session_destroy();
            break;

        case 'GUARDAR_VENTA':
            session_start();

            //logica de ventas
            //--------------------------
            //fin logica de ventas


            //INICIO PROCESO FACTURACION

            //$generadoXML = new Funciones();

            //obtenemos los datos del emisor de la BD
            $idemisor = $_POST['idemisor'];
            $emisor = $objEmisor->obtenerEmisor($idemisor);
            $emisor = $emisor->fetch(PDO::FETCH_NAMED);


            $cliente = array(
                'tipodoc'		=> $_POST['tipodoc'],//6->ruc, 1-> dni 
                'ruc'			=> $_POST['nrodoc'], 
                'razon_social'  => $_POST['razon_social'], 
                'direccion'		=> $_POST['direccion'],
                'pais'			=> 'PE'
                );	

            $cliente_existe = $objCliente->consultarCliente($_POST['nrodoc']);

            if($cliente_existe->rowCount()>0){
                $cliente_existe = $cliente_existe->fetch(PDO::FETCH_NAMED);
            }else{
                $objCliente->insertarCliente($cliente);
                $cliente_existe = $objCliente->consultarCliente($_POST['nrodoc']);
                $cliente_existe = $cliente_existe->fetch(PDO::FETCH_NAMED);
            }
            $idcliente = $cliente_existe['id'];

            $carrito = $_SESSION['carrito'];
            $detalle = array();
            $igv_porcentaje = 0.18;

            $op_gravadas=0.00;
            $op_exoneradas=0.00;
            $op_inafectas=0.00;
            $igv = 0;

            foreach ($carrito as $k => $v){

                $producto = $objCompartido->obtenerProducto($v['codigo']);
                $producto = $producto->fetch(PDO::FETCH_NAMED);

                $afectacion = $objCompartido->obtenerRegistroAfectacion($producto['codigoafectacion']);
                $afectacion = $afectacion->fetch(PDO::FETCH_NAMED);

                $igv_detalle =0;
                $factor_porcentaje = 1;
                if($producto['codigoafectacion']==10){
                    $igv_detalle = $v['precio']*$v['cantidad']*$igv_porcentaje;
                    $factor_porcentaje = 1+ $igv_porcentaje;
                }

                $itemx = array(
                    'item' 				=> $k,
                    'codigo'			=> $v['codigo'],
                    'descripcion'		=> $v['nombre'],
                    'cantidad'			=> $v['cantidad'],
                    'valor_unitario'	=> $v['precio'],
                    'precio_unitario'	=> $v['precio']*$factor_porcentaje,
                    'tipo_precio'		=> $producto['tipo_precio'], //ya incluye igv
                    'igv'				=> $igv_detalle,
                    'porcentaje_igv'	=> $igv_porcentaje*100,
                    'valor_total'		=> $v['precio']*$v['cantidad'],
                    'importe_total'		=> $v['precio']*$v['cantidad']*$factor_porcentaje,
                    'unidad'			=> $v['unidad'],//unidad,
                    'tipo_afectacion_igv'	=> $producto['codigoafectacion'],
                    'codigo_tipo_tributo'	=> $afectacion['codigo_afectacion'],
                    'nombre_tributo'	=> $afectacion['nombre_afectacion'],
                    'tipo_tributo'	=> $afectacion['tipo_afectacion'],
                    'bolsa_plastica'            =>  'NO'		 
                );

                $itemx;

                $detalle[] = $itemx;

                if($itemx['tipo_afectacion_igv']==10){
                    $op_gravadas = $op_gravadas + $itemx['valor_total'];
                }

                if($itemx['tipo_afectacion_igv']==20){
                    $op_exoneradas = $op_exoneradas + $itemx['valor_total'];
                }				

                if($itemx['tipo_afectacion_igv']==30){
                    $op_inafectas = $op_inafectas + $itemx['valor_total'];
                }

                $igv = $igv + $igv_detalle;				
            }


            $total = $op_gravadas + $op_exoneradas + $op_inafectas + $igv;

            $idserie = $_POST['idserie'];

            $seriex = $objCompartido->obtenerSerie($idserie);
            $seriex = $seriex->fetch(PDO::FETCH_NAMED);

            $monto_pendiente = 0;
            if ($_POST['forma_pago'] == 'Credito') {
                $monto_pendiente = $_POST['monto_pendiente'];
            }

            $comprobante =	array(
                    'tipodoc'		=> $_POST['tipocomp'],
                    'idserie'		=> $idserie,
                    'serie'			=> $seriex['serie'],
                    'correlativo'	=> $seriex['correlativo']+1,
                    'fecha_emision' => $_POST['fecha_emision'],
                    'moneda'		=> $_POST['moneda'], //PEN->SOLES; USD->DOLARES
                    'total_opgravadas'	=> $op_gravadas,
                    'igv'			=> $igv,
                    'total_opexoneradas' => $op_exoneradas,
                    'total_opinafectas'	=> $op_inafectas,
                    'total_impbolsas'               =>  0,
                    'total'			=> $total,
                    'total_texto'	=> CantidadEnLetra($total),
                    'codcliente'	=> $idcliente,
                    'forma_pago'           =>  $_POST['forma_pago'], //contado, credito,
                    'monto_pendiente'       =>  $monto_pendiente
                );
            
            if ($_POST['forma_pago'] == 'Credito') {
                $nrocuotas = $_POST['cuotas'];

                $cuotas = array();

                for ($i = 1; $i <= $nrocuotas ; $i++) { 
                    $cuotas[] = array(
                        'cuota'     => 'Cuota' . str_pad($i, 3, "0", STR_PAD_LEFT),
                        'monto'     =>  $_POST['txtMonto' . $i],
                        'fecha'     =>  $_POST['txtFecha' . $i]
                    );
                }
            }else{
                $cuotas = null;
            }

            $objCompartido->actualizarSerie($idserie, $comprobante['correlativo']);

            $nombre = $emisor['ruc'].'-'.$comprobante['tipodoc'].'-'.$comprobante['serie'].'-'.$comprobante['correlativo'];

            $ruta = "../xml/";

            if($comprobante['tipodoc']=='01'){
                $generadorXML->CrearXMLFactura($ruta.$nombre, $emisor, $cliente, $comprobante, $detalle, $cuotas);
            }elseif($comprobante['tipodoc']=='03'){
                $generadorXML->CrearXMLFactura($ruta.$nombre, $emisor, $cliente, $comprobante, $detalle);
            }
            
            $api->EnviarComprobanteElectronico($emisor,$nombre,"../","../xml/","../cdr/");
            //FIN FACTURACION ELECTRONICA


            //REGISTRO EN BASE DE DATOS

            $objVenta->insertarVenta($idemisor, $comprobante);
            $venta = $objVenta->obtenerUltimoComprobanteId();
            $venta = $venta->fetch(PDO::FETCH_NAMED);

            $objVenta->insertarDetalle($venta['id'],$detalle);

            //FIN DE REGISTRO EN BASE DE DATOS
            echo "<br/>VENTA CORRECTA";
            session_destroy(); // elimina sesion blanquea el carrito

            //MODO DE IMPRESION INICIO
            echo "<script>window.open('./apifacturacion/pdfFacturaElectronica.php?id=".$venta['id']."','_blank')</script>";	
            //MODO DE IMPRESION FIN

            break;
           default:
            # code...
            break;
    }
}

?>