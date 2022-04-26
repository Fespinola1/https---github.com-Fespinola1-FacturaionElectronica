<?php

class ApiFacturacion
{
    public function EnviarComprobanteElectronico($emisor, $nombreXML, $ruta_certificado = '', $ruta_archivo_xml = 'xml/', $ruta_archivo_cdr = 'cdr/')
    {
        //PARTE II - EMSION DE FE
        echo '</br> PARTE II';
        //PASO 01 - FIRMAR EL XML
        require_once('signature.php');
        $objFirma = new Signature();
        $flag_firma = 0; //posicion de la firma en el xml
        $ruta = $ruta_archivo_xml . $nombreXML . '.XML';
        $ruta_firma = $ruta_certificado . 'certificado_prueba_sunat.pfx';
        $pass_firma = 'ceti';

        $objFirma->signature_xml($flag_firma, $ruta, $ruta_firma, $pass_firma);
        echo '</br> - PASO 01: XML FIRMADO DIGITALMENTE';
        //PASO 01 - FIN

        //PASO 02 - COMPRIMIR EN FORMATO ZIP
        $zip = new ZipArchive();
        $nombreZip = $nombreXML . '.ZIP';
        $ruta_zip = $ruta_archivo_xml . $nombreXML . '.ZIP';

        if ($zip->open($ruta_zip, ZipArchive::CREATE) == true) {
            $zip->addFile($ruta, $nombreXML . '.XML');
            $zip->close();
        }
        echo '</br> - POSO 02: XML ZIPEADO';
        //PASO 02 - FIN

        //PASO 03 - CODIFICAR EN BASE 64 - INICIO
        $ruta_archivo = $ruta_zip;
        $nombre_archivo = $nombreZip;

        $contenido_del_zip = base64_encode(file_get_contents($ruta_archivo));
        echo '</br> - PASO 03: XML/ZIP CODIFICADO EN BASE 64 '; // . $contenido_del_zip;
        //PASO 03 - FIN

        //PASO 04 - CONSUMIR WEB SERVICES - API DE SUNAT - INICIO
        $ws = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService'; //ruta beta de sunat
        //$ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService'; //ruta de produccion @cambio_prod

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasisopen.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                    <soapenv:Header>
                        <wsse:Security>
                            <wsse:UsernameToken>
                                <wsse:Username>' . $emisor['ruc'] . $emisor['usuario_secundario'] . '</wsse:Username>
                                <wsse:Password>' . $emisor['clave_usuario_secundario'] . '</wsse:Password>
                            </wsse:UsernameToken>
                        </wsse:Security>
                    </soapenv:Header>
                    <soapenv:Body>
                        <ser:sendBill>
                            <fileName>' . $nombre_archivo . '</fileName>
                            <contentFile>' . $contenido_del_zip . '</contentFile>
                        </ser:sendBill>
                    </soapenv:Body>
                </soapenv:Envelope>';

        //incializamos 
        $ch = curl_init();

        //seteamos valores
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);

        //ejecutamos y obtenemos rpta
        $respuesta = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        echo '</br> - PASO 04 CONSUMIMOS WS DE SUNAT METODO SENDBILL';

        //PAS0 04 - FIN

        //PASO 05 - RPTA DE SUNAT - INICIO

        $estado_fe = 0; //0: XML NO SE ENVIA AUN, 1: OK CDR, 2: RECHAZO, 3: PROBLEMA CONEXION

        if ($httpcode == 200) { //ok hubo rpta
            $doc = new DOMDocument();
            $doc->loadXML($respuesta);

            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {
                $cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
                echo '</br> - PASO 05: SE OBTUVO RPTA DE SUNAT';

                $cdr = base64_decode($cdr);
                echo '</br> - PASO 06: CDR DECODIFICADO: OBTENEMOS EL ZIP';

                file_put_contents($ruta_archivo_cdr . 'R-' . $nombreZip, $cdr); //El CDR de memoria a disco

                $zip = new ZipArchive();
                if ($zip->open($ruta_archivo_cdr . 'R-' . $nombreZip) == true) {
                    $zip->extractTo($ruta_archivo_cdr);
                    $zip->close();
                    echo '</br> - PASO 07: COPIADO A DISCO Y DESCOMPRIMIDO';

                    $estado_fe = 1;
                    echo '</br> - PASO 08: PROCESO TERMINADO';

                    $xml_cdr = $ruta_archivo_cdr . 'R-' . $nombreXML . '.XML';
                    $doc_cdr = new DOMDocument();
                    $doc_cdr->loadXML(file_get_contents($xml_cdr));
                    $msje1 = ''; //muestra observaciones
                    $msje2 = ''; // muestra la aprobacion

                    if (isset($doc_cdr->getElementsByTagName('Note')->item(0)->nodeValue)) {
                        $msje1 = $doc_cdr->getElementsByTagName('Note')->item(0)->nodeValue;
                    }

                    if (isset($doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue)) {
                        $msje2 = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                    }

                    echo '</br>  ' . $msje1;
                    echo '</br>  ' . $msje2;
                }
            }
            else{
                $estado_fe = 2;
                $codigo = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                echo '</br> ERROR: ' . $mensaje . ' </br> CODIGO: ' . $codigo;
            }
        }
        else{
            $estado_fe = 3;
            echo curl_error($ch);
            echo '</br> PROBLEMAS DE CONEXION: ' . $respuesta;
        }

        curl_close($ch);

        //PASO 05 -FIN
    }

    public function EnviarResumenComprobantes($emisor, $nombreXML, $ruta_certificado = '', $ruta_archivo_xml = 'xml/', $ruta_archivo_cdr = 'cdr/')
    {
        require_once('signature.php');
        $objFirma = new Signature();
        $flag_firma = 0;
        $ruta = $ruta_archivo_xml . $nombreXML . '.XML';
        $ruta_firma = $ruta_certificado . 'certificado_prueba_sunat.pfx';
        $pass_firma = 'ceti';

        $objFirma->signature_xml($flag_firma, $ruta, $ruta_firma, $pass_firma);
        echo '</br> PASO 02: XML FIRMADO DIGITALMENTE';

        $zip = new ZipArchive();
        $nombreZip = $nombreXML . '.ZIP';
        $ruta_zip = $ruta_archivo_xml . $nombreXML . '.ZIP';

        if ($zip->open($ruta_zip, ZipArchive::CREATE) == TRUE) {
            $zip->addFile($ruta, $nombreXML . '.XML');
            $zip->close();
        }
        echo '</br> PASO 03: XML COMPRIMIDO EN FORMATO ZIP';

        $ruta_archivo = $ruta_zip;
        $nombre_archivo = $nombreZip;
        $contenido_del_zip = base64_encode(file_get_contents($ruta_archivo));
        echo '</br> PASO 04: ZIP CODIFICADO EN BASE64';


        //PASO 04 - CONSUMIR WEB SERVICES - API DE SUNAT - INICIO
        $ws = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService'; //ruta beta de sunat
        //$ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService'; //ruta de produccion @cambio_prod

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasisopen.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                    <soapenv:Header>
                        <wsse:Security>
                            <wsse:UsernameToken>
                                <wsse:Username>' . $emisor['ruc'] . $emisor['usuario_secundario'] . '</wsse:Username>
                                <wsse:Password>' . $emisor['clave_usuario_secundario'] . '</wsse:Password>
                            </wsse:UsernameToken>
                        </wsse:Security>
                    </soapenv:Header>
                    <soapenv:Body>
                        <ser:sendSummary>
                            <fileName>' . $nombre_archivo . '</fileName>
                            <contentFile>' . $contenido_del_zip . '</contentFile>
                        </ser:sendSummary>
                    </soapenv:Body>
                </soapenv:Envelope>';

        //incializamos 
        $ch = curl_init();

        //seteamos valores
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);

        //ejecutamos y obtenemos rpta
        $respuesta = curl_exec($ch);
        echo $respuesta;
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        echo '</br> - PASO 05 CONSUMIMOS WS DE SUNAT METODO SENDSUMMARY';

        //PAS0 04 - FIN

        $estado_fe = 0;
        $ticket = 0;

        if ($httpcode == 200) {
            $doc = new DOMDocument();
            $doc->loadXML($respuesta);

            if (isset($doc->getElementsByTagName('ticket')->item(0)->nodeValue)) {
                $ticket = $doc->getElementsByTagName('ticket')->item(0)->nodeValue;
                $estado_fe = 1;
                echo '</br> - PASO 06: NRO DE TICKET: ' . $ticket;
            }else{
                $estado_fe = 2;
                $codigo = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                echo '</br> ERROR EN FE: MENSAJE: ' . $mensaje . ' CODIGO: ' . $codigo;
            }
        }else{
            $estado_fe = 3;
            echo curl_error($ch);
            echo '</br> PROBLEMAS DE CONEXIÓN';
            echo '</br> ' . $respuesta;
        }
        curl_close($ch);

        return $ticket;
    }

    public function ConsultarTicket($emisor, $cabecera, $ticket, $ruta_archivo_cdr = 'cdr/')
    {
        $ws = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService'; //ruta beta de sunat
        //$ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService'; //ruta de produccion @cambio_prod

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasisopen.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                    <soapenv:Header>
                        <wsse:Security>
                            <wsse:UsernameToken>
                                <wsse:Username>' . $emisor['ruc'] . $emisor['usuario_secundario'] . '</wsse:Username>
                                <wsse:Password>' . $emisor['clave_usuario_secundario'] . '</wsse:Password>
                            </wsse:UsernameToken>
                        </wsse:Security>
                    </soapenv:Header>
                    <soapenv:Body>
                        <ser:getStatus>
                            <ticket>' . $ticket . '</ticket>
                        </ser:getStatus>
                    </soapenv:Body>
                </soapenv:Envelope>';

        //incializamos 
        $ch = curl_init();

        //seteamos valores
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);

        //ejecutamos y obtenemos rpta
        $respuesta = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        echo '</br> - PASO 07 CONSUMIMOS EL NRO DE TICKET';
        $nombre = $emisor['ruc'] . '-' . $cabecera['tipodoc'] . '-' . $cabecera['serie'] . '-' . $cabecera['correlativo'];
        $nombreZip = $nombre . '.ZIP';
        $estado_fe = 0;

        if ($httpcode == 200) { //ok hubo rpta
            $doc = new DOMDocument();
            $doc->loadXML($respuesta);

            if (isset($doc->getElementsByTagName('content')->item(0)->nodeValue)) {
                $cdr = $doc->getElementsByTagName('content')->item(0)->nodeValue;
                echo '</br> - PASO 05: SE OBTUVO RPTA DE SUNAT';

                $cdr = base64_decode($cdr);
                echo '</br> - PASO 06: CDR DECODIFICADO: OBTENEMOS EL ZIP';

                file_put_contents($ruta_archivo_cdr . 'R-' . $nombreZip, $cdr); //El CDR de memoria a disco

                $zip = new ZipArchive();
                if ($zip->open($ruta_archivo_cdr . 'R-' . $nombreZip) == true) {
                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $nombre . '.XML');
                    $zip->close();
                    echo '</br> - PASO 07: COPIADO A DISCO Y DESCOMPRIMIDO';

                    $estado_fe = 1;
                    echo '</br> - PASO 08: PROCESO TERMINADO';

                    $xml_cdr = $ruta_archivo_cdr . 'R-' . $nombre . '.XML';
                    $doc_cdr = new DOMDocument();
                    $doc_cdr->loadXML(file_get_contents($xml_cdr));
                    $msje1 = ''; //muestra observaciones
                    $msje2 = ''; // muestra la aprobacion

                    if (isset($doc_cdr->getElementsByTagName('Note')->item(0)->nodeValue)) {
                        $msje1 = $doc_cdr->getElementsByTagName('Note')->item(0)->nodeValue;
                    }

                    if (isset($doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue)) {
                        $msje2 = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                    }

                    echo '</br>  ' . $msje1;
                    echo '</br>  ' . $msje2;
                }
            }
            else{
                $estado_fe = 2;
                $codigo = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                echo '</br> ERROR: ' . $mensaje . ' </br> CODIGO: ' . $codigo;
            }
        }
        else{
            $estado_fe = 3;
            echo curl_error($ch);
            echo '</br> PROBLEMAS DE CONEXION: ' . $respuesta;
        }

        curl_close($ch);
    }
}

?>