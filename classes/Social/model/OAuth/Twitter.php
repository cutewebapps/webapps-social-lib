<?php

class Social_OAuth_Twitter
{
    public $strOauthToken  = '';
    public $strOauthSecret = '';

    /** @var Social_Application_Twitter  */
    protected $_app = null;

    public function setApplication( Social_Application_Twitter $app )
    {
        $this->_app = $app;
    }

    public function getAuthLink( $strRedirectBase = '' )
    {
        if ( !is_object( $this->_app ))
             throw new App_Exception( 'Twitter application was not specified' );

    	$strHost = $this->_app->getHost();
        $url = 'https://api.twitter.com/oauth/request_token';


        $strCallbackURL = $strHost . ( ( $strRedirectBase ) ?  $strRedirectBase : '/oauth/twitter/callback' );
        $config = array (
            'consumer_key'               => $this->_app->getConsumerKey(),
            'consumer_secret'            => $this->_app->getConsumerSecretKey(),
            // 'user_key'                   => $this->_app->getToken(),
            // 'user_secret'                => $this->_app->getTokenSecret(),
            'curl_ssl_verifyhost' => 0,
            'curl_ssl_verifypeer' => 0,
            'debug' => true,
            // 'oauth1_params' => array(
            //     'oauth_callback' => $strCallbackURL
            // )
        );
        if ( isset( $_SERVER['HTTP_USER_AGENT'] )) {
            $config['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }

        $TwitterConnect = new Social_OAuth_Twitter_Auth($config);
        Sys_Debug::dump( $TwitterConnect->apponly_request( array(

            'consumer_key'               => $this->_app->getConsumerKey(),
            'consumer_secret'            => $this->_app->getConsumerSecretKey(),
            'with_bearer' => true,
            'method' => 'POST',
            'url' => $url,
            'debug' => true,
            'curl_ssl_verifyhost' => 0,
            'curl_ssl_verifypeer' => 0,
            'params' => array(
                'oauth_callback' => $strCallbackURL
            )
        ) ));


        Sys_Debug::dump( $TwitterConnect->response );
        die;


        // $TwitterConnect->streaming_request('POST', $url, $params, $callback );
        $headers = $TwitterConnect->headers;
        $header = array();

        $signing_params = $TwitterConnect->extract_params($TwitterConnect->signing_params);
        $header[] = $TwitterConnect->base_string;
        $header[] = $TwitterConnect->signing_key;
        $header1  = $TwitterConnect->auth_header;


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
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($handle);
        $no = curl_errno($handle);
        if ( $no ) throw new Social_OAuth_Exception( curl_error ( $handle ) );
        curl_close($handle);

        Sys_Debug::alert( $result );

        $arrParts = explode( '&', $result );
        $this->strOauthToken = Sys_String::x('@oauth_token=(\w+)@sim', $result );
        $this->strOauthSecret = Sys_String::x('@oauth_token_secret=(\w+)@sim', $result );

        if ( ! $this->strOauthToken )
            throw new Social_OAuth_Exception( 'OAuth token could not be parsed' );

        return 'https://api.twitter.com/oauth/authenticate?oauth_token='.$this->strOauthToken."&oauth_callback=".$strRedirect;
    }
}


