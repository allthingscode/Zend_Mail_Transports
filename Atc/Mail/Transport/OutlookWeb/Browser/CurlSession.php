<?php
/**
 * This class implements the singleton pattern.
 * @package Atc_Mail
 */
final class Atc_Mail_Transport_OutlookWeb_Browser_CurlSession
{
    /**
     * This statically stores a single instance of this class.
     * @var resource
     */
    private static $_instance = null;

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
     * Since this class is implementing a singleton pattern,
     *   the constructor cannot be called externally.
     * This must be called from the static getInstance() method.
     * constructor
     */
    private function __construct()
    {
        // Default the cookie file location
        $this->_setCookieFilePath( sys_get_temp_dir() . __CLASS__ . '_cookies' );

        // Create a new curl session handle and set the local property.
        $this->_createCurlSessionHandle();
    }


    /**
     * Do any necessary cleanup
     */
    public function __destruct()
    {
        $curlHandle = $this->getHandle();
        curl_close( $curlHandle );
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
     * @param resource
     */
    private function _setHandle( $newValue )
    {
        $this->_properties['Handle'] = $newValue;
    }
    /**
     * @return resource
     */
    public function getHandle()
    {
        return $this->_properties['Handle'];
    }


    /**
     * @param string
     */
    private function _setCookieFilePath( $newValue )
    {
        // @TODO Make sure it's writable
        $this->_properties['CookieFilePath'] = $newValue;
    }
    /**
     * @return string
     */
    public function getCookieFilePath()
    {
        return $this->_properties['CookieFilePath'];
    }

    // ------------------------------------------------------------------------




    // ----- Public Methods ---------------------------------------------------

    /**
     * This implements the singleton pattern for this class.
     * @return resource
     */
    public static function getInstance()
    {
        // Create a new instance if we don't already have one.
        if ( true === is_null( self::$_instance ) ) {
            self::$_instance = new Atc_Mail_Transport_OutlookWeb_Browser_CurlSession();
        }

        return self::$_instance;
    }
    // ------------------------------------------------------------------------






    // ----- Private Methods --------------------------------------------------

    /**
     * This sets the local CurlSessionHandle property.
     */
    private function _createCurlSessionHandle()
    {
        // Initialize a curl session handle
        $curlSessionHandle = curl_init();
        if ( false === $curlSessionHandle ) {
            throw new Atc_Mail_Transport_Exception( 'Unable to initialize curl' );
        }

        // Set some global curl options
        $curlOptions = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,    // @TODO Make this configurable
            CURLOPT_FOLLOWLOCATION => true,     // There are lots of redirects
            CURLOPT_COOKIESESSION  => true,
            CURLOPT_COOKIEFILE     => $this->getCookieFilePath()
            );
        if( false === curl_setopt_array( $curlSessionHandle, $curlOptions ) ) {
            throw new Atc_Mail_Transport_Exception( 'Unable to set curl options' );
        }

        // Save the curl session handle with this class instance
        $this->_setHandle( $curlSessionHandle );
    }
    // ------------------------------------------------------------------------
}
