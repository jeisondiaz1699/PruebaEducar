<?php
include 'Admin/ComparModelo.php';
?>
<!doctype html>
<html lang="es">

<head>
    <title>Usuarios</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="bootstrap-4/4.1/css/bootstrap.min.css" />
</head>

<body>
    <div class="" style="width : 100%; height : 100vh;">
        <?php
        include('menu.php');
        ?>

        <div class="form-group col-4">
            <label>Buscar por nombre</label>
            <input type="text" class="form-control buscar" id="buscar" name="buscar">
        </div>
        <div>
            <table class="table tableUsuarios table-sm table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th style="width : 60px;">Id</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Aumentar saldo</th>
                        <th>Transferir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $DB = new Conexion();
                    $DB->conectar();
                        $DB->Consulta('ID_USUARIO, NOMBRE, APELLIDO', 'usuario', '', 'ID_USUARIO');
                    while ($usuario = $DB->sigRegistro()) {
                    ?>
                        <tr>
                            <td><?php echo $usuario['ID_USUARIO']; ?></td>
                            <td><?php echo $usuario['NOMBRE']; ?></td>
                            <td><?php echo $usuario['APELLIDO']; ?></td>
                            <td>
                                <button onclick="aumentarSaldo(<?php echo $usuario['ID_USUARIO']; ?>)" type="button" class="btn btn-primary-outline aumentarSaldo" data-toggle="modal" data-target="#exampleModal"><img src="bootstrap-4/open-iconic-master/svg/plus.svg"></button>
                            </td>
                            <td>
                                <button onclick="transferir(<?php echo $usuario['ID_USUARIO']; ?>)" id="transferir" type="button" class="btn btn-primary-outline transferir" data-toggle="modal" data-target="#exampleModal"><img src="bootstrap-4/open-iconic-master/svg/data-transfer-upload.svg"></button>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titulo"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body contenido">

                    </div>
                    <div class="modal-footer" id="footer">
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
        <!-- fin modal*-->

    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="jquery/jquery-3.4.1.min.js"></script>
    <script src="bootstrap-4/4.1/popper.min.js"></script>
    <script src="bootstrap-4/4.1/js/bootstrap.min.js"></script>
    <script src="bootstrap-4/4.1/solid.js"></script>
    <script defer src="bootstrap-4/4.1/fontawesome.js"></script>
    <script type="text/javascript">
        function aumentarSaldo(id) {
            $('#titulo').html('');
            $('.contenido').html('<h1>Cargando</h1>');
            $('.footer').html('');
            var formData = new FormData();
            formData.append('opcion', '0000001');
            formData.append('idUsuario', id);
            $.ajax({
                data: formData,
                url: 'controlador.php',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                dataType: "json",
                beforeSend: function() {
                    /*
                    $("#resultado").html($("#resultado").html()+"Procesando"+ejecucion+", espere por favor...<br />");
                    */
                },
                success: function(response) {
                    $('#titulo').html('Aumentar saldo');
                    $('.contenido').html('<table  class="table">' +
                        '<th>Id</th>' +
                        '<th>Nombre</th>' +
                        '<th>Apellido</th>' +
                        '<th>Saldo actual</th>' +
                        '<th>Agregar saldo</th>' +
                        '<tr>' +
                        '<td>' + response[0][0] + '</td>' +
                        '<td>' + response[0][1] + '</td>' +
                        '<td>' + response[0][2] + '</td>' +
                        '<td>' + response[0][3] + '</td>' +
                        '<td><input type="number" name="saldoAgregar" id="saldoAgregar"></td>' +
                        '</tr>' +
                        '</table>');
                    $('#footer').html(
                        '<button type="button" id="enviarAumentoDeSaldo" onclick="enviarAumentoDeSaldo(' +
                        response[0][0] +
                        ')" class="btn btn-success btn-lg btn-block w-25">Enviar</button>');
                }
            });
        };

        function enviarAumentoDeSaldo(IdUsuario) {
            var saldoAgregar = $('#saldoAgregar').val();
            var formData = new FormData();
            formData.append('opcion', '0000002');
            formData.append('idUsuario', IdUsuario);
            formData.append('saldoAgregar', saldoAgregar);
            $.ajax({
                data: formData,
                url: 'controlador.php',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                dataType: "json",
                beforeSend: function() {
                    /*
                    $("#resultado").html($("#resultado").html()+"Procesando"+ejecucion+", espere por favor...<br />");
                    */
                },
                success: function(response) {
                    if (response['resultado'] == 'Ok') {
                        $('#titulo').html('');
                        $('.contenido').html('');
                        $('.footer').html('');
                        location.reload();
                    } else {
                        alert('Datos no guardados');
                    }
                }
            });
        }

        function transferir(id) {
            $('#titulo').html('');
            $('.contenido').html('<h1>Cargando</h1>');
            $('.footer').html('');
            var formData = new FormData();
            formData.append('opcion', '0000003');
            formData.append('idUsuario', id);
            $.ajax({
                data: formData,
                url: 'controlador.php',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                dataType: "json",
                beforeSend: function() {
                    /*
                    $("#resultado").html($("#resultado").html()+"Procesando"+ejecucion+", espere por favor...<br />");
                    */
                },
                success: function(response) {
                    $('#titulo').html('Aumentar saldo');
                    $('.contenido').html('<table  class="table">' +
                        '<th>Saldo disponible</th>' +
                        '<th>Seleccion usuario</th>' +
                        '<th>Cantidad a transferir</th>' +
                        '<tr>' +
                        '<td>' + response.saldo.SALDO + '</td>' +
                        '<td> <select class="usuarios">' +
                        '</select></td>' +
                        '<td><input type="number" name="transferencia" id="transferencia"></td>' +
                        '</tr>' +
                        '</table>');
                    $('#footer').html(
                        '<button type="button" id="enviarAumentoDeSaldo" onclick="enviarSaldo(' +
                        id + ',' + response.saldo.SALDO +
                        ')" class="btn btn-success btn-lg btn-block w-25">Enviar</button>');

                    $.each(response.datosUsuarios, function(ind, elem) {
                        $(".usuarios").append('<option value=' + elem[0] + '>' + elem[1] +
                            '</option>');
                    });
                }
            });
        };

        function enviarSaldo(idUsuario, saldoActual) {
            var transferencia = $('#transferencia').val();
            var idUsuarioEnvio = $('.usuarios').val();
            if (saldoActual < transferencia) {
                alert('No se puede enviar mas dinero del que tiene disponible')
            } else {
                var formData = new FormData();
                formData.append('opcion', '0000004');
                formData.append('idUsuario', idUsuario);
                formData.append('transferencia', transferencia);
                formData.append('idUsuarioEnvio', idUsuarioEnvio);
                $.ajax({
                    data: formData,
                    url: 'controlador.php',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    dataType: "json",
                    beforeSend: function() {
                        /*
                        $("#resultado").html($("#resultado").html()+"Procesando"+ejecucion+", espere por favor...<br />");
                        */
                    },
                    success: function(response) {
                        if (response['resultado'] == 'Ok') {
                            $('#titulo').html('');
                            $('.contenido').html('');
                            $('.footer').html('');
                            location.reload();
                        } else {
                            alert('Datos no guardados');
                        }
                    }
                });
            }
        }

        $('#buscar').keyup(function() {
            formData = new FormData();
            formData.append('opcion', '0000005');
            formData.append('nombre', $('#buscar').val());
            $.ajax({
                data: formData,
                url: 'controlador.php',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                dataType: "json",
                beforeSend: function() {
                    /*
                    $("#resultado").html($("#resultado").html()+"Procesando"+ejecucion+", espere por favor...<br />");
                    */
                },
                success: function(response) {
                    var html = [];
                    $.each(response, function(ind, elem) {
                        if (elem[0] != "") {
                            html.push('<tr><td>' + elem[0] + '</td>' +
                                '<td>' + elem[1] + '</td>' +
                                '<td>' + elem[2] + '</td>' +
                                '<td><button  type="button" onclick="aumentarSaldo(' + elem[0] + ')" class="btn btn-primary-outline aumentarSaldo" data-toggle="modal" data-target="#exampleModal"><img src="bootstrap-4/open-iconic-master/svg/plus.svg"></button>' +
                                ' </td>' +
                                ' <td>' +
                                '<button id="transferir" onclick="transferir(' + elem[0] + ')" type="button" class="btn btn-primary-outline transferir" data-toggle="modal" data-target="#exampleModal"><img src="bootstrap-4/open-iconic-master/svg/data-transfer-upload.svg"></button>' +
                                '</td>' +
                                '</tr>');
                        }
                    })
                    $('.tableUsuarios').html('<thead class="thead-dark">' +
                        '<tr>' +
                        '<th style="width : 60px;">Id</th>' +
                        '<th>Nombre</th>' +
                        '<th>Apellido</th>' +
                        '<th>Aumentar saldo</th>' +
                        '<th>Transferir</th>' +
                        '</tr>' +
                        '</thead>' +
                        html+
                        '</table>');
                }
            });
        });
    </script>
</body>


</html>