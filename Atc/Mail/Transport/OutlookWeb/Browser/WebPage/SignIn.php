<?php
require_once 'Atc/Mail/Transport/OutlookWeb/Browser/WebPage.php';

/**
 * @package Atc
 * @author Matthew Hayes <Matthew.Hayes@AllThingsCode.com>
 */
final class Atc_Mail_Transport_OutlookWeb_Browser_WebPage_SignIn extends Atc_Mail_Transport_OutlookWeb_Browser_WebPage
{
    // All properties are stored in the parent class.
    // ------------------------------------------------------------------------


    /**
     * @param Atc_Mail_Transport_OutlookWeb_Browser
     */
    public function __construct( Atc_Mail_Transport_OutlookWeb_Browser &$browser )
    {
        // Call the parent constructor
        parent::__construct( $browser );
    }

    // ----- Setters/Getters --------------------------------------------------


    // ------------------------------------------------------------------------




    // ----- Public Methods ---------------------------------------------------

    /**
     * @return void
     */
    public function postSignInForm()
    {
        $browser     = $this->_getBrowser();
        $curlSession = $browser->getCurlSession();
        $curlHandle  = $curlSession->getHandle();

        $headers = array(
        	'Content-Type: application/x-www-form-urlencoded',
        	'User-Agent: ' . $browser->getUserAgent()
        	);

        $postData = array();
        $postData['destination']    = $browser->getBaseOutlookUrl() . '/owa/';
        $postData['flags']          = '0';
        $postData['forcedownlevel'] = '0';
        $postData['trusted']        = '0';
        $postData['username']       = $browser->getUsername();
        $postData['password']       = $browser->getPassword();
        $postData['isUtf8']         = '1';
        $postString = $browser->CreatePostString( $postData );

        $curlOptions = array( CURLOPT_URL        => $browser->getBaseOutlookUrl() . '/owa/auth/owaauth.dll',
                              CURLOPT_POST       => true,
                              CURLOPT_HTTPHEADER => $headers,
                              CURLOPT_POSTFIELDS => $postString );
        if( false === curl_setopt_array( $curlHandle, $curlOptions ) ) {
            throw new Atc_Mail_Transport_Exception( 'curl_setopt_array failed with the following error while trying to sign in:  ' . curl_error ( $curlHandle ) );
        }

        $curlExecResult = curl_exec( $curlHandle );
        if( false === $curlExecResult ) {
            throw new Atc_Mail_Transport_Exception( 'curl_exec failed with the following error while trying to sign in:  ' . curl_error ( $curlHandle ) );
        }

        // Verify that we got logged in
        if( false === strpos( $curlExecResult, '<input type=hidden name="hidcanary" value="' ) ) {
            throw new Atc_Mail_Transport_Exception( 'Unknown error occured while trying to sign in' );
        }

        // Get the hidcanary value
        $pregMatches = array();
       	if( 0 === preg_match( '/<input\s+type=["]*hidden["]*\s+name="hidcanary"\s+value="([^"]*)"/i', $curlExecResult, $pregMatches ) ) {
	        throw new Atc_Mail_Transport_Exception( 'Unable to retrieve hidcanary value after successful sign in' );
        }

        // Remember this value so that it can be used later.
        $browser->setHidCanary( $pregMatches[1] );
    }

    // ------------------------------------------------------------------------





    // ----- Private Methods --------------------------------------------------

    // ------------------------------------------------------------------------
}
