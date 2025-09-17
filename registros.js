$(buscar_registro());

function buscar_registro(consulta) {
    $.ajax({
        url: 'buscar_registro.php',
        type: 'POST',
        dataType: 'html',
        data: { consulta: consulta },
    })
    .done(function(respuesta) {
        $("#datos").html(respuesta);
    })
    .fail(function() {
        console.log("error");
    });
}

$(document).on('keyup', '#caja-busqueda', function() {
    var valor = $(this).val();
    if (valor != "") {
        buscar_registro(valor);
    } else {
        buscar_registro();
    }
});