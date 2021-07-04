<?php

$dir1 = __DIR__ . "/../data";
$dir2 = __DIR__ . "/../data2";
$sameFileCount = 0;
$sameFileSize = 0;
$sameModTime = 0;
$sameHash = 0;
$duplicatesArr = [];

$dir_1_array = scandir($dir1);
$dir_2_array = scandir($dir2);

echo "count dir 1: " . count($dir_1_array) . "\n";
echo "count dir 2: "  . count($dir_2_array) . "\n";


foreach ($dir_2_array as $file2 ){

		if($file2 == '.'|| $file2 == '..') {
			continue;
		}

		if(in_array($file2, $dir_1_array)) {
			$sameFileCount += 1;
			//create a new array
			array_push($duplicatesArr,$file2);
			//then find file in array2

			foreach ($dir_1_array as $file1) {
				if($file1 == '.'|| $file1 == '..'){
					continue;
				}
				if($file1 == $file2) {
					array_push($duplicatesArr,$file1);
				}
			}

			$rawfile1 = $duplicatesArr[1];
			$rawfile2 = $duplicatesArr[0];

			$file1_path = $dir1 . "/" . $rawfile1;
			$file2_path = $dir2 . "/" . $rawfile2;

			//now stat both files
			$stats_file1 = stat($file1_path);
			$stats_file2 = stat($file2_path);

			//comparison 1
			$bytes_1 = $stats_file1[7];
			$bytes_2 = $stats_file2[7];
			if($bytes_1 == $bytes_2) {
				$sameFileSize += 1;
				echo "size of File 1 and 2 are the same size: " . $sameFileSize . "\n";

			}

			//comparison 3  removes duplicates from the new arrivals directory (data2)
			//after processing then the new arrivals are written over the current state directory (data)
			$hash1 = md5_file($file1_path);
			$hash2 = md5_file($file2_path);
			if($hash1 == $hash2) {
				$sameHash += 1;
				echo "MD5 hash of File 1 and 2 are the same " . $sameHash  . "\n";
				shell_exec('rm -f ' . $file2_path);
			}


			//flush the array
			$duplicatesArr = array();

		}
}


echo "count dir 1: " . (count($dir_1_array) - 2) . "\n";
echo "count dir 2: "  . (count($dir_2_array) - 2) . "\n";

echo "same file count: " . $sameFileCount . "\n";
echo "same file size: " . $sameFileSize . "\n";

echo "same file hash: " . $sameHash . "\n";
