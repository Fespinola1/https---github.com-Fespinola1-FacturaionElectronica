<?php

//SERIE BOLETA     03 B001
//SERIE NCBOLETA   07 BNC1


//SERIE FACTURA    01 F001
//SERIE NCBOLETA   07 FNC1


$emisor = array(
    'tipodoc'               =>  '6',
    'ruc'                   =>  '20123456789',
    'razon_social'          =>  'CETI ORG',
    'nombre_comercial'      =>  'CETI',
    'direccion'             =>  'VIRTUAL',
    'ubigeo'                =>  '130101',
    'departamento'          =>  'LAMBAYEQUE',
    'provincia'             =>  'CHICLAYO',
    'distrito'              =>  'CHICLAYO',
    'pais'                  =>  'PE',
    'usuario_secundario'    =>  'MODDATOS', //Usuario para conectarnos, autenticarnos con los servicios web de SUNAT
    'clave_usuario_secundario'  =>  'MODDATOS'
);

$cliente = array(
    'tipodoc'               =>  '6',
    'ruc'                   =>  '10123456789',
    'razon_social'          =>  'CLIENTE DE PRUEBA',
    'direccion'             =>  'VIRTUAL',
    'pais'                  =>  'PE'
);

$comprobante = array(
    'tipodoc'               =>  '08',
    'serie'                 =>  'FND1',
    'correlativo'           =>  '4',
    'fecha_emision'         =>  date('Y-m-d'),
    'hora'                  =>  '00:00:00',
    'fecha_vencimiento'     =>  date('Y-m-d'),
    'moneda'                =>  'PEN',
    'total_opgravadas'      =>  0,
    'total_opexoneradas'    =>  0,
    'total_opinafectas'     =>  0,
    'total_impbolsas'       =>  0,
    'igv'                   =>  0,
    'total'                 =>  0,
    'total_texto'           =>  '',

    'tipodoc_ref'           =>  '01',
    'serie_ref'             =>  'F001',
    'correlativo_ref'       =>  '123',
    'codmotivo'             =>  '02',
    'descripcion'           =>  'AUMENTO EN EL VALOR'

);

$detalle = array(
    array(
        'item'                      =>  1,
        'codigo'                    =>  'COD01',
        'descripcion'               =>  'LAPTOP',
        'cantidad'                  =>  1,
        'valor_unitario'            =>  1016.95, //no incluye IGV
        'precio_unitario'           =>  1200, //incluye igv=18%
        'tipo_precio'               =>  '01',
        'igv'                       =>  183.05,
        'porcentaje_igv'            =>  18,
        'valor_total'               =>  1016.95, //cantidad * valor unitario
        'importe_total'             =>  1200, //cantidad * precio unitario
        'unidad'                    =>  'NIU',
        'tipo_afectacion_igv'       =>  '10', //Gravadas:10, Exoneradas: 20, Inafectas: 30
        'codigo_tipo_tributo'       =>  '1000',
        'tipo_tributo'              =>  'VAT',
        'nombre_tributo'            =>  'IGV',
        'bolsa_plastica'            =>  'NO'
    ),
    array(
        'item'                      =>  2,
        'codigo'                    =>  'COD02',
        'descripcion'               =>  'LIBRO COQUITO',
        'cantidad'                  =>  1,
        'valor_unitario'            =>  35, //no incluye IGV
        'precio_unitario'           =>  35, //incluye igv=0%
        'tipo_precio'               =>  '01',
        'igv'                       =>  0,
        'porcentaje_igv'            =>  0,
        'valor_total'               =>  35, //cantidad * valor unitario
        'importe_total'             =>  35, //cantidad * precio unitario
        'unidad'                    =>  'NIU',
        'tipo_afectacion_igv'       =>  '20', //Gravadas:10, Exoneradas: 20, Inafectas: 30
        'codigo_tipo_tributo'       =>  '9997',
        'tipo_tributo'              =>  'VAT',
        'nombre_tributo'            =>  'EXO',
        'bolsa_plastica'            =>  'NO'
    ),
    array(
        'item'                      =>  3,
        'codigo'                    =>  'COD03',
        'descripcion'               =>  'SANDIA',
        'cantidad'                  =>  1,
        'valor_unitario'            =>  8, //no incluye IGV
        'precio_unitario'           =>  8, //incluye igv=0%
        'tipo_precio'               =>  '01',
        'igv'                       =>  0,
        'porcentaje_igv'            =>  0,
        'valor_total'               =>  8, //cantidad * valor unitario
        'importe_total'             =>  8, //cantidad * precio unitario
        'unidad'                    =>  'NIU',
        'tipo_afectacion_igv'       =>  '30', //Gravadas:10, Exoneradas: 20, Inafectas: 30
        'codigo_tipo_tributo'       =>  '9998',
        'tipo_tributo'              =>  'FRE',
        'nombre_tributo'            =>  'INA',
        'bolsa_plastica'            =>  'NO'
    ),
    // array(
    //     'item'                      =>  4,
    //     'codigo'                    =>  'COD04',
    //     'descripcion'               =>  'BOLSA PLASTICA',
    //     'cantidad'                  =>  4,
    //     'valor_unitario'            =>  0.05, //no incluye IGV
    //     'precio_unitario'           =>  0.059, //incluye igv=18%
    //     'tipo_precio'               =>  '01',
    //     'igv'                       =>  0.04,
    //     'porcentaje_igv'            =>  18,
    //     'valor_total'               =>  0.20, //cantidad * valor unitario
    //     'importe_total'             =>  0.80, //cantidad * precio unitario * factor (0.40)
    //     'unidad'                    =>  'NIU',
    //     'tipo_afectacion_igv'       =>  '10', //Gravadas:10, Exoneradas: 20, Inafectas: 30
    //     'codigo_tipo_tributo'       =>  '1000',
    //     'tipo_tributo'              =>  'VAT',
    //     'nombre_tributo'            =>  'IGV',
    //     'bolsa_plastica'            =>  'SI'
    // )
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
$objXML->CrearXMLNotaDebito($rutaXML, $emisor, $cliente, $comprobante, $detalle);
echo '</br> PARTE I </br> - XML DE NOTA DE DEBITO CREADO';

require_once('api_Facturacion.php');
$obj_ApiFac = new ApiFacturacion();
$obj_ApiFac->EnviarComprobanteElectronico($emisor, $nombreXML);

//PARTE I - CREAR EL XML - FIN

?>