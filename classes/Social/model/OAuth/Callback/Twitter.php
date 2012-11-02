<?php

class Social_OAuth_Callback_Twitter
{
    /** @var Social_Application_Twitter */
    protected $_app = null;

    public function setApplication( $app )
    {
        $this->_app = $app;
    }
    
    public function register( $strOauthToken, $strOauthVerifier )
    {
        $url3 = 'https://api.twitter.com/oauth/access_token';
        $config = array (
            'consumer_key'               => $this->_app->getConsumerKey(),
            'consumer_secret'            => $this->_app->getConsumerSecretKey(),
        );
        $TwitterConnect = new Social_OAuth_Twitter_Auth($config);
        $TwitterConnect->streaming_request('POST', $url3);
        $signing_params = "oauth_verifier=" . $strOauthVerifier;
        $header1 = $TwitterConnect->auth_header;
        $header = $header1 . ", oauth_token=" . $strOauthToken;
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_POST, 1); // set POST method
        curl_setopt($handle, CURLOPT_URL, $url3); // set url to post to
        curl_setopt($handle, CURLOPT_POSTFIELDS, $signing_params); // add POST fields
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Authorization: ' . $header));
        curl_setopt($handle, CURLOPT_HEADER, FALSE);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, FALSE);// allow redirects
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE); // return into a variable
        curl_setopt($handle, CURLOPT_TIMEOUT, 20); // times out after 4s
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($handle);
        $no = curl_errno($handle);
        if ( $no ) throw new Social_OAuth_Exception( curl_error ( $handle ) );
        curl_close($handle);

        if ( ! $result )  throw new Social_OAuth_Exception( "empty result received" );
        $arrParts = explode( '&', $result );
        $strOauthToken = Sys_String::x('@oauth_token=(\w+)@sim', $result );
        $strOauthSecret = Sys_String::x('@oauth_token_secret=(\w+)@sim', $result );
        $strUserId = Sys_String::x('@user_id=(\w+)@sim', $result);

        
        $url4 = 'https://api.twitter.com/1/users/lookup.json?user_id=' . $strUserId;
        $TwitterConnect->config['user_secret'] = $this->_app->getTokenSecret();
        $TwitterConnect->config['user_token'] = $this->_app->getToken();
        
        $handle = curl_init();
            curl_setopt($handle, CURLOPT_POST, 0); // set POST method
            curl_setopt($handle, CURLOPT_URL, $url4); // set url to post to;
            curl_setopt($handle, CURLOPT_FOLLOWLOCATION, FALSE);// allow redirects
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE); // return into a variable
            curl_setopt($handle, CURLOPT_TIMEOUT, 20); // times out after 4s
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
        $result2 = curl_exec($handle);
        $no = curl_errno($handle);
        if ( $no )
            throw new Social_OAuth_Exception( curl_error ( $handle ) );
        curl_close($handle);

        $this->_arrUser = json_decode($result2, false );
         
        $this->_strID = $this->_arrUser[0]->id;
        $this->_strName = $this->_arrUser[0]->name;

        if ( !$this->_strID )
            throw new Social_OAuth_Exception( 'Empty twitter ID');
        if ( !$this->_strName )
            throw new Social_OAuth_Exception( 'Empty twitter Name');

	return null;
    }

    protected $_strID = '';
    protected $_strLastName = '';
    protected $_arrUser = array();

    /**
     * @return string
     */
    public function getID()
    {
        return $this->_strID;
    }
    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->_strLastName;
    }
    /**
     * @return array
     */
    public function getUserArray()
    {
        return $this->_arrUser;
    }
}