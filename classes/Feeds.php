<?php
namespace JDApp;

	$myarr = json_decode(file_get_contents('myfile.json'));

$first = $myarr->cik;
$obj1 = $myarr->units; //84
$obj2 = $obj1->USD;  //82  array of stdclass objects
if (is_array($obj2)) {
echo "array\n";
}

if (is_object($obj1)) {
	echo "true\n";
}

foreach ($obj2 as $item) {

	if ($item->form === $form) {
		continue;
	}
	echo $item->end . "\t" . strval($item->val / 1000000) . " millions  " . $item->form . "\n";
	$form = $item->form;
	//echo $item->accn . "\n";
	//echo $item->fy . "\n";
	//echo $item->fp . "\n";
	//echo $item->form . "\n";
	//echo $item->filed . "\n";


	$feed = "https://www.sec.gov/cgi-bin/browse-edgar?action=getcurrent&CIK=&type=&company=&dateb=&owner=include&start=0&count=40&output=atom";

//$data=file("https://data.sec.gov/api/xbrl/companyconcept/CIK0001318605/us-gaap/AccountsPayableCurrent.json");
//$alldata = file("https://data.sec.gov/api/xbrl/companyfacts/CIK0001318605.json");
//file_put_contents('allTesladata.json',$alldata);
	$mytesla = json_decode(file_get_contents('allTesladata.json'));

//var_dump($mytesla);

	$string = file_get_contents('allTesladata.json');
	$arrT = explode(":", $string);
//foreach($arrT as $s) {
//	echo $s . "\n";
//
//}

	$tobj1 = $mytesla->dei;  //object
	$tobj2 = $tobj1->EntityCommonStockSharesOutstanding;
	$tobj3 = $tobj2->units;
	$tobj4 = $tobj3->shares;

	if (is_object($tobj4)) {
		echo "is object\n";
	} elseif (is_array($tobj4)) {
		echo " is array\n";
	}

	$count = $operand1 = 0;
//end,val,accn,fy,fp,form
	foreach ($tobj4 as $obj) {

		echo $obj->end . "\t" . "shares out: " . $obj->val . "\n";
		$issued = $obj->val - $operand1;
		echo "shares issued: " . $issued . "\n";
		$operand1 = $obj->val;

		//echo $obj->accn . "\n";
		//echo $obj->fy . "\n";
		//echo $obj->fp . "\n";
		//echo $obj->form . "\n";
		//sleep(1);
		$count++;

	}
	echo "count: " . $count . "\n";

//$bulkdata = file("http://www.sec.gov/Archives/edgar/daily-index/xbrl/companyfacts.zip");
//if ($size = file_put_contents('bulkdatafile.zip', $bulkdata)) {
//	echo "bulkdata file size: " . $size . "\n";
//}

//for($i = 0; $i <50;$i++) {
//	`cat companyfacts/CIK0001847462.json|awk -F: '{ print $i }'`;
//	sleep(3);
//
//}

	$data = file_get_contents('companyfacts/CIK0001847462.json');
	$info = explode("label", $data);

	foreach ($info as $value) {
		echo $value . "\n\n";
		sleep(2);
	}

}
