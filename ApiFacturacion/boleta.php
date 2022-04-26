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

$cliente = array(
    'tipodoc'                   =>  '1',
    'ruc'                       =>  '12345678',
    'razon_social'              =>  'PETER CASTLE',
    'direccion'                 =>  'CHOTA',
    'pais'                      =>  'PE'
);

$comprobante = array(
    'tipodoc'                   =>  '03',
    'serie'                     =>  'BXYZ',
    'correlativo'               =>  '1212',
    'fecha_emision'             =>  date('Y-m-d'),
    'hora'                      =>  '00:00:00',
    'fecha_vencimiento'         =>  date('Y-m-d'),
    'moneda'                    =>  'PEN',
    'total_opgravadas'          =>  0,
    'total_opexoneradas'        =>  0,
    'total_opinafectas'         =>  0,
    'total_impbolsas'           =>  0,
    'igv'                       =>  0,
    'total'                     =>  0,
    'total_texto'               =>  '',

    'forma_pago'                =>  '',
    'monto_pendiente'           =>  ''
);

$detalle = array(
    array(
        'item'                          =>  1,
        'codigo'                        =>  'COD100',
        'descripcion'                   =>  'BICICLETA GOLIAT 29 DOBLE AMORTIGUADOR',
        'cantidad'                      =>  1,
        'valor_unitario'                =>  1271.19, //no incluye igv
        'precio_unitario'               =>  1500, //incluye igv
        'tipo_precio'                   =>  '01',
        'igv'                           =>  228.81,
        'porcentaje_igv'                =>  18,
        'valor_total'                   =>  1271.19, //cantidad * valor unitario
        'importe_total'                 =>  1500,//cantidad * precio unitario
        'unidad'                        =>  'NIU',
        'tipo_afectacion_igv'           =>  '10', //Catálogo No. 07: Códigos de tipo de afectación del IGV //gravados:10 (igv=18%, afectos), exonerados: 20 (igv=0), inafectos: 30(igv=0)
        'codigo_tipo_tributo'           =>  '1000', //Catálogo No. 05: Códigos de tipos de tributos
        'tipo_tributo'                  =>  'VAT',
        'nombre_tributo'                =>  'IGV',
        'bolsa_plastica'                =>  'NO'
    ),
    array(
        'item'                          =>  2,
        'codigo'                        =>  'COD234',
        'descripcion'                   =>  'LIBRO DE MATEMATICA',
        'cantidad'                      =>  3,
        'valor_unitario'                =>  50, // igv = 0
        'precio_unitario'               =>  50, // igv = 0
        'tipo_precio'                   =>  '01',
        'igv'                           =>  0,
        'porcentaje_igv'                =>  18,
        'valor_total'                   =>  150, //cantidad * valor unitario
        'importe_total'                 =>  150,//cantidad * precio unitario
        'unidad'                        =>  'NIU',
        'tipo_afectacion_igv'           =>  '20', //Catálogo No. 07: Códigos de tipo de afectación del IGV //gravados:10 (igv=18%, afectos), exonerados: 20 (igv=0), inafectos: 30(igv=0)
        'codigo_tipo_tributo'           =>  '9997', //Catálogo No. 05: Códigos de tipos de tributos
        'tipo_tributo'                  =>  'VAT',
        'nombre_tributo'                =>  'EXO',
        'bolsa_plastica'                =>  'NO'
    ),
    array(
        'item'                          =>  3,
        'codigo'                        =>  'COD890',
        'descripcion'                   =>  'MANZANA ROJA IMPORTADA USA',
        'cantidad'                      =>  12,
        'valor_unitario'                =>  1.50, // igv = 0
        'precio_unitario'               =>  1.50, // igv = 0
        'tipo_precio'                   =>  '01',
        'igv'                           =>  0,
        'porcentaje_igv'                =>  18,
        'valor_total'                   =>  18, //cantidad * valor unitario
        'importe_total'                 =>  18,//cantidad * precio unitario
        'unidad'                        =>  'NIU',
        'tipo_afectacion_igv'           =>  '30', //Catálogo No. 07: Códigos de tipo de afectación del IGV //gravados:10 (igv=18%, afectos), exonerados: 20 (igv=0), inafectos: 30(igv=0)
        'codigo_tipo_tributo'           =>  '9998', //Catálogo No. 05: Códigos de tipos de tributos
        'tipo_tributo'                  =>  'FRE',
        'nombre_tributo'                =>  'INA',
        'bolsa_plastica'                =>  'NO'
    ),
    // array(
    //     'item'                          =>  4,
    //     'codigo'                        =>  'CODBOL',
    //     'descripcion'                   =>  'BOLSA PLASTICA',
    //     'cantidad'                      =>  4,
    //     'valor_unitario'                =>  0.05, // igv = 0
    //     'precio_unitario'               =>  0.059, // igv = 0
    //     'tipo_precio'                   =>  '01',
    //     'igv'                           =>  0.04,
    //     'porcentaje_igv'                =>  18,
    //     'valor_total'                   =>  0.20, //cantidad * valor unitario
    //     'importe_total'                 =>  0.80,//cantidad * precio unitario
    //     'unidad'                        =>  'NIU',
    //     'tipo_afectacion_igv'           =>  '10', //Catálogo No. 07: Códigos de tipo de afectación del IGV //gravados:10 (igv=18%, afectos), exonerados: 20 (igv=0), inafectos: 30(igv=0)
    //     'codigo_tipo_tributo'           =>  '1000', //Catálogo No. 05: Códigos de tipos de tributos
    //     'tipo_tributo'                  =>  'VAT',
    //     'nombre_tributo'                =>  'IGV',
    //     'bolsa_plastica'                =>  'SI' //ICBPER
    // ),
    array(
        'item'                          =>  5,
        'codigo'                        =>  'PAN',
        'descripcion'                   =>  'PAN EXONERADO POR LAPICITO',
        'cantidad'                      =>  4,
        'valor_unitario'                =>  0.40, // igv = 0
        'precio_unitario'               =>  0.40, // igv = 0
        'tipo_precio'                   =>  '01', //Catálogo No. 16: Códigos – Tipo de precio de venta unitario
        'igv'                           =>  0,
        'porcentaje_igv'                =>  18,
        'valor_total'                   =>  1.60, //cantidad * valor unitario
        'importe_total'                 =>  1.60,//cantidad * precio unitario
        'unidad'                        =>  'NIU', //Catálogo No. 03: Códigos de tipo de unidad de medida comercial, ZZ=UNIDAD SERVICIOS
        'tipo_afectacion_igv'           =>  '20', //Catálogo No. 07: Códigos de tipo de afectación del IGV //gravados:10 (igv=18%, afectos), exonerados: 20 (igv=0), inafectos: 30(igv=0)
        'codigo_tipo_tributo'           =>  '9997', //Catálogo No. 05: Códigos de tipos de tributos
        'tipo_tributo'                  =>  'VAT',
        'nombre_tributo'                =>  'EXO',
        'bolsa_plastica'                =>  'NO' //ICBPER
    ),
);

//inicializar variables
$total_opgravadas = 0;
$total_opexoneradas = 0;
$total_opinafectas = 0;
$total_impbolsas = 0;
$igv = 0;
$total = 0;

foreach ($detalle as $key => $value) {
    if ($value['tipo_afectacion_igv'] == 10) {
        $total_opgravadas += $value['valor_total'];
    }
    if ($value['tipo_afectacion_igv'] == 20) {
        $total_opexoneradas += $value['valor_total'];
    }
    if ($value['tipo_afectacion_igv'] == 30) {
        $total_opinafectas += $value['valor_total'];
    }
    if ($value['bolsa_plastica'] == 'SI') {
        $total_impbolsas += $value['cantidad'] * 0.40;
    }

    $igv += $value['igv'];
    $total += $value['importe_total'] + $total_impbolsas;
}

$comprobante['total_opgravadas'] = $total_opgravadas;
$comprobante['total_opexoneradas'] = $total_opexoneradas;
$comprobante['total_opinafectas'] = $total_opinafectas;
$comprobante['igv'] = $igv;
$comprobante['total'] = $total;
$comprobante['total_impbolsas'] = $total_impbolsas;

require_once('cantidad_en_letras.php');
$comprobante['total_texto'] = CantidadEnLetra($total);


//PARTE I - CREAR EL XML - INICIO
require_once('api_GeneradorXML.php');
$objXML = new GeneradorXML();
$nombreXML = $emisor['ruc'] . '-' . $comprobante['tipodoc'] . '-' . $comprobante['serie'] . '-' . $comprobante['correlativo'];
$rutaXML = 'xml/' . $nombreXML;
$objXML->CrearXMLFactura($rutaXML, $emisor, $cliente, $comprobante, $detalle);
echo '</br> PARTE I </br> - XML DE BOLETA ELECTRÓNICA CREADO';

require_once('api_Facturacion.php');
$obj_ApiFac = new ApiFacturacion();
$obj_ApiFac->EnviarComprobanteElectronico($emisor, $nombreXML);

//PARTE I - CREAR EL XML - FIN

?>