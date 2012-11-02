<?php

/**
  * Include those routes to be a part of your CWA application config
  */

$strSocialApp = 'example-app';

return array(

	// example of social applications configuration
    'social' => array(
        'apps' => array(
            'google' => array(
		// Google application configuration
                'example-app' => array(
                    'client_id' => 'xxxxxxx.apps.googleusercontent.com',
                    'secret'    => 'xxxxxxxxxxxxxxxxxx-Lg',
                )
            ),
            'twitter' => array(
	         // Twitter application credentials - can be retrieved at:
                 // https://dev.twitter.com/apps/xxx/show
                'example-app' => array(
                    'oauth_token'               => '38136142-DMp0VkNZYDYWRQvRYHJAaQEDJ0OTmRSu56kgns73m',
                    'oauth_token_secret'        => 'Le63qsRNOiyjV5vGkbTKMq7WhabS2OQPzlIMj7iWW8',
                    'oauth_consumer_key'        => 'cGguarAUAqAphiRln2kMw',
                    'oauth_consumer_secret_key' => '1npKQ7MLLn194U5kHNBegj257gZgIoPjRtljQNa6P0',
                 ),
            ),
            'facebook' => array(
		// Facebook application credentials
		// http://www.facebook.com/developers/
                'example-app' => array(
                    'AppID'     => '112849478866913',
                    'AppSecret' => '4b3f5449ac666b3f23cf89838830dd4f',
                    'AppScope'  => 'email',
                    'ApiUrl'    => '/oauth/facebook/callback',
                ),
            ),
        )
    ),


	// FBauth registration - is for calling from javascript action which received callback
	// from successfull authorization
        'fbauth' => array(
            'route' => '/fbauth',
            'defaults' => array( 'module' => 'hawp', 'controller' => 'user', 'action' => 'fbauth', 'application' => $strSocialApp ),
        ),

        // start: social apps login
        'oauth/facebook/callback' => array(
            'route' => '/oauth/facebook/callback',
            'defaults' => array( 'module' => 'hawp', 'controller' => 'user', 'action' => 'oauth', 'provider' => 'facebook', 'application' => $strSocialApp ),
        ),
        'oauth/twitter/callback' => array(
            'route' => '/oauth/twitter/callback',
            'defaults' => array( 'module' => 'hawp', 'controller' => 'user', 'action' => 'oauth', 'provider' => 'twitter', 'application' => $strSocialApp ),
        ),
        'oauth/google/callback' => array(
            'route' => '/oauth/google/callback',
            'defaults' => array( 'module' => 'hawp', 'controller' => 'user', 'action' => 'oauth', 'provider' => 'google', 'application' => $strSocialApp ),
        ),
        'oauth/facebook' => array(
            'route' => '/oauth/facebook',
            'defaults' => array( 'module' => 'hawp', 'controller' => 'user', 'action' => 'login', 'provider' => 'facebook', 'application' => $strSocialApp),
        ),
        'oauth/twitter' => array(
            'route' => '/oauth/twitter',
            'defaults' => array( 'module' => 'hawp', 'controller' => 'user', 'action' => 'login', 'provider' => 'twitter', 'application' => $strSocialApp ),
        ),
        'oauth/google' => array(
            'route' => '/oauth/google',
            'defaults' => array( 'module' => 'hawp', 'controller' => 'user', 'action' => 'login', 'provider' => 'google', 'application' => $strSocialApp ),
        ),
        // end: social apps login
);