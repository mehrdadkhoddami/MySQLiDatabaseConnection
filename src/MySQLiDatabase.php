<?php
namespace MehrdadKhoddami\MySQLiDatabase;
use mysqli;


/**
 * class for creating, connecting and excuting queries
 *
 * PHP version 7
 *
 * @category Database
 * @package  Database
 * @author   Mehrdad Khoddami <khoddami.me@gmail.com>
 * @license  https://opensource.org/licenses/MIT The MIT License (MIT)
 */
 
class DB
{
    /**
     * MySQL credentials
     *
     * @var array
     */
    static protected $mysql_credentials = [];

    static protected $_connection;
	// Store the single instance.
    static protected $_instance;

    /**
     * Table prefix
     *
     * @var string
     */
    static protected $table_prefix;
	
	/**
	 * Get an instance of the Database.
     *
     * @param array                         $credentials  Database connection details
     * @param string                        $table_prefix Table prefix
     * @param string                        $encoding     Database character encoding
     *
	 * @return Database
     * @throws \Mehrdad\MySQLiDatabase\MysqlCredentialsNotProvidedException
     * @throws \Mehrdad\MySQLiDatabase\MysqlCanNotConnectException
	 */
	public static function getInstance(
        array $credentials,
        $table_prefix = null,
        $encoding = 'utf8mb4'
    )
	{
        if (empty($credentials)) {
            throw new MysqlCredentialsNotProvidedException('MySQL credentials not provided!');
        }

        if (!self::$_instance) {
            self::$_instance = new self($credentials, $table_prefix, $encoding);
        }
        return self::$_instance;
	}
	
	/**
	 * Constructor
     *
     * @param array                         $credentials  Database connection details
     * @param string                        $table_prefix Table prefix
     * @param string                        $encoding     Database character encoding
     *
     * @throws \Mehrdad\MySQLiDatabase\MysqlCredentialsNotProvidedException
     * @throws \Mehrdad\MySQLiDatabase\MysqlCanNotConnectException
	 */
	public function __construct(
        array $credentials,
        $table_prefix = null,
        $encoding = 'utf8mb4'
    )
	{
        if (empty($credentials)) {
            throw new MysqlCredentialsNotProvidedException('MySQL credentials not provided!');
        }

        try {
            $connection = new mysqli($credentials['host'], $credentials['user'], $credentials['password'], $credentials['database']);
            mysqli_set_charset($connection, $encoding);
        } catch (\Exception $e) {
            throw new MysqlCanNotConnectException($e->getMessage());
        }


        self::$_connection          = $connection;
        self::$mysql_credentials    = $credentials;
        self::$table_prefix         = $table_prefix;

        return self::$_connection;
	}
	
	/**
	 * Empty clone magic method to prevent duplication. 
     *
     * @return void
	 */
    protected function __clone()
	{
	}

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    protected function __wakeup()
    {
    }
	
	/**
	 * Get the mysqli connection. 
	 */
	public function getConnection()
	{
	    return self::$_connection;
	}

    /**
     * Check if database connection has been created
     *
     * @return bool
     */
    public static function isDbConnected()
    {
        return self::$_connection !== null;
    }

    /**
     * Convert from unix timestamp to timestamp
     *
     * @param int $time Unix timestamp (if null, current timestamp is used)
     *
     * @return string
     */
    protected static function getTimestamp($time = null)
    {
        if ($time === null) {
            $time = time();
        }

        return date('Y-m-d H:i:s', $time);
    }
}