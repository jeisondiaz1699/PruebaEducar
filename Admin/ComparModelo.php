<?php

class Conexion {
  //Clase modelo para MariaDB
  private $servidor = "localhost";
  private $usuario = "root";
  private $contrasena = "";
  private $based = "educar";
  //private $usuario = "clinicav1_Veterinaria";
  //private $contrasena = "KGme.TFGJ;(B";
  //private $based = "clinicav1_Veterinaria";
  private $conexion;
  private $result;
  private $registros;

  private $codigoSeguimiento;
  private $codigoUsuario = 0;

  public function setConexion($Conexion) {
    $this->conexion = $Conexion;
  } 
  function getConexion() {
    return $this->conexion;
  }
  function getRegistros() {
    return $this->Registros;
  }


  public function conectar() {
    $this->conexion = new mysqli(
        $this->servidor, $this->usuario, $this->contrasena, $this->based
    );
    if ($this->conexion->connect_errno) {
      //echo "Fallo al contenctar a MySQL: (" . $this->conn->connect_errno . ") " . $this->conn->connect_error;
      //return "Fallo al contenctar a MySQL: (" . $this->conn->connect_errno . ") " . $this->conn->connect_error;
      return false;
    }
    //echo $this->conn->host_info . "\n";
    //return $this->conn->host_info . "\n";
    return true;
  }
  /*
  public function desconectar() {
    self::conectar();
    $this->conexion->close();
  }
  */
  public function buscar($campos, $tabla, $criterio = '', $orden = ''){
    $respuesta = 'Modelo';
    // Realizar una consulta SQL
    $sql = "SELECT $campos FROM $tabla ";
    if ( !empty($criterio) ){
      $sql .= " WHERE $criterio "; 
    }

    if ( !empty($orden) ){
      $sql .= " ORDER BY $orden ";
    }
    //$resultado = $this->conn->query($sql);
    
    if (!$resultado = $this->conexion->query($sql)) {
      // ¡Oh, no! La consulta falló. 
      //echo "Lo sentimos, este sitio web está experimentando problemas.";

      // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
      // cómo obtener información del error
      //echo "Error: La ejecución de la consulta falló debido a: \n";
      //echo "Query: " . $sql . "\n";
      //echo "Errno: " . $mysqli->errno . "\n";
      echo "Error: " . $this->conexion->error . "\n";
      //exit;
      //return false;
      $respuesta = 'Fallo consulta';
    } else {
      // ¡Uf, lo conseguimos!. Sabemos que nuestra conexión a MySQL y nuestra consulta
      // tuvieron éxito, pero ¿tenemos un resultado?
      if ($resultado->num_rows === 0) {
        // ¡Oh, no ha filas! Unas veces es lo previsto, pero otras
        // no. Nosotros decidimos. En este caso, ¿podría haber sido
        // actor_id demasiado grande? 
        //echo "Lo sentimos. No se pudo encontrar una coincidencia para el ID $aid. Inténtelo de nuevo.";
        //exit;
        return false;
        $respuesta = 'No hay filas';
      } else {
        $respuesta = 'Lo encontro';
        return $resultado->fetch_assoc();  
      }
      //return $respuesta;
      //$respuesta = 'Sin registro';
    }

    // Ahora, sabemos que existe solamente un único resultado en este ejemplo, por lo
    // que vamos a colocarlo en un array asociativo donde las claves del mismo son los
    // nombres de las columnas de la tabla
    //return $resultado->fetch_assoc();
    return $respuesta;
  }

  public function creaRegistro($Tabla, $Campos, $Datos) {
    $numCampos = count($Campos);
    $numDatos = count($Datos);
    
    if ( $numDatos != $numCampos ){
      return false;
    }

    $cadCampos = '';
    $cadDatos = '';

    for ($Campo = 0; $Campo < $numCampos; $Campo++) {
      if ($Campo > 0) {
        $cadCampos .=  ', ';
        $cadDatos .=  ', ';
      }
      $cadCampos .= $Campos[$Campo];
      if ( substr($Datos[$Campo],0,5) == '(BIT)' ){
        $cadDatos .= substr($Datos[$Campo],5,1);
      } else {
        $cadDatos .= "'".$Datos[$Campo]."'";
      }
    }

    $sql = "INSERT INTO $Tabla ";
    $sql .= "( $cadCampos ) VALUES ( $cadDatos )";
    if ($this->conexion->query($sql) === TRUE) {
      $Id = 0;
        
      $Id = $this->conexion->insert_id;
      return $Id;
      //echo "Record updated successfully";
    } else {
      //die($sql);
      return $sql;
      return false;
      //echo "Error updating record: " . $conn->error;
    }

    $Id = 0;
        
    //$Rs = $this->conexion->insert_id;
    $resultado = $this->conexion->query('SELECT LAST_INSERT_ID() AS id '); 
    $Rs = $resultado->fetch_assoc();
    print_r($Rs);
    die('??');
    $Id = $resultado['id'];
    //$Id = $Rs;

    if ($Rs > 0 ) {
      $Id = $Rs;
    } else {
      echo 'ERROR';
      //Registra la información en la tabla 
      $nCampos = array(
        "ENTIDAD",
        "CAMPOS",
        "DATOS",
        "CAMPO_ACTUALIZA",
        "DATO_ACTUALIZA",
        "USUARIO_MODIFICA",
        "ERROR_REPORTADO"
      );

      if(count(sqlsrv_errors()) > 0){
        $Errores = implode(",", sqlsrv_errors());
      } else {
        $Errores = "";
      }
      $nDatos = array(
        $Tabla,
        implode(",", $Campos),
        implode(",", $Datos),
        implode(",", $CamCriterio),
        implode(",", $DatCriterio),
        $usuarioLogueado,
        $Errores
      );

      $codigoNotificacion = $this->creaRegistro("ERROR_REGISTRO", $nCampos, $nDatos);
      echo "<h3>Informaci&oacute;n</h3><p>Se ha producido un error inesperado, se ha notificado con el c&oacute;digo: <b>" . $codigoNotificacion . "</b> por favor contacte al administrador.</p><p>Para volver al inicio haga clic <a href='http://192.168.0.38/claro/AccesoGNP.php'>aqui</a></p>";
      die();
    }
    return $Id;
  
  }

  public function actualizaRegistro($Tabla, $Campos, $Datos, $DatCriterio, $CamCriterio) {
    $sql = "UPDATE $Tabla SET ";
    for ($Campo = 0; $Campo < count($Campos); $Campo++) {
      if ($Campo > 0) {
        $sql = $sql . ", ";
      }
      if ( is_null($Datos[$Campo]) ) {
        $sql = $sql . "$Campos[$Campo] = NULL ";
      } else {
        $sql = $sql . $Campos[$Campo].' = '."'".$Datos[$Campo]."'";
      }
    }

    if (count($CamCriterio) > 0) {
      $sql = $sql . " WHERE ";
      for ($Campo = 0; $Campo < count($CamCriterio); $Campo++) {
        if ($Campo > 0) {
          $sql = $sql . " AND ";
        }
        $sql = $sql .  $CamCriterio[$Campo].' = '."'".$DatCriterio[$Campo]."'";
      }
    }
    //$stmt = sqlsrv_query($this->conexion, $sql, $Datos);
    if ($this->conexion->query($sql) === TRUE) {
      return true;
      //echo "Record updated successfully";
    } else {
      //return $sql;
      return false;
      //echo "Error updating record: " . $conn->error;
    }      
  }
  public function consulta($Campos, $Tabla, $Criterio, $Orden, $adicion = '', $adicionFin = '') {

    $sql = " SELECT $Campos FROM $Tabla";
    if (!empty($Criterio)) {
      $sql = $sql . " WHERE " . $Criterio;
    }
    if (!empty($adicion)) {
      $sql = $sql . $adicion;
    }
    if (!empty($Orden)) {
      $sql = $sql . " ORDER BY $Orden";
    }
    if (!empty($adicionFin)) {
      $sql = $sql . $adicionFin;
    }
    //Al definir el sql se crea el seguimiento
    //$codigoSeguimiento = $this->registrarSeguimiento("", $sql, 2);

    //if($this->result = sqlsrv_query($this->conexion, $sql)){
    if ($this->result = $this->conexion->query($sql)) {

    } else {
      $codigoUsuario = 0;
      $nombreUsuario = "";
      foreach ($_SESSION as $key => $value) {
        if(strpos($key, "sr_")>0){
          $codigoUsuario = $_SESSION[$key]['Id'];
          $nombreUsuario = $_SESSION[$key]['Nombre'];
        }
      }

      //Registra el error en la base de datos
      $camposError = array(
        "FECHA_HORA",
        "DESCRIPCION_ERROR",
        "FK_CODIGO_TIPO_ERROR",
        "FK_CODIGO_USUARIO"
      );

      $datosError = array(
        date("Y-m-d H:i"),
        "Usuario: " . $nombreUsuario . ". Se ha producido un error al ejecutar la consulta: " . $sql,
        "1",
        $codigoUsuario
      );

      print_r($datosError);
      //$codigoError = $this->creaRegistro("ERROR", $camposError, $datosError);

      //Envia el mensaje al usuario con el codigo del error 
      echo 'Se ha producido el error número ' . $codigoError . " por favor contacte al administrador.";
    }
    
    $this->Registros = $this->result->num_rows;
    //$this->Registros = count($this->result);
    //if ($LogMovil) {
    //  $this->creaLogMovil($sql);
    //}

    //$this->actualizarSeguimiento($codigoSeguimiento);
  }
  function sigRegistro() {
    return $this->result->fetch_assoc();
    //return sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC);
  }
  public function eliminar($Tabla, $Criterio) {
    $sql = " DELETE FROM $Tabla";
    if (!empty($Criterio)) {
      $sql = $sql . " WHERE " . $Criterio;
    }
    //sqlsrv_query($this->conexion, $sql);
    if ($this->conexion->query($sql) === TRUE) {
      return true;
      //echo "Record updated successfully";
    } else {
      //return $sql;
      return false;
      //echo "Error updating record: " . $conn->error;
    }
  }
}

/*
class conexionGNP {
    private $conexion;
    private $result;
    private $Registros;

    private $codigoSeguimiento;
    private $codigoUsuario = 0;       

    function setConexion($Conexion) {
        $this->conexion = $Conexion;
    }

    function setResult() {
        
    }

    function setRegistros() {
        
    }

    function getConexion() {
        return $this->conexion;
    }

    function getResult() {
        
    }

    function getRegistros() {
        return $this->Registros;
    }

    function conectar($BaseDatos) {
        $_SESSION['version'] = "20190402";
        $serverName = "DA-PRINC\SQLEXPRESS";
        $database = "DEM1_CLAROGES";
        $uid = "gnp1";  
        $pwd = "aplignp252";

        if (empty($database)) {
            return;
        }

        $connectionInfo = array("Database" => "$database", "UID" => "$uid", "PWD" => "$pwd");
        $this->conexion = sqlsrv_connect($serverName, $connectionInfo);

        if ($this->conexion) {
            return true;
            echo "Conexión establecida.<br />";
        } else {
            echo "Conexión no se pudo establecer.<br />";
            die(print_r(sqlsrv_errors(), true));
            return false;
        }
    }

    function desconectar() {
        sqlsrv_close($this->conexion);
    }

    function consulta($Campos, $Tabla, $Criterio, $Orden, $adicion = '', $LogMovil = FALSE) {
        global $codigoSeguimiento;

        $sql = " SELECT $Campos FROM $Tabla";
        if (!empty($Criterio)) {
            $sql = $sql . " WHERE " . $Criterio;
        }
        if (!empty($adicion)) {
            $sql = $sql . $adicion;
        }
        if (!empty($Orden)) {
            $sql = $sql . " ORDER BY $Orden";
        }

        //Al definir el sql se crea el seguimiento
        //$codigoSeguimiento = $this->registrarSeguimiento("", $sql, 2);

        if($this->result = sqlsrv_query($this->conexion, $sql)){
        }else{
            $codigoUsuario = 0;
            $nombreUsuario = "";

            foreach ($_SESSION as $key => $value) {
                if(strpos($key, "sr_")>0){
                    $codigoUsuario = $_SESSION[$key]['Id'];
                    $nombreUsuario = $_SESSION[$key]['Nombre'];
                }
            }

            //Registra el error en la base de datos
            $camposError = array(
                "FECHA_HORA",
                "DESCRIPCION_ERROR",
                "FK_CODIGO_TIPO_ERROR",
                "FK_CODIGO_USUARIO"
            );

            $datosError = array(
                date("Y-m-d H:i"),
                "Usuario: " . $nombreUsuario . ". Se ha producido un error al ejecutar la consulta: " . $sql,
                "1",
                $codigoUsuario
            );

            $codigoError = $this->creaRegistro("ERROR", $camposError, $datosError);

            //Envia el mensaje al usuario con el codigo del error 
            echo 'Se ha producido el error número ' . $codigoError . " por favor contacte al administrador.";
        }


        $this->Registros = count($this->result);
        if ($LogMovil) {
            $this->creaLogMovil($sql);
        }

        //$this->actualizarSeguimiento($codigoSeguimiento);
    }

    function consulta($Campos, $Tabla, $Criterio, $Orden, $adicion = '', $LogMovil = FALSE) {
        global $codigoSeguimiento;

        $sql = " SELECT $Campos FROM $Tabla";
        if (!empty($Criterio)) {
            $sql = $sql . " WHERE " . $Criterio;
        }
        if (!empty($adicion)) {
            $sql = $sql . $adicion;
        }
        if (!empty($Orden)) {
            $sql = $sql . " ORDER BY $Orden";
        }

        //Al definir el sql se crea el seguimiento
        //$codigoSeguimiento = $this->registrarSeguimiento("", $sql, 2);

        if($this->result = sqlsrv_query($this->conexion, ($sql))){

        }else{
            $codigoUsuario = 0;
            $nombreUsuario = "";
            
            foreach ($_SESSION as $key => $value) {
                if(strpos($key, "sr_")>0){
                    $codigoUsuario = $_SESSION[$key]['Id'];
                    $nombreUsuario = $_SESSION[$key]['Nombre'];
                }
            }

            //Registra el error en la base de datos
            $camposError = array(
                "FECHA_HORA",
                "DESCRIPCION_ERROR",
                "FK_CODIGO_TIPO_ERROR",
                "FK_CODIGO_USUARIO"
            );

            $datosError = array(
                date("Y-m-d H:i"),
                "Usuario: " . $nombreUsuario . ". Se ha producido un error al ejecutar la consulta: " . $sql,
                "1",
                $codigoUsuario
            );

            $codigoError = $this->creaRegistro("ERROR", $camposError, $datosError);

            //Envia el mensaje al usuario con el codigo del error 
            echo 'Se ha producido el error número ' . $codigoError . " por favor contacte al administrador.";
        }

        $this->Registros = count($this->result);
        if ($LogMovil) {
            $this->creaLogMovil($sql);
        }

        //$this->actualizarSeguimiento($codigoSeguimiento);
    }

    function buscar($Campos, $Tabla, $Criterio, $Orden) {
        global $codigoSeguimiento;

        $sql = " SELECT $Campos FROM $Tabla";
        if (!empty($Criterio)) {
            $sql = $sql . " WHERE " . $Criterio;
        }

        if (!empty($Orden)) {
            $sql = $sql . " ORDER BY $Orden";
        }

        //Al definir el sql se crea el seguimiento
       // $codigoSeguimiento = $this->registrarSeguimiento("", $sql, 2);

        if($registro = sqlsrv_query($this->conexion, $sql)){

        }else{
            $codigoUsuario = 0;
            $nombreUsuario = "";
            
            foreach ($_SESSION as $key => $value) {
                if(strpos($key, "sr_")>0){
                    $codigoUsuario = $_SESSION[$key]['Id'];
                    $nombreUsuario = $_SESSION[$key]['Nombre'];
                }
            }

            //Registra el error en la base de datos
            $camposError = array(
                "FECHA_HORA",
                "DESCRIPCION_ERROR",
                "FK_CODIGO_TIPO_ERROR",
                "FK_CODIGO_USUARIO"
            );

            $datosError = array(
                date("Y-m-d H:i"),
                "Usuario: " . $nombreUsuario . ". Se ha producido un error al ejecutar la consulta: " . $sql,
                "1",
                $codigoUsuario
            );

            $codigoError = $this->creaRegistro("ERROR", $camposError, $datosError);

            //Envia el mensaje al usuario con el codigo del error 
            echo 'Se ha producido el error número ' . $codigoError . " por favor contacte al administrador.";
        }

        //$this->actualizarSeguimiento($codigoSeguimiento);

        if (count($registro) == 1) {
            return sqlsrv_fetch_array($registro, SQLSRV_FETCH_ASSOC);
        }

        return false;
    }

    function buscar($Campos, $Tabla, $Criterio, $Orden) {
        global $codigoSeguimiento;

        $sql = " SELECT $Campos FROM $Tabla";
        if (!empty($Criterio)) {
            $sql = $sql . " WHERE " . $Criterio;
        }
        if (!empty($Orden)) {
            $sql = $sql . " ORDER BY $Orden";
        }

        //Al definir el sql se crea el seguimiento
        //$codigoSeguimiento = $this->registrarSeguimiento("", $sql, 2);

        if($registro = sqlsrv_query($this->conexion, ($sql))){

        }else{
            $codigoUsuario = 0;
            $nombreUsuario = "";
            
            foreach ($_SESSION as $key => $value) {
                if(strpos($key, "sr_")>0){
                    $codigoUsuario = $_SESSION[$key]['Id'];
                    $nombreUsuario = $_SESSION[$key]['Nombre'];
                }
            }

            //Registra el error en la base de datos
            $camposError = array(
                "FECHA_HORA",
                "DESCRIPCION_ERROR",
                "FK_CODIGO_TIPO_ERROR",
                "FK_CODIGO_USUARIO"
            );

            $datosError = array(
                date("Y-m-d H:i"),
                "Usuario: " . $nombreUsuario . ". Se ha producido un error al ejecutar la consulta: " . $sql,
                "1",
                $codigoUsuario
            );

            $codigoError = $this->creaRegistro("ERROR", $camposError, $datosError);

            //Envia el mensaje al usuario con el codigo del error 
            echo 'Se ha producido el error número ' . $codigoError . " por favor contacte al administrador.";
        }

        //$this->actualizarSeguimiento($codigoSeguimiento);

        if (count($registro) == 1) {
            return sqlsrv_fetch_array($registro, SQLSRV_FETCH_ASSOC);
        }
        return false;
    }

    function sigRegistro() {
        return sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC);
    }

    function creaRegistro($Tabla, $Campos, $Datos, $LogMovil = false, $usuarioLogueado = 0) {
        $sql = "INSERT INTO $Tabla (";
        for ($Campo = 0; $Campo < count($Campos); $Campo++) {
            if ($Campo > 0) {
                $sql = $sql . ", ";
            }
            $sql = $sql . "$Campos[$Campo]";
        }

        $sql = $sql . ") VALUES (";
        for ($Campo = 0; $Campo < count($Campos); $Campo++) {
            if ($Campo > 0) {
                $sql = $sql . ", ";
            }
            $sql = $sql . "?";
        }
        $sql = $sql . ") ";

        $stmt = sqlsrv_query($this->conexion, $sql, $Datos);
        
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        
        if ($LogMovil) {
            $this->creaLogMovil($sql, $Datos);
        }

        $Id = 0;
        
        $Rs = sqlsrv_query($this->conexion, "SELECT @@identity AS Id");

        if ($Row = sqlsrv_fetch_array($Rs, SQLSRV_FETCH_ASSOC)) {
            $Id = trim($Row['Id']);
        }else{
            echo 'ERROR';
            //Registra la información en la tabla 
            $nCampos = array(
                "ENTIDAD",
                "CAMPOS",
                "DATOS",
                "CAMPO_ACTUALIZA",
                "DATO_ACTUALIZA",
                "USUARIO_MODIFICA",
                "ERROR_REPORTADO"
            );

            if(count(sqlsrv_errors()) > 0){
                $Errores = implode(",", sqlsrv_errors());
            }else{
                $Errores = "";
            }

            $nDatos = array(
                $Tabla,
                implode(",", $Campos),
                implode(",", $Datos),
                implode(",", $CamCriterio),
                implode(",", $DatCriterio),
                $usuarioLogueado,
                $Errores
            );

            $codigoNotificacion = $this->creaRegistro("ERROR_REGISTRO", $nCampos, $nDatos);

            echo "<h3>Informaci&oacute;n</h3><p>Se ha producido un error inesperado, se ha notificado con el c&oacute;digo: <b>" . $codigoNotificacion . "</b> por favor contacte al administrador.</p><p>Para volver al inicio haga clic <a href='http://192.168.0.38/claro/AccesoGNP.php'>aqui</a></p>";
            die;
        }
        return $Id;
    }

    function eliminar($Tabla, $Criterio) {

        $sql = " DELETE FROM $Tabla";
        if (!empty($Criterio)) {
            $sql = $sql . " WHERE " . $Criterio;
        }

        sqlsrv_query($this->conexion, $sql);
    }

    function actualizaRegistro($Tabla, $Campos, $Datos, $DatCriterio, $CamCriterio, $LogMovil = false, $usuarioLogueado = 0) {
        global $codigoUsuario;

        $sql = "UPDATE $Tabla SET ";
        for ($Campo = 0; $Campo < count($Campos); $Campo++) {
            if ($Campo > 0) {
                $sql = $sql . ", ";
            }
            if ($Datos[$Campo] == "NULL" && $Datos[$Campo] != 0) {
                $sql = $sql . "$Campos[$Campo] = NULL ";
            } else {
                $sql = $sql . "$Campos[$Campo] = ?";
            }
        }
        
        $ArrayTemp = array();
        for ($Campo = 0; $Campo < count($Datos); $Campo++) {
            if ($Datos[$Campo] != "NULL" || $Datos[$Campo] == 0) {
                $ArrayTemp[] = $Datos[$Campo];
            }
        }

        $Datos = $ArrayTemp;

        if (count($CamCriterio) > 0) {
            $sql = $sql . " WHERE ";
            for ($Campo = 0; $Campo < count($CamCriterio); $Campo++) {
                if ($Campo > 0) {
                    $sql = $sql . " AND ";
                }
                $sql = $sql . " $CamCriterio[$Campo] = ?";
            }
            for ($Campo = 0; $Campo < count($DatCriterio); $Campo++) {
                $Datos[] = $DatCriterio[$Campo];
            }
        }

        $stmt = sqlsrv_query($this->conexion, $sql, $Datos);
        if (sqlsrv_rows_affected($stmt) == 0) {

            //Registra la información en la tabla 
            $nCampos = array(
                "ENTIDAD",
                "CAMPOS",
                "DATOS",
                "CAMPO_ACTUALIZA",
                "DATO_ACTUALIZA",
                "USUARIO_MODIFICA",
                "ERROR_REPORTADO"
            );

            if(count(sqlsrv_errors()) > 0){
                $Errores = implode(",", sqlsrv_errors());
            }else{
                $Errores = "";
            }

            $nDatos = array(
                $Tabla,
                implode(",", $Campos),
                implode(",", $Datos),
                implode(",", $CamCriterio),
                implode(",", $DatCriterio),
                $usuarioLogueado,
                $Errores
            );

            $codigoNotificacion = $this->creaRegistro("ERROR_REGISTRO", $nCampos, $nDatos);

            echo "<h3>Informaci&oacute;n</h3><p>Se ha producido un error inesperado, se ha notificado con el c&oacute;digo: <b>" . $codigoNotificacion . "</b> por favor contacte al administrador.</p><p>Para volver al inicio haga clic <a href='http://192.168.0.38/claro/AccesoGNP.php'>aqui</a></p>";
            die;
            return false;
        } else if (sqlsrv_rows_affected($stmt) == -1) {
            echo "No information available.<br />";
            return false;
        } else {
            if ($LogMovil) {
                $this->creaLogMovil($sql, $Datos);
            }

            //Valida si la entidad es solicitud de servicios para hacer el seguimiento
            if($Tabla == "`SOLICITUD DE SERVICIOS`"){
                //Valida si alguno de los campos de edicion es el estado
                if(in_array("ESTADO_SERVICIO", $Campos)){
                    $posicionDato = array_search("ESTADO_SERVICIO", $Campos);

                    foreach ($_SESSION as $key => $value) {
                        if(strpos($key, "sr_")>0){
                            $codigoUsuario = $_SESSION[$key]['Id'];
                        }
                    }

                    $Campos = array(
                        "ENTIDAD",
                        "VALOR_NUEVO",
                        "USUARIO_MODIFICA",
                        "ID_MODIFICA"
                    );

                    $Datos = array(
                        $Tabla,
                        $Datos[$posicionDato],
                        $codigoUsuario,
                        $DatCriterio[0]
                    );

                    //Registra la modificacion en la entidad de auditoria
                    $this->creaRegistro("AUDITORIA_GENERAL", $Campos, $Datos);
                }
            }

            return true;
            echo $rows_affected . " rows were updated.<br />";
        }
    }

    function creaLogMovil($consulta = "", $Datos = "") {

        $fecha = date("Y-m-d H:i");
        $DatString = "";
        if (!empty($Datos)) {
            $DatString = json_encode($Datos);
        }
        $DatosL = array(
            $fecha,
            $consulta,
            $DatString
        );

        $sql = "INSERT INTO LogMovil ( Fecha, Consulta, Datos ) VALUES ( ?, ?, ? ) ";

        $stmt = sqlsrv_query($this->conexion, $sql, $DatosL);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        return;
    }

    function registrarSeguimiento($Tabla, $Descripcion, $tipoMovimiento){
        $codigoUsuario = 0;

        foreach ($_SESSION as $key => $value) {
            if(strpos($key, "sr_")>0){
                $codigoUsuario = $_SESSION[$key]['Id'];
            }
        }

        $Campos = array(
            "NOMBRE_TABLA",
            "DESCRIPCION",
            "USUARIO",
            "TIPO_MOVIMIENTO",
            "FUENTE",
            "TIPO_SOLICITUD",
            "FECHA_HORA_INICIO"
        );


        if(!empty($_SESSION['Fuente'])){
            $Fuente = $_SESSION['Fuente'];
        }else{
            $Fuente = "S/S";
        }

        if(!empty($_SESSION['TIPO_SOLICITUD'])){
            $tipoSolicitud = $_SESSION['TIPO_SOLICITUD'];
        }else{
            $tipoSolicitud = 0;
        }

        $Datos = array(
            $Tabla,
            $Descripcion,
            $codigoUsuario,
            $tipoMovimiento,
            $Fuente,
            $tipoSolicitud,
            date("Y-m-d H:i:s:m")
        );

        return $this->creaRegistroTemp("SEGUIMIENTO", $Campos, $Datos);
    }

    function actualizarSeguimiento($codigoSeguimiento){
        $Campos = array(
            "FECHA_HORA_FIN"
        );

        $Datos = array(
            Date("Y-m-d H:i:s:m")
        );

        $campoActualiza = array(
            "CODIGO_SEGUIMIENTO"
        );

        $datoActualiza = array(
            $codigoSeguimiento
        );

        $this->actualizaRegistroTemp("SEGUIMIENTO", $Campos, $Datos, $datoActualiza, $campoActualiza);
    }

    function creaRegistroTemp($Tabla, $Campos, $Datos, $LogMovil = false) {
        $sql = "INSERT INTO $Tabla (";
        for ($Campo = 0; $Campo < count($Campos); $Campo++) {
            if ($Campo > 0) {
                $sql = $sql . ", ";
            }
            $sql = $sql . "$Campos[$Campo]";
        }

        $sql = $sql . ") VALUES (";
        for ($Campo = 0; $Campo < count($Campos); $Campo++) {
            if ($Campo > 0) {
                $sql = $sql . ", ";
            }
            $sql = $sql . "?";
        }
        $sql = $sql . ") ";

        $stmt = sqlsrv_query($this->conexion, $sql, $Datos);
        
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        
        if ($LogMovil) {
            $this->creaLogMovil($sql, $Datos);
        }

        $Id = 0;
        
        $Rs = sqlsrv_query($this->conexion, "SELECT @@identity AS Id");
        if ($Row = sqlsrv_fetch_array($Rs, SQLSRV_FETCH_ASSOC)) {
            $Id = trim($Row['Id']);
        }
        return $Id;
    }

    function actualizaRegistroTemp($Tabla, $Campos, $Datos, $DatCriterio, $CamCriterio, $LogMovil = false) {
        $sql = "UPDATE $Tabla SET ";
        for ($Campo = 0; $Campo < count($Campos); $Campo++) {
            if ($Campo > 0) {
                $sql = $sql . ", ";
            }
            if ($Datos[$Campo] == "NULL" && $Datos[$Campo] != 0) {
                $sql = $sql . "$Campos[$Campo] = NULL ";
            } else {
                $sql = $sql . "$Campos[$Campo] = ?";
            }
        }
        
        $ArrayTemp = array();
        for ($Campo = 0; $Campo < count($Datos); $Campo++) {
            if ($Datos[$Campo] != "NULL" || $Datos[$Campo] == 0) {
                $ArrayTemp[] = $Datos[$Campo];
            }
        }

        $Datos = $ArrayTemp;

        if (count($CamCriterio) > 0) {
            $sql = $sql . " WHERE ";
            for ($Campo = 0; $Campo < count($CamCriterio); $Campo++) {
                if ($Campo > 0) {
                    $sql = $sql . " AND ";
                }
                $sql = $sql . " $CamCriterio[$Campo] = ?";
            }
            for ($Campo = 0; $Campo < count($DatCriterio); $Campo++) {
                //die($DatCriterio[$Campo]);
                $Datos[] = $DatCriterio[$Campo];
            }
        }

        $stmt = sqlsrv_query($this->conexion, $sql, $Datos);
        if (sqlsrv_rows_affected($stmt) == 0) {
            //die("Aca ha pasado algo!");
            die(print_r(sqlsrv_errors(), true));
            return false;
        } else if (sqlsrv_rows_affected($stmt) == -1) {
            //die("Hay errores");
            echo "No information available.<br />";
            return false;
        } else {
            //die("No hubo errores");
            if ($LogMovil) {
                $this->creaLogMovil($sql, $Datos);
            }

            return true;
            echo $rows_affected . " rows were updated.<br />";
        }
    }
}
*/
