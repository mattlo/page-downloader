<?php

namespace Http;

class Request {

	protected $responseBody;
	protected $url;
	protected $basename;
	protected $extension;
	protected $filename;
	protected $urlFragments;
	
	/**
	 * Constructor
	 * @param {string} $url
	 */
	public function __construct($url) {
		$this->setUrl($url);
		
		// get path info and set as properties
		$pathInfo = (object) pathinfo($this->getUrl());
		$this->setBasename($pathInfo->basename);
		
		if (isset($pathInfo->extension) === true) {
			$this->setExtension($pathInfo->extension);
		}
		
		$this->setFilename($pathInfo->filename);
		$this->setUrlFragments($url);
	}

	/**
	 * Get Request
	 * @return \Webextract\Http
	 */
	public function request() {
		// open up cURL request
		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		$this->responseBody = curl_exec($ch);
		curl_close($ch);
		
		return $this;
	}
	
	public function getResponseBody() {
		return $this->responseBody;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setResponseBody($responseBody) {
		$this->responseBody = $responseBody;
		return $this;
	}

	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}
	
	public function getBasename() {
		return $this->basename;
	}

	public function getExtension() {
		return $this->extension;
	}

	public function setBasename($basename) {
		$this->basename = $basename;
		return $this;
	}

	public function setExtension($extension) {
		$this->extension = $extension;
		return $this;
	}
	
	public function getFilename() {
		return $this->filename;
	}

	public function setFilename($filename) {
		$this->filename = $filename;
		return $this;
	}
	
	public function getUrlFragments() {
		return $this->urlFragments;
	}

	public function setUrlFragments($urlFragments) {
		$this->urlFragments = (object) parse_url($urlFragments);
		
		return $this;
	}
}
