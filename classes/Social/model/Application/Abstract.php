<?php

abstract class Social_Application_Abstract  extends App_Parameter_Storage
{
    protected $host = '';

    public function __construct( $strApplicationName, $config = array() )
    {
	if ( isset( $config[ 'host' ] ) )  {
		$this->host = $config['host'];
	} else {
		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        		$this->host = $protocol.'://'.$_SERVER['HTTP_HOST'];
			if ( isset( $_SERVER['HTTP_PORT'] ) && $_SERVER['HTTP_PORT'] != 80 ) $this->host .= ':'.$_SERVER['HTTP_PORT'];
		}
	}
    } 

    /**
     * @return string
     */
    public function getHost()
    {
	return $this->host;
    }
}