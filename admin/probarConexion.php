 <?php
/*
 * Probar conexión de un cliente API Web Service de SIIL
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
 * Obtener y validar datos post
 */
$nRfc   = isset($_POST['rfc'])? $_POST['rfc'] : '';

if (empty($nRfc)) {
    $aParametros = array('@descripcion' => 'RFC vac&iacute;o.');
    $sError = Errores::procesarError(700, $aParametros);
    
    $aRespuesta = array(
                        'estatus' => 'error', 
                        'textoError' => $sError
                       );
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($aRespuesta);
    exit();
}

/*
 * Crear objeto de conneccion
 */
ini_set('display_errors', 1);
$o = new MysqlConnection();

/*
 * Obtener y validar RFC
 */
$aOwner = $o->getRows("SELECT token, rfcEF FROM CFDI_siil_owner WHERE idOwner = {$nRfc};");

if (is_array($aOwner) && count($aOwner) < 1) {
    $aParametros = array('@descripcion' => 'No se encontr&oacute; RFC en base de datos.');
    $sError = Errores::procesarError(700, $aParametros);
    
    $aRespuesta = array(
                        'estatus' => 'error', 
                        'textoError' => $sError
                       );
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($aRespuesta);
    exit();
}

//Definir variables a utilizar
$sRfcDB = $aOwner[0]['rfcEF'];
$sToken = $aOwner[0]['token'];

/*
 * Crear xml probar conexion
 */
$sXml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<Solicitud xmlns="https://esquemas.enlacefiscal.com/EF/API_CFDi/Solicitudes"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="https://esquemas.enlacefiscal.com/EF/API_CFDi/Solicitudes">
    <rfc>{$sRfcDB}</rfc>
    <accion>probarConexion</accion>
    <modo>produccion</modo>
</Solicitud>

EOF;

/*
 * Crear arreglo con parametro para el soap
 */
$aParametros = array(
                        'login'    => $sRfcDB,
                        'password' => $sToken
                    );

/*
 * Crear objeto SoapClient
 */
try{
		
    $oSoapClient=  new SoapClient("https://api.enlacefiscal.com/wsdl/APIEF_cfdi.wsdl", $aParametros);

}catch(SoapFault $oException){
    $sError = Errores::procesarError(703);
    $aRespuesta = array(
                        'estatus' => 'error',
                        'textoError' => $sError
                       );
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($aRespuesta);
    exit();

}

/*
 * Llamar probar conexion
 */
try {
    
    $sRSoapCliente = $oSoapClient->probarConexion($sXml);
    
} catch (SoapFault $oException){
    $sError = Errores::procesarError(704);
    $aRespuesta = array(
                        'estatus' => 'error',  
                        'textoError' => $sError
                       );
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($aRespuesta);
    exit();
    
}

/*
 * Parcear la respuesta
 */
$oXml = new DOMDocument;
$oXml->loadXML($sRSoapCliente);

//Obtener Estatus del documento
$sEstatusDoc = $oXml->documentElement->attributes->getNamedItem("estatusDocumento")->nodeValue;

if ($sEstatusDoc == 'aceptado') {
    $aRespuesta = array('estatus' => 'aceptado');
} else {
    $sCodigoError = $oXml->getElementsByTagName('codigoError')->item(0)->nodeValue;
    $sTextoError = $oXml->getElementsByTagName('texto')->item(0)->nodeValue;
    
    $aRespuesta = array(
                        'estatus' => 'rechazado', 
                        'codigoError' => $sCodigoError, 
                        'textoError' => $sTextoError
                       );
}

/*
 * Retornar respuesta
 */
header('Content-Type: application/json; charset=utf-8');
echo json_encode($aRespuesta);
exit();