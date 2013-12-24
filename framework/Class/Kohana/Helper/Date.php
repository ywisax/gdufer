<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 日期助手类
 *
 * @package    Kohana
 * @category   Helpers
 */
class Kohana_Helper_Date {

	// Second amounts for various time increments
	const YEAR   = 31556926;
	const MONTH  = 2629744;
	const WEEK   = 604800;
	const DAY    = 86400;
	const HOUR   = 3600;
	const MINUTE = 60;

	// Available formats for Helper_Date::months()
	const MONTHS_LONG  = '%B';
	const MONTHS_SHORT = '%b';

	// 默认时间戳格式
	const DEFAULT_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';

	/**
	 * Timezone for formatted_time
	 *
	 * @var  string
	 */
	public static $timezone;
	
	/**
	 * Checks whether a string is a date
	 *
	 * @param  string date
	 * @return bool
	 */
	public static function is_date($str)
	{
		return (boolean) strtotime($str);
	}

	/**
	 * Returns the offset (in seconds) between two time zones. Use this to
	 * display dates to users in different time zones.
	 *
	 *     $seconds = Helper_Date::offset('America/Chicago', 'GMT');
	 *
	 * [!!] A list of time zones that PHP supports can be found at
	 * <http://php.net/timezones>.
	 *
	 * @param   string  $remote timezone that to find the offset of
	 * @param   string  $local  timezone used as the baseline
	 * @param   mixed   $now    UNIX timestamp or date string
	 * @return  integer
	 */
	public static function offset($remote, $local = NULL, $now = NULL)
	{
		if ($local === NULL)
		{
			// Use the default timezone
			$local = date_default_timezone_get();
		}

		if (is_int($now))
		{
			// Convert the timestamp into a string
			$now = date(DateTime::RFC2822, $now);
		}

		// Create timezone objects
		$zone_remote = new DateTimeZone($remote);
		$zone_local  = new DateTimeZone($local);

		// Create date objects from timezones
		$time_remote = new DateTime($now, $zone_remote);
		$time_local  = new DateTime($now, $zone_local);

		// Find the offset
		$offset = $zone_remote->getOffset($time_remote) - $zone_local->getOffset($time_local);

		return $offset;
	}

	/**
	 * Number of seconds in a minute, incrementing by a step. Typically used as
	 * a shortcut for generating a list that can used in a form.
	 *
	 *     $seconds = Helper_Date::seconds(); // 01, 02, 03, ..., 58, 59, 60
	 *
	 * @param   integer $step   amount to increment each step by, 1 to 30
	 * @param   integer $start  start value
	 * @param   integer $end    end value
	 * @return  array   A mirrored (foo => foo) array from 1-60.
	 */
	public static function seconds($step = 1, $start = 0, $end = 60)
	{
		// Always integer
		$step = (int) $step;

		$seconds = array();

		for ($i = $start; $i < $end; $i += $step)
		{
			$seconds[$i] = sprintf('%02d', $i);
		}

		return $seconds;
	}

	/**
	 * Number of minutes in an hour, incrementing by a step. Typically used as
	 * a shortcut for generating a list that can be used in a form.
	 *
	 *     $minutes = Helper_Date::minutes(); // 05, 10, 15, ..., 50, 55, 60
	 *
	 * @param   integer $step   amount to increment each step by, 1 to 30
	 * @return  array   A mirrored (foo => foo) array from 1-60.
	 */
	public static function minutes($step = 5)
	{
		// Because there are the same number of minutes as seconds in this set,
		// we choose to re-use seconds(), rather than creating an entirely new
		// function. Shhhh, it's cheating! ;) There are several more of these
		// in the following methods.
		return Helper_Date::seconds($step);
	}

	/**
	 * Number of hours in a day. Typically used as a shortcut for generating a
	 * list that can be used in a form.
	 *
	 *     $hours = Helper_Date::hours(); // 01, 02, 03, ..., 10, 11, 12
	 *
	 * @param   integer $step   amount to increment each step by
	 * @param   boolean $long   use 24-hour time
	 * @param   integer $start  the hour to start at
	 * @return  array   A mirrored (foo => foo) array from start-12 or start-23.
	 */
	public static function hours($step = 1, $long = FALSE, $start = NULL)
	{
		// Default values
		$step = (int) $step;
		$long = (bool) $long;
		$hours = array();

		// Set the default start if none was specified.
		if ($start === NULL)
		{
			$start = ($long === FALSE) ? 1 : 0;
		}

		$hours = array();

		// 24-hour time has 24 hours, instead of 12
		$size = ($long === TRUE) ? 23 : 12;

		for ($i = $start; $i <= $size; $i += $step)
		{
			$hours[$i] = (string) $i;
		}

		return $hours;
	}

	/**
	 * Returns AM or PM, based on a given hour (in 24 hour format).
	 *
	 *     $type = Helper_Date::ampm(12); // PM
	 *     $type = Helper_Date::ampm(1);  // AM
	 *
	 * @param   integer $hour   number of the hour
	 * @return  string
	 */
	public static function ampm($hour)
	{
		// Always integer
		$hour = (int) $hour;

		return ($hour > 11) ? 'PM' : 'AM';
	}

	/**
	 * Adjusts a non-24-hour number into a 24-hour number.
	 *
	 *     $hour = Helper_Date::adjust(3, 'pm'); // 15
	 *
	 * @param   integer $hour   hour to adjust
	 * @param   string  $ampm   AM or PM
	 * @return  string
	 */
	public static function adjust($hour, $ampm)
	{
		$hour = (int) $hour;
		$ampm = strtolower($ampm);

		switch ($ampm)
		{
			case 'am':
				if ($hour == 12)
				{
					$hour = 0;
				}
			break;
			case 'pm':
				if ($hour < 12)
				{
					$hour += 12;
				}
			break;
		}

		return sprintf('%02d', $hour);
	}

	/**
	 * Number of days in a given month and year. Typically used as a shortcut
	 * for generating a list that can be used in a form.
	 *
	 *     Helper_Date::days(4, 2010); // 1, 2, 3, ..., 28, 29, 30
	 *
	 * @param   integer $month  number of month
	 * @param   integer $year   number of year to check month, defaults to the current year
	 * @return  array   A mirrored (foo => foo) array of the days.
	 */
	public static function days($month, $year = FALSE)
	{
		static $months;

		if ($year === FALSE)
		{
			// Use the current year by default
			$year = date('Y');
		}

		// Always integers
		$month = (int) $month;
		$year  = (int) $year;

		// We use caching for months, because time functions are used
		if (empty($months[$year][$month]))
		{
			$months[$year][$month] = array();

			// Use date to find the number of days in the given month
			$total = date('t', mktime(1, 0, 0, $month, 1, $year)) + 1;

			for ($i = 1; $i < $total; $i++)
			{
				$months[$year][$month][$i] = (string) $i;
			}
		}

		return $months[$year][$month];
	}

	/**
	 * Number of months in a year. Typically used as a shortcut for generating
	 * a list that can be used in a form.
	 *
	 * By default a mirrored array of $month_number => $month_number is returned
	 *
	 *     Helper_Date::months();
	 *     // aray(1 => 1, 2 => 2, 3 => 3, ..., 12 => 12)
	 *
	 * But you can customise this by passing in either Helper_Date::MONTHS_LONG
	 *
	 *     Helper_Date::months(Helper_Date::MONTHS_LONG);
	 *     // array(1 => 'January', 2 => 'February', ..., 12 => 'December')
	 *
	 * Or Helper_Date::MONTHS_SHORT
	 *
	 *     Helper_Date::months(Helper_Date::MONTHS_SHORT);
	 *     // array(1 => 'Jan', 2 => 'Feb', ..., 12 => 'Dec')
	 *
	 * @param   string  $format The format to use for months
	 * @return  array   An array of months based on the specified format
	 */
	public static function months($format = NULL)
	{
		$months = array();

		if ($format === Helper_Date::MONTHS_LONG OR $format === Helper_Date::MONTHS_SHORT)
		{
			for ($i = 1; $i <= 12; ++$i)
			{
				$months[$i] = strftime($format, mktime(0, 0, 0, $i, 1));
			}
		}
		else
		{
			$months = Helper_Date::hours();
		}

		return $months;
	}

	/**
	 * Returns an array of years between a starting and ending year. By default,
	 * the the current year - 5 and current year + 5 will be used. Typically used
	 * as a shortcut for generating a list that can be used in a form.
	 *
	 *     $years = Helper_Date::years(2000, 2010); // 2000, 2001, ..., 2009, 2010
	 *
	 * @param   integer $start  starting year (default is current year - 5)
	 * @param   integer $end    ending year (default is current year + 5)
	 * @return  array
	 */
	public static function years($start = FALSE, $end = FALSE)
	{
		// Default values
		$start = ($start === FALSE) ? (date('Y') - 5) : (int) $start;
		$end   = ($end   === FALSE) ? (date('Y') + 5) : (int) $end;

		$years = array();

		for ($i = $start; $i <= $end; $i++)
		{
			$years[$i] = (string) $i;
		}

		return $years;
	}
	
	const SPAN_SPLIT_REGEX = '/[^a-z]+/';

	/**
	 * Returns time difference between two timestamps, in human readable format.
	 * If the second timestamp is not given, the current time will be used.
	 * Also consider using [Helper_Date::fuzzy_span] when displaying a span.
	 *
	 *     $span = Helper_Date::span(60, 182, 'minutes,seconds'); // array('minutes' => 2, 'seconds' => 2)
	 *     $span = Helper_Date::span(60, 182, 'minutes'); // 2
	 *
	 * @param   integer $remote timestamp to find the span of
	 * @param   integer $local  timestamp to use as the baseline
	 * @param   string  $output formatting string
	 * @return  string   when only a single output is requested
	 * @return  array    associative list of all outputs requested
	 */
	public static function span($remote, $local = NULL, $output = 'years,months,weeks,days,hours,minutes,seconds')
	{
		// Normalize output
		$output = trim(strtolower( (string) $output));

		if ( ! $output)
		{
			// Invalid output
			return FALSE;
		}

		// Array with the output formats
		$output = preg_split(Helper_Date::SPAN_SPLIT_REGEX, $output);

		// Convert the list of outputs to an associative array
		$output = array_combine($output, array_fill(0, count($output), 0));

		// Make the output values into keys
		extract(array_flip($output), EXTR_SKIP);

		if ($local === NULL)
		{
			// Calculate the span from the current time
			$local = time();
		}

		// Calculate timespan (seconds)
		$timespan = abs($remote - $local);

		if (isset($output['years']))
		{
			$timespan -= Helper_Date::YEAR * ($output['years'] = (int) floor($timespan / Helper_Date::YEAR));
		}

		if (isset($output['months']))
		{
			$timespan -= Helper_Date::MONTH * ($output['months'] = (int) floor($timespan / Helper_Date::MONTH));
		}

		if (isset($output['weeks']))
		{
			$timespan -= Helper_Date::WEEK * ($output['weeks'] = (int) floor($timespan / Helper_Date::WEEK));
		}

		if (isset($output['days']))
		{
			$timespan -= Helper_Date::DAY * ($output['days'] = (int) floor($timespan / Helper_Date::DAY));
		}

		if (isset($output['hours']))
		{
			$timespan -= Helper_Date::HOUR * ($output['hours'] = (int) floor($timespan / Helper_Date::HOUR));
		}

		if (isset($output['minutes']))
		{
			$timespan -= Helper_Date::MINUTE * ($output['minutes'] = (int) floor($timespan / Helper_Date::MINUTE));
		}

		// Seconds ago, 1
		if (isset($output['seconds']))
		{
			$output['seconds'] = $timespan;
		}

		if (count($output) === 1)
		{
			// Only a single output was requested, return it
			return array_pop($output);
		}

		// Return array
		return $output;
	}

	/**
	 * Returns the difference between a time and now in a "fuzzy" way.
	 * Displaying a fuzzy time instead of a date is usually faster to read and understand.
	 *
	 *     $span = Helper_Date::fuzzy_span(time() - 10); // "moments ago"
	 *     $span = Helper_Date::fuzzy_span(time() + 20); // "in moments"
	 *
	 * A second parameter is available to manually set the "local" timestamp,
	 * however this parameter shouldn't be needed in normal usage and is only
	 * included for unit tests
	 *
	 * @param   integer $timestamp          "remote" timestamp
	 * @param   integer $local_timestamp    "local" timestamp, defaults to time()
	 * @return  string
	 */
	public static function fuzzy_span($timestamp, $local_timestamp = NULL)
	{
		$local_timestamp = ($local_timestamp === NULL) ? time() : (int) $local_timestamp;

		// Determine the difference in seconds
		$offset = abs($local_timestamp - $timestamp);

		if ($offset <= Helper_Date::MINUTE)
		{
			$span = 'moments';
		}
		elseif ($offset < (Helper_Date::MINUTE * 20))
		{
			$span = 'a few minutes';
		}
		elseif ($offset < Helper_Date::HOUR)
		{
			$span = 'less than an hour';
		}
		elseif ($offset < (Helper_Date::HOUR * 4))
		{
			$span = 'a couple of hours';
		}
		elseif ($offset < Helper_Date::DAY)
		{
			$span = 'less than a day';
		}
		elseif ($offset < (Helper_Date::DAY * 2))
		{
			$span = 'about a day';
		}
		elseif ($offset < (Helper_Date::DAY * 4))
		{
			$span = 'a couple of days';
		}
		elseif ($offset < Helper_Date::WEEK)
		{
			$span = 'less than a week';
		}
		elseif ($offset < (Helper_Date::WEEK * 2))
		{
			$span = 'about a week';
		}
		elseif ($offset < Helper_Date::MONTH)
		{
			$span = 'less than a month';
		}
		elseif ($offset < (Helper_Date::MONTH * 2))
		{
			$span = 'about a month';
		}
		elseif ($offset < (Helper_Date::MONTH * 4))
		{
			$span = 'a couple of months';
		}
		elseif ($offset < Helper_Date::YEAR)
		{
			$span = 'less than a year';
		}
		elseif ($offset < (Helper_Date::YEAR * 2))
		{
			$span = 'about a year';
		}
		elseif ($offset < (Helper_Date::YEAR * 4))
		{
			$span = 'a couple of years';
		}
		elseif ($offset < (Helper_Date::YEAR * 8))
		{
			$span = 'a few years';
		}
		elseif ($offset < (Helper_Date::YEAR * 12))
		{
			$span = 'about a decade';
		}
		elseif ($offset < (Helper_Date::YEAR * 24))
		{
			$span = 'a couple of decades';
		}
		elseif ($offset < (Helper_Date::YEAR * 64))
		{
			$span = 'several decades';
		}
		else
		{
			$span = 'a long time';
		}

		if ($timestamp <= $local_timestamp)
		{
			// This is in the past
			return __($span.' ago');
		}
		else
		{
			// This in the future
			return __('in '.$span);
		}
	}

	/**
	 * Converts a UNIX timestamp to DOS format. There are very few cases where
	 * this is needed, but some binary formats use it (eg: zip files.)
	 * Converting the other direction is done using [Helper_Date::dos2unix].
	 *
	 *     $dos = Helper_Date::unix2dos($unix);
	 *
	 * @param   integer $timestamp  UNIX timestamp
	 * @return  integer
	 */
	public static function unix2dos($timestamp = FALSE)
	{
		$timestamp = ($timestamp === FALSE) ? getdate() : getdate($timestamp);

		if ($timestamp['year'] < 1980)
		{
			return (1 << 21 | 1 << 16);
		}

		$timestamp['year'] -= 1980;

		// What voodoo is this? I have no idea... Geert can explain it though,
		// and that's good enough for me.
		return ($timestamp['year']    << 25 | $timestamp['mon']     << 21 |
		        $timestamp['mday']    << 16 | $timestamp['hours']   << 11 |
		        $timestamp['minutes'] << 5  | $timestamp['seconds'] >> 1);
	}

	/**
	 * Converts a DOS timestamp to UNIX format.There are very few cases where
	 * this is needed, but some binary formats use it (eg: zip files.)
	 * Converting the other direction is done using [Helper_Date::unix2dos].
	 *
	 *     $unix = Helper_Date::dos2unix($dos);
	 *
	 * @param   integer $timestamp  DOS timestamp
	 * @return  integer
	 */
	public static function dos2unix($timestamp = FALSE)
	{
		$sec  = 2 * ($timestamp & 0x1f);
		$min  = ($timestamp >>  5) & 0x3f;
		$hrs  = ($timestamp >> 11) & 0x1f;
		$day  = ($timestamp >> 16) & 0x1f;
		$mon  = ($timestamp >> 21) & 0x0f;
		$year = ($timestamp >> 25) & 0x7f;

		return mktime($hrs, $min, $sec, $mon, $day, $year + 1980);
	}

	/**
	 * Returns a date/time string with the specified timestamp format
	 *
	 *     $time = Helper_Date::formatted_time('5 minutes ago');
	 *
	 * @param   string  $datetime_str       datetime string
	 * @param   string  $timestamp_format   timestamp format
	 * @param   string  $timezone           timezone identifier
	 * @return  string
	 */
	public static function formatted_time($datetime_str = 'now', $timestamp_format = NULL, $timezone = NULL)
	{
		$timestamp_format = ($timestamp_format == NULL) ? Helper_Date::DEFAULT_TIMESTAMP_FORMAT : $timestamp_format;
		$timezone         = ($timezone === NULL) ? Helper_Date::$timezone : $timezone;

		$tz   = new DateTimeZone($timezone ? $timezone : date_default_timezone_get());
		$time = new DateTime($datetime_str, $tz);

		if ($time->getTimeZone()->getName() !== $tz->getName())
		{
			$time->setTimeZone($tz);
		}

		return $time->format($timestamp_format);
	}
	
	/**
	 * Return week days
	 *
	 * @return array
	 */
	static function weeekdays()
	{
		return array(
			0 => __('Sunday'),
			1 => __('Monday'),
			2 => __('Tuesday'),
			3 => __('Wednesday'),
			4 => __('Thursday'),
			5 => __('Friday'),
			6 => __('Saturday')
		);
	}
	
	/**
	 * Return a list of timezones
	 *
	 * @return array
	 */
	public static function timezones()
	{
		$continents = array(
			'Africa',
			'America',
			'Antarctica',
			'Arctic',
			'Asia',
			'Atlantic',
			'Australia',
			'Europe',
			'Indian',
			'Pacific'
		);

		$zones = DateTimeZone::listIdentifiers();

		$locations = array();

		foreach ($zones AS $zone)
		{
			$zone = explode('/', $zone); // 0 => Continent, 1 => City

			if ( ! in_array($zone[0], $continents))
			{
				continue;
			}

			if (isset($zone[1]) != '')
			{
				// Creates array(DateTimeZone => 'Friendly name')
				$locations[$zone[0]][__($zone[0] . '/' . $zone[1])] = __(str_replace('_', ' ', $zone[1]));
			}
		}

		$offset_range = array(
			-12,
			-11.5,
			-11,
			-10.5,
			-10,
			-9.5,
			-9,
			-8.5,
			-8,
			-7.5,
			-7,
			-6.5,
			-6,
			-5.5,
			-5,
			-4.5,
			-4,
			-3.5,
			-3,
			-2.5,
			-2,
			-1.5,
			-1,
			-0.5,
			0,
			0.5,
			1,
			1.5,
			2,
			2.5,
			3,
			3.5,
			4,
			4.5,
			5,
			5.5,
			5.75,
			6,
			6.5,
			7,
			7.5,
			8,
			8.5,
			8.75,
			9,
			9.5,
			10,
			10.5,
			11,
			11.5,
			12,
			12.75,
			13,
			13.75,
			14
		);

		foreach ($offset_range AS $offset)
		{
			if (0 <= $offset)
			{
				$offset_name = '+' . $offset;
			}
			else
			{
				$offset_name = (string) $offset;
			}

			$offset_value = $offset_name;
			$offset_name  = str_replace(array(
				'.25',
				'.5',
				'.75'
			), array(
				':15',
				':30',
				':45'
			), $offset_name);

			$locations[__('Manual Offsets')]['UTC' . $offset_value] = __('UTC :value', array(
				':value' => $offset_name
			));
		}

		return $locations;
	}
	
	/**
	 * Return available date time formats
	 *
	 * @param boolean $timestamp Unix timestamp [Optional]
	 * @return array
	 */
	public static function date_time_formats($timestamp = FALSE)
	{
		$date_time_format = array(
			'l, F j, Y - H:i',
			'l, j F, Y - H:i',
			'l, Y, F j - H:i',
			'l, F j, Y - g:ia',
			'l, j F Y - g:ia',
			'l, Y, F j - g:ia',
			'l, j. F Y - G:i',
			'D, Y-m-d H:i',
			'D, m/d/Y - H:i',
			'D, d/m/Y - H:i',
			'D, Y/m/d - H:i',
			'F j, Y - H:i',
			'j F, Y - H:i',
			'Y, F j - H:i',
			'D, m/d/Y - g:ia',
			'D, d/m/Y - g:ia',
			'D, Y/m/d - g:ia',
			'F j, Y - g:ia',
			'j F Y - g:ia',
			'Y, F j - g:ia',
			'j. F Y - G:i',
			'Y-m-d H:i',
			'm/d/Y - H:i',
			'd/m/Y - H:i',
			'Y/m/d - H:i',
			'd.m.Y - H:i',
			'm/d/Y - g:ia',
			'd/m/Y - g:ia',
			'Y/m/d - g:ia',
			'M j Y - H:i',
			'j M Y - H:i',
			'Y M j - H:i',
			'M j Y - g:ia',
			'j M Y - g:ia',
			'Y M j - g:ia'
		);

		if ($timestamp)
		{
			foreach ($date_time_format AS $f)
			{
				$date_choices[$f] = date($f, time());
			}

			return $date_choices;
		}

		return $date_time_format;
	}
	
	/**
	 * Return available date formats
	 *
	 * @param  boolean $timestamp Unix timestamp [Optional]
	 * @return array
	 */
	public static function date_formats($timestamp = FALSE)
	{
		$date_format = array(
			'l, F j, Y',
			'l, j F, Y',
			'l, Y, F j',
			'l, F j, Y',
			'l, j F Y',
			'l, Y, F j',
			'l, j. F Y',
			'D, Y-m-d',
			'D, m/d/Y',
			'D, d/m/Y',
			'D, Y/m/d',
			'F j, Y',
			'j F, Y',
			'Y, F j',
			'j. F Y',
			'Y-m-d',
			'm/d/Y',
			'd/m/Y',
			'Y/m/d',
			'd.m.Y',
			'M j Y',
			'M j, Y',
			'j M Y',
			'Y M j'
		);

		if ($timestamp)
		{
			foreach ($date_format AS $f)
			{
				$date_choices[$f] = date($f, time());
			}

			return $date_choices;
		}

		return $date_format;
	}
	
	/**
	 * Return available time formats
	 *
	 * @param  boolean $timestamp Unix timestamp [Optional]
	 * @return array
	 */
	public static function time_formats($timestamp = FALSE)
	{
		$time_format = array(
			'g:i:s a',
			'g:i:s A',
			'g:i a',
			'g:i A',
			'H:i:s',
			'G:i'
		);

		if ($timestamp)
		{
			foreach ($time_format AS $f)
			{
				$time_choices[$f] = date($f, time());
			}

			return $time_choices;
		}

		return $time_format;
	}
	
	/**
	 * Return month start timestamp
	 *
	 * @param   integer $month  Month
	 * @param   integer $year   Year
	 * @return  integer Unix timestamp
	 */
	public static function first_of_month($month, $year)
	{
		return strtotime($month . '/01/' . $year . ' 00:00:00');
	}
	
	/**
	 * Return month end timestamp
	 *
	 * @param   integer $month  Month
	 * @param   integer $year   Year
	 * @return  integer Unix timestamp
	 */
	public static function last_of_month($month, $year)
	{
		return strtotime('-1 second', strtotime('+1 month', strtotime($month . '/01/' . $year . ' 00:00:00')));
	}
	
	/**
	 * Number of months in a year. Value will hold month name
	 *
	 * @param   boolean     Long (TRUE) or short (FALSE) months names [Optional]
	 * @return  array  Array from 1-12 with month names
	 */
	public static function months_with_name($long = FALSE)
	{
		// Default values
		$long = (bool) $long;
		$months = Date::hours();

		for ($i = 1; $i <= 12; $i++)
		{
			$timestamp  = mktime(0, 0, 0, $i);
			$months[$i] = date(($long) ? "F" : "M", $timestamp);
		}

		return $months;
	}
	
	/**
	 * Convert Seconds to time ago mode
	 *
	 * @param int $date 
	 * @return string
	 */
	public static function time_ago($date)
	{
		$timesince = FALSE;
		if ( ! empty($date))
		{
			$ago = date('U') - $date;
			$periods = array(__('second'), __('minute'), __('hour'), __('day'), __('week'), __('month'), __('year'), __('ten year'));
			$lengths = array('60', '60', '24', '7', '4.35', '12', '10');
			for ($j = 0; $ago >= $lengths[$j]; $j++)
			{
				$ago /= $lengths[$j];
			}
			$ago = round($ago);

			if ($ago != 1)
			{
				$periods[$j].= __('s');
			}
			$timesince = $ago.' '.$periods[$j].__(' ago');
		}

		return $timesince;
	}

} // End date