<?php


/**
 * @see Atc_Mail_Transport_OutlookWeb_Browser_CurlSession
 */
require_once 'Atc/Mail/Transport/OutlookWeb/Browser/CurlSession.php';

/**
 * @see Atc_Mail_Transport_OutlookWeb_Browser_WebPage_SignIn
 */
require_once 'Atc/Mail/Transport/OutlookWeb/Browser/WebPage/SignIn.php';

/**
 * @see Atc_Mail_Transport_OutlookWeb_Browser_WebPage_ComposeEmail
 */
require_once 'Atc/Mail/Transport/OutlookWeb/Browser/WebPage/ComposeEmail.php';
// ============================================================================



/**
 * Web browser client
 *
 *
 *
 * @category   Atc
 * @package    Atc_Mail_Transport_OutlookWeb
 * @subpackage Browser
 */
final class Atc_Mail_Transport_OutlookWeb_Browser
{
    /**
     * All properties for this object are stored in this array.
     * Default values are not populated,
     *   so if properties are accessed before they are set,
     *   a php notice is generated.  This behavior helps identify coding errors.
     * @var array
     */
    private $_properties = array();
    // ------------------------------------------------------------------------


    /**
     * constructor
     */
    public function __construct( Atc_Mail_Transport_OutlookWeb &$outlookWebTransport )
    {
        $this->setOutlookWebTransport( $outlookWebTransport );

        // Default the IsSignedInFlag value
        $this->_setIsSignedInFlag( false );

        // This is the user agent we will be using in all requests
        // @todo Make this configurable
        $this->setUserAgent( 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14' );
    }


    /**
     * This helps prevent invalid property assignments.
     * @param string
     * @param mixed
     */
    public function __set( $propertyName, $propertyValue )
    {
        throw new Atc_Mail_Transport_Exception( 'Invalid property assignment: ' . $propertyName . ' => ' . $propertyValue );
    }
    /**
     * This helps catch invalid property retreival
     * @param string
     */
    public function __get( $propertyName )
    {
        throw new Atc_Mail_Transport_Exception( 'Invalid property retreival: ' . $propertyName );
    }



    // ----- Setters/Getters --------------------------------------------------

    /**
     * @return Atc_Mail_Transport_OutlookWeb_Browser_CurlSession
     */
    public function getCurlSession()
    {
        $curlSession = Atc_Mail_Transport_OutlookWeb_Browser_CurlSession::getInstance();
        return $curlSession;
    }


    /**
     * @param Atc_Mail_Transport_OutlookWeb
     */
    private function setOutlookWebTransport( Atc_Mail_Transport_OutlookWeb $newValue )
    {
        $this->_properties['OutlookWebTransport'] = $newValue;
    }
    /**
     * @return Atc_Mail_Transport_OutlookWeb
     */
    public function getOutlookWebTransport()
    {
        return $this->_properties['OutlookWebTransport'];
    }


    /**
     * @param string
     */
    public function setUserAgent( $newValue )
    {
        $this->_properties['UserAgent'] = $newValue;
    }
    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->_properties['UserAgent'];
    }


    /**
     * @return string
     */
    public function getUsername()
    {
        $outlookWebTransport = $this->getOutlookWebTransport();
        return $outlookWebTransport->getUsername();
    }


    /**
     * @return string
     */
    public function getPassword()
    {
        $outlookWebTransport = $this->getOutlookWebTransport();
        return $outlookWebTransport->getPassword();
    }


    /**
     * @return string
     */
    public function getBaseOutlookUrl()
    {
        $outlookWebTransport = $this->getOutlookWebTransport();
        return $outlookWebTransport->getBaseOutlookUrl();
    }


    /**
     * @param bool
     */
    private function _setIsSignedInFlag( $newValue )
    {
        $this->_properties['IsSignedInFlag'] = $newValue;
    }
    /**
     * @return bool
     */
    public function getIsSignedInFlag()
    {
        return $this->_properties['IsSignedInFlag'];
    }
    /**
     * @return bool
     */
    public function isSignedIn()
    {
        return $this->getIsSignedInFlag();
    }


    /**
     * @param string
     */
    public function setHidCanary( $newValue )
    {
        $this->_properties['HidCanary'] = $newValue;
    }
    /**
     * @return string
     */
    public function getHidCanary()
    {
        return $this->_properties['HidCanary'];
    }
    // ------------------------------------------------------------------------




    // ----- Public Methods ---------------------------------------------------

    /**
     * Most methods in this class
     *   will automatically call this method if we are not already signed in.
     */
    public function signIn()
    {
        // Only sign in once
        if( true === $this->isSignedIn() ) {
            return;
        }

        // Submit the sign-in web form
        $signInPage = new Atc_Mail_Transport_OutlookWeb_Browser_WebPage_SignIn( $this );
        $signInPage->postSignInForm();

        // We are now signed into Outlook
        $this->_setIsSignedInFlag( true );
    }



    /**
     *
     */
    public function sendEmail()
    {
        // Make sure we are signed in
        if ( false === $this->isSignedIn() ) {
            $this->signIn();
        }

        $transport       = $this->getOutlookWebTransport();
        $zendMail        = $transport->getZendMail();
        $zendMailHeaders = $zendMail->getHeaders();

        // Extract the TO string
        $toString = '';
        if ( false === array_key_exists( 'To', $zendMailHeaders ) ) {
            throw new Atc_Mail_Transport_Exception( 'No TO recipients set' );
        }
        $toArray = $zendMailHeaders['To'];
        unset( $toArray['append'] );
        $toString = implode( ';', $toArray );

        // Extract the CC string
        $ccString = '';
        if ( true === array_key_exists( 'Cc', $zendMailHeaders ) ) {
            $ccArray = $zendMailHeaders['Cc'];
            unset( $ccArray['append'] );
            if ( false === empty( $ccArray ) ) {
                $ccString = implode( ';', $ccArray );
            }
        }

        // Extract the BCC string
        $bccString = '';
        if ( true === array_key_exists( 'Bcc', $zendMailHeaders ) ) {
            $bccArray = $zendMailHeaders['Bcc'];
            unset( $bccArray['append'] );
            if ( false === empty( $bccArray ) ) {
                $bccString = implode( ';', $bccArray );
            }
        }

        // Extract the subject string
        $subjectString = '';
        if ( false === array_key_exists( 'Subject', $zendMailHeaders ) ) {
            throw new Atc_Mail_Transport_Exception( 'No subject set' );
        }
        $subjectArray  = $zendMailHeaders['Subject'];
        $subjectString = implode( ';', $subjectArray );

        // Extract the body string
        $bodyString = $zendMail->getBodyText( true );

        // Set the email compose form values
        $composeEmailPage = new Atc_Mail_Transport_OutlookWeb_Browser_WebPage_ComposeEmail( $this );
        $composeEmailPage->setTo(      $toString      );
        $composeEmailPage->setCc(      $ccString      );
        $composeEmailPage->setBcc(     $bccString     );
        $composeEmailPage->setSubject( $subjectString );
        $composeEmailPage->setBody(    $bodyString    );

        // Submit the compose form
        $composeEmailPage->postEmailComposeForm();
    }






    /**
     * Helper function to generate post data strings
     */
	public function CreatePostString( $postArray )
	{
    	if ( true === empty( $postArray ) ) {
    	    return '';
	    }
	    if ( false === is_array( $postArray ) ) {
	        return '';
	    }

	    $postString = '';
	    foreach ( $postArray as $varName => $varValue ) {
	        $postString .= urlencode( $varName  ) . '=' .
	                       urlencode( $varValue ) . '&';
	    }

	    return substr( $postString, 0, -1 );
	}

    // ------------------------------------------------------------------------




    // ----- Private Methods --------------------------------------------------


    // ------------------------------------------------------------------------
}