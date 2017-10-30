<?php
/**
 * CModel base class file
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * __construct
 * __set
 * __get
 * getError
 * getErrorMessage
 *
 * STATIC:
 * ---------------------------------------------------------------
 *
 */

class CModel
{
    /** @var object */
    private static $_instance;
    /** @var CDatabase */
    protected $_db;
    /**	@var boolean */
    protected $_error;
    /**	@var string */
    protected $_errorMessage;
    /**	@var array */
    protected $_columns = array();

    /**
     * Class constructor
     * @param array $params
     */
    public function __construct($params = array())
    {
        $this->_db = CDatabase::init($params);

        $this->_error = CDatabase::getError();
        $this->_errorMessage = CDatabase::getErrorMessage();
    }

    /**
     * Initializes the database class
     * @param array $params
     * @return \CModel|object
     */
    public static function init($params = array())
    {
        if(self::$_instance == null) self::$_instance = new self($params);
        return self::$_instance;
    }

    /**
     * Setter
     * @param $index
     * @param $value
     */
    public function __set($index, $value)
    {
        $this->_columns[$index] = $value;
    }

    /**
     * Getter
     * @param $index
     * @return string
     */
    public function __get($index)
    {
        return isset($this->_columns[$index]) ? $this->_columns[$index] : '';
    }

    /**
     * Get error status
     * @return boolean
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Get error message
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }
}