<?php

namespace App\Service\Stats;

class CurlBuilder {
	private $method;
	private $headers;
	private $body;
	private $returnTransfer;
	private $endPoint;

	public function __construct(string $url) 
	{
		$this->endPoint = $url;
		$this->headers = [];
		$this->body = null;
	}

	public function execute()
	{
		$ch = curl_init($this->endPoint);
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->returnTransfer);
		
		if($this->body) {
			$encodedBody = json_encode($this->body);
			$this->addHeader("Content-Lenght: " . strlen($encodedBody));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedBody);
		}

		if(count($this->headers) !== 0) curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		
		$response = curl_exec($ch);
		//dump($response);
		return array(
			'content' => json_decode($response), 
			'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE)
		);
	}

	public function setMethod(string $method) : self 
	{
		$this->method = $method;

		return $this;
	}

	public function setReturnTransfer(bool $value) : self
	{
		$this->returnTransfer = $value;

		return $this;
	}

	public function addHeader(string $header) : self
	{
		$this->headers[] = $header;

		return $this;
	}

	public function setBody($body) : self
	{
		$this->body = $body;

		return $this;
	}
}