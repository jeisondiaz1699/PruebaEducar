<?php
$opcion = '';
if (!empty($_GET['opcion'])) {
    $opcion = $_GET['opcion'];
} else {
    if (!empty($_POST['opcion'])) {
        $opcion = $_POST['opcion'];
    }
}

if (empty($opcion)) {
    die('Posible error');
}
session_start();

include 'Admin/ComparModelo.php';

$DB = new Conexion();
$DB->conectar();

switch ($opcion) {
    case '0000001':
        $respuesta = array('resultado' => 'Error indeterminado');
        if (empty($_POST['idUsuario'])) {
            $respuesta = array('resultado' => 'Error datos');
            echo json_encode($respuesta);
            die(0);
        }

        $DB->consulta('*', 'usuario', 'ID_USUARIO = ' . $_POST['idUsuario'], '');
        $respuesta = array();

        while ($datosUsuario = $DB->sigRegistro()) {

            $respuesta[] = array($datosUsuario['ID_USUARIO'], $datosUsuario['NOMBRE'], $datosUsuario['APELLIDO'], $datosUsuario['SALDO']);
        }

        echo json_encode($respuesta);
        die(0);

        break;
    case '0000002':
        $respuesta = array('resultado' => 'Error indeterminado');
        if (empty($_POST['idUsuario']) || empty($_POST['saldoAgregar'])) {
            $respuesta = array('resultado' => 'Error datos');
            echo json_encode($respuesta);
            die(0);
        }

        $saldoActual = $DB->Buscar('SALDO', 'usuario', 'ID_USUARIO = ' . $_POST['idUsuario'], '');

        $nuevoSaldo = $saldoActual['SALDO'] + $_POST['saldoAgregar'];

        $campos = array(
            'TIPO_DE_TRANSACCION',
            'FECHA_TRANSACCION',
            'CANTIDAD',
            'USUARIO_ID_USUARIO'
        );
        $fechaHora = date('Y-m-d H:i:s');
        $datos = array(
            'Aumento de saldo ' . $_POST['saldoAgregar'],
            $fechaHora,
            $_POST['saldoAgregar'],
            $_POST['idUsuario']
        );
        $idtransaccion = $DB->creaRegistro('transacciones', $campos, $datos);

        $campos = array(
            'SALDO'
        );
        $datos = array(
            $nuevoSaldo
        );
        $camCriterio = array(
            'ID_USUARIO'
        );
        $datCriterio = array(
            $_POST['idUsuario']
        );

        $actualizacionSaldo = $DB->actualizaRegistro('usuario', $campos, $datos, $datCriterio, $camCriterio);

        if ($idtransaccion > 0 && $actualizacionSaldo == true) {
            $respuesta = array('resultado' => 'Ok');
        } else {
            $respuesta = array('resultado' => 'Error creando');
        }

        echo json_encode($respuesta);
        die(0);
        break;
    case '0000003':
        $respuesta = array('resultado' => 'Error indeterminado');
        if (empty($_POST['idUsuario'])) {
            $respuesta = array('resultado' => 'Error datos');
            echo json_encode($respuesta);
            die(0);
        }

        $idUsuario = $_POST['idUsuario'];

        $saldo = $DB->buscar('SALDO', 'usuario', 'ID_USUARIO = ' . $idUsuario, '');


        $DB->consulta('ID_USUARIO, NOMBRE', 'usuario', 'ID_USUARIO <> ' . $idUsuario, '');
        $datosUsuarios = array();
        $a = 0;

        while ($usuarios = $DB->sigRegistro()) {
            $datosUsuarios[] = array($usuarios['ID_USUARIO'], $usuarios['NOMBRE']);
        }

        $respuesta['datosUsuarios'] = $datosUsuarios;
        $respuesta['saldo'] = $saldo;
        $respuesta['resultado'] = 'Ok';


        echo json_encode($respuesta);
        die(0);

        break;

    case '0000004':
        $respuesta = array('resultado' => 'Error indeterminado');
        if (empty($_POST['idUsuario']) || empty($_POST['transferencia']) || empty($_POST['idUsuarioEnvio'])) {
            $respuesta = array('resultado' => 'Error datos');
            echo json_encode($respuesta);
            die(0);
        }

        $saldoActual = $DB->Buscar('SALDO', 'usuario', 'ID_USUARIO = ' . $_POST['idUsuario'], '');

        $nuevoSaldo1 = $saldoActual['SALDO'] - $_POST['transferencia'];

        $saldoActual = $DB->Buscar('SALDO', 'usuario', 'ID_USUARIO = ' . $_POST['idUsuarioEnvio'], '');

        $nuevoSaldoUsuarioRecibe = $saldoActual['SALDO'] + $_POST['transferencia'];

        $campos = array(
            'TIPO_DE_TRANSACCION',
            'FECHA_TRANSACCION',
            'CANTIDAD',
            'USUARIO_ID_USUARIO'
        );
        $fechaHora = date('Y-m-d H:i:s');
        $datos = array(
            'Envio de saldo a usuario id ' . $_POST['idUsuarioEnvio'],
            $fechaHora,
            $_POST['transferencia'],
            $_POST['idUsuario']
        );
        $idtransaccion1 = $DB->creaRegistro('transacciones', $campos, $datos);

        $campos = array(
            'TIPO_DE_TRANSACCION',
            'FECHA_TRANSACCION',
            'CANTIDAD',
            'USUARIO_ID_USUARIO'
        );
        $fechaHora = date('Y-m-d H:i:s');
        $datos = array(
            'recivido de dinero del usuario ' . $_POST['idUsuario'],
            $fechaHora,
            $_POST['transferencia'],
            $_POST['idUsuarioEnvio']
        );
        $idtransaccion2 = $DB->creaRegistro('transacciones', $campos, $datos);

        $campos = array(
            'SALDO'
        );
        $datos = array(
            $nuevoSaldo1
        );
        $camCriterio = array(
            'ID_USUARIO'
        );
        $datCriterio = array(
            $_POST['idUsuario']
        );

        $actualizacionSaldo1 = $DB->actualizaRegistro('usuario', $campos, $datos, $datCriterio, $camCriterio);

        $campos = array(
            'SALDO'
        );
        $datos = array(
            $nuevoSaldoUsuarioRecibe
        );
        $camCriterio = array(
            'ID_USUARIO'
        );
        $datCriterio = array(
            $_POST['idUsuarioEnvio']
        );

        $actualizacionSaldo2 = $DB->actualizaRegistro('usuario', $campos, $datos, $datCriterio, $camCriterio);

        if ($idtransaccion1 > 0 && $idtransaccion2 > 0 && $actualizacionSaldo1 == true && $actualizacionSaldo2 == true) {
            $respuesta = array('resultado' => 'Ok');
        } else {
            $respuesta = array('resultado' => 'Error');
        }

        echo json_encode($respuesta);
        die(0);
        break;
    case '0000005':
        $respuesta = array('resultado' => 'Error indeterminado');
        if (empty($_POST['nombre'])) {
            $respuesta = array('resultado' => 'Error datos');
            echo json_encode($respuesta);
            die(0);
        }

        $DB->consulta('ID_USUARIO,NOMBRE,APELLIDO', 'usuario', 'NOMBRE LIKE "%' . $_POST['nombre'] . '%"', '');
        $respuesta = array();

        while ($datosUsuario = $DB->sigRegistro()) {

            $respuesta[] = array($datosUsuario['ID_USUARIO'], $datosUsuario['NOMBRE'], $datosUsuario['APELLIDO']);
        }

        echo json_encode($respuesta);
        die(0);

        break;
    case '0000006':
        $respuesta = array('resultado' => 'Error indeterminado');
        if (empty($_POST['nombre'])) {
            $respuesta = array('resultado' => 'Error datos');
            echo json_encode($respuesta);
            die(0);
        }

        $DB->consulta('ID_TRANSACCION,TIPO_DE_TRANSACCION,FECHA_TRANSACCION,NOMBRE', 'transacciones INNER JOIN usuario ON ID_USUARIO = USUARIO_ID_USUARIO', 'NOMBRE LIKE "%' . $_POST['nombre'] . '%"', '');
        $respuesta = array();

        while ($datosUsuario = $DB->sigRegistro()) {

            $respuesta[] = array($datosUsuario['ID_TRANSACCION'], $datosUsuario['TIPO_DE_TRANSACCION'], $datosUsuario['FECHA_TRANSACCION'], $datosUsuario['NOMBRE']);
        }

        echo json_encode($respuesta);
        die(0);

        break;
}
