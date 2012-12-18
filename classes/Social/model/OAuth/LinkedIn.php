<?php

class Social_OAuth_LinkedIn
{
    /** @var Social_Application_LinkedIn  */
    protected $_app = null;
    protected $_LinkedInConnect = null;

    public function setApplication( Social_Application_LinkedIn $app )
    {
        $this->_app = $app;
    }

    public function getTokenSecret()
    {
         return $this->_LinkedInConnect->getTokenSecret();
    }
    
    public function getAuthLink()
    {
        
        if ( !is_object( $this->_app ))
             throw new App_Exception( 'Linked In application was not specified' );

        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $strHost = $_SERVER['HTTP_HOST'];
        $strRedirect = $protocol . '://' . $strHost . '/oauth/linkedin/callback';
        $url = 'https://api.linkedin.com/uas/oauth/requestToken';

        $config = array (
            'appKey'                => $this->_app->getToken(),
            'appSecret'            => $this->_app->getTokenSecret(),
            'callbackUrl'           => $strRedirect,
        );
        $this->_LinkedInConnect = new Social_OAuth_LinkedIn_Auth($config);
        $response = $this->_LinkedInConnect->retrieveTokenRequest();
        
        $strOauthTokenSecret = $response['linkedin']['oauth_token_secret'];
        $this->_LinkedInConnect->setTokenSecret($strOauthTokenSecret);
        
        
        //Sys_Debug::dumpdie($this->_LinkedInConnect->getTokenSecret());
        //Sys_Debug::dumpdie($response);
        
        if($response['success'] === TRUE) {
            // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
            return Social_OAuth_LinkedIn_Auth::_URL_AUTH . $response['linkedin']['oauth_token'];
        } else {
            // bad token request
            throw new Social_OAuth_Exception( 'OAuth token could not be parsed' );
        }
    }
}


