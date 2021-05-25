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
                        <th>ID</th>
                        <th>Transacción</th>
                        <th>Fecha</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $DB = new Conexion();
                    $DB->conectar();
                    $DB->Consulta('ID_TRANSACCION,TIPO_DE_TRANSACCION,FECHA_TRANSACCION,NOMBRE', 'transacciones INNER JOIN usuario ON ID_USUARIO = USUARIO_ID_USUARIO', '', '');

                    while ($usuario = $DB->sigRegistro()) {
                    ?>
                        <tr>
                            <td><?php echo $usuario['ID_TRANSACCION']; ?></td>
                            <td><?php echo $usuario['TIPO_DE_TRANSACCION']; ?></td>
                            <td><?php echo $usuario['FECHA_TRANSACCION']; ?></td>
                            <td><?php echo $usuario['NOMBRE']; ?></td>
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
        $('#buscar').keyup(function() {
            formData = new FormData();
            formData.append('opcion', '0000006');
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
                                '<td>' + elem[3] + '</td>' +
                                '</tr>');
                        }
                    })
                    $('.tableUsuarios').html('<thead class="thead-dark">' +
                        '<tr>' +
                        '<th>ID</th>' +
                        '<th>Transcción</th>' +
                        '<th>Fecha</th>' +
                        '<th>Nombre</th>' +
                        '</tr>' +
                        '</thead>' +
                        html +
                        '</table>');
                }
            });
        });
    </script>
</body>


</html>