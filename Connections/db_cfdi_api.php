<?php
include_once 'db_configuracion.php';

class MysqlConnection
{
    private $sHost     = HOST;
    
    private $sDatabase = DATABASE;
    
    private $sUser     = USER;
    
    private $sPassword = PASS;
    
    public $oMysqli;
    
    
    function __construct()
    {
    	$this->Connection();
    }
    
    private function Connection ()
    {
        $this->oMysqli = mysqli_connect($this->sHost, 
                                    $this->sUser, 
                              		$this->sPassword, 
                              		$this->sDatabase);
        
        mysqli_query($this->oMysqli, 'SET NAMES utf8');
        
    }
    
    public function getRows ($sQuery)
    {
        $hResult = @mysqli_query($this->oMysqli, $sQuery);
        
        $aResult = array();
        
        if ($hResult != false) {
        	while ($row = mysqli_fetch_assoc($hResult)) {
            	$aResult[] = $row;
        	}
        }
        
        return $aResult;
    }
    
    public function insert ($sQuery)
    {
        $hResult = mysqli_query($this->oMysqli, $sQuery);
        /*if("INSERT INTO CFDI_errores (idTipoError, error) VALUES (1, 'Usuario o Contrasea no valido')" == $sQuery)
        exit('dsad-'.mysqli_error($this->oMysqli));*/
        if ($hResult == false) {
            return false;
        } else {
            return true;
        }
    }
    
    public function CloseConnection ()
    {
        mysqli_close($this->oMysqli);
    }
}

/*

ini_set('display_errors', 1);
$o = new MysqlConnection();

print_r($o->getRows("SELECT * FROM API_siil_owner"));
//$bConect = mysqli_real_query($o->oMysqli, "SELECT * FROM API_siil_owner");

*/