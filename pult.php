<?php

include("conf.php");
$commit_path="commit/";
$image_path="image/";

	set_time_limit(0);
//$cmd=$_POST['cmd'];
//var_dump($argv);
$cmd=$argv[1];
echo $cmd."\n";
//echo "<asdf style='font-size:30px;'>".substr(time(), 7)."</asdf><br>";
$delete=false;
$commit=false;
$anyway=false;
$push=false;
$track=false;
$pull=false;
$init=false;
$test=false;
$know=false;
$dry=false;    //vanhentunut komento ?????
$list=false;
$copy=false;
$file=false;
$file_push=false;
$file_push_name=false;
$file_pull=false;
$file_pull_name=false;
$brave=false;
$verbose=false;
$force=false;
$check=false;
$local=false;
$cat=false;
$send=false;
$get=false;
$last=false;
$filelist=false;
$i=0;
for($i=0;$i<count($argv);$i++){
	echo $i.": ".$argv[$i]."\n";
	if($argv[$i]=="delete" && isset($argv[$i+1])){
		$file_delete_name=$argv[$i+1];	
		$delete=true;
	}
	if($argv[$i]=="get")
		$get=true;
	if($argv[$i]=="track")
		$track=true;
	if($argv[$i]=="last")
		$last=true;
	if($argv[$i]=="test")
		$test=true;
	if($argv[$i]=="send")
		$send=true;
	if($argv[$i]=="copy")
		$copy=true;
	if($argv[$i]=="local")
		$local=true;
	if($argv[$i]=="dry")
		$dry=true;
	if($argv[$i]=="verbose")
		$verbose=true;
	if($argv[$i]=="force")
		$force=true;
	if($argv[$i]=="anyway")
		$anyway=true;
	if($argv[$i]=="brave")
		$brave=true;
	if($argv[$i]=="filelist" && isset($argv[$i+1])){
			$filelist_name=$argv[$i+1];	
			$filelist=true;
		}
	if($argv[$i]=="list")
		$list=true;
	if($argv[$i]=="file" && isset($argv[$i+1])){
			$file_push_name=$argv[$i+1];	
			$file_push=true;
	}
	if($argv[$i]=="file" && isset($argv[$i+1])){
			$file_pull_name=$argv[$i+1];	
			$file_pull=true;
	}
}
if($filelist && $file_pull){
	echo "filelist and file arguments are not compatible.\n";
	exit();
}
if($cmd=="commit") $commit=true;
if($cmd=="push") $push=true;
if($cmd=="fast") {$commit=true;$push=true;}
if($cmd=="pull") $pull=true;
if($cmd=="init") $init=true;
if($cmd=="uptodate") $know=true;
if($cmd=="cat") $cat=true;
if($cmd=="check") $check=true;
if(count($argv)==1){
	echo "init\n";
	echo "delete [FILE]\n";
	echo "push\n";
	echo "push list\n";
	echo "push anyway\n";
	echo "push file [FILE] (You must create folders manually)\n";
	echo "push dry   vanhentunut komento ?????\n";
	echo "push track\n";
	echo "push test    toimii\n";
	echo "pull\n";
	echo "pull brave\n";
	echo "pull local\n";
	echo "pull force\n";
	echo "pull file [FILE] (You must create folders manually)\n";
	echo "pull filelist [DIR] (List files in a directory in server)\n";
	echo "pull dry    vanhentunut komento ????\n";
	echo "pull test    toimii\n";
	echo "uptodate\n";
	echo "copy\n";
	echo "cat get\n";
	echo "cat send\n";
	echo "cat last\n";
	}
if($copy){
	$copydir=date('dmyHis');
	echo_cmd("mkdir copy/".$copydir);
	echo_cmd("cp -r ".$local_path."* copy/".$copydir."/");
	exit();
}
if($cat){
	if($send){
		echo file_get_contents("files/send_ftpcatap.ult");
	}
	if($get){
		echo file_get_contents("files/get_ftpcatap.ult");
	}
	if($last){
		echo file_get_contents("files/lasts.ync")."\n";
	}
	exit();
}
if($know){
	file_put_contents("files/lasts.ync", time());
}
if($init){
	if($local_path==""){
		echo "Error: local_path in conf.php is not set.\n";
		exit();
	}
	echo_cmd("mkdir files");
	echo_cmd("mkdir image");
	echo_cmd("mkdir copy");
	echo_cmd("cp -r ".$local_path."* image/"); 
	file_put_contents("files/lasts.ync", time());
}
if($commit){
	echo "DEPRECATED";
	exit();
}
if($check){
	$rt_array=read_ftpcatapult();
	$rt_assoc=get_assoc($rt_array);
	echo "Files in ftpcatap.ult: ".count($rt_assoc)."\n";
	for($i=0;$i<count($rt_array);$i=$i+2){
		$file=$rt_array[$i];
		if(is_dir($local_path.$file))continue;
		if(!file_exists($local_path.$file)) echo $file." does not exist locally.\n";
	}
	exit();

}
if($delete){
	// set up basic connection
	try{	
	$conn_id = ftp_connect($ftp_server);
	}
	catch(Exception $e){
		echo "Ftp connect->Error\n";
		exit();
	}
	// login with username and password
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	if($login_result){}else{
		echo "Failed to login to ".$ftp_server.".\n";
		exit();
	}
	if($file_delete_name){
		$remote_file=$remote_path.$file_delete_name;
		$local_file=$local_path.$file_delete_name;
		echo "FTP delete file ".$remote_file."";
		if (ftp_delete($conn_id, $remote_file)) {
			echo "->OK\n";
		}
	} else {
		echo "ei file-delete-namea.\n";
		exit();
	}
	exit();
}
if($pull){
	if($local){
		echo_cmd("cp -r image/"."* ".$local_path);
		exit();
	}
	// set up basic connection
	try{	
	$conn_id = ftp_connect($ftp_server);
	}
	catch(Exception $e){
		echo "Ftp connect->Error\n";
		exit();
	}
	// login with username and password
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	if($login_result){}else{
		echo "Failed to login to ".$ftp_server.".\n";
		exit();
	}
if(!$dry)
	if(ftp_get($conn_id, "files/get_ftpcatap.ult", $remote_path."ftpcatap.ult", FTP_ASCII)) {
		echo "ftpcatap.ult<-OK\n";
	} else echo "ftpcatap.ult<-virhe\n";
	
	$rt_array=read_ftpcatapult();
	$rt_assoc=get_assoc($rt_array);
	echo "Files in ftpcatap.ult: ".count($rt_assoc)."\n";
	if(!$file_pull)
		$conflict=check_conflict($rt_array, $force);	
	else{
		$conflict=array();
		$conflict[]=$file_pull_name;	
	}
	if($test){
		echo "Test: ".json_encode($conflict)."\n";
		exit();
	}
	if($filelist){
		$conflict=array();
		$list1=ftp_nlist($conn_id, $filelist_name);
		foreach($list1 as $f){
			echo $f."\n";
		}
		exit();
	}
	$errors=array();
	file_put_contents("files/pull_errors.txt", "");
	if(isset($conflict[0])){
		for($i=0;$i<count($conflict);$i++){
			if(substr($conflict[$i], -1)=="/"){
				shell_exec("mkdir ".$image_path.$conflict[$i]);
			if($brave)echo_cmd("mkdir ".$local_path.$conflict[$i]);
			}
			else{
				//echo $remote_path.$conflict[$i]."\n";
				 if(ftp_get($conn_id, $image_path.$conflict[$i], $remote_path.$conflict[$i], FTP_ASCII)){
					echo "get ".$conflict[$i].": OK\n";
				}else{
					echo "get ".$conflict[$i].": ERROR\n";
					$errors[]=$conflict[$i];
					file_put_contents("files/pull_errors.txt", $conflict[$i]." \n", FILE_APPEND);
					
				} 
				if($brave)echo_cmd("cp ".$image_path.$conflict[$i]." ".$local_path.$conflict[$i]);
			}
			$MIL=1000*1000;
			$microsec=0.5*$MIL;
			usleep($microsec);
		}
	}else echo "Everything was up to date.";

	file_put_contents("files/lasts.ync", time());

	//write_ftpcatapult($rt_array, $rt_assoc);

	//ftp_put($conn_id, $remote_path."ftpcatap.ult", "files/send_ftpcatap.ult", FTP_ASCII);
	ftp_close($conn_id);
	echo "\nPulled: ".json_encode($conflict)."\n";
	echo "Error: ".json_encode($errors)."\n";
}
if($push){
	$untrackedfiles=0;
	$pushed=array();
	//hae dirlist last_commitista
	$jotain=false;
	$start_dir=$local_path;
	$target_dir=$image_path;
	$dir_length=strlen($start_dir);
	$dir_list=array();
	$dir_list[]=substr($start_dir,0,$dir_length-1);
	$dir_list=find_dir($start_dir, $dir_list);
	
	$file = '';
	$remote_file = '';
	// set up basic connection
	try{	
	$conn_id = ftp_connect($ftp_server);
	}
	catch(Exception $e){
		echo "Ftp connect->Error\n";
		exit();
	}
	// login with username and password
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	if($login_result){}else{
		echo "Failed to login to ".$ftp_server.".\n";
		exit();
	}
if(!$dry){
	if(ftp_get($conn_id, "files/get_ftpcatap.ult", $remote_path."ftpcatap.ult", FTP_ASCII)) {
		echo "ftpcatap.ult<-OK\n";
	} else {
		echo "ftpcatap.ult<-error\n";
		exit();
	}
	}else echo "ftpcatap.ult<-dry\n";
	$rt_array=read_ftpcatapult();
	$rt_assoc=get_assoc($rt_array);
	$conflict=check_conflict($rt_array, false);	
	if(isset($conflict[0])){
		echo "Check conflicts->\n";
		for($i=0;$i<count($conflict);$i++){
			echo "Conflict: ".$conflict[$i]."\n";
		}
		if(!$anyway)exit();
	}	else echo "Check conflicts->OK\n";
	//käy läpi joka tiedosto joka kansiossa
	if($file_push){
		$remote_file=$remote_path.$file_push_name;
		$local_file=$local_path.$file_push_name;
		echo "FTP send file ".$local_file." -> ".$remote_file."\n";
		if (ftp_put($conn_id, $remote_file, $local_file, FTP_ASCII)) {
			if(isset($rt_assoc[$file_push_name])){
				$rt_assoc[$file_push_name]=time();
				write_ftpcatapult($rt_array, $rt_assoc);
				if(ftp_put($conn_id, $remote_path."ftpcatap.ult", "files/send_ftpcatap.ult", FTP_ASCII)){
					echo "ftpcatap.ult->OK\n";
				}else echo "ftpcatap.ult->virhe\n";
			}
			echo_cmd("cp ".$local_file." ".$image_path.$file_push_name);
		}else echo "Error in ftp_put\n";
		exit();
	}
			file_put_contents("files/ftpcatap.ult2", "");
	$test_push=array();
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
				//echo "löytyi kansio: $f\n";
				if(!file_exists($target_path)){
					$jotain=true;
					echo "FTP create directory ".$remote_path.$target.$f."/"."\n";
					//kansiota ei ole
					//FTP tee kansio
					$newdir=$remote_path.$target.$f."/";
					if($test){
						$test_push[]=$target.$f."/";
					}
					else if (ftp_mkdir($conn_id, $newdir)) {
					 echo "successfully created ".$newdir."\n";
					$realfilename=substr($d."/".str_replace(" ","@",$f."/"), strlen($start_dir));
					$rt_assoc[$realfilename]=time();
					$rt_array[]=$realfilename;
					$rt_array[]=time();
					$pushed[]=$realfilename;
					} else {
					 echo "There was a problem while creating ".$newdir."\n";
					}
					$MIL=1000*1000;
					$microsec=0.5*$MIL;
					usleep($microsec);
				}
			}
			//mitä tehdään jokaiselle tiedostolle
			else if(!is_dir($d."/".$f)&& $f!="." && $f!=".."){
				//echo "löytyi tiedosto: $f\n";
				if(!file_exists($target_path)){
					$jotain=true;
					echo $d."/".$f." is new.\n";
					echo "FTP send file -> ".$remote_path.$target.$f."\n";
					//tiedostoa ei ole					
					//FTP siirrä tiedosto
					// upload a file
					$file=$d."/".$f;
					$remote_file=$remote_path.$target.$f;
					if($test){
						$test_push[]=$target.$f;
					}
					else if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
			///***
			if(!$all_cp)
			echo_cmd("cp ".$file." ".$image_path.$target.$f);
					 echo "successfully uploaded ".$f."\n";
					$realfilename=substr($d."/".str_replace(" ","@",$f), strlen($start_dir));
					$rt_assoc[$realfilename]=time();
					$rt_array[]=$realfilename;
					$rt_array[]=time();
					$pushed[]=$realfilename;
					} else {
					 echo "There was a problem while uploading ".$file."\n";
					}								
					$MIL=1000*1000;
					$microsec=0.5*$MIL;
					usleep($microsec);
					
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
					if($test){
						$test_push[]=$target.$f;
					}
					else if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
			///***
			if(!$all_cp)
			echo_cmd("cp ".$file." ".$image_path.$target.$f);
					 echo " -> OK!\n";
					$rt_assoc[$f]=time();
					$pushed[]=$f;
					} else {
					 echo "There was a problem while uploading".$file."\n";
					}			
					$MIL=1000*1000;
					$microsec=0.5*$MIL;
					usleep($microsec);
					
				}
				//file_put_contents("files/ftpcatap.ult2", substr($d."/".str_replace(" ","@",$f)."/ ".time()." \n", strlen($start_dir)), FILE_APPEND);
				//tiedosto ei ole muuttunut.
				else if(!isset($rt_assoc[substr($d."/".str_replace(" ","@",$f), strlen($start_dir))])){
					$untrackedfiles=$untrackedfiles+1;
					if($verbose)
					echo $d."/".$f." is not in the file list.\n";	
					if($track){
						$realfilename=substr($d."/".str_replace(" ","@",$f), strlen($start_dir));
						$rt_assoc[$realfilename]=time();
						$rt_array[]=$realfilename;
						$rt_array[]=time();
						echo "->track\n";
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
	if((isset($pushed[0]) || $track || $dry) && !$test){
	echo "Remote path:".$remote_path."\n";
	if(ftp_put($conn_id, $remote_path."ftpcatap.ult", "files/send_ftpcatap.ult", FTP_ASCII)){
		echo "ftpcatap.ult->OK\n";
	}else echo "ftpcatap.ult->virhe\n";
	}else if($list){
	echo "Remote path:".$remote_path."\n";
	if(ftp_put($conn_id, $remote_path."ftpcatap.ult", "files/ftpcatap.ult2", FTP_ASCII)){
		echo "ftpcatap.ult2->OK\n";
	}else echo "ftpcatap.ult2->virhe\n";

	}else echo "ftpcatap.ult<-no";
	ftp_close($conn_id);
	//push();
	if(!$list && !$test){
		if($all_cp)
		copy_dir2($local_path, $image_path);
		file_put_contents("files/lasts.ync", time());
	}
	if(!$jotain){
		echo "Nothing happens.";
	}
	echo "Push done\n\n";
	echo "Conflict: ".json_encode($conflict)."\n";
	echo "Pushed: ".json_encode($pushed)."\n";
	if($test)
	echo "Test: ".json_encode($test_push)."\n";
	if($untrackedfiles>0)
	echo "Untracked: ".$untrackedfiles."\n";
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
function check_conflict($rt_array, $force){
	include("conf.php");
	//checkaa conflikti	
	$dl_array=array();
	if(file_exists("files/lasts.ync"))
	$last_push=file_get_contents("files/lasts.ync");	
	else $last_push="1";
	if($force)$last_push="1";
	

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
			if($f!="ftpcatapult"){
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
