<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Syslog log writer.
 *
 * @package    Kohana
 * @category   Logging
 */
class Kohana_Log_Writer_Syslog extends Log_Writer {

	/**
	 * @var  string  The syslog identifier
	 */
	protected $_ident;

	/**
	 * Creates a new syslog logger.
	 *
	 * @link    http://www.php.net/manual/function.openlog
	 *
	 * @param   string  $ident      syslog identifier
	 * @param   int     $facility   facility to log to
	 * @return  void
	 */
	public function __construct(array $config = NULL)
	{
		$ident = Helper_Array::get($config, 'ident', 'KohanaPHP');
		$facility = Helper_Array::get($config, 'facility', LOG_USER);

		$this->_ident = $ident;
		// Open the connection to syslog
		openlog($this->_ident, LOG_CONS, $facility);
	}

	/**
	 * Writes each of the messages into the syslog.
	 *
	 * @param   array   $messages
	 * @return  void
	 */
	public function write(array $messages)
	{
		foreach ($messages AS $message)
		{
			syslog($message['level'], $message['body']);

			if (isset($message['additional']['exception']))
			{
				syslog(Log_Writer::$strace_level, $message['additional']['exception']->getTraceAsString());
			}
		}
	}

	/**
	 * Closes the syslog connection
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		// Close connection to syslog
		closelog();
	}

} // End Kohana_Log_Syslog
