<?php
/*
 * Consulta de CFDI para cliente API Web Service de SIIL
 * 2010 (c) Facturación Electrónica por Internet, S. de R.L. de C.V
 * http://www.enlacefiscal.com
 * 
 */

/*
 * Incluir archivos 
 */
require_once '../Connections/db_cfdi_api.php';
require_once '../_clases/Errores.php';

/*
 * Obtener datos post
 */
$sSerie = isset($_GET['s'])? $_GET['s'] : '';
$sFolio = isset($_GET['f'])? $_GET['f'] : '';
$nIdRfc = isset($_GET['i'])? $_GET['i'] : '';

/*
 * Validar datos post
 */
$aMensajeError  = array();
$sError         = array();
$nLogMensajes   = 0;
$aLogMensajes   = array();

if (empty($sSerie)) {
    $aMensajeError['serie'] = 'Serie vacía.';
}

if (empty($sFolio)) {
    $aMensajeError['folio'] = 'Folio vacío.';
}

if (empty($nIdRfc)) {
    $aMensajeError['rfc'] = 'RFC vacío.';
}

if (!empty($aMensajeError)) {
    $sMensaje   = implode(' ', $aMensajeError);
    $aParametros = array('@descripcion' => $sMensaje);
    $sError = Errores::procesarError(700, $aParametros);
} else {
    /*
     * Crear objeto de conneccion
     */
    ini_set('display_errors', 1);
    $o = new MysqlConnection();

    $sQuery     = "SELECT idOwner, RFC FROM CFDI_siil_owner WHERE idOwner = {$nIdRfc}";
    $aIdOwner   = $o->getRows($sQuery);
    
    if (is_array($aIdOwner) && count($aIdOwner) < 1) {
        $aParametros = array('@rfc' => '-');
        $sError = Errores::procesarError(701, $aParametros);
    } else {
        $aOwner = $aIdOwner[0];
        
        $sQuery = "SELECT * FROM CFDI_logMensajes WHERE idOwner = {$nIdRfc} AND folioInterno = {$sFolio} AND serie = '{$sSerie}' ORDER BY idLogMensajes DESC;";
        $aLogMensajes = $o->getRows($sQuery);
        $nLogMensajes = count($aLogMensajes);
        
        if (is_array($aLogMensajes) && $nLogMensajes < 1) {
            $aParametros = array('@rfc' => $aOwner['RFC'], '@serie' => $sSerie, '@folio' => $sFolio);
            $sError = Errores::procesarError(702, $aParametros);
        }
    }
}

/*
 * Error?
 */
if (empty($sError)) {
    $sStyle = "style='display:none;'";
} else {
    $sStyle = "";
}

/*
 * Obtener la version
 */
$sQuery = "SELECT version FROM CFDI_versiones WHERE bVigente='1' LIMIT 1;";
$aVersion = $o->getRows($sQuery);
$sVersion = isset($aVersion[0]['version'])? $aVersion[0]['version']: '-';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Detalle del comprobante CFDi :: SIIL-Enlace Fiscal</title>
    <link type="text/css" rel="stylesheet" href="../_src/lib/jquery-ui-1.8.16.custom/css/smoothness/jquery-ui-1.8.16.custom.css" />
    <link href="/_src/admin.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="main">
        
        <h1><span>Detalle del comprobante CFDi</span></h1>
        
        <div id="respuesta">
            
            <div class="version">
                <span><strong>Versión <?php echo $sVersion; ?></strong></span>
            </div>
            
            <div>
                <span><strong>Información del Comprobante:</strong></span>
            </div>
            
            <div class="detalleForm">
                
                <div id="errorG" <?php echo $sStyle; ?> class="divError errorG formatoLetra"><?php echo $sError; ?></div>
                
                <?php 
                    if ($nLogMensajes > 0) {
                    foreach ($aLogMensajes as $nKey=>$aValue) { 
                        $sStyle         = empty($aValue['bError'])? 'colorVerde' : 'colorRojo';
                        $sFolioInterno  = empty($aValue['folioInterno'])? '-' : $aValue['folioInterno'];
                        $sSerie         = empty($aValue['serie'])? '-' : $aValue['serie'];
                        $sFechaP        = empty($aValue['fechaPeticion'])? '-' : $aValue['fechaPeticion'];
                        $sFechaR        = empty($aValue['fechaRespuesta'])? '-' : $aValue['fechaRespuesta'];
                        $sFechaTFD      = '-';
                        $sTipoPeticion  = ($aValue['tipoPeticion'] == 1)? 'Generar' : 'Cancelar';
                        
                        if (empty($aValue['bError']) && !empty($aValue['xmlRespuesta']) && $aValue['tipoPeticion'] == 1) {
                           /*
                            * Parcear la respuesta
                            */
                           $oXml = new DOMDocument;
                           $oXml->loadXML($aValue['xmlRespuesta']);

                           //Obtener fecha de timbrado
                           $sFechaTFD = $oXml->getElementsByTagName('fechaTFD')->item(0)->nodeValue;
                           $sFechaTFD = str_replace('T', ' ', $sFechaTFD);
                        }
                        
                        //Tiempos
                        $sTiempos = false;
                        $sEstatus = '';
                        if (empty($aValue['bError'])) {
                            if ($aValue['tipoPeticion'] == 1) {
                                $sQuery = "SELECT * FROM CFDI_tiempoRespuesta WHERE idLogMensaje = {$aValue['idLogMensajes']};";
                                $aTiempos = $o->getRows($sQuery);
                            } else {
                                $sQuery = "SELECT * FROM CFDI_tiempoRespuestaCancelar WHERE idLogMensaje = {$aValue['idLogMensajes']};";
                                $aTiempos = $o->getRows($sQuery);
                            }
                            
                            if (is_array($aTiempos) && count($aTiempos) > 0) {
                                $sTiempos = true;
                                $sEstatus = 'Éxito';
                            } else {
                                $sEstatus = 'Éxito (Peticion duplicada)';
                            }
                        } else {
                            $sEstatus = 'Error';
                        }
                ?>
                <table width="100%">
                    <tr>
                        <td width="200"><strong>Estatus:</strong></td>
                        <td><span  class="<?php echo $sStyle; ?>"><strong><?php echo $sEstatus; ?></strong></span></td>
                    </tr>
                    <tr>
                        <td><strong>Tipo Petición:</strong></td>
                        <td><span  class="<?php echo $sStyle; ?>"><strong><?php echo $sTipoPeticion; ?></strong></span></td>
                    </tr>
                    <tr>
                        <td><strong>Folio Interno:</strong></td>
                        <td><?php echo $sFolioInterno; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Serie:</strong></td>
                        <td><?php echo $sSerie; ?></td>
                    </tr>
                    <?php if (empty($aValue['bError'])) { ?>
                    <?php if ($aValue['tipoPeticion'] == 1) {?>
                    <tr>
                        <td><strong>Fecha Timbrado:</strong></td>
                        <td><?php echo $sFechaTFD; ?></td>
                    </tr>
                    <?php }?>
                    <?php } ?>
                    <tr>
                        <td><strong>Fecha Petición:</strong></td>
                        <td><?php echo $sFechaP; ?></td>
                    </tr>
                    <tr>
                        <td><strong>PreCFDi:</strong></td>
                        <td>
                            <?php if (!empty($aValue['xmlPeticion'])) { ?>
                                <img src="../_img/header-peticion_32.png" onClick="mostrarPeticion(<?php echo $aValue['idLogMensajes']; ?>)" style="cursor:pointer;" width="32" height="32" border="0" alt="PreCFDi" />
                            <?php } else {?>
                            -
                            <?php }?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Fecha Respuesta:</strong></td>
                        <td><?php echo $sFechaR; ?></td>
                    </tr>
                    <tr>
                        <td><strong>XmlRespuesta:</strong></td>
                        <td>
                            <?php if (!empty($aValue['xmlRespuesta'])) { ?>
                                <img src="../_img/header-respuesta_32.png" onClick="mostrarRespuesta(<?php echo $aValue['idLogMensajes']; ?>)" style="cursor:pointer;" width="32" height="32" border="0" alt="XmlRespuesta"/>
                            <?php } else {?>
                            -
                            <?php }?>
                        </td>
                    </tr>
                </table>
                
                <?php 
                if (empty($aValue['bError'])) {
                    if ($aValue['tipoPeticion'] == 1) {
                        if ($sTiempos) {
                ?>
                    <table width="100%">
                        <tr>
                            <td colspan="3" align="center" class="tituloTabla"><strong>Tiempos</strong></td>
                        </tr>
                        <tr>
                            <td width="250"><strong>Abrir archivo preCFDi:</strong></td>
                            <td align="right"><?php echo number_format(($aTiempos[0]['abrirPreCFDI'] * 1000), 2, '.', ','); ?></td>
                            <td width="200">&nbsp;&nbsp;&nbsp;ms</td>
                        </tr>
                        <tr>
                            <td><strong>Petición de timbrado:</strong></td>
                            <td align="right"><?php echo number_format(($aTiempos[0]['timbrar'] * 1000), 2, '.', ','); ?></td>
                            <td>&nbsp;&nbsp;&nbsp;ms</td>
                        </tr>
                        <tr>
                            <td><strong>Guardado QR:</strong></td>
                            <td align="right"><?php echo number_format(($aTiempos[0]['guardarQR'] * 1000), 2, '.', ','); ?></td>
                            <td>&nbsp;&nbsp;&nbsp;ms</td>
                        </tr>
                        <tr>
                            <td><strong>Guardado respuesta en Oracle:</strong></td>
                            <td align="right"><?php echo number_format(($aTiempos[0]['guardarOracle'] * 1000), 2, '.', ','); ?></td>
                            <td>&nbsp;&nbsp;&nbsp;ms</td>
                        </tr>
                    </table>
                <?php
                        }
                    } else {
                        if ($sTiempos) {
                ?>
                    <table width="100%">
                        <tr>
                            <td colspan="3" align="center" class="tituloTabla"><strong>Tiempos</strong></td>
                        </tr>
                        <tr>
                            <td width="250"><strong>Generar XML:</strong></td>
                            <td align="right"><?php echo number_format(($aTiempos[0]['generarXML'] * 1000), 2, '.', ','); ?></td>
                            <td width="200">&nbsp;&nbsp;&nbsp;ms</td>
                        </tr>
                        <tr>
                            <td><strong>Cancelar:</strong></td>
                            <td align="right"><?php echo number_format(($aTiempos[0]['cancelar'] * 1000), 2, '.', ','); ?></td>
                            <td>&nbsp;&nbsp;&nbsp;ms</td>
                        </tr>
                        <tr>
                            <td><strong>Guardar en Oracle:</strong></td>
                            <td align="right"><?php echo number_format(($aTiempos[0]['guardarOracle'] * 1000), 2, '.', ','); ?></td>
                            <td>&nbsp;&nbsp;&nbsp;ms</td>
                        </tr>
                    </table>
                <?php
                        }
                    }
                ?>
                
                <?php if (($nKey+1) != $nLogMensajes) { ?>
                <div class="linea"></div>
                <?php } ?>
                
                <?php } else {?>
                    <?php if (($nKey+1) < $nLogMensajes) { ?>
                        <div class="linea"></div>
                    <?php }?>
                <?php }}}?>
            </div>
            <p class="instruccion"><strong>Ya puede cerrar esta ventana.</strong></p>
        </div>
        <h2><span>Enlace Fiscal</span></h2>
    </div>
    
    <?php foreach ($aLogMensajes as $nKey=>$aValue) { ?>
    <div id="mostarPeticion<?php echo $aValue['idLogMensajes']; ?>" title="Xml Petición" style="display:none;">        
        <table class="tabla_detalle" >
            <tr>
                <td><?php echo '<pre>', htmlentities($aValue['xmlPeticion'], ENT_NOQUOTES, 'UTF-8'), '</pre>';?></td>
            </tr>
        </table>
    </div>

    <div id="mostarRespuesta<?php echo $aValue['idLogMensajes']; ?>" title="Xml Respuesta" style="display:none;">        
        <table class="tabla_detalle" >
            <tr>
                <td><?php echo '<pre>', htmlentities($aValue['xmlRespuesta'], ENT_NOQUOTES, 'UTF-8'), '</pre>';?></td>
            </tr>
        </table>
    </div>
    <?php } ?>
    
    <script src="../_src/lib/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" language="javascript" type="text/javascript"></script>
    <script src="../_src/lib/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" language="javascript" type="text/javascript"></script>
    <script language="javascript" type="text/javascript" src="../_src/detalleComprobante.js"></script>
</body>

</html>