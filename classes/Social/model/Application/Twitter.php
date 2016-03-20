<?php

class Social_Application_Twitter extends Social_Application_Abstract
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
	parent::__construct( $strApplicationName, $config);

        if ( empty( $config ) ) {
            // put more userfriendly exceptions in case of invalid app configuration
            $confSocial = App_Application::getInstance()->getConfig()->social;
            if ( !is_object( $confSocial ))
                throw new Social_Application_Exception( 'no social section in config' );

            $confSocialApps = $confSocial->apps;
            if ( !is_object( $confSocialApps ))
                throw new Social_Application_Exception( 'no social/apps section in config' );
            
            $confSocialAppsTwitter = $confSocialApps->twitter;
            if ( !is_object( $confSocialAppsTwitter ))
                throw new Social_Application_Exception( 'no social/apps/twitter section in config' );

            if ( !is_object( $confSocialAppsTwitter->$strApplicationName ))  
                throw new Social_Application_Exception( 'twitter social application not found in config' );
            
            $config = $confSocialAppsTwitter->$strApplicationName->toArray();
        }

        $this->_strToken             = isset( $config[ 'oauth_token'] ) ? $config[ 'oauth_token'] : '';
        $this->_strTokenSecret       = isset( $config[ 'oauth_token_secret'] ) ? $config[ 'oauth_token_secret'] : '';
        $this->_strConsumerKey       = $config[ 'oauth_consumer_key'];
        $this->_strConsumerSecretKey = $config[ 'oauth_consumer_secret_key'];

        // if ( !$this->_strToken) 
           // throw new Social_Application_Exception( 'oauth_token is not specified for Twitter application '.$strApplicationName );
        // if ( !$this->_strTokenSecret) 
           // throw new Social_Application_Exception( 'oauth_token_secret is not specified  for Twitter application '.$strApplicationName );
        if ( !$this->_strConsumerKey) 
            throw new Social_Application_Exception( 'oauth_consumer_key is not specified  for Twitter application '.$strApplicationName );
        if ( !$this->_strConsumerSecretKey) 
            throw new Social_Application_Exception( 'oauth_consumer_secret_key is not specified  for Twitter application '.$strApplicationName );
    }
}
