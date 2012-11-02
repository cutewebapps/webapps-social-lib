<?php

class Social_OAuth_Facebook
{
    /** @var Social_Application_Facebook  */
    protected $_app = null;

    public function setApplication( Social_Application_Facebook $app )
    {
        $this->_app = $app;
    }

    public function getAuthLink()
    {
        if ( !is_object( $this->_app ) )
            throw new Social_OAuth_Exception ("Facebook application was not specified");

        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $strHost = $_SERVER['HTTP_HOST'];
        if ( isset( $_SERVER['HTTP_PORT'])  && $_SERVER['HTTP_PORT'] != 80 ) $strHost .= ':'.$_SERVER['HTTP_PORT'];
        
        $strCallback = $protocol . '://' . $strHost .  '/oauth/facebook/callback';

        return $protocol .= "://www.facebook.com/dialog/oauth/?"
            . '&client_id='.$this->_app->getId()
            . '&redirect_uri='.$strCallback
            . '&scope='.$this->_app->getScope();
    }

}