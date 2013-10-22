<?php

namespace IO;

use \Exception;
use Http\Request;
use PageDownloader\Main;

class File {

	const PERMISSIONS_OCTET = 0777;
	const RECURSIVE_CREATE_DIRS = true;

	protected $filepath;
	protected $contents;
	protected $parentDirectory;
	protected $exists = false;

	/**
	 * Constructor
	 * Sets filepath and file system directory
	 * @param string $filepath
	 */
	public function __construct($filepath) {
		$this->filepath = $filepath;
	}

	/**
	 * Writes contents to file
	 * @param type $contents
	 * @return \IO\File
	 */
	public function write($contents) {
		// path has directories?
		if (strpos($this->filepath, '/') !== false) {
			// prepare directory string
			$this->setParentDirectory(substr($this->filepath, 0, strrpos($this->filepath, '/')));

			// fs directory
			$relativeDirectory = $this->getParentDirectory();

			// create directory path
			if (is_dir($relativeDirectory) === false && mkdir($relativeDirectory, self::PERMISSIONS_OCTET, self::RECURSIVE_CREATE_DIRS) === false) {
				throw new Exception('Failed to create folders: ' . $relativeDirectory);
			}

			// defect in `mkdir` where sub directories aren't applying permissions
			chmod($relativeDirectory, self::PERMISSIONS_OCTET);
		}
		
		// open IO
		$fp = fopen($this->filepath, 'w');
		fwrite($fp, $contents);
		fclose($fp);

		// set permissions on new file
		chmod($this->filepath, self::PERMISSIONS_OCTET);

		// save contents in memory
		$this->setContents($contents);

		return $this;
	}

	/**
	 * Streams down a file from a remote path
	 * Downloads mimic the directory they live in on the target source
	 * @param string $url
	 * @param string $directory
	 * @param boolean $overwrite
	 * @return IO\File
	 */
	public static function download($url, $directory, $overwrite = false) {
		$url = preg_replace('/(\?|#).*/', '', $url);

		$file = new Request($url);
		$filepath = $file->getUrlFragments()->path;

		// concat paths
		$fullFilePath = $directory . $filepath;

		// validation check
		if (file_exists($fullFilePath) === false || $overwrite === true) {
			// @TODO add logger for file overwrites
			$outputFile = new File($fullFilePath);

			// request and record downloaded file
			$outputFile->write($file->request()->getResponseBody());

			return $outputFile;
		} else {
			$existingFile = new File($fullFilePath);
			$existingFile->open();
			$existingFile->setExists(true);
			
			return $existingFile;
		}
	}

	/**
	 * Open file
	 * @return void
	 */
	public function open() {
		$handle = fopen($this->filepath, "rb");
		$contents = '';
		while (!feof($handle)) {
			$contents .= fread($handle, 8192);
		}
		
		$this->contents = $contents;
		
		fclose($handle);
	}

	/**
	 * Releases file contents before GC hits
	 * @return void
	 */
	public function releaseContents() {
		$this->setContents(null);
	}

	public function getFilepath() {
		return $this->filepath;
	}

	public function getContents() {
		return $this->contents;
	}

	public function setFilepath($filepath) {
		$this->filepath = $filepath;
		return $this;
	}

	public function setContents($contents) {
		$this->contents = $contents;
		return $this;
	}

	public function getParentDirectory() {
		return $this->parentDirectory;
	}

	public function setParentDirectory($parentDirectory) {
		$this->parentDirectory = $parentDirectory;
		return $this;
	}
	
	public function getExists() {
		return $this->exists;
	}

	public function setExists($exists) {
		$this->exists = $exists;
		return $this;
	}
}
