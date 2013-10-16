$(document).ready(function() {
    
    $('#cargandoGenerarXml').show();
    $('#errorG').hide();
	
    $.ajax({
        type: 'POST',
        url: 'cancelarGenerarXml.php',
        
        success: function(result) {
            if (result.ERROR) {
            	$('#cargandoGenerarXml').hide();
                $('#errorG').html(result.ERROR);
                $('#errorG').show();
                $('#imgError').show();
                $('#imgErrorGenerarXml').show();
                $('#tablaComprobante').html('<strong>Error</strong>');
                $('#tablaComprobante').attr('class', 'colorRojo tituloTabla');
                $(window).unbind('beforeunload');
            
            } else if(result.DUPLICADA) {
                $('#tablaComprobante').addClass('colorVerde');
                $('#tablaComprobante').html('<strong>Éxito (petición duplicada)</strong>');
                $('#lineaDivi').hide();
                $('#proceso').hide();
                $('#imgExito').show();
                $('#fechaCancelacion').html(result.fechaCancelacion);
                $(window).unbind('beforeunload');
                
            } else if(result.SUCCESS) {
                $('#cargandoGenerarXml').hide();
            	$('#okGenerarXml').show();
            	$('#cargandoCancelar').show();
            	
                $.ajax({
                    type: 'POST',
                    url: 'cancelarCancelar.php',

                    success: function(result) {
                        if (result.ERROR) {
                            $('#cargandoCancelar').hide();
                            $('#errorG').html(result.ERROR);
                            $('#errorG').show();
                            $('#imgError').show();
                            $('#imgCancelar').show();
                            $('#tablaComprobante').html('<strong>Error</strong>');
                            $('#tablaComprobante').attr('class', 'colorRojo tituloTabla');
                            $(window).unbind('beforeunload');
                            
                        } else if(result.SUCCESS){
                            $('#cargandoCancelar').hide();
                            $('#okCancelar').show();
                            $('#cargandoGuardarOracle').show();
                            $.ajax({
                                type: 'POST',
                                url: 'cancelarGuardarOracle.php',

                                success: function(result) {
                                    if (result.ERROR) {
                                    $('#cargandoGuardarOracle').hide();
                                    $('#errorG').html(result.ERROR);
                                    $('#errorG').show();
                                    $('#imgError').show();
                                    $('#imgGuardarOracle').show();
                                    $('#tablaComprobante').html('<strong>Error</strong>');
                                    $('#tablaComprobante').attr('class', 'colorRojo tituloTabla');
                                    $(window).unbind('beforeunload');
                                    
                                    } else if(result.SUCCESS) {
                                        $('#cargandoGuardarOracle').hide();
                                        if(result.GUARDADO == 1){
                                            $('#okGuardarOracle').show();
                                        } else {
                                            $('#okGuardarOracleGris').show();
                                            $('#pruebaOracle').html(' - MODO PRUEBAS');
                                        }
                                        
                                        $('#imgExito').show();
                                        $('#fechaCancelacion').html(result.fechaCancelacion);
                                        $(window).unbind('beforeunload');
                                    } else {
                                        $('#cargandoGuardarOracle').hide();
                                        $('#imgGuardarOracle').show();
                                        $('#errorG').html('Error de conexión');
                                        $('#errorG').show();
                                        $('#imgError').show();
                                        $('#tablaComprobante').html('<strong>Error</strong>');
                                        $('#tablaComprobante').attr('class', 'colorRojo tituloTabla');
                                        $(window).unbind('beforeunload');
                                    }
                                }, error: function(XMLHttpRequest, textStatus, errorThrown) {
                                    $('#cargandoGuardarOracle').hide();
                                    $('#imgGuardarOracle').show();
                                    $('#errorG').html('Error de conexión');
                                    $('#errorG').show();
                                    $('#imgError').show();
                                    $('#tablaComprobante').html('<strong>Error</strong>');
                                    $('#tablaComprobante').attr('class', 'colorRojo tituloTabla');
                                    $(window).unbind('beforeunload');
                                }
                            });
                        }  else {
                            $('#cargandoCancelar').hide();
                            $('#imgCancelar').show();
                            $('#errorG').html('Error de conexión');
                            $('#errorG').show();
                            $('#imgError').show();
                            $('#tablaComprobante').html('<strong>Error</strong>');
                            $('#tablaComprobante').attr('class', 'colorRojo tituloTabla');
                            $(window).unbind('beforeunload');
                        }
                    }, error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $('#cargandoCancelar').hide();
                        $('#imgCancelar').show();
                        $('#errorG').html('Error de conexión');
                        $('#errorG').show();
                        $('#imgError').show();
                        $('#tablaComprobante').html('<strong>Error</strong>');
                        $('#tablaComprobante').attr('class', 'colorRojo tituloTabla');
                        $(window).unbind('beforeunload');
                    }
                });
            } else {
                $('#cargandoGenerarXml').hide();
                $('#imgErrorGenerarXml').show();
                $('#errorG').html('Error de conexión');
                $('#errorG').show();
                $('#imgError').show();
                $('#tablaComprobante').html('<strong>Error</strong>');
                $('#tablaComprobante').attr('class', 'colorRojo tituloTabla');
                $(window).unbind('beforeunload');
            }
        }, error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#cargandoGenerarXml').hide();
            $('#imgErrorGenerarXml').show();
            $('#errorG').html('Error de conexión');
            $('#errorG').show();
            $('#imgError').show();
            $('#tablaComprobante').html('<strong>Error</strong>');
            $('#tablaComprobante').attr('class', 'colorRojo tituloTabla');
            $(window).unbind('beforeunload');
        }
    });
    
    $(window).bind("beforeunload",function(event) {
        return "¡Atención! La cancelación del comprobante se encuentra en proceso, le pedimos esperar a que concluya para cerrar la ventana.";
    });
    
});