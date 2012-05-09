<?php
/**
 * @see Atc_Mail_Transport_OutlookWeb_Browser_WebPage
 */
require_once 'Atc/Mail/Transport/OutlookWeb/Browser/WebPage.php';

/**
 * @see Atc_Mail_Transport_Exception
 */
require_once 'Atc/Mail/Transport/Exception.php';
// ============================================================================


/**
 * @category   Atc
 * @package    Atc_Mail
 * @subpackage Transport
 * @author Matthew Hayes <Matthew.Hayes@AllThingsCode.com>
 */
final class Atc_Mail_Transport_OutlookWeb_Browser_WebPage_ComposeEmail extends Atc_Mail_Transport_OutlookWeb_Browser_WebPage
{
    // All properties are stored in the parent class.
    // ------------------------------------------------------------------------


    /**
     * @param Atc_Mail_Transport_OutlookWeb_Browser
     */
    public function __construct( Atc_Mail_Transport_OutlookWeb_Browser &$browser )
    {
        // Default the importance to normal
        $this->setImportance( 1 );

        // Call the parent constructor
        parent::__construct( $browser );
    }


    // ----- Setters/Getters --------------------------------------------------

    /**
     * @param string
     * @return bool
     */
    public function isKnown( $propertyName )
    {
        $isKnown = isset( $this->_properties[ $propertyName ] );
        return $isKnown;
    }


    /**
     * @param string
     */
    public function setTo( $newValue )
    {
        $this->_properties['To'] = $newValue;
    }
    /**
     * @return string
     */
    public function getTo()
    {
        return $this->_properties['To'];
    }


    /**
     * @param string
     */
    public function setCc( $newValue )
    {
        $this->_properties['Cc'] = $newValue;
    }
    /**
     * @return string
     */
    public function getCc()
    {
        return $this->_properties['Cc'];
    }


    /**
     * @param string
     */
    public function setBcc( $newValue )
    {
        $this->_properties['Bcc'] = $newValue;
    }
    /**
     * @return string
     */
    public function getBcc()
    {
        return $this->_properties['Bcc'];
    }


    /**
     * @param string
     */
    public function setSubject( $newValue )
    {
        $this->_properties['Subject'] = $newValue;
    }
    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->_properties['Subject'];
    }


    /**
     * @param string
     */
    public function setBody( $newValue )
    {
        $this->_properties['Body'] = $newValue;
    }
    /**
     * @return string
     */
    public function getBody()
    {
        return $this->_properties['Body'];
    }


    /**
     * 0=low, 1=normal, 2=high
     * @param string
     */
    public function setImportance( $newValue )
    {
        $this->_properties['Importance'] = $newValue;
    }
    /**
     * @return string
     */
    public function getImportance()
    {
        return $this->_properties['Importance'];
    }
    // ------------------------------------------------------------------------




    // ----- Public Methods ---------------------------------------------------

    /**
     * This mimics posting the Outlook "New Email" web form.
     */
    public function postEmailComposeForm()
    {
        $browser     = $this->_getBrowser();
        $curlSession = $browser->getCurlSession();
        $curlHandle  = $curlSession->getHandle();

        $postUrl = $browser->getBaseOutlookUrl() . '/owa/?ae=PreFormAction&t=IPM.Note&a=Send';

        $headers = array(
        	'Content-Type: application/x-www-form-urlencoded',
        	'User-Agent: ' . $browser->getUserAgent()
        	);

		$postData = array();
	    $postData['hidcmdpst'] = 'snd';
	    $postData['hidmsgimp'] = $this->getImportance();
	    $postData['hidpid']    = 'EditMessage';
	    $postData['hidcanary'] = $browser->getHidCanary();
        $postData['txtto']     = $this->getTo();
        $postData['txtcc']     = $this->getCc();
        $postData['txtbcc']    = $this->getBcc();
        $postData['txtsbj']    = $this->getSubject();
        $postData['txtbdy']    = $this->getBody();
		$postString = $browser->CreatePostString( $postData );

        $curlOptions = array( CURLOPT_VERBOSE    => false,
        					  CURLOPT_URL        => $postUrl,
                              CURLOPT_POST       => true,
                              CURLOPT_HTTPHEADER => $headers,
                              CURLOPT_POSTFIELDS => $postString );
        if( false === curl_setopt_array( $curlHandle, $curlOptions ) ) {
            throw new Atc_Mail_Transport_Exception( 'curl_setopt_array failed with the following error while trying to send email:  ' . curl_error ( $curlHandle ) );
        }

        $curlExecResult = curl_exec( $curlHandle );
        if( false === $curlExecResult ) {
            throw new Atc_Mail_Transport_Exception( 'curl_exec failed with the following error while trying to send email:  ' . curl_error ( $curlHandle ) );
        }

        // Verify that the email was indeed sent
        if( 0 === preg_match( '/class="btn" title="New Message"/i', $curlExecResult, $pregMatches ) ) {
	        throw new Atc_Mail_Transport_Exception( 'Unable to verify that the email was successfully sent' );
        }
    }

    // ------------------------------------------------------------------------





    // ----- Private Methods --------------------------------------------------

    // ------------------------------------------------------------------------
}
