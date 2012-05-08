<?php
/**
 * @see Zend_Mail_Transport_Abstract
 */
require_once 'Zend/Mail/Transport/Abstract.php';

/**
 * @see Atc_Mail_Transport_Exception
 */
require_once 'Atc/Mail/Transport/Exception.php';

/**
 * @see Atc_Mail_Transport_OutlookWeb_Browser
 */
require_once 'Atc/Mail/Transport/OutlookWeb/Browser.php';
// =============================================================================


/**
 * OutlookWeb connection object
 *
 * Loads an instance of Atc_Mail_Protocol_Curl
 *   and interacts with an outlook/exchange web application.
 *
 * @category   Atc
 * @package    Atc_Mail
 * @subpackage Transport
 */
final class Atc_Mail_Transport_OutlookWeb extends Zend_Mail_Transport_Abstract
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
    public function __construct( $baseOutlookUrl, $username, $password )
    {
        // Save local properties
        $this->setBaseOutlookUrl( $baseOutlookUrl );
        $this->setUsername(       $username       );
        $this->setPassword(       $password       );
    }


    /**
     * Do any necessary cleanup
     */
    public function __destruct()
    {
        // @TODO Sign out of Outlook
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
    private function setBaseOutlookUrl( $newValue )
    {
        $this->_properties['BaseOutlookUrl'] = $newValue;
    }
    /**
     * @return string
     */
    public function getBaseOutlookUrl()
    {
        return $this->_properties['BaseOutlookUrl'];
    }
    /**
     * @return bool
     */
    public function hasBaseOutlookUrl()
    {
        if ( false === $this->isKnown('BaseOutlookUrl') ) {
            return false;
        }
        if( 0 === strlen( trim( $this->getBaseOutlookUrl() ) ) ) {
            return false;
        }
        return true;
    }


    /**
     * @param string
     */
    private function setUsername( $newValue )
    {
        $this->_properties['Username'] = $newValue;
    }
    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->_properties['Username'];
    }
    /**
     * @return bool
     */
    public function hasUsername()
    {
        if ( false === $this->isKnown('Username') ) {
            return false;
        }
        if( 0 === strlen( trim( $this->getUsername() ) ) ) {
            return false;
        }
        return true;
    }


    /**
     * @param string
     */
    private function setPassword( $newValue )
    {
        $this->_properties['Password'] = $newValue;
    }
    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_properties['Password'];
    }
    /**
     * @return bool
     */
    public function hasPassword()
    {
        if ( false === $this->isKnown('Password') ) {
            return false;
        }
        if( 0 === strlen( trim( $this->getPassword() ) ) ) {
            return false;
        }
        return true;
    }


    /**
     * @return Zend_Mail
     */
    public function getZendMail()
    {
        $zendMail = $this->_mail;
        return $zendMail;
    }
    // ------------------------------------------------------------------------








    // ----- Public Methods ---------------------------------------------------


    /**
     * Send an email via the CURL connection
     *
     * @return void
     */
    public function _sendMail()
    {
        $this->_validateRequiredProperties();

        $browser = new Atc_Mail_Transport_OutlookWeb_Browser( $this );

        $browser->sendEmail();
    }

    // ------------------------------------------------------------------------




    // ----- Private Methods --------------------------------------------------

    /**
     * @throws Atc_Mail_Transport_Exception
     */
    private function _validateRequiredProperties()
    {
        if ( false === $this->hasBaseOutlookUrl() ) {
            throw new Atc_Mail_Transport_Exception( 'Invalid baseOutlookUrl Property Value' );
        }
        if ( false === $this->hasUsername() ) {
            throw new Atc_Mail_Transport_Exception( 'Invalid username Property Value' );
        }
        if ( false === $this->hasPassword() ) {
            throw new Atc_Mail_Transport_Exception( 'Invalid password Property Value' );
        }
    }

    // ------------------------------------------------------------------------
}
