$(document).ready(function() {
    $.ajax({
        type: 'POST',
        url: 'generarAbrirCFDI.php',
        
        success: function(result) {
            $('#img_paso1').hide();
            $('#img_paso2').hide();
            $('#img_paso3').hide();
            $('#img_paso4').hide();

            if (result.ERROR) {
                $('#tituloOperacion').addClass('colorRojo');
                $('#tituloOperacion').html('<strong>Error</strong>');
                $('#errorG').html(result.ERROR);
                $('#errorG').show();
                $("#img_paso1").show()
                $('#img_paso1').attr('src','../_img/error.png');
                ;
                $('#imgError').show();
                $(window).unbind('beforeunload');
            
            } else if(result.DUPLICADA) {
                if (result.UUID) {
                    $('#folioFiscal').html(result.UUID);
                }
                
                if (result.fechaTimbre) {
                    $('#fechaTimbre').html(result.fechaTimbre);
                }
                
                $('#tituloOperacion').addClass('colorVerde');
                $('#tituloOperacion').html('<strong>Éxito (petición duplicada)</strong>');
                $('#lineaDivi').hide();
                $('#proceso').hide();
                $('#imgExito').show();

                $("#btn_imprimir").show();
                $(window).unbind('beforeunload');
                
            } else if(result.SUCCESS){
                $('#img_paso1').attr('src','../_img/palomita.gif');
                $("#img_paso1").show();
                $('#img_paso2').show();
                $('#img_paso2').attr('src','../_img/ajax-loader.gif');
                
                $.ajax({
                    type: 'POST',
                    url: 'generarTimbrar.php',

                    success: function(result) {
                        if (result.ERROR) {
                            $('#tituloOperacion').addClass('colorRojo');
                            $('#tituloOperacion').html('<strong>Error</strong>');
                            $('#errorG').html(result.ERROR);
                            $('#errorG').show();
                            $('#img_paso2').attr('src','../_img/error.png');
                            $('#imgError').show();
                            $(window).unbind('beforeunload');
                            
                        } else if(result.SUCCESS) {
                            $('#img_paso2').attr('src','../_img/palomita.gif');

                            $('#img_paso3').show();
                            $('#img_paso3').attr('src','../_img/ajax-loader.gif');
                            
                            if (result.UUID) {
                                $('#folioFiscal').html(result.UUID);
                                
                            }
                            
                            if (result.fechaTimbre) {
                                $('#fechaTimbre').html(result.fechaTimbre);
                            }
                            
                            $.ajax({
                                type: 'POST',
                                url: 'generarGuardarQr.php',

                                success: function(result) {
                                    if (result.ERROR) {
                                        $('#tituloOperacion').addClass('colorRojo');
                                        $('#tituloOperacion').html('<strong>Error</strong>');
                                        $('#errorG').html(result.ERROR);
                                        $('#errorG').show();
                                        $('#img_paso3').attr('src','../_img/error.png');
                                        $('#imgError').show();
                                        $(window).unbind('beforeunload');

                                    } else if(result.SUCCESS){
                                        
                                        
                                        $('#img_paso3').attr('src','../_img/palomita.gif');

                                        $("#img_paso4").show();
                                        $('#img_paso4').attr('src','../_img/ajax-loader.gif');


                                        $.ajax({
                                            type: 'POST',
                                            url: 'generarGuardarOracle.php',

                                            success: function(result) {
                                                if (result.ERROR) {
                                                    $('#tituloOperacion').addClass('colorRojo');
                                                    $('#tituloOperacion').html('<strong>Error</strong>');
                                                    $('#errorG').html(result.ERROR);
                                                    $('#errorG').show();
                                                    $('#img_paso4').attr('src','../_img/error.png');
                                                    $('#imgError').show();
                                                    $("#btn_imprimir").show();

                                                    $(window).unbind('beforeunload');
                                                } else if(result.SUCCESS){
                                                    if(result.GUARDADO == 1){
                                                        $('#img_paso4').attr('src','../_img/palomita.gif');
                                                    } else {
                                                        $('#img_paso4').attr('src','../_img/palomitaGris.gif');
                                                        $('#pruebaOracle').html(' - MODO PRUEBAS');
                                                    }
                                                    
                                                    $('#tituloOperacion').addClass('colorVerde');
                                                    $('#imgExito').show();
                                                    $('#tituloOperacion').addClass('colorVerde');
                                                    $('#tituloOperacion').html('<strong>Éxito</strong>');

                                                    $("#btn_imprimir").show();
                                                    $(window).unbind('beforeunload');
                                                } else {
                                                    $("#btn_imprimir").show();

                                                    $('#tituloOperacion').addClass('colorRojo');
                                                    $('#tituloOperacion').html('<strong>Error</strong>');
                                                    $('#errorG').html('Error Interno');
                                                    $('#errorG').show();
                                                    $('#img_paso4').attr('src','../_img/error.png');
                                                    $('#imgError').show();
                                                    $(window).unbind('beforeunload');
                                                }
                                            },
                                            error: function(XMLHttpRequest, textStatus, errorThrown) { 
                                                $('#tituloOperacion').addClass('colorRojo');
                                                $('#tituloOperacion').html('<strong>Error</strong>');
                                                $('#errorG').html('Error Interno');
                                                $('#errorG').show();
                                                $('#img_paso4').attr('src','../_img/error.png');
                                                $('#imgError').show();
                                                $("#btn_imprimir").show();

                                                $(window).unbind('beforeunload');
                                            }
                                        });
                                    } else {
                                        $('#tituloOperacion').addClass('colorRojo');
                                        $('#tituloOperacion').html('<strong>Error</strong>');
                                        $('#errorG').html('Error Interno');
                                        $('#errorG').show();
                                        $('#img_paso3').attr('src','../_img/error.png');
                                        $('#imgError').show();
                                        $(window).unbind('beforeunload');
                                    }
                                },
                                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                                    $('#tituloOperacion').addClass('colorRojo');
                                    $('#tituloOperacion').html('<strong>Error</strong>');
                                    $('#errorG').html('Error Interno');
                                    $('#errorG').show();
                                    $('#img_paso3').attr('src','../_img/error.png');
                                    $('#imgError').show();
                                    $(window).unbind('beforeunload');
                                }
                            });
                        } else {
                            $('#tituloOperacion').addClass('colorRojo');
                            $('#tituloOperacion').html('<strong>Error</strong>');
                            $('#errorG').html('Error Interno');
                            $('#errorG').show();
                            $('#img_paso2').attr('src','../_img/error.png');
                            $('#imgError').show();
                            $(window).unbind('beforeunload');
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) { 
                        $('#tituloOperacion').addClass('colorRojo');
                        $('#tituloOperacion').html('<strong>Error</strong>');
                        $('#errorG').html('Error Interno');
                        $('#errorG').show();
                        $('#img_paso2').attr('src','../_img/error.png');
                        $('#imgError').show();
                        $(window).unbind('beforeunload');
                    }
                });
            } else {
                $('#tituloOperacion').addClass('colorRojo');
                $('#tituloOperacion').html('<strong>Error</strong>');
                $('#errorG').html('Error Interno');
                $('#errorG').show();
                $('#img_paso1').attr('src','../_img/error.png');
                $('#imgError').show();
                $(window).unbind('beforeunload');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            $('#tituloOperacion').addClass('colorRojo');
            $('#tituloOperacion').html('<strong>Error</strong>');
            $('#errorG').html('Error Interno');
            $('#errorG').show();
            $('#img_paso1').attr('src','../_img/error.png');
            $('#imgError').show();
            $(window).unbind('beforeunload');
        }
    });
    
    $(window).bind("beforeunload",function(event) {
        return "¡Atención! El proceso de timbrado del comprobante se encuentra en proceso, le pedimos esperar a que concluya para cerrar la ventana.";
    });
    
});

function imprimirTicket (rfc, serie, folio)
{
    window.open('imprimirTicket.php?rfc='+rfc+'&serie='+serie+'&folio='+folio, '_blank', 'width=300, height=900');
}