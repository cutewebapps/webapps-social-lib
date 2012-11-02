<?php

class Social_OAuth_Callback_Facebook
{
    /** @var string */
    protected $_strAccessToken = null;
    /** @var array */
    protected $_arrGraphData = null;

    /** @var Social_Application_Facebook */
    protected $_app = null;

    public function setApplication( $app )
    {
        $this->_app = $app;
    }

    public function register( $strCode )
    {
        $this->_arrGraphData = $this->getGraphFromCode($strCode);
        return $this->save( $this->_arrGraphData );
    }

    protected function _fetchUrl( $url )
    {
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_HEADER, 0 );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	$res = curl_exec( $ch );
	$no = curl_errno($ch);
        if ( $no ) throw new Social_OAuth_Exception( curl_error ( $ch ) );
        
        curl_close( $ch );
        //Sys_Debug::dump( $res );
	return $res;
    }
    
    public function getGraphFromCode( $paramFacebookCode )
    {
        // accepting data after dialog of authorization
       
        $strTokenUrl = implode( '&', array(
            'https://graph.facebook.com/oauth/access_token?client_id=' . $this->_app->getId(),
            'redirect_uri=' . rawurlencode( $this->_app->getUri() ),
            'client_secret=' . $this->_app->getSecret(),
            'code=' . $paramFacebookCode ) );
         
        // requesting Graph API for details
        $this->_strAccessToken = $this->_fetchUrl( $strTokenUrl );

	if ( $this->_strAccessToken == '' )
	    throw new Social_OAuth_Exception( 'Access token was not received' );
	    
	if ( substr( $this->_strAccessToken, 0, 1 ) == '{' ) {
	    $dataInsteadOfAccessToken = json_decode( $this->_strAccessToken );
	    throw new Social_OAuth_Exception( 'Oauth Error: '. $dataInsteadOfAccessToken->error->message );
	} 
        
        $strGraphUrl = 'https://graph.facebook.com/me?' . $this->_strAccessToken;
        $this->_strRawGraphData = $this->_fetchUrl( $strGraphUrl );
        $this->_arrGraphData = (array)json_decode( $this->_strRawGraphData, false );
        $this->_strAccessToken = str_replace( 'access_token=', '', $this->_strAccessToken );
        return  $this->_arrGraphData;
    }

    public function save( $arrGraphData, $strAccessToken = '', $strSignedRequest = '' )
    {
        if ( $strAccessToken == '' )
            $strAccessToken = $this->_strAccessToken ;

//         Sys_Debug::dumpDie( $arrGraphData );

/*      
        $objFacebookUser = Facebook_User::Table()->identify( $arrGraphData );
        if ( !is_object( $objFacebookUser )) return false;

        $objFacebookUser->fbu_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '?';
        $objFacebookUser->fbu_raw = serialize( $arrGraphData );
        Sys_Debug::dumpDie( $objFacebookUser );
        $objFacebookUser->save();

        $this->view->objFacebookUser = $objFacebookUser;
//        $objSession->user = $objFacebookUser;
//        $objSession->access_token = $strAccessToken;
//        $objSession->signed_request = $strSignedRequest;

        $objFbLogs = Facebook_Log::Table()->createRow();
        $objFbLogs->fbl_user_id = $objFacebookUser->fbu_id;
        $objFbLogs->fbl_status = Facebook_Log::STATUS_SUCCESS;
        $objFbLogs->save( false );
*/
        $this->_strEmail = isset( $arrGraphData['email'] ) ? $arrGraphData['email'] : '';
        $this->_strFirstName = $arrGraphData['first_name'];
        $this->_strLastName = $arrGraphData['last_name'];
        $this->_strID = $arrGraphData['id'];

        return null;
    }


    protected $_strID = '';
    protected $_strEmail = '';
    protected $_strFirstName = '';
    protected $_strLastName = '' ;

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
        return $this->_arrGraphData;
    }

}
