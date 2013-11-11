<?php

namespace PageDownloader;

use IO\File;
use PageDownloader\Main;
use PageDownloader\CssSourceResolver;
use PageDownloader\SmartDOMDocument;
use Util\Conversions;

/**
 * **NOTE**: Source resolver assumes all refs are absolute
 * Also all downloads are assumed under HTTP protocol
 * @TODO add SSL support
 */
class DomSourceResolver {
	
	protected $dom;
	protected $host;
	protected $path;
	
	const CSS_DIR = '/css';
	const JS_DIR = '/js';
	const ASSET_DIR = '/assets';
	
	/**
	 * Constructor
	 * @param \PageDownloader\SmartDOMDocument $dom
	 */
	public function __construct(SmartDOMDocument $dom) {
		$this->setDom($dom);
	}
	
	/**
	 * Syntatic sugar for conversion methods
	 * @param type $type
	 * @return \PageDownloader\DomSourceResolver
	 */
	public function convert($type) {
		switch ($type) {
			case 'CSS':
				$this->convertCss();
			break;
			case 'JavaScript':
				$this->convertJs();
			break;
			case 'objects':
				$this->convertSwfObjects();
			break;
			case 'images':
				$this->convertImages();
			break;
		}
		
		return $this;
	}
	
	/**
	 * Find all CSS references, update sources, and download all images within CSS references
	 * @return void
	 */
	public function convertCss() {
		$styleTags = $this->dom->documentElement->getElementsByTagName('link');
		for($i = 0; $i < $styleTags->length; ++$i) {
			$styleTag = $styleTags->item($i);
			
			// match non print valid external styles
			if ($styleTag->getAttribute('media') !== 'print' && strpos('//', $styleTag->getAttribute('href')) === false) {
				$file = File::download('http://' . $this->host . Conversions::resolveReferencePath($styleTag->getAttribute('href'), $this->path), Main::FS_ROOT . self::CSS_DIR, false);
				
				// only handle assets if file doesnt exist
				if ($file->getExists() === false) {
					// download references in CSS file
					$referenceResolver = new CssSourceResolver($file, $this->host, $this->path);
					$referenceResolver->convertAndPersist();
				}

				// update node
				$styleTag->setAttribute('href', Main::$ROOT_HTTP_DIR . self::CSS_DIR .  Conversions::resolveReferencePath($styleTag->getAttribute('href'), $this->path));
			}
		}
	}
	
	/**
	 * Update JS sources
	 * @return void
	 */
	public function convertJs() {
		$this->convertGenericSrc('script', 'src', self::JS_DIR);
	}
	
	/**
	 * Update SWF sources
	 * @return void
	 */
	public function convertSwfObjects() {
		$this->convertGenericSrc('param', 'value', self::ASSET_DIR, 'name', 'movie');
		$this->convertGenericSrc('param', 'value', self::ASSET_DIR, 'name', 'expressinstall');
		$this->convertGenericSrc('object', 'data', self::ASSET_DIR, 'type', 'application/x-shockwave-flash');
	}
	
	/**
	 * Update image sources
	 */
	public function convertImages() {
		$this->convertGenericSrc('img', 'src', self::ASSET_DIR);
	}
	
	/**
	 * Tag src replacer
	 * @param type $tag
	 * @param type $key
	 * @param type $directory
	 * @param type $requiredAttr
	 * @param type $requiredValue
	 * @return void
	 */
	public function convertGenericSrc($tag, $key, $directory, $requiredAttr = null, $requiredValue = null) {
		$paramTags = $this->dom->documentElement->getElementsByTagName($tag);
		
		for($i = 0; $i < $paramTags->length; ++$i) {
			$paramTag = $paramTags->item($i);
			
			// match non print valid external scripts
			if (strlen($paramTag->getAttribute($key)) > 0 && strpos('//', $paramTag->getAttribute($key) === false)) {
				if ($requiredAttr !== null) {
					if ($paramTag->getAttribute($requiredAttr) === $requiredValue) {
						File::download('http://' . $this->host . Conversions::resolveReferencePath($paramTag->getAttribute($key), $this->path), Main::FS_ROOT . $directory, false);
						$paramTag->setAttribute($key, Main::$ROOT_HTTP_DIR . $directory .  Conversions::resolveReferencePath($paramTag->getAttribute($key), $this->path));
					}
				} else {
					File::download('http://' . $this->host . Conversions::resolveReferencePath($paramTag->getAttribute($key), $this->path), Main::FS_ROOT . $directory, false);
					$paramTag->setAttribute($key, Main::$ROOT_HTTP_DIR . $directory .  Conversions::resolveReferencePath($paramTag->getAttribute($key), $this->path));
				}
				
			}
		}
	}
	
	public function getDom() {
		return $this->dom;
	}

	public function setDom($dom) {
		$this->dom = $dom;
		return $this;
	}
	
	public function getHost() {
		return $this->host;
	}

	public function setHost($host) {
		$this->host = $host;
		return $this;
	}
	
	public function getPath() {
		return $this->path;
	}

	public function setPath($path) {
		$this->path = $path;
	}
}