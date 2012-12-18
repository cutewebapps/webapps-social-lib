<?php

class Social_Application_LinkedIn
{
    protected $_strToken             = '';
    protected $_strTokenSecret       = '';
    protected $_strConsumerKey       = '';
    protected $_strConsumerSecretKey = '';

    public function getToken()             { return $this->_strToken; }
    public function getTokenSecret()       { return $this->_strTokenSecret; }
    public function getConsumerKey()       { return $this->_strConsumerKey; }
    public function getConsumerSecretKey() { return $this->_strConsumerSecretKey; }

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
            
            $confSocialAppsLinkedIn = $confSocialApps->linkedin;
            if ( !is_object( $confSocialAppsLinkedIn ))
                throw new Social_Application_Exception( 'no social/apps/twitter section in config' );

            if ( !is_object( $confSocialAppsLinkedIn->$strApplicationName ))  
                throw new Social_Application_Exception( 'twitter social application not found in config' );
            
            $config = $confSocialAppsLinkedIn->$strApplicationName->toArray();
        }

        $this->_strToken             = $config[ 'api_key'];
        $this->_strTokenSecret       = $config[ 'secret_key'];
        $this->_strConsumerKey       = $config[ 'oauth_user_token'];
        $this->_strConsumerSecretKey = $config[ 'oauth_user_secret'];

        if ( !$this->_strToken) 
            throw new Social_Application_Exception( 'oauth_token is not specified for LinkedIn application '.$strApplicationName );
        if ( !$this->_strTokenSecret) 
            throw new Social_Application_Exception( 'oauth_token_secret is not specified  for LinkedIn application '.$strApplicationName );
        if ( !$this->_strConsumerKey) 
            throw new Social_Application_Exception( 'oauth_consumer_key is not specified  for LinkedIn application '.$strApplicationName );
        if ( !$this->_strConsumerSecretKey) 
            throw new Social_Application_Exception( 'oauth_consumer_secret_key is not specified  for LinkedIn application '.$strApplicationName );
    }
}
