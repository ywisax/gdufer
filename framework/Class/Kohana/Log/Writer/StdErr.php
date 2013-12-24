<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * STDERR log writer. Writes out messages to STDERR.
 *
 * @package    Kohana
 * @category   Logging
 */
class Kohana_Log_Writer_StdErr extends Log_Writer {
	/**
	 * Writes each of the messages to STDERR.
	 *
	 *     $writer->write($messages);
	 *
	 * @param   array   $messages
	 * @return  void
	 */
	public function write(array $messages)
	{
		foreach ($messages AS $message)
		{
			// Writes out each message
			fwrite(STDERR, $this->format_message($message).PHP_EOL);
		}
	}

} // End Kohana_Log_StdErr
