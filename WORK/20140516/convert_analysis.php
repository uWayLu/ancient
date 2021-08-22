﻿<?php
header ('Content-Type: text/html; charset=utf-8');

$librariespath = 'php_libraries/';
include $librariespath.'Schrenk/LIB_parse.php';
include $librariespath.'Schrenk/LIB_mysql.php';

//create variable names
$uploaddir = 'uploads/';
$uploadfile = $uploaddir.basename($_FILES['file']['name']);
//$lastno = $_POST['lastno'];
$lastno = 80;

//print_r($_FILES);
echo "<pre>";
if (move_uploaded_file($_FILES['file']['tmp_name'], iconv("utf-8", "big5", $uploadfile)))
	{
	echo "Upload OK \n";
	$content = file_get_contents($uploadfile);
	// 在DB中新增table
	$tablename = split_string($_FILES['file']['name'], '.', BEFORE, EXCL); // 先依照檔名建立table; *中文會出錯; 目前設定欄位而已
	$sql = "CREATE TABLE $tablename
			(
			CapSeq int NOT NULL AUTO_INCREMENT PRIMARY KEY,
			CataCode char(12),
			SubjCode char(1),
			PageNo char(2),
			ChapNo char(2),
			CapType char(1),
			CapSrc1 char(50),
			CapSrc2 char(50),
			CapCode char(12),
			Diffcult char(2),
			CapGrp char(12),
			GrpSubj text,
			CapSubj text,
			Answer char(50),
			AnsA text,
			AnsB text,
			AnsC text,
			AnsD text,
			AnsE text,
			AnsF text,
			Analysis text
			)DEFAULT CHARSET=utf8 ENGINE = MYISAM";
	exe_sql(DATABASE, $sql);
	// follow rules of context arranged and php
		$str_reason = '＊';
		$data_array['CapSrc1'] = split_string($content, ' 年專門職業及技術人員普通考試導遊人員、領隊人員考試試題' , BEFORE, EXCL);
		if ( strlen($data_array['CapSrc1']) < 3 ) 
			{
				$data_array['CapSrc1'] = "0" .$data_array['CapSrc1'] ."年"; 
				echo "小於100年所以加個0在前方</br>";
			}
		else $data_array['CapSrc1'] = $data_array['CapSrc1'] ."年";
		$data_array['CapSrc2'] = '導遊實務(二)';
		//$data_array['CapSrc2'] = '領隊實務(二)';
	for ($i=1; $i < $lastno; $i++)
		{
		$data_array['Analysis'] = '';
		$fraction = return_between($content,  $i.'.', $i+1 .'.', INCL);
		
		$data_array['Analysis'] = return_between($fraction, '解 析', $i+1 .'.', EXCL);
		
		insert(DATABASE, $tablename, $data_array); //插入資料庫
		
		}
	// 最後一題	
		$data_array['Analysis'] = '';
		$fraction = split_string($content, $i.'.', AFTER, INCL);

		$data_array['Analysis'] = split_string($fraction, '解 析', AFTER, EXCL);

		echo '總題數' .$i .'輸入資料庫完畢。</br>';
		insert(DATABASE, $tablename, $data_array); //插入資料庫
		//從資料庫讀取出插入的資料
		$result = exe_sql(DATABASE,"SELECT * FROM $tablename");
		print_r($result);
				
	echo "--------------------------------------------------------------- \n";
	// 處理後刪除檔案
	unlink($uploadfile);
	}
else 
	{
    echo "Upload failed \n";
	}
print_r($_FILES);
echo "</pre>";
?>