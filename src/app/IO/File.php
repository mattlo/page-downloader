<?php
namespace IO;

use \Exception;
use Http\Request;

class File {
	
	const PERMISSIONS_OCTET = 0777;
	const RECURSIVE_CREATE_DIRS = true;
	
	protected $filepath;
	
	/**
	 * Constructor
	 * @param string $filepath
	 */
	public function __construct($filepath) {
		$this->filepath = $filepath;
		
		// path has directories?
		if (strpos($filepath, '/') !== false) {
			// prepare directory string
			$relativeDirectory = substr($filepath, 0, strrpos($filepath, '/'));
			
			// create directory path
			if (is_dir($relativeDirectory) === false && mkdir($relativeDirectory, self::PERMISSIONS_OCTET, self::RECURSIVE_CREATE_DIRS) === false) {
				throw new Exception('Failed to create folders: ' . $parentDirectory);
			}

			// defect in `mkdir` where sub directories aren't applying permissions
			chmod($relativeDirectory, self::PERMISSIONS_OCTET);
		}
	}
	
	public function write($contents) {
		// open IO
		$fp = fopen($this->filepath, 'w');
		fwrite($fp, $contents);
		fclose($fp);
		
		// set permissions on new file
		chmod($this->filepath, self::PERMISSIONS_OCTET);
	}
	
	/**
	 * Streams down a file from a remote path
	 * Downloads mimic the directory they live in on the target source
	 * @param string $url
	 * @param string $directory
	 * @param boolean $overwrite
	 * @return string
	 */
	public static function download($url, $directory, $overwrite = false) {
		$url = preg_replace('/(\?|#).*/', '', $url);
		
		$file = new Request($url);
		$filepath = $file->getUrlFragments()->path;
		
		// concat paths
		$fullFilePath = $directory . $filepath;
		
		// validation check
		if (file_exists($fullFilePath) === false || $overwrite === true) {
			$outputFile = new File($directory . $filepath);
			
			// request and record downloaded file
			$outputFile->write($file->request()->getResponseBody());
		}
	}
}