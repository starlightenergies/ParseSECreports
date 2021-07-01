<?php
namespace JDApp;
include '../vendor/autoload.php';

use Nette\Http\Request;
use Nette\Http\UrlScript;
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
$url2 = "https://data.sec.gov/api/xbrl/companyfacts/CIK0001691421.json";


//below not working. need browser
//create URL script object
$urlObject = new UrlScript($url2);

//request data with object
$httpRequest = new Request($urlObject);							//this contacts the server. the object is just useful for methods
$body = $httpRequest->getRawBody();      //???
file_put_contents('../data/allLemonade_data.json',$body);

//returns clone
	//withUrl(Nette\Http\UrlScript $url): Nette\Http\Request

//returns URLScript object
	//$url = $httpRequest->getUrl();
	//$body = $httpRequest->getRawBody();

$headers = $httpRequest->getHeaders();
var_dump($headers);



//curl

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
