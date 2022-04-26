<?php

try {
    $manejador = 'mysql';
    $servidor = 'localhost';
    $base = 'facturacion29';
    $usuario = 'root';
    $pass = '';

    $cadena = "$manejador:host=$servidor;dbname=$base";

    $cnx = new PDO($cadena, $usuario, $pass, array(
        PDO::ATTR_PERSISTENT => TRUE,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
    ));

    // //CRUD DE CLIENTES

    // //INSERT
    // $query = 'INSERT INTO Cliente(tipodoc, nrodoc, razon_social, direccion)
    //             VALUES(:tipodoc, :nrodoc, :razon_social, :direccion)';
    // $parametros = array(
    //     ':tipodoc'  =>  0,
    //     ':nrodoc'   =>  '0000',
    //     ':razon_social' => 'CLIENTE POR DEFECTO',
    //     ':direccion' => 'CIX'
    // );

    // $pre = $cnx->prepare($query);
    // $pre->execute($parametros);

    // echo 'Cliente registrado';


    // //SELECT
    // $query = 'SELECT * FROM CLIENTE';
    // $res = $cnx->query($query);
    // $res = $res->fetchAll(PDO::FETCH_NAMED);

    // foreach ($res as $key => $value) {
    //     echo '</br> RUC/DNI: ' . $value['nrodoc'] . ' NOMBRE/RAZON SOCIAL: ' . $value['razon_social'];
    // }

} catch (\Throwable $th) {
    throw $th;
}

?>