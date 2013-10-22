<?php

namespace PageDownloader;

use \Exception;
use Http\Request;
use Util\Conversions;
use IO\File;
use PageDownloader\DomSourceResolver;
use PageDownloader\SmartDOMDocument;
use PageDownloader\CssSourceResolver;

class Main {
	
	const ROOT_HTTP_DIR = '/output';
	const FS_ROOT = '../../output';
	
	/**
	 * Main application initializer
	 * @throws Exception
	 */
	public function init() {
		// get query string
		$qs = $_SERVER["QUERY_STRING"];
		
		// validate
		if (strlen($_SERVER["QUERY_STRING"]) === 0) {
			throw new Exception('No parameters');
		}
		
		// get array from query string
		$qs = Conversions::getListFromQueryString($qs);
		
		if (isset($qs['url']) === false) {
			throw new Exception('Url not set');
		}
		
		// get page ref
		$page = new Request($qs['url']->getValue());
		$contents = $page->request()->getResponseBody();

		// handle inline CSS images
		$contents = CssSourceResolver::convertUrlFunctions($contents, $page->getUrlFragments()->host);

		$dom = new SmartDOMDocument('1.0');
		$dom->resolveExternals = true;
		$dom->substituteEntities = false;
		$dom->loadHTML($contents);
		
		// update sources
		$domSourceResolver = new DomSourceResolver($dom);
		$domSourceResolver->setHost($page->getUrlFragments()->host);
		$domSourceResolver
			->convert('CSS')
			->convert('JavaScript')
			->convert('images')
			->convert('objects');
		
		echo $dom->saveHTML();
	}
}