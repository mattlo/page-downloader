<?php

namespace Util;

use Common\Entity;

class Conversions {

	/**
	 * Rewrite all references that are relative
	 * @param type $prefix
	 * @param type $contents
	 * @return type
	 */
	static public function addHostToReferences($prefix, $contents) {
		$contents = str_replace('src="/', 'src="' . $prefix, $contents);
		$contents = str_replace('href="/', 'href="' . $prefix, $contents);
		$contents = str_replace('src="../', 'src="' . $prefix . '../', $contents);
		$contents = str_replace('href="../', 'href="' . $prefix . '../', $contents);
		$contents = str_replace('src=\'/', 'src=\'' . $prefix, $contents);
		$contents = str_replace('href=\'/', 'href=\'' . $prefix, $contents);

		return $contents;
	}

	/**
	 * Convert query string into an array
	 * @param type $qs
	 * @return array
	 */
	static public function getListFromQueryString($qs) {
		$items = explode('&', $qs);
		$output = [];

		foreach ($items as $item) {
			$keyvalue = explode('=', $item);

			$en = new Entity;
			$en->setKey($keyvalue[0]);
			$en->setValue($keyvalue[1]);

			$output[$en->getKey()] = $en;
		}

		return $output;
	}
	
	/**
	 * Resolves ./ and ../  in URLs
	 * @param type $path
	 * @return type
	 */
	static public function canonicalizeStringPath($path) {
		$path = explode('/', $path);
		$newpath = array();
		for ($i = 0; $i < sizeof($path); $i++) {
			if ($path[$i] === '' || $path[$i] === '.')
				continue;
			if ($path[$i] === '..') {
				array_pop($newpath);
				continue;
			}
			array_push($newpath, $path[$i]);
		}
		
		return "/" . implode('/', $newpath);
	}
	
	static public function resolveReferencePath($path, $prefixPath) {
		if (substr($path, 0, 1) !== '/') {
			$path = $prefixPath . $path;
		}
		
		return $path;
	}
}