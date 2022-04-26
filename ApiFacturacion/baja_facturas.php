<?php

$emisor = array(
    'tipodoc'                   =>  '6', //ruc: 6 https://cpe.sunat.gob.pe/sites/default/files/inline-files/anexoV-340-2017.pdf
    'ruc'                       =>  '20123456789',
    'razon_social'              =>  'CETI ORG',
    'nombre_comercial'          =>  'CETI',
    'direccion'                 =>  'VIRTUAL',
    'ubigeo'                    =>  '130101',
    'departamento'              =>  'LAMBAYEQUE',
    'provincia'                 =>  'CHICLAYO',
    'distrito'                  =>  'CHICLAYO',
    'pais'                      =>  'PE',
    'usuario_secundario'        =>  'MODDATOS', //es el usuario que permite conectarse a los web services de SUNAT
    'clave_usuario_secundario'  =>  'MODDATOS'
);

$cabecera = array(
    'tipodoc'                   =>  'RA', //RC: resumen de comprobantes, RA: resumen de anulaciones
    'serie'                     =>  date('Ymd'), //feche de envío
    'correlativo'               =>  1,
    'fecha_emision'             =>  date('Y-m-d'),
    'fecha_envio'               =>  date('Y-m-d')
);

$detalle = array();

$cant = 10;

for ($i=1; $i <= $cant ; $i++) { 
    $detalle[] = array(
        'item'          =>  $i,
        'tipodoc'       =>  '01',
        'serie'         =>  'F002',
        'correlativo'   =>  $i,
        'motivo'        =>  'ERROR EN EL DOCUMENTO'
    );
}

//PASO 01 - CREAR EL XML DE RESUMEN DE ANULACIONES
require_once('api_GeneradorXML.php');
$objXML = new GeneradorXML();
$nombreXML = $emisor['ruc'] . '-' . $cabecera['tipodoc'] . '-' . $cabecera['serie'] . '-' . $cabecera['correlativo'];
$rutaXML = 'xml/';

$objXML->CrearXmlBajaDocumentos($emisor, $cabecera, $detalle, $rutaXML . $nombreXML);
echo '</br> PASO 01: XML DE RESUMEN DE ANULACIONES CREADO';

//PASO 02: LLAMAR AL API FACTURACION
require_once('api_Facturacion.php');
$objAPI = new ApiFacturacion();
$ticket = $objAPI->EnviarResumenComprobantes($emisor, $nombreXML);

if($ticket>0){
    $objAPI->ConsultarTicket($emisor, $cabecera, $ticket);
}

?>