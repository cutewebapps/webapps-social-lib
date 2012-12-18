<?php

class Social_OAuth_Callback_LinkedIn
{
     /** @var Social_Application_LinkedIn */
    protected $_app = null;

    public function setApplication( $app )
    {
        $this->_app = $app;
    }
    
    public function register( $strOauthToken, $strOauthVerifier, $strTokenSecret )
    {
        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $strHost = $_SERVER['HTTP_HOST'];
        $strRedirect = $protocol . '://' . $strHost . '/oauth/linkedin/callback';
        $config = array (
            'appKey'                => $this->_app->getToken(),
            'appSecret'            => $this->_app->getTokenSecret(),
            'callbackUrl'           => $strRedirect,
        );

        $LinkedInConnect = new Social_OAuth_LinkedIn_Auth($config);
        $response = $LinkedInConnect->retrieveTokenAccess($strOauthToken, $strTokenSecret, $strOauthVerifier);
        $LinkedInConnect->setTokenAccess($response['linkedin']);
        $options = '~:(id,first-name,last-name,picture-url)';
        $response1 = $LinkedInConnect->profile($options);
        $this->_arrUser = json_decode($response1['linkedin']);
        $this->_strID = $this->_arrUser->id;
        $this->_strLastName = $this->_arrUser->lastName;
        $this->_strFirstName = $this->_arrUser->firstName;
        $this->_strPictureUrl = $this->_arrUser->pictureUrl;
        
        if ( !$this->_strID )
            throw new Social_OAuth_Exception( 'Empty LinkedIn ID');
        if ( !$this->_strLastName )
            throw new Social_OAuth_Exception( 'Empty LinkedIn Name');
    }
    protected $_strPictureUrl = '';
    protected $_strID = '';
    protected $_strFirstName = '';
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
        return $this->_strName;
    }
    /**
     * @return array
     */
    public function getUserArray()
    {
        return $this->_arrUser;
    }
}