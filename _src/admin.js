$(document).ready(function() {
    
    $("#rfc").attr('value', '');
    $('#ok').hide();
    $('#labelRFC').attr('class', '');
    $('#errorG').hide();
    $('#errorP').hide();
    $('#procesando').hide();
    $('#serie').attr('value', '');
    $('#folio').attr('value', '');
    $('#labelSerie').attr('class', '');
    $('#labelFolio').attr('class', '');
    
    $('#folio').keydown(function(event) {
        // Allow: backspace, delete, tab, escape, and enter
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
             // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        else {
            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
            }   
        }
    });
        
});

function selectRfc(){
    var idRfc = $("#rfc").val();
    if (idRfc == '') {
        $('#formulario').hide("slow");
    } else {
        $('#formulario').show("slow");
    }
}

function probar(){
    
    $('#ok').hide();
    $('#labelRFC').attr('class', '');
    $('#errorG').hide();
    $('#errorP').hide();
    $('#labelSerie').attr('class', '');
    $('#labelFolio').attr('class', '');
    $('#procesando').show();
    
    $.ajax({
        type: 'POST',
        url: 'probarConexion.php',
        data: $('#formularioIndex').serialize(),
        success: function (result) {
            if (result.estatus == 'aceptado') {
                $('#procesando').hide();
                $('#ok').show();
            } else {
                if (result.estatus == 'rechazado') {
                    $('#labelRFC').attr('class', 'colorRojo');
                    $('#procesando').hide();
                    $('#errorG').html(result.textoError);
                    $('#errorG').show();
                    $('#errorP').show();
                } else{
                    if (result.estatus == 'error') {
                        $('#labelRFC').attr('class', 'colorRojo');
                        $('#procesando').hide();
                        $('#errorG').html(result.textoError);
                        $('#errorG').show();
                        $('#errorP').show();
                    }
                }
            }
            
            return false;
        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
            var rechazado = "<strong>Error:</strong> Error de conexi&oacute;n.<br/><strong>C&oacute;digo:</strong> 000";
            $('#errorG').html(rechazado);
            $('#errorG').show();
        }
    });
}

function consultar(){
    $('#ok').hide();
    $('#labelRFC').attr('class', '');
    $('#errorG').hide();
    $('#errorP').hide();
    $('#procesando').hide();
    
    var serie = $('#serie').val();
    var folio = $('#folio').val();
    var idRfc = $('#rfc').val();
    var error = '';
    
    if (idRfc < 1) {
        $('#labelRFC').attr('class', 'colorRojo');
        $('#errorG').html("<strong>Error:</strong> Error en el formulario: RFC vac&iacute;o. <br/> <strong>C&oacute;digo:</strong> 300");
        $('#errorG').show();
    } else {
        if (serie == '' && folio == '') {
                $('#labelSerie').attr('class', 'colorRojo');
                $('#labelFolio').attr('class', 'colorRojo');
                error = "<strong>Error:</strong> Error en el formulario: "
                      + "Serie vac&iacute;o, Folio vac&iacute;o."
                      + "<br/><strong>C&oacute;digo:</strong> 700";
            } else {
                if (serie == '') {
                    $('#labelSerie').attr('class', 'colorRojo');
                    serie = 'Serie vacia.';
                    error = "<strong>Error:</strong> Error en el formulario:"
                          + "<br>Serie vac&iacute;o."
                          + "<br/><strong>C&oacute;digo:</strong> 700";
                }

                if (folio == '') {
                    $('#labelFolio').attr('class', 'colorRojo');
                    folio = 'Folio vac&iacute;o.';
                    error = "<strong>Error:</strong> Error en el formulario:"
                          + "<br>Folio vac&iacute;o."
                          + "<br/><strong>C&oacute;digo:</strong> 700 ";
                }
            }

        if (error != '') {
            $('#errorG').html(error);
            $('#errorG').show();
        } else {
            window.open('detalleComprobante.php?f=' + folio + '&i=' + idRfc + '&s=' + serie, "","location=1,status=1,scrollbars=1,width=1200,height=1000");
        }
    }
}