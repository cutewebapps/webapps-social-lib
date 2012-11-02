<?php

class Social_OAuth_Twitter
{
    /** @var Social_Application_Twitter  */
    protected $_app = null;

    public function setApplication( Social_Application_Twitter $app )
    {
        $this->_app = $app;
    }

    public function getAuthLink()
    {
        if ( !is_object( $this->_app ))
             throw new App_Exception( 'Twitter application was not specified' );

        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $strHost = $_SERVER['HTTP_HOST'];
        $url = 'https://api.twitter.com/oauth/request_token';
        $strRedirect = $protocol . '://' . $strHost . '/oauth/twitter/callback';
        $config = array (
            'consumer_key'               => $this->_app->getConsumerKey(),
            'consumer_secret'            => $this->_app->getConsumerSecretKey(),
        );
        $TwitterConnect = new Social_OAuth_Twitter_Auth($config);
        //$TwitterConnect($config);
        $TwitterConnect->streaming_request('POST', $url);

        //Sys_Debug::dump( $TwitterConnect );
        $headers = $TwitterConnect->headers;
        $header = array();

        $signing_params = $TwitterConnect->extract_params($TwitterConnect->signing_params);
        $header[] = $TwitterConnect->base_string;
        $header[] = $TwitterConnect->signing_key;
        $header1 = $TwitterConnect->auth_header;


        $handle = curl_init();
        curl_setopt($handle, CURLOPT_POST, 1); // set POST method
        curl_setopt($handle, CURLOPT_URL, $url); // set url to post to
        curl_setopt($handle, CURLOPT_POSTFIELDS, $signing_params); // add POST fields
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Authorization: ' . $header1));
        curl_setopt($handle, CURLOPT_HEADER, FALSE);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, FALSE);// allow redirects
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE); // return into a variable
        curl_setopt($handle, CURLOPT_TIMEOUT, 20); // times out after 4s
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($handle);
        $no = curl_errno($handle);
        if ( $no ) throw new Social_OAuth_Exception( curl_error ( $handle ) );
        curl_close($handle);

        $arrParts = explode( '&', $result );
        $strOauthToken = Sys_String::x('@oauth_token=(\w+)@sim', $result );
        $strOauthSecret = Sys_String::x('@oauth_token_secret=(\w+)@sim', $result );

        if ( ! $strOauthToken )
            throw new Social_OAuth_Exception( 'OAuth token could not be parsed' );

        return 'https://api.twitter.com/oauth/authenticate?oauth_token='.$strOauthToken;
    }
}


