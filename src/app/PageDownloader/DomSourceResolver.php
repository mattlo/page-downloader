<?php

namespace PageDownloader;

use IO\File;
use PageDownloader\Main;
use PageDownloader\CssSourceResolver;
use PageDownloader\SmartDOMDocument;

/**
 * **NOTE**: Source resolver assumes all refs are absolute
 * Also all downloads are assumed under HTTP protocol
 * @TODO Add functionality to support relative paths and "../" like paths
 * @TODO add SSL support
 */
class DomSourceResolver {
	protected $dom;
	protected $host;
	
	const CSS_DIR = '/css';
	const JS_DIR = '/js';
	const ASSET_DIR = '/assets';
	
	public function __construct(SmartDOMDocument $dom) {
		$this->setDom($dom);
	}
	
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
	
	public function convertCss() {
		$styleTags = $this->dom->documentElement->getElementsByTagName('link');
		for($i = 0; $i < $styleTags->length; ++$i) {
			$styleTag = $styleTags->item($i);
			
			// match non print valid external styles
			if ($styleTag->getAttribute('media') !== 'print') {
				$file = File::download('http://' . $this->host . $styleTag->getAttribute('href'), Main::FS_ROOT . self::CSS_DIR, false);
				
				// only handle assets if file doesnt exist
				if ($file->getExists() === false) {
					// download references in CSS file
					$referenceResolver = new CssSourceResolver($file, $this->host);
					$referenceResolver->convertAndPersist();
				}

				// update node
				$styleTag->setAttribute('href', Main::ROOT_HTTP_DIR . self::CSS_DIR .  $styleTag->getAttribute('href'));
			}
		}
	}
	
	public function convertJs() {
		
	}
	
	public function convertSwfObjects() {
		
	}
	
	public function convertImages() {
		
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
}