<?php

class Social_Application_Facebook
{
    protected $_strId     = '';
    protected $_strSecret = '';
    protected $_strScope  = 'email';
    protected $_strUrl    = '';
    protected $_strPageUrl    = '';

    /**
     * @param string $strApplicationName - name of application in the config
     */
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

            $confSocialAppsFacebook = $confSocialApps->facebook;
            if ( !is_object( $confSocialAppsFacebook))
                throw new Social_Application_Exception( 'no social/apps/facebook section in config' );


            if ( !is_object( $confSocialAppsFacebook->$strApplicationName ))
                throw new Social_Application_Exception( 'facebook social application not found in config' );

            $config = $confSocialAppsFacebook->$strApplicationName->toArray();
        }
        
        $this->_strId      = $config[ 'AppID'];
        $this->_strSecret  = $config[ 'AppSecret'];
        $this->_strScope   = isset( $config[ 'AppScope'] ) ? $config[ 'AppScope'] : '';
        $this->_strUrl     = isset( $config[ 'ApiUrl'] ) ? $config[ 'ApiUrl'] : '';
        $this->_strPageUrl = isset( $config[ 'PageUrl'] ) ? $config[ 'PageUrl'] : '';

        // correcting URL - make it absolute
        if ( substr( $this->_strUrl, 0, 1 ) == '/' && isset( $_SERVER[ 'HTTP_HOST' ] ) ) {
            $this->_strUrl = (( Sys_Mode::isSsl() ) ? 'https' : 'http' )
                    . '://' . $_SERVER[ 'HTTP_HOST' ] . $this->_strUrl;
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_strId;
    }
    /**
     * can be seen on application settings screen:
     * http://www.facebook.com/developers/apps.php?app_id=XXX
     * @return string
     */
    public  function getApiKey()
    {
        throw new App_Exception_Deprecated( "Using Facebook API key is deprecated, please use" );
    }
    /**
     * can be seen on application settings screen:
     * http://www.facebook.com/developers/apps.php?app_id=XXX
     * @return string
     */
    public function getSecret()
    {
        return $this->_strSecret;
    }
    /**
     * What data is required by application
     *
     * see full list of permissions here:
     * http://developers.facebook.com/docs/authentication/permissions
     * @return string
     */
    public function getScope()
    {
        return $this->_strScope;
    }
    /**
     * @return string
     */
    public function getUri()
    {
        return $this->_strUrl;
    }

    /** @return string */
    public function getPageUrl()
    {
        if ( $this->_strPageUrl == '' )
                throw new App_Exception('Face Page URL was not provided');

        $strUrl = (Sys_Mode::isSsl() ? 'https' : 'http' ).'://www.facebook.com' .$this->_strPageUrl;
        return $strUrl;
    }

    /**
     * this is example of Signed Request when you are beeing in Facebook tab
     * @return Array (
            [algorithm] => HMAC-SHA256
            [issued_at] => 1308905771
            [page] => Array (
                [id] => 132648240148230
                [liked] => 1
                [admin] =>
            )
            [user] => Array (
                [country] => ua
                [locale] => en_US
                [age] => Array (
                    [min] => 0
                    [max] => 12
                )
            )
        )
     */
    public function getSignedRequest()
    {
        if ( !isset($_POST['signed_request']) )
            return '';

        $signed_request = $_POST['signed_request'];
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        // decode the data
        $sig = self::base64UrlDecode($encoded_sig);
        $data = json_decode(self::base64UrlDecode($payload), true);

        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            throw new App_Exception('Unknown algorithm. Expected HMAC-SHA256');
            return null;
        }
        // check sig
        // this is used only when the corresponding settings is enabled in FB app
        
//        $expected_sig = hash_hmac('sha256', $payload,
//                              $this->getSecret(), $raw = true);
//        if ($sig !== $expected_sig) {
//            throw new App_Exception('Bad Signed JSON signature!');
//            return null;
//        }
        return $data;
    }

    /**
     * if there is no active request, takes the result from session
     * if there is active request, takes it as priority
     *
     * @return boolean
     */
    public function isPageLiked()
    {
        if ( isset( $_POST['signed_request'] ) ) {
            $arrRequest = $this->getSignedRequest();
            if ( isset( $arrRequest['page'] ) && isset( $arrRequest['page']['liked'] ) ) {
                    $objSession->$strLikeVarId = $arrRequest['page']['liked'];
                    return $arrRequest['page']['liked'];
            }
        } 
        return false;
    }

    /**
     * Detect whether we are on a facebook Page Tab
     * Note: this function may work only on first load of the page,
     *
     * if you will post/get the data inside Facebook iframe, it will be not working!
     *
     * @return boolean
     */
    public function isFacebookTab()
    {
        if ( !isset( $_POST['signed_request'] ) ) return false;
        $arrRequest = $this->getSignedRequest();
        return ( isset( $arrRequest['page'] ));
    }


    protected static function base64UrlDecode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }

}
