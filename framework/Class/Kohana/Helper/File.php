<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 文件助手类
 *
 * @package    Kohana
 * @category   Helpers
 */
class Kohana_Helper_File {

	/**
	 * 读取文件
	 *
	 * 打开文件同时返回其内容
	 *
	 * @param	string	path to file
	 * @return	string
	 */
	public static function read($file)
	{
		if ( ! file_exists($file))
		{
			return FALSE;
		}

		if (function_exists('file_get_contents'))
		{
			return file_get_contents($file);
		}

		if ( ! $fp = @fopen($file, FOPEN_READ))
		{
			return FALSE;
		}

		flock($fp, LOCK_SH);

		$data = '';
		if (filesize($file) > 0)
		{
			$data =& fread($fp, filesize($file));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $data;
	}
	
	/**
	 * Write File
	 *
	 * Writes data to the file specified in the path.
	 * Creates a new file if non-existent.
	 *
	 * @param	string	path to file
	 * @param	string	file data
	 * @return	bool
	 */
	public static function write($path, $data, $mode = FOPEN_WRITE_CREATE_DESTRUCTIVE)
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		return TRUE;
	}
	
	/**
	 * 复制文件
	 */
	public static function copy($source, $dest)
	{
		if ( ! file_exists($source))
		{
			return FALSE;
		}
		if ( ! IN_SAE)
		{
			// 先检测并创建目录
			Helper_Directory::create(dirname($dest), TRUE);
		}
		return @copy($source, $dest);
	}
	
	const MIME_EXTENSION_REGEX = '/^(?:jpe?g|png|[gt]if|bmp|swf)$/';

	/**
	 * Attempt to get the mime type from a file. This method is horribly
	 * unreliable, due to PHP being horribly unreliable when it comes to
	 * determining the mime type of a file.
	 *
	 *     $mime = Helper_File::mime($file);
	 *
	 * @param   string  $filename   file name or path
	 * @return  string  mime type on success
	 * @return  FALSE   on failure
	 */
	public static function mime($filename)
	{
		// Get the complete path to the file
		$filename = realpath($filename);

		// Get the extension from the filename
		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

		if (preg_match(Helper_File::MIME_EXTENSION_REGEX, $extension))
		{
			// Use getimagesize() to find the mime type on images
			$file = getimagesize($filename);

			if (isset($file['mime']))
				return $file['mime'];
		}

		if (class_exists('finfo', FALSE))
		{
			if ($info = new finfo(defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME))
			{
				return $info->file($filename);
			}
		}

		if (ini_get('mime_magic.magicfile') AND function_exists('mime_content_type'))
		{
			// The mime_content_type function is only useful with a magic file
			return mime_content_type($filename);
		}

		if ( ! empty($extension))
		{
			return Helper_File::mime_by_ext($extension);
		}

		// Unable to find the mime-type
		return FALSE;
	}

	/**
	 * 返回指定扩展名的MIME类型
	 *
	 *     $mime = Helper_File::mime_by_ext('png'); // "image/png"
	 *
	 * @param   string  $extension  php, pdf, txt, 等等
	 * @return  string  成功时返回MIME类型
	 * @return  FALSE   失败时
	 */
	public static function mime_by_ext($extension)
	{
		$mimes = Kohana::config('Mime');
		return isset($mimes[$extension]) ? $mimes[$extension][0] : FALSE;
	}

	/**
	 * Lookup MIME types for a file
	 *
	 * @see Kohana_Helper_File::mime_by_ext()
	 * @param string $extension Extension to lookup
	 * @return array Array of MIMEs associated with the specified extension
	 */
	public static function mimes_by_ext($extension)
	{
		$mimes = Kohana::config('Mime');
		return isset($mimes[$extension]) ? ( (array) $mimes[$extension]) : array();
	}
	
	const OCTET_STREAM_MIME = 'application/octet-stream';

	/**
	 * 返回指定MIME类型的文件扩展名
	 *
	 * @param   string  $type  MIME类型
	 * @return  array   符合条件的扩展名
	 */
	public static function exts_by_mime($type)
	{
		static $types = array();

		// Fill the static array
		if (empty($types))
		{
			foreach (Kohana::config('Mime') AS $ext => $mimes)
			{
				foreach ($mimes AS $mime)
				{
					if ($mime == Helper_File::OCTET_STREAM_MIME)
					{
						// octet-stream is a generic binary
						continue;
					}

					if ( ! isset($types[$mime]))
					{
						$types[$mime] = array( (string) $ext);
					}
					elseif ( ! in_array($ext, $types[$mime]))
					{
						$types[$mime][] = (string) $ext;
					}
				}
			}
		}

		return isset($types[$type]) ? $types[$type] : FALSE;
	}

	/**
	 * 根据MIME类型查找文件扩展名
	 *
	 * @param   string  $type  MIME type to lookup
	 * @return  mixed          First file extension matching or false
	 */
	public static function ext_by_mime($type)
	{
		return current(Helper_File::exts_by_mime($type));
	}

	/**
	 * Split a file into pieces matching a specific size. Used when you need to
	 * split large files into smaller pieces for easy transmission.
	 *
	 *     $count = Helper_File::split($file);
	 *
	 * @param   string  $filename   file to be split
	 * @param   integer $piece_size size, in MB, for each piece to be
	 * @return  integer The number of pieces that were created
	 */
	public static function split($filename, $piece_size = 10)
	{
		// 打开指定的文件
		$file = fopen($filename, 'rb');
		// Change the piece size to bytes
		$piece_size = floor($piece_size * 1024 * 1024);
		// Write files in 8k blocks
		$block_size = 1024 * 8;
		// Total number of peices
		$peices = 0;

		while ( ! feof($file))
		{
			// Create another piece
			$peices += 1;
			// Create a new file piece
			$piece = str_pad($peices, 3, '0', STR_PAD_LEFT);
			$piece = fopen($filename.'.'.$piece, 'wb+');
			// Number of bytes read
			$read = 0;
			do
			{
				// Transfer the data in blocks
				fwrite($piece, fread($file, $block_size));

				// Another block has been read
				$read += $block_size;
			}
			while ($read < $piece_size);
			// Close the piece
			fclose($piece);
		}

		// Close the file
		fclose($file);

		return $peices;
	}

	/**
	 * Join a split file into a whole file. Does the reverse of [Helper_File::split].
	 *
	 *     $count = Helper_File::join($file);
	 *
	 * @param   string  $filename   split filename, without .000 extension
	 * @return  integer The number of pieces that were joined.
	 */
	public static function join($filename)
	{
		// 打开文件
		$file = fopen($filename, 'wb+');
		// Read files in 8k blocks
		$block_size = 1024 * 8;
		// Total number of peices
		$pieces = 0;

		while (is_file($piece = $filename.'.'.str_pad($pieces + 1, 3, '0', STR_PAD_LEFT)))
		{
			// Read another piece
			$pieces += 1;

			// Open the piece for reading
			$piece = fopen($piece, 'rb');

			while ( ! feof($piece))
			{
				// Transfer the data in blocks
				fwrite($file, fread($piece, $block_size));
			}

			// Close the peice
			fclose($piece);
		}

		return $pieces;
	}

} // End file
