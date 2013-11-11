<?php

namespace PageDownloader;

use IO\File;
use Util\Conversions;
use PageDownloader\Main;
use PageDownloader\DomSourceResolver;

class CssSourceResolver {
	
	protected $host;
	protected $file;
	protected $path;
	
	/**
	 * Construct
	 * @param \IO\File $file
	 * @param type $host
	 */
	public function __construct(File $file, $host, $path) {
		$this->host = $host;
		$this->file = $file;
		$this->path = $path;
	}
	
	/**
	 * Downloads all references (assumed as images) and stores them under the defined directory
	 * @return void
	 */
	public function convertAndPersist() {
		$resolvedRelativePath = substr($this->file->getParentDirectory(), strlen(Main::FS_ROOT . DomSourceResolver::CSS_DIR));
		$inputString = self::convertUrlFunctions($this->file->getContents(), $this->host, $resolvedRelativePath . '/');
		
		$this->file->write($inputString);
	}
	
	/**
	 * Downloads all references (assumed as images) and stores them under the defined directory
	 * @param type $inputString
	 * @return type
	 */
	static public function convertUrlFunctions($inputString, $host, $resolvedRelativePath = '') {
		$filesImported = [];
		
		$matches = array();
		preg_match_all('/url\((\s|){0,5}(\'|"|)(.+?)(\'|"|)(\s|){0,5}\)/i', $inputString, $matches);
		
		foreach($matches[3] as $image) {
			
			if (in_array($image, $filesImported) === false && strpos('//', $image) === false) {
				$filesImported[] = $image;
				
				// image path
				$imagePath = Conversions::canonicalizeStringPath($resolvedRelativePath . $image);
				
				// download image
				$file = File::download('http://' . $host . $imagePath, Main::FS_ROOT . DomSourceResolver::ASSET_DIR);
				
				// replace resource
				$inputString = str_replace($image, '' . Main::$ROOT_HTTP_DIR . DomSourceResolver::ASSET_DIR . $imagePath, $inputString);
				
				// release
				$file->releaseContents();
			}
		}
		
		return $inputString;
	}
}