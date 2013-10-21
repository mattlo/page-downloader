<?php

namespace PageDownloader;

use \Exception;
use Http\Request;
use Util\Conversions;
use IO\File;

class Main {
	
	const ROOT_HTTP_DIR = '/output';
	
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
	}
}