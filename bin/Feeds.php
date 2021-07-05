<?php
namespace JDApp;
include '../vendor/autoload.php';

//use Nette\Http\Request;
//use Nette\Http\UrlScript;
//use Nette\Http\FileUpload;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;

/*
//use Nette\Http\RequestFactory;

//this works once then fails
//$homedepot = file("https://data.sec.gov/api/xbrl/companyfacts/CIK0000354950.json");
//file_put_contents('../data/allHomeDepotdata.json',$homedepot);

//nette preferred
//can create with factory
	//$factory = new RequestFactory;
	//$httpRequest = $factory->fromGlobals();

//home depot
$url = "https://data.sec.gov/api/xbrl/companyfacts/CIK0000354950.json";

//lemonade
$url2 = "https://data.sec.gov/api/xbrl/companyfacts/";

//bulkdata
$bulkdata = "https://www.sec.gov/Archives/edgar/daily-index/xbrl/companyfacts.zip";
$file = "companyfacts.zip";

//below not working. need browser
//create URL script object
$urlObject = new UrlScript($bulkdata);

//request data with object
$httpRequest = new Request($urlObject);							//this contacts the server. the object is just useful for methods
//$body = $httpRequest->getRawBody();      //???

//returns a FileUpload Object hopefully
$bulkFile = $httpRequest->getFile($file);
//file upload methods
echo $bulkFile->getName() . " - name\n";
echo $bulkFile->getUntrustedName() . " - untrusted name\n";
echo $bulkFile->getSanitizedName() . " - sanitized name\n";
echo $bulkFile->getContentType() . " - content type name\n";
echo $bulkFile->getSize() . " - file size\n";
echo $bulkFile->getTemporaryFile() . " - temp file path\n";
echo $bulkFile->getError() . " - error code\n";
echo $bulkFile->isOk() . " - bool if downloaded ok\n";
echo $bulkFile->hasFile() . " - true if downloaded\n";
$dest = __DIR__ . "/../downloads/";
//returns new FileUpload object
$bulkFile = $bulkFile->move($dest) . " - move to another location name\n";

//file_put_contents('../data/allLemonade_data.json',$body);

//returns clone
	//withUrl(Nette\Http\UrlScript $url): Nette\Http\Request

//returns URLScript object
	//$url = $httpRequest->getUrl();
	//$body = $httpRequest->getRawBody();

$headers = $httpRequest->getHeaders();
var_dump($headers);
*/

//curl using guzzlehttp/guzzle

//bulkdata
$bulkdata = "https://www.sec.gov/Archives/edgar/daily-index/xbrl/companyfacts.zip";
$test = "https://www.sec.gov/Archives/edgar/xbrlrss.all.xml";
$resource = fopen("../data/companydata.zip", 'w');
//$resource = Utils::tryFopen('../data2/companydata.zip', 'w');
$stream = Utils::streamFor($resource);
$client = new \GuzzleHttp\Client();
$response = $client->request('GET', $bulkdata, [
	//'save_to' => $stream,
	'sink'		=> '../data2/companyfacts.zip',
	//'stream' => true,
	'headers' => [
			'User-Agent'		=> 'StarlightEnergies admin@workinout.com',
			'Accept'			=> 'application/zip',
			'Accept-Encoding' 	=> ['gzip', 'deflate'],
			'Host'				=> 'www.sec.gov'
		],
	'progress' => function (
		$downloadTotal,
		$downloadBytes,
		$uploadTotal,
		$uploadBytes
	) {
		//do something
		echo "download Total: " . floatval($downloadBytes/$downloadTotal) . "\n";

	},
]);

echo $response->getStatusCode() . " - status \n"; 										// 200
echo $response->getReasonPhrase() . " - reason \n";
$vals = $response->getHeader('Content-Length') . " - length \n";
var_dump($vals);
echo $response->getHeaderLine('content-type') . "\n"; 							// 'application/json; charset=utf8'
//$body = $response->getBody();
//while (!$body->eof()) {
//	$bytes = $body->read(1024);
//	fwrite($resource, $bytes)
//}
// '{"id": 1420053, "name": "guzzle", ...}'
chdir('../data2');
if(file_exists('companyfacts.zip')) {
	echo "Found file companyfacts.txt\n";
	//stat
}

foreach($response->getHeaders() as $name => $values) {

	echo $name . ': ' . implode(', ',$values) . "\n";
}
fclose($resource);



// Send an asynchronous request.
//$request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org');
//$promise = $client->sendAsync($request)->then(function ($response) {
//	echo 'I completed! ' . $response->getBody();
//});

//$promise->wait();


//packagist

//javascript



/*
//examples

//"https://data.sec.gov/api/xbrl/companyfacts/CIK##########.json"
//$data=file("https://data.sec.gov/api/xbrl/companyconcept/CIK0001318605/us-gaap/AccountsPayableCurrent.json");
//$alldata = file("https://data.sec.gov/api/xbrl/companyfacts/CIK0001318605.json");
	$mytesla = json_decode(file_get_contents('allTesladata.json'));

//var_dump($mytesla);

	$string = file_get_contents('allTesladata.json');
	$arrT = explode(":", $string);
//fo
	$myarr = json_decode(file_get_contents('myfile.json'));


//	$feed = "https://www.sec.gov/cgi-bin/browse-edgar?action=getcurrent&CIK=&type=&company=&dateb=&owner=include&start=0&count=40&output=atom";

//	"https://data.sec.gov/api/xbrl/companyfacts/CIK##########.json"
//$data=file("https://data.sec.gov/api/xbrl/companyconcept/CIK0001318605/us-gaap/AccountsPayableCurrent.json");
//$alldata = file("https://data.sec.gov/api/xbrl/companyfacts/CIK0001318605.json");
//file_put_contents('allTesladata.json',$alldata);
//	$mytesla = json_decode(file_get_contents('allTesladata.json'));

//var_dump($mytesla);

//$bulkdata = file("http://www.sec.gov/Archives/edgar/daily-index/xbrl/companyfacts.zip");
//if ($size = file_put_contents('bulkdatafile.zip', $bulkdata)) {
//	echo "bulkdata file size: " . $size . "\n";
//}

//for($i = 0; $i <50;$i++) {
//	`cat companyfacts/CIK0001847462.json|awk -F: '{ print $i }'`;
//	sleep(3);
//
//}

*/
