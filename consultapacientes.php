<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
    integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
</script>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


<?php 

	require 'conexionInfarto.php';
    $sqlQueryComentarios  = $conexion2->query("SELECT dato_personal.id, dato_personal.nombrecompleto, dato_personal.edad, dato_personal.curp, dato_personal.sexo, dato_personal.fechanacimiento, tratamiento.identificador, tratamiento.seguimiento FROM dato_personal left outer join tratamiento on tratamiento.id_paciente = dato_personal.id where tratamiento.identificador = 'cest' and tratamiento.seguimiento = 'inicial'");
    $total_registro       = mysqli_num_rows($sqlQueryComentarios);
    
	$sql = "SELECT COUNT(*) total FROM tratamiento where identificador = 'cest' and seguimiento = 'inicial'";
    $result = mysqli_query($conexion2, $sql);
    $fila = mysqli_fetch_assoc($result);

    $query= $conexion->prepare("SELECT dato_personal.id, dato_personal.nombrecompleto, dato_personal.edad, dato_personal.curp, dato_personal.sexo, dato_personal.fechanacimiento, tratamiento.identificador, tratamiento.seguimiento FROM dato_personal left outer join tratamiento on tratamiento.id_paciente = dato_personal.id where tratamiento.identificador = 'cest' and tratamiento.seguimiento = 'inicial' order by dato_personal.id DESC LIMIT 15 ");
    if(isset($_POST['pacientes']))
{
	$q=$conexion2->real_escape_string($_POST['pacientes']);
	$query=$conexion->prepare("SELECT dato_personal.id, dato_personal.nombrecompleto, dato_personal.edad, dato_personal.curp, dato_personal.sexo, dato_personal.fechanacimiento, tratamiento.identificador, tratamiento.seguimiento FROM dato_personal left outer join tratamiento on tratamiento.id_paciente = dato_personal.id  where
		dato_personal.id LIKE '%".$q."%' OR
        dato_personal.nombrecompleto LIKE '%".$q."%' OR
		dato_personal.fechanacimiento LIKE '%".$q."%' OR
		dato_personal.edad LIKE '%".$q."%' OR
		dato_personal.sexo LIKE '%".$q."%' OR
		dato_personal.curp LIKE '%".$q."%' AND tratamiento.seguimiento = 'incial' group by dato_personal.id");
}
        ?>
<input type="submit" id="totalregistro" value="Total:&nbsp;<?php echo $fila['total']; ?>">

<input type="submit" data-bs-toggle="modal" data-bs-target="#pacienteconelevacion" value="+Cargar Paciente"
    id="boton_paciente">

<table class="table table-responsive  table-bordered table-striped table-hover display" id="lista-comentarios">


    <tbody>
        <input type="hidden" name="total_registro" id="total_registro" value="<?php echo $total_registro; ?>" />
        <?php
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        while($dataRegistro= $query->fetch())
        { ?>
        <div class="row border_special item-comentario" id="<?php echo $dataRegistro['id']; ?>">
            <?php
            $id = $dataRegistro['id'];

                if($dataRegistro['identificador'] == 'cest' and $dataRegistro['seguimiento'] == 'inicial'){
            ?>
            <tr>
                <td id='<?php echo $id ?>' class='ver-info' style="font-size: 12px">
                    <?php echo $dataRegistro['nombrecompleto'].'<br>'.'<strong style="font-size: 9px;">'.$dataRegistro['curp'].'</strong>'.'<br>'.'<strong style="float:right; font-size: 7px; margin-top: -20px;">&nbsp'.$dataRegistro['sexo'].'</strong>' ?>
                </td>

            </tr>
            <?php } 
        }?>
    </tbody>

</table>
</script>

<div class="col-md-12 col-sm-12">
    <div class="ajax-loader text-center">
        <img src="img/cargando.svg">
        <br>
        Cargando más Registros...
    </div>
</div>


<script>
$(function() {

    $('table').on('click', '.ver-info', function() {

        var id = $(this).prop('id');

        let ob = {
            id: id
        };
        $.ajax({
            type: "POST",
            url: "consultaPacienteBusqueda.php",
            data: ob,
            beforeSend: function() {

            },
            success: function(data) {

                $("#tabla_resultado").html(data);

            }
        });

    });
});
$(document).ready(function() {
    $('table').on('click', '.ver-info', function() {

        //Añadimos la imagen de carga en el contenedor
        $('#tabla_resultado').html(
            '<div id="tabla_resultado" style="position: fixed;  top: 0px; left: 0px;  width: 100%; height: 100%; z-index: 9999;  opacity: .7; background: url(imagenes/loader2.gif) 50% 50% no-repeat rgb(249,249,249);"><br/></div>'
        );


        return false;
    });
});


$(document).ready(function() {
    pageScroll();

    $('.ajax-loader').hide();

});

document.addEventListener('keydown', (event) => {

    if (event.keyCode == 8 || event.keyCode == 46) {
        $("#tabla_resultadobus").off("scroll");
    }
}, false);

function pageScroll() {
    $("#tabla_resultadobus").on("scroll", function() {
        var scrollHeight = $(document).height();
        var scrollPos = $("#tabla_resultadobus").height() + $("#tabla_resultadobus").scrollTop();
        var totalregistro = $("#totalregistro").val();

        if ((((scrollHeight - 250) >= scrollPos) / scrollHeight == 0) || (((scrollHeight - 300) >=
                scrollPos) / scrollHeight == 0) || (((scrollHeight - 350) >= scrollPos) / scrollHeight ==
                0) || (((scrollHeight - 400) >= scrollPos) / scrollHeight == 0) || (((scrollHeight - 450) >=
                scrollPos) / scrollHeight == 0) || (((scrollHeight - 500) >= scrollPos) / scrollHeight ==
                0)) {
            if ($(".item-comentario").length < $("#total_registro").val()) {
                var utimoId = $(".item-comentario:last").attr("id");


                $("#tabla_resultadobus").off("scroll");
                $.ajax({
                    url: 'obteniedoMasDatos.php?utimoId=' + utimoId + '&totalregistro' + totalregistro,
                    type: "get",
                    beforeSend: function() {
                        $('.ajax-loader').show();
                    },
                    success: function(data) {
                        setTimeout(function() {
                            $('.ajax-loader').hide();
                            $("#lista-comentarios").append(data);
                            pageScroll();
                        }, 1000);
                    }
                });


            } else {

            }
        }
    });
}
</script>