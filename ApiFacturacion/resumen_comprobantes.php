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
    'tipodoc'                   =>  'RC', //RC: resumen de comprobantes, RA: resumen de anulaciones
    'serie'                     =>  date('Ymd'), //feche de envío
    'correlativo'               =>  1,
    'fecha_emision'             =>  date('Y-m-d'),
    'fecha_envio'               =>  date('Y-m-d')
);

$detalle = array();

$cant = 500;

for ($i=1; $i <=$cant ; $i++) { 
    $item_total = rand(10, 800);
    $item_valor = $item_total / 1.18;
    $item_valor = (float) number_format($item_valor, 2, '.', 1);
    $item_igv = $item_total - $item_valor;

    $detalle[] = array(
        'item'              =>  $i,
        'tipodoc'           =>  '03',
        'serie'             =>  'B001',
        'correlativo'       =>  $i,
        'tipodoci'          =>  '1',//tipo de documento de identidad del cliente 1: DNI, 6:RUC
        'numdoci'           =>  rand(10000000, 99999999),
        'condicion'         =>  rand(1, 3),
        'moneda'            =>  'PEN',
        'importe_total'     =>  $item_total,
        'valor_total'       =>  $item_valor,
        'igv_total'         =>  $item_igv,
        'tipo_total'        =>  '01',
        'codigo_afectacion' =>  '1000',
        'nombre_afectacion' =>  'IGV',
        'tipo_afectacion'   =>  'VAT'
    );

}

//PASO 01: CREAR EL XML
require_once('api_GeneradorXML.php');
$objXML = new GeneradorXML();

$nombreXML = $emisor['ruc'] . '-' . $cabecera['tipodoc'] . '-' . $cabecera['serie'] . '-' . $cabecera['correlativo'];
$rutaXML = 'xml/';

$objXML->CrearXMLResumenDocumentos($emisor, $cabecera, $detalle, $rutaXML . $nombreXML);
echo '</br> PASO 01: XML DE RESUMEN DIARIO CREADO';

//PASO 03 - CONSUMIR EL API FACTUARACION
require_once('api_Facturacion.php');
$objApi = new ApiFacturacion();
$ticket = $objApi->EnviarResumenComprobantes($emisor, $nombreXML);

if ($ticket > 0) {
    $objApi->ConsultarTicket($emisor, $cabecera, $ticket);
}

?>