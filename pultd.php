<?php

include("conf.php");
$commit_path="commit/";
$image_path="image/";

//$cmd=$_POST['cmd'];
var_dump($argv);
$cmd=$argv[1];
echo $cmd."\n";
//echo "<asdf style='font-size:30px;'>".substr(time(), 7)."</asdf><br>";
$commit=false;
$push=false;
$pull=false;
$init=false;
$know=false;
$dry=false;
$list=false;
$file=false;
$brave=false;
if(isset($argv[2]))
	if($argv[2]=="dry")
		$dry=true;
if(isset($argv[2]))
	if($argv[2]=="brave")
		$brave=true;
if(isset($argv[2]))
	if($argv[2]=="list")
		$list=true;
if(isset($argv[2]))
	if($argv[2]=="file")
		if(isset($argv[3])){
			$file_pull_name=$argv[3];	
			$file_pull=true;
		}
if($cmd=="commit") $commit=true;
if($cmd=="push") $push=true;
if($cmd=="fast") {$commit=true;$push=true;}
if($cmd=="pull") $pull=true;
if($cmd=="init") $init=true;
if($cmd=="i'm_up_to_date") $know=true;
if($know){
	file_put_contents("files/lasts.ync", time());
}
if($init){
	echo_cmd("mkdir files");
	echo_cmd("mkdir image");
	echo_cmd("cp -r ".$local_path."* image/"); 
	file_put_contents("files/lasts.ync", time());
}
if($commit){
	echo "DEPRECATED";
	exit();
}
if($pull){
	// set up basic connection
	
	$conn_id = ftp_connect($ftp_server);

	// login with username and password
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
if(!$dry)
	if(ftp_get($conn_id, "files/get_ftpcatap.ult", $remote_path."ftpcatap.ult", FTP_ASCII)) {
		echo "ftpcatap.ult<-OK\n";
	} else echo "ftpcatap.ult<-virhe\n";
	
	$rt_array=read_ftpcatapult();
	$rt_assoc=get_assoc($rt_array);
	$conflict=check_conflict($rt_array);	
	if($file_pull){
		$conflict[]=$file_pull_name;	
	}
	if(isset($conflict[0])){
		for($i=0;$i<count($conflict);$i++){
			if(substr($conflict[$i], -1)=="/"){
				shell_exec("mkdir ".$image_path.$conflict[$i]);
			if($brave)cmd_echo("mkdir ".$local_path.$conflict[$i]);
			}
			else{
				 if(ftp_get($conn_id, $image_path.$conflict[$i], $remote_path.$conflict[$i], FTP_ASCII)){
					echo "get ".$conflict[$i].": OK";
				}else{
					echo "get ".$conflict[$i].": ERROR";
				} 
				if($brave)cmd_echo("cp ".$image_path.$conflict[$i]." ".$local_path.$conflict[$i]);
			}
		}
	}else echo "Everything was up to date.";

	file_put_contents("files/lasts.ync", time());

	write_ftpcatapult($rt_array, $rt_assoc);

	ftp_put($conn_id, $remote_path."ftpcatap.ult", "files/send_ftpcatap.ult", FTP_ASCII);
	ftp_close($conn_id);
	echo "\nPulled: ".json_encode($conflict)."\n";
}
if($push){
	$pushed=array();
	//hae dirlist last_commitista
	$jotain=false;
	$start_dir=$local_path;
	$target_dir=$image_path;
	$dir_length=strlen($start_dir);
	$dir_list=array();
	$dir_list[]=substr($start_dir,0,$dir_length-1);
	$dir_list=find_dir($start_dir, $dir_list);
	
	set_time_limit(0);
	$file = '';
	$remote_file = '';

	// set up basic connection
	
	$conn_id = ftp_connect($ftp_server);

	// login with username and password
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
if(!$dry)
	if(ftp_get($conn_id, "files/get_ftpcatap.ult", $remote_path."ftpcatap.ult", FTP_ASCII)) {
		echo "ftpcatap.ult<-OK\n";
	} else echo "ftpcatap.ult<-virhe\n";
	$rt_array=read_ftpcatapult();
	$rt_assoc=get_assoc($rt_array);
	$conflict=check_conflict($rt_array);	
	if(isset($conflict[0])){
		echo "Check conflicts->\n";
		for($i=0;$i<count($conflict);$i++){
			echo "Conflict: ".$conflict[$i]."\n";
		}
	}	else echo "Check conflicts->OK\n";
	//käy läpi joka tiedosto joka kansiossa
			file_put_contents("files/ftpcatap.ult2", "");
	$ignored=array();
	$ignored[]="ohje.php";
	$ignored[]="lasts.ync";
	$ignored[]=".DS_Store";
	$ignored[]="ftpcatap.ult";
	foreach($dir_list as $d){
		
		$target=substr($d,$dir_length)."/";
		if($target=="/"){$target="";}
		//echo "***".$d."\n";
		//if($d."/"!=$start_dir)
		//file_put_contents("files/ftpcatap.ult2", substr($d."/ ".time()." \n", strlen($start_dir)), FILE_APPEND);
		$files=scandir($d);
		foreach($files as $f){
			$cont=false;
			foreach($ignored as $ig){
				if($f==$ig){
					echo "Ignored ".$ig."\n";
					$cont=true;
					continue;
				}
			}
			if($cont)continue;
			//if($f=="ftpcatap.ult" || $f=="lasts.ync" || $f==".DS_Store" || $f=="ohje.php")continue;
//			if($f=="lasts.ync")continue;
			//mitä tehdään jokaisella kansiolle
			$target_path=$target_dir.$target.$f;
			//echo "Löytyi ".$d."//".$f."<br>";
//			echo $target_path."\n";
			if($f!="." && $f!=".."){
			if(!is_dir($d."/".$f))
				file_put_contents("files/ftpcatap.ult2", substr($d."/".str_replace(" ","@",$f)." ".time()." \n", strlen($start_dir)), FILE_APPEND);
			if(is_dir($d."/".$f))
				file_put_contents("files/ftpcatap.ult2", substr($d."/".str_replace(" ","@",$f)."/ ".time()." \n", strlen($start_dir)), FILE_APPEND);
			}
			if(!$list){
			if(is_dir($d."/".$f)&& $f!="." && $f!=".."){
				if(!file_exists($target_path)){
					$jotain=true;
					echo "FTP create directory ".$remote_path.$target.$f."/"."\n";
					//kansiota ei ole
					//FTP tee kansio
					$newdir=$remote_path.$target.$f."/";
					if (ftp_mkdir($conn_id, $newdir)) {
					 echo "successfully created ".$newdir."\n";
					$realfilename=substr($d."/".str_replace(" ","@",$f."/"), strlen($start_dir));
					$rt_assoc[$realfilename]=time();
					$rt_array[]=$realfilename;
					$rt_array[]=time();
					$pushed[]=$realfilename;
					} else {
					 echo "There was a problem while creating ".$newdir."\n";
					 exit;
					}
				}
			}
			//mitä tehdään jokaiselle tiedostolle
			else if(!is_dir($d."/".$f)&& $f!="." && $f!=".."){
				if(!file_exists($target_path)){
					$jotain=true;
					echo $d."/".$f." is new.\n";
					echo "FTP send file -> ".$remote_path.$target.$f."\n";
					//tiedostoa ei ole					
					//FTP siirrä tiedosto
					// upload a file
					$file=$d."/".$f;
					$remote_file=$remote_path.$target.$f;
					if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
					 echo "successfully uploaded ".$f."\n";
					$realfilename=substr($d."/".str_replace(" ","@",$f), strlen($start_dir));
					$rt_assoc[$realfilename]=time();
					$rt_array[]=$realfilename;
					$rt_array[]=time();
					$pushed[]=$realfilename;
					} else {
					 echo "There was a problem while uploading ".$file."\n";
					}								
				}
				else if(!files_are_equal($d."/".$f,$target_path)){
					$jotain=true;
					echo $f." has changed";
//					echo "FTP send file -> ".$f;
//					echo "FTP send file -> ".$remote_path.$target.$f."<br>";				
					//tiedosto on muuttunut
					//FTP siirrä tiedosto
					// upload a file
					$file=$d."/".$f;
					$remote_file=$remote_path.$target.$f;
					if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
					 echo " -> OK!\n";
					$rt_assoc[$f]=time();
					$pushed[]=$f;
					} else {
					 echo "There was a problem while uploading".$file."\n";
					}			
					
				}
			}
			}
		}
	}
		//onko tiedosto last_pushissa
		//onko tiedosto sama
		//jos ei: tarkista onko kansio olemassa, siirrä ftp:llä
		//jos on: älä siirrä
	//kopioi last_commit->last_push
	write_ftpcatapult($rt_array, $rt_assoc);
	if(!$list){
	if(ftp_put($conn_id, $remote_path."ftpcatap.ult", "files/send_ftpcatap.ult", FTP_ASCII)){
		echo "ftpcatap.ult->OK\n";
	}else echo "ftpcatap.ult->virhe\n";
	}else{
	if(ftp_put($conn_id, $remote_path."ftpcatap.ult", "files/ftpcatap.ult2", FTP_ASCII)){
		echo "ftpcatap.ult2->OK\n";
	}else echo "ftpcatap.ult2->virhe\n";

	}
	ftp_close($conn_id);
	//push();
	copy_dir2($local_path, $image_path);
	file_put_contents("files/lasts.ync", time());
	if(!$jotain){
		echo "Nothing happens.";
	}
	echo "Push done\n\n";
	echo "Conflict: ".json_encode($conflict)."\n";
	echo "Pushed: ".json_encode($pushed)."\n";
}
//$dir_list=array();
function get_assoc($rt_array){
	$rt_assoc=array();
	for($i=0;$i<count($rt_array);$i=$i+2){
		if($rt_array[$i]=="")continue;
		$rt_assoc[$rt_array[$i]]=$rt_array[$i+1];
	//	echo "rt_assoc: ".$rt_array[$i]."->".$rt_array[$i+1]."\n";
	}
	return $rt_assoc;
}
function read_ftpcatapult() {
	$rt_array=array();
	include("conf.php");
	$remote_times=file_get_contents("files/get_ftpcatap.ult");	
	$remote_times=str_replace("\r\n"," ",$remote_times);
	//echo $remote_times."\n";
	$rt_array=explode(" ", str_replace("\n","",$remote_times));
//	$rt_array=explode(" ", $remote_times);
	array_pop($rt_array);
	//echo json_encode($rt_array);
	return $rt_array;
}
function write_ftpcatapult($rt_array, $rt_assoc){
	include("conf.php");
	file_put_contents("files/send_ftpcatap.ult","");
	for($i=0;$i<count($rt_array);$i=$i+2){
		$file=$rt_array[$i];
		if($file=="")continue;
		if(strpos($rt_array[$i+1],'.')!==false){
			$rt_assoc[$file]=time();
		}	
	//	echo "write:".$file." ".$rt_assoc[$file]."\n";
//		file_put_contents("ftpcatapult/send_ftpcatap.ult", str_replace(" ","@",$file)." ".$rt_assoc[$file]." \n", FILE_APPEND);
		file_put_contents("files/send_ftpcatap.ult", $file." ".$rt_assoc[$file]." \n", FILE_APPEND);
	}
}
function check_conflict($rt_array){
	include("conf.php");
	//checkaa conflikti	
	$dl_array=array();
	$last_push=file_get_contents("files/lasts.ync");	

	for($i=1;$i<count($rt_array);$i=$i+2){
		if($rt_array[$i]=="")continue;
		if($rt_array[$i]>$last_push || strpos($rt_array[$i],'.')!==false){
			$filename=str_replace("@"," ",$rt_array[$i-1]);
	//		echo "\nConflict:".$filename."\n";
			$dl_array[]=$filename;
		}
//		else echo ".";
	}
	return $dl_array;
}


function find_dir($dir, $dir_list){
	$files=scandir($dir);
	//$directories=array();
	foreach ($files as $f){	
		if(is_dir($dir.$f)&& $f!="." && $f!=".."){
			if($f!="ftpcatapult" && $f!="lib"){
				$dir_list[]=$dir.$f;
				//echo $dir.$f."<br>";
				//echo json_encode($dir_list);
				$dir_list=find_dir($dir.$f."/",$dir_list);
			}
		}
	}
	return $dir_list;
}

function files_are_equal($a, $b)
{
  // Check if filesize is different
  if(filesize($a) !== filesize($b))
      return false;

  // Check if content is different
  $ah = fopen($a, 'rb');
  $bh = fopen($b, 'rb');

  $result = true;
  while(!feof($ah))
  {
    if(fread($ah, 8192) != fread($bh, 8192))
    {
      $result = false;
      break;
    }
  }

  fclose($ah);
  fclose($bh);

  return $result;
}
//substr($row['juttu'],0,4)
function echo_cmd($cmd){
	echo $cmd."\n";
	shell_exec($cmd);

}
function copy_dir2($start_dir, $target_dir){
	echo "\n";
	echo "cp -r ".$start_dir."* ".$target_dir."\n";
	shell_exec ("cp -r ".$start_dir."* ".$target_dir);

}
function copy_dir($start_dir, $target_dir){
	$dir_length=strlen($start_dir);
	$dir_list=array();
	$dir_list=find_dir($start_dir, $dir_list);
	$dir_list[]=substr($start_dir,0,$dir_length-1);
	//echo "Loppu: ".json_encode($dir_list)."<br>";
		echo substr($start_dir,$dir_length)."\n";
	foreach($dir_list as $d){
		$target=substr($d,$dir_length);
		//echo "Copying ".$d." to ".$target_dir.$target."<br>";
		if(!file_exists($target_dir.$target)){
//			shell_exec ( str_replace("/","\\","mkdir ".$target_dir.$target ));			
			echo 'shell_exec ( "mkdir '.$target_dir.$target.'" )\n';
			echo shell_exec ( "mkdir ".$target_dir.$target." && echo '!'");
		}
		//echo str_replace("/","\\", "copy ".$d."/*.* ".$target_dir.$target)." /Y";
//		shell_exec ( str_replace("/","\\", "copy ".$d."/*.* ".$target_dir.$target)." /Y" );		
		echo 'shell_exec ( "cp '.$d.'/* '.$target_dir.$target.'")\n';
		echo shell_exec ( "cp ".$d."/* ".$target_dir.$target." && echo '!'");
	}
	
}


?>
