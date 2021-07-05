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
 * @lastUpdate:  	2021-07-04
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
	-- most of this is all setup but if done from scratch it would be:
	- set bulkdata URL
	- smart to use test url first

*/


class Downloader {

	public const DOWNLOADDIR = __DIR__ . "/../downloads/";
	public const USERAGENT = 'StarlightEnergies admin@workinout.com';
	public const GETMETHOD = 'GET';
	public string $co_facts_url;
	public string $bulk_data_url;
	public string $test_SEC_url;
	public string $latest_filings_xml_url;
	private string $guzzle_sink;
	private $resource;
	private Utils $stream;


	//home depot default json :-)
	public function __construct(string $json_file='CIK0000354950.json') {

		//just append CIK JSON filename to co_facts_url for whatever company you may want
		$this->co_facts_url = "https://data.sec.gov/api/xbrl/companyfacts/" . $json_file;
		$this->bulk_data_url = "https://www.sec.gov/Archives/edgar/daily-index/xbrl/companyfacts.zip";
		$this->latest_filings_xml_url = "https://www.sec.gov/Archives/edgar/xbrlrss.all.xml";
		$this->test_SEC_url = "https://www.sec.gov/Archives/edgar/xbrlrss.all.xml";

	}

	public function setGuzzleSink() {

		$this->guzzle_sink = '../data2/companyfacts.zip';

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


	public function downloadSECBulkFile($client): Response {

		//returns $response
		return $client->request(self::GETMETHOD, $this->bulk_data_url, [
			//'save_to' => $stream,
			'sink' => $this->guzzle_sink,
			//'stream' => true,
			'headers' => [
				'User-Agent' => self::USERAGENT,
				'Accept' => 'application/zip',
				'Accept-Encoding' => ['gzip', 'deflate'],
				'Host' => 'www.sec.gov'
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

		public function checkDownloadFileStatus() {

			chdir('../data2');
			if(file_exists('companyfacts.zip')) {
				echo "Found file companyfacts.txt\n";
				//stat
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
}

