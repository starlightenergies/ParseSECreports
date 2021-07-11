<?php
namespace JDApp;
include '../vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;

/*
MIT LICENSE
Copyright 2021 StarlightEnergies.com
Permission is hereby granted, free of charge, to any person obtaining a copy of this software
and associated documentation files (the "Software"), to deal in the Software without restriction,
including without limitation the rights to use, copy, modify, merge, publish, distribute,
sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH
THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/**
 * @purpose:    	XBRL Report Processing Application
 * @filename:    	Downloader.php
 * @version:    	1.0
 * @lastUpdate:  	2021-07-08
 * @author:        	James Danforth <james@reemotex.com>
 * @pattern:		Wrapper around GuzzleHttp, makes HTTP Clients.
 * @since:    		2021-07-04
 * @controller:
 * @view:
 * @mytodo:
 * @delegates:		GuzzleHttp
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:		https://www.sec.gov/os/accessing-edgar-data
 */

/* usage:
	-- there are too many examples to list but here is one:
		//create client // $dl = new Downloader();  $client = $dl->guzzleClient();
		//set name of file to create with downloaded info // $file = "../data2/companyfacts.zip";
		//set target to download (or provide URL string) // $target = $dl->bulk_data_url;
		//set location to store data (or send location) // $dl->setGuzzleSink($file);
		//setup resource to write to (..data2/companydata.zip, etc)  // $dl->setGuzzleResource($file);
		//setup guzzle stream (uses resource created above)  // $dl->setGuzzleStream();
		//set appropriate header for file type  // application/zip, text/xml, etc // $dl->setAcceptHeader('application/zip');
		//download the file with get method //$response = $dl->downloadSEFile($client, $target);
		//display common response codes and headers  //$dl->getCommonResponseCodes($response);
		//check download file status	// $dl->checkDownloadFileStatus();
		//list headers separately(optional) // $dl->listHeadersOnly($response);
		//close file resource // $dl->closeFileHandle();

//create client
$dl = new Downloader();
$client = $dl->guzzleClient();
$domain = "www.alphavantage.com";
$dl->setHostDomain($domain);								//set the host in the headers
$file = "../includes/listing_status.csv";					//set name of file to create with downloaded info
$target = $activeListReq;									//set target to download (or provide URL string)
$dl->setGuzzleSink($file);									//set location to store data (or send location)
$dl->setGuzzleResource($file);								//setup resource to write to (..data2/companydata.zip, etc)
$dl->setGuzzleStream();										//setup guzzle stream (uses resource created above)
$dl->setAcceptHeader('text/plain');							//set media header application/zip, text/xml, text/plain etc
$response = $dl->downloadSEFile($client, $target);			//download the file with get method
$dl->getCommonResponseCodes($response);						//display common response codes and headers
$dl->checkDownloadFileStatus($file);						//check download file status
$dl->listHeadersOnly($response);							//list headers separately(optional)
$dl->closeFileHandle();										//close file resource

*/



class Downloader {


	const DOWNLOADDIR = __DIR__ . "/../downloads/";
	const USERAGENT = 'StarlightEnergies <admin@workinout.com>';
	const GETMETHOD = 'GET';
	public $co_facts;
	public $bulk_data_url;
	public $test_SEC_url;
	public $latest_filings_xml_url;
	private $guzzle_sink;
	private $resource;
	private $stream;
	private $accept_header;
	public $cik_list;
	public $host_domain;


	//home depot default json :-)
	public function __construct() {

		//just append CIK JSON filename to co_facts_url for whatever company you may want
		$this->co_facts = "https://data.sec.gov/api/xbrl/companyfacts/CIK0000354950.json";
		$this->bulk_data_url = "https://www.sec.gov/Archives/edgar/daily-index/xbrl/companyfacts.zip";
		$this->latest_filings_xml_url = "https://www.sec.gov/Archives/edgar/xbrlrss.all.xml";
		$this->test_SEC_url = "https://www.sec.gov/Archives/edgar/xbrlrss.all.xml";
		$this->cik_list = "https://www.sec.gov/include/ticker.txt";
		$this->host_domain = "www.sec.gov";

	}

	public function setHostDomain($domain) {
		$this->host_domain = $domain;
	}

	public function setAcceptHeader($header) {
		$this->accept_header = $header;
	}

	public function setGuzzleSink(string $file="") {

		if(empty($file)) {
			$this->guzzle_sink = '../data2/companyfacts.zip';
		} else {
			$this->guzzle_sink = $file;
		}

	}

	public function setGuzzleResource(string $file) {

		//need to test the second one TODO
		$this->resource = fopen($file, 'w');
		//$resource = Utils::tryFopen('../data2/companydata.zip', 'w');
	}

	public function setGuzzleStream() {

		$this->stream = Utils::streamFor($this->resource);
	}

	public function guzzleClient(): Client {

		return new \GuzzleHttp\Client();
	}

	public function getGuzzleHeaders($resp) {

		foreach ($resp->getHeaders() as $name => $values) {
			echo $name . ': ' . implode(', ', $values) . "\n";
		}
	}


	public function downloadSEFile($client,$target): Response {

		//returns $response
		return $client->request(self::GETMETHOD, $target, [
			//'save_to' => $stream,
			'sink' => $this->guzzle_sink,
			//'stream' => true,
			'headers' => [
				'User-Agent' => self::USERAGENT,
				'Accept' => $this->accept_header,
				'Accept-Encoding' => ['gzip', 'deflate'],
				'Host' => $this->host_domain
			],
			'progress' => function (
				$downloadTotal,
				$downloadBytes,
				$uploadTotal,
				$uploadBytes
			) {
				//do something
				echo "download Total: " . floatval($downloadBytes / $downloadTotal) . "\n";
			},
		]);
	}

		public function getCommonResponseCodes($response) {

			echo $response->getStatusCode() . " - status \n"; 				// 200 is good
			echo $response->getReasonPhrase() . " - reason \n";
			$vals = $response->getHeader('Content-Length') . " - length \n";
			var_dump($vals);
			echo $response->getHeaderLine('content-type') . "\n";

		}

		public function checkDownloadFileStatus(?string $file) {

			if(empty($file)) {
				chdir('../data2');
				if(file_exists('companyfacts.zip')) {
					echo "Found file companyfacts.zip\n";
					//stat
				} else{
					if(file_exists($file)) {
						echo "Found file: " . $file . "\n";
						//stat
					}
				}
			}
		}

		public function handleBodyResponse($response) {

				$body = $response->getBody();
				while (!$body->eof()) {
					$bytes = $body->read(1024);
					fwrite($this->resource, $bytes);
				}
		}

		public function sendAsyncRequest(): Promise {

			$client = $this->guzzleClient();
			// Send an asynchronous request.
			$request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org');
			$promise = $client->sendAsync($request)->then(function ($response) {
				echo 'I completed! ' . $response->getBody();
			});
			return $promise->wait();
		}

		public function listHeadersOnly($resp) {

			foreach($resp->getHeaders() as $name => $values) {
				echo $name . ': ' . implode(', ',$values) . "\n";
			}
		}

		public function closeFileHandle() {
			fclose($this->resource);
		}

}

