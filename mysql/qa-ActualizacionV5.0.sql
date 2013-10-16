-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 03, 2013 at 05:05 PM
-- Server version: 5.0.95
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: API_enlacefiscal
--

-- --------------------------------------------------------

--
-- Table structure for table CFDI_catalogoErrores
--

CREATE TABLE IF NOT EXISTS CFDI_catalogoErrores (
  idTipoError int(11) NOT NULL auto_increment,
  codigo int(11) NOT NULL,
  descripcion text collate utf8_spanish_ci NOT NULL,
  patronPublico text collate utf8_spanish_ci NOT NULL,
  patronPrivado text collate utf8_spanish_ci NOT NULL,
  PRIMARY KEY  (idTipoError)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=11 ;

--
-- Dumping data for table CFDI_catalogoErrores
--

INSERT INTO CFDI_catalogoErrores (idTipoError, codigo, descripcion, patronPublico, patronPrivado) VALUES
(1, 700, 'Error de formulario', 'Error en el formulario: @descripcion', 'Error en el formulario: @descripcion'),
(2, 701, 'No se encuentra el cliente en base de datos (CFDI_siil_owner)', 'El cliente @rfc no se encuentra en base de datos.', 'El cliente @rfc no se encuentra en base de datos.'),
(3, 702, 'No se encontro el comprobante a buscar.', 'No se encontro el comprobante del cliente: @rfc, con la serie: @serie y folio: @folio', 'No se encontro el comprobante del cliente: @rfc, con la serie: @serie y folio: @folio'),
(4, 703, 'Error al crear el objeto SoapClient', 'Error de conexión, intente mas tarde', '@error'),
(5, 704, 'Error al llamar el webservice', 'Error de conexión, intente mas tarde', '@error'),
(6, 705, 'Error que retorna el webservice', '@error', '@error'),
(7, 706, 'Error al parsear la respuesta de una peticion duplicada', '@error', '@error'),
(8, 707, 'Error al abrir el archivo preCFDi', 'Error al abrir el preCFDi, no existe el archivo o se encuentra mal formado', 'Error al abrir el preCFDi @error'),
(9, 708, 'Cuando no se puede escribir en la bd oracle', 'No se pudo escribir en BD Oracle', 'No se pudo escribir en BD Oracle'),
(10, 709,'Cuando no se tienen permisos para escribir el archivo qr generado', 'No se tienen permisos de escritura para guardar el archivo QR', 'No se tienen permisos de escritura para guardar el archivo QR');

-- --------------------------------------------------------

--
-- Table structure for table CFDI_errores
--

CREATE TABLE IF NOT EXISTS CFDI_errores (
  idError bigint(20) NOT NULL auto_increment,
  idTipoError int(11) NOT NULL,
  error text collate utf8_spanish_ci NOT NULL,
  PRIMARY KEY  (idError)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ;


--
-- Table structure for table CFDI_logMensajes
--

CREATE TABLE IF NOT EXISTS CFDI_logMensajes (
  idLogMensajes bigint(20) NOT NULL auto_increment,
  idOwner mediumint(9) NOT NULL,
  folioInterno bigint(20) default NULL,
  serie varchar(10) collate utf8_spanish_ci default NULL,
  tipoPeticion enum('1','2') collate utf8_spanish_ci default NULL COMMENT '1=’generación’, 2=’cancelación’',
  fechaPeticion datetime NOT NULL,
  xmlPeticion text collate utf8_spanish_ci,
  fechaRespuesta datetime default NULL,
  xmlRespuesta text collate utf8_spanish_ci,
  bError enum('0','1') collate utf8_spanish_ci default '0',
  idError bigint(20) default NULL,
  PRIMARY KEY  (idLogMensajes),
  KEY folioInterno (folioInterno,serie,idOwner),
  KEY peticionError (tipoPeticion,bError)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ;

--
-- Table structure for table CFDI_seriesTipoCFDI
--

CREATE TABLE IF NOT EXISTS CFDI_seriesTipoCFDI (
  idTipo int(10) NOT NULL auto_increment,
  serie varchar(25) collate utf8_spanish_ci NOT NULL,
  idTipoCFDI int(11) NOT NULL,
  idOwner mediumint(9) NOT NULL,
  PRIMARY KEY  (idTipo)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ;


-- --------------------------------------------------------

--
-- Table structure for table CFDI_siil_owner
--

CREATE TABLE IF NOT EXISTS CFDI_siil_owner (
  idOwner mediumint(9) NOT NULL auto_increment,
  RFC varchar(14) collate utf8_spanish_ci NOT NULL,
  owner varchar(255) collate utf8_spanish_ci NOT NULL,
  token varchar(32) collate utf8_spanish_ci NOT NULL,
  rfcEF varchar(14) collate utf8_spanish_ci NOT NULL,
  bDebug enum('0','1') collate utf8_spanish_ci NOT NULL,
  PRIMARY KEY  (idOwner)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table CFDI_siil_owner
--

INSERT INTO CFDI_siil_owner (idOwner, RFC, owner, token, rfcEF, bDebug) VALUES
(1, 'AAA010101AAA', 'siil_demo', '827ccb0eea8a706c4c34a16891f84e7b', 'AAA010101AAA', '1');

-- --------------------------------------------------------

--
-- Table structure for table CFDI_tiempoRespuesta
--

CREATE TABLE IF NOT EXISTS CFDI_tiempoRespuesta (
  idTiempoRespuesta bigint(20) NOT NULL auto_increment,
  idLogMensaje bigint(20) NOT NULL,
  abrirPreCFDI double NOT NULL,
  timbrar double NOT NULL,
  guardarQR double NOT NULL,
  guardarOracle double NOT NULL,
  total double NOT NULL,
  PRIMARY KEY  (idTiempoRespuesta),
  KEY idLogMensaje (idLogMensaje)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Table structure for table CFDI_tiempoRespuestaCancelar
--

CREATE TABLE IF NOT EXISTS CFDI_tiempoRespuestaCancelar (
  idTiempoRespuestaCancelar bigint(20) NOT NULL auto_increment,
  idLogMensaje bigint(20) NOT NULL,
  generarXML double NOT NULL,
  cancelar double NOT NULL,
  guardarOracle double NOT NULL,
  total double NOT NULL,
  PRIMARY KEY  (idTiempoRespuestaCancelar)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ;

--
-- Table structure for table CFDI_tiposCFDI
--

CREATE TABLE IF NOT EXISTS CFDI_tiposCFDI (
  idTipoCFDI tinyint(10) NOT NULL auto_increment,
  nombre varchar(255) collate utf8_spanish_ci NOT NULL default '',
  nombrePlural varchar(255) collate utf8_spanish_ci NOT NULL default '',
  b_masculinoOFemenino enum('0','1') collate utf8_spanish_ci NOT NULL default '0',
  tipoDeComprobante enum('ingreso','egreso','traslado') collate utf8_spanish_ci NOT NULL default 'ingreso',
  PRIMARY KEY  (idTipoCFDI)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table CFDI_tiposCFDI
--

INSERT INTO CFDI_tiposCFDI (idTipoCFDI, nombre, nombrePlural, b_masculinoOFemenino, tipoDeComprobante) VALUES
(1, 'Factura', 'Facturas', '1', 'ingreso'),
(2, 'Nota de Crédito', 'Notas de Crédito', '1', 'egreso'),
(3, 'Nota de Cargo', 'Notas de Cargo', '1', 'ingreso'),
(4, 'Recibo de Honorarios', 'Recibos de Honorarios', '0', 'ingreso'),
(5, 'Recibo de Arrendamiento', 'Recibos de Arrendamiento', '0', 'ingreso'),
(6, 'Recibo de Donativo', 'Recibos de Donativos', '0', 'ingreso'),
(7, 'Recibo de Pago', 'Recibos de Pago', '0', 'ingreso'),
(8, 'Carta Porte', 'Cartas Porte', '1', 'traslado');

-- --------------------------------------------------------

--
-- Table structure for table CFDI_versiones
--

CREATE TABLE IF NOT EXISTS CFDI_versiones (
  id tinyint(4) NOT NULL auto_increment,
  version varchar(50) collate utf8_spanish_ci NOT NULL,
  fecha datetime NOT NULL,
  bVigente enum('0','1') collate utf8_spanish_ci NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table CFDI_versiones
--

INSERT INTO CFDI_versiones (id, version, fecha, bVigente) VALUES
(1, '5.0', '2013-09-30 00:00:00', '1');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
