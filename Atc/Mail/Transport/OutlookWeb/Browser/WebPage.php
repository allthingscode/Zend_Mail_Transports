<?php
/**
 * @category   Atc
 * @package    Atc_Mail
 * @subpackage Transport
 */
abstract class Atc_Mail_Transport_OutlookWeb_Browser_WebPage
{
    /**
     * All properties for this object are stored in this array.
     * Default values are not populated,
     *   so if properties are accessed before they are set,
     *   a php notice is generated.  This behavior helps identify coding errors.
     * @var array
     */
    protected $_properties = array();
    // ------------------------------------------------------------------------


    /**
     * @param Atc_Mail_Transport_OutlookWeb_Browser
     */
    public function __construct( Atc_Mail_Transport_OutlookWeb_Browser &$browser )
    {
        $this->_setBrowser( $browser );
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
     * @param Browser
     */
    protected function _setBrowser( &$newValue )
    {
        $this->_properties['Browser'] = $newValue;
    }
    /**
     * @return Browser
     */
    protected function _getBrowser()
    {
        return $this->_properties['Browser'];
    }
    // ------------------------------------------------------------------------




    // ----- Public Methods ---------------------------------------------------

    // ------------------------------------------------------------------------





    // ----- Private Methods --------------------------------------------------

    // ------------------------------------------------------------------------
}
