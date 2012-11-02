<?php

class Social_Application_Google
{
    protected $_strClientId = '';
    protected $_strSecret = '';

    public function getClientId() { return $this->_strClientId; }
    public function getSecret() { return $this->_strSecret; }

    public function __construct( $strApplicationName, $config = array() )
    {
        if ( empty( $config ) ) {
             // put more userfriendly exceptions in case of invalid app configuration
            $confSocial = App_Application::getInstance()->getConfig()->social;
            if ( !is_object( $confSocial ))
                throw new Social_Application_Exception( 'no social section in config' );

            $confSocialApps = $confSocial->apps;
            if ( !is_object( $confSocialApps ))
                throw new Social_Application_Exception( 'no social/apps section in config' );

            $confSocialAppsGoogle = $confSocialApps->google;
            if ( !is_object( $confSocialAppsGoogle))
                throw new Social_Application_Exception( 'no social/apps/google section in config' );

            if ( !is_object( $confSocialAppsGoogle->$strApplicationName ))
                throw new Social_Application_Exception( 'google social application not found in config' );

            $config = $confSocialAppsGoogle->$strApplicationName->toArray();
        }
        
        $this->_strClientId = $config[ 'client_id'];
        $this->_strSecret   = $config[ 'secret'];

        if ( !$this->_strClientId) throw new Social_Application_Exception( 'client_id is not specified for google application '.$strApplicationName );
        if ( !$this->_strSecret) throw new Social_Application_Exception( 'secret is not specified for google application '. $strApplicationName );

    }
}