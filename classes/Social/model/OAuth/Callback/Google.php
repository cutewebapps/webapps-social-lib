<?php

class Social_OAuth_Callback_Google
{


    /** @var Social_Application_Google */
    protected $_app = null;

    public function setApplication( $app )
    {
        $this->_app = $app;
    }

    public function register( $code )
    {
        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $strHost = $_SERVER['HTTP_HOST'];
        $url = 'https://accounts.google.com/o/oauth2/token';


        $strClientId = $this->_app->getClientId();
        $strSecret = $this->_app->getSecret();
        
        $strRedirect = $protocol . '://' . $strHost . '/oauth/google/callback';
        $strPOST = 'code=' . urlencode( $code ) . '&client_id=' . urlencode( $strClientId)
                .'&client_secret=' . urlencode( $strSecret )
                .'&redirect_uri=' . urlencode( $strRedirect )
                .'&grant_type=authorization_code';
        $google = curl_init();
        curl_setopt($google, CURLOPT_POST, 1); // set POST method
        curl_setopt($google, CURLOPT_URL, $url); // set url to post to
        curl_setopt($google, CURLOPT_POSTFIELDS, $strPOST); // add POST fields
        curl_setopt($google, CURLOPT_HEADER, false );
        curl_setopt($google, CURLOPT_FOLLOWLOCATION, FALSE);// allow redirects
        curl_setopt($google, CURLOPT_RETURNTRANSFER, TRUE); // return into a variable
        curl_setopt($google, CURLOPT_TIMEOUT, 20); // times out after 4s
        curl_setopt($google, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($google); // run the whole process
        $no = curl_errno($google);
        if ( $no ) throw new App_Exception( curl_error ( $google ) );

        $datajson = json_decode($result);
        curl_close($google);

        $access_token = $datajson->access_token;

        $token_type = $datajson->token_type;
        $id_token = $datajson->id_token;
        if ( empty($access_token))
            throw Social_OAuth_Exception( "Empty access token" );

        $strUrl = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $access_token;
        $googleUser = curl_init();
        curl_setopt($googleUser, CURLOPT_POST, 0); // set POST method
        curl_setopt($googleUser, CURLOPT_URL, $strUrl); // set url to post to
        curl_setopt($googleUser, CURLOPT_HEADER, false );
        curl_setopt($googleUser, CURLOPT_FOLLOWLOCATION, FALSE);// allow redirects
        curl_setopt($googleUser, CURLOPT_RETURNTRANSFER, TRUE); // return into a variable
        curl_setopt($googleUser, CURLOPT_TIMEOUT, 20); // times out after 4s
        curl_setopt($googleUser, CURLOPT_SSL_VERIFYHOST, 0);
        $result2 = curl_exec($googleUser);

        $result = curl_exec($googleUser); // run the whole process
        $no = curl_errno($googleUser);
        if ( $no ) throw new Social_OAuth_Exception( curl_error ( $googleUser ) );

        $this->_arrUser = json_decode( $result2, false );
        curl_close($googleUser);
        
        $this->_strEmail     = $this->_arrUser->email;
        $this->_strFirstName = $this->_arrUser->given_name;
        $this->_strLastName  = $this->_arrUser->family_name;
        //Sys_Debug::dumpDie( $this );
        return null;
    }

    protected $_strEmail = '';
    protected $_strFirstName = '';
    protected $_strLastName = '' ;
    protected $_arrUser = array();

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->_strEmail;
    }
    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->_strFirstName;
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