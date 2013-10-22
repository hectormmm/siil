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

/*
 * Crear objeto de conneccion
 */
ini_set('display_errors', 1);
$o = new MysqlConnection();

/*
 * Obtener un listado de RFC
 */
$aRfc = $o->getRows("SELECT idOwner, RFC FROM CFDI_siil_owner;");

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
    <title>Consulta de CFDi :: SIIL-Enlace Fiscal</title>
    <link type="text/css" rel="stylesheet" href="../_src/lib/jquery-ui-1.8.16.custom/css/smoothness/jquery-ui-1.8.16.custom.css" />
    <link href="/_src/admin.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="main">
        
            <h1><span>Consulta de CFDi</span></h1>
            
            <div id="respuesta">
                
                <div class="version">
                    <span><strong>Versión <?php echo $sVersion; ?></strong></span>
                </div>
                
                <div>
                    <span><strong>Operaciones:</strong></span>
                </div>
                
                <div class="detalleForm">
                    
                    <div id="errorG" style="display:none;" class="divError errorG formatoLetra"></div>
                    
                    <form id="formularioIndex" name="formularioIndex" action="#" method="post" onSubmit="return false">
                        
                        <table width="100%">
                            <tr>
                                <td width="200" height="30">
                                    <span id="labelRFC"><strong>R.F.C. Emisor:</strong></span>
                                </td>
                                <td  colspan="4">
                                    <?php if (is_array($aRfc) && count($aRfc) > 0) { ?>
                                    <select id="rfc" name="rfc" onchange="selectRfc()">
                                        <option value="">-- Select --</option>
                                        <?php foreach ($aRfc as $nKey=>$aValue) { ?>
                                        <option value="<?php echo $aValue['idOwner']; ?>"><?php echo $aValue['RFC']; ?></option>
                                        <?php }?>
                                    </select>
                                    <?php }?>
                                </td>
                            </tr>
                        </table>
                        
                        <div id="formulario" style="display:none;">
                            
                            <table width="100%">
                                <tr>
                                    <td width="200" height="30">
                                        <span><strong>Probar Conexión:</strong></span>
                                    </td>
                                    <td colspan="4">
                                        <input id="probarC" name="probarC" type="button" value="Probar" onclick="probar();" style="cursor:pointer;"/>&nbsp;
                                        <span id="procesando" style="display:none;"><img src="../_img/procesando.gif" alt="..."/>&nbsp;Procesando</span>
                                        <span id="ok" style="display:none;" class="colorVerde"><img src="../_img/ok.png" alt="√" width="20" height="20"/>&nbsp;Conexión exitosa.</span>
                                        <span id="errorP" style="display:none;" class="colorRojo"><img src="../_img/error.png" alt="X" width="20" height="20"/>&nbsp;Error en la conexión.</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200" height="30">
                                        <span><strong>Consultar Comprobante:</strong></span>
                                    </td>
                                    <td>
                                        <input id="serie" name="serie" type="text" size="15"/>
                                    </td>
                                    <td>
                                        <input id="folio" name="folio" type="text" size="15"/>
                                    </td>
                                    <td width="78">
                                        <input id="consultarC" name="consultarC" type="button" value="Consultar" onclick="consultar();" style="cursor:pointer;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200" height="30">
                                        &nbsp;
                                    </td>
                                    <td align="center">
                                        <span id="labelSerie">Serie</span>
                                    </td>
                                    <td align="center">
                                        <span id="labelFolio">Folio</span>
                                    </td>
                                    <td>
                                        &nbsp;
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
            <h2><span>Enlace Fiscal</span></h2>
    </div>
    <script src="../_src/lib/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" language="javascript" type="text/javascript"></script>
    <script src="../_src/lib/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" language="javascript" type="text/javascript"></script>
    <script language="javascript" type="text/javascript" src="../_src/admin.js"></script>
</body>

</html>