<?php
/*
 * https://code.google.com/apis/console/b/0/#project:667369836148:access

Product name:	Hawp.net
Google account:	webcerebrium@gmail.com
Home page URL:	http://hawp.net/


Client ID: 667369836148.apps.googleusercontent.com
Email address: 667369836148@developer.gserviceaccount.com
Client secret: eXlHzDQ8rwM0wpdSc2gSH-Lg
Redirect URIs:	https://www.hawp.net/oauth2callback
JavaScript origins:	https://www.hawp.net
 * 
 * *
 */
class Social_OAuth_Google 
{
    /** @var Social_Application_Google  */
    protected $_app = null;

    public function setApplication( Social_Application_Google $app )
    {
        $this->_app = $app;
    }

    public function getAuthLink()
    {
        if ( !is_object( $this->_app ))
             throw new App_Exception( 'Google application was not specified' );


        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $strHost = $_SERVER['HTTP_HOST'];
        if ( isset( $_SERVER['HTTP_PORT'])  && $_SERVER['HTTP_PORT'] != 80 ) $strHost .= ':'.$_SERVER['HTTP_PORT'];
        $strCallback = $protocol . '://' . $strHost .  '/oauth/google/callback';

        $strClientId = $this->_app->getClientId();

        $strScope = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';
        return "https://accounts.google.com/o/oauth2/auth?redirect_uri="
                  . urlencode( $strCallback ) ."&response_type=code&client_id=$strClientId"
                    ."&scope=" .urlencode( $strScope );
    }
}