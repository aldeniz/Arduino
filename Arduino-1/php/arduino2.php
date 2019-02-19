<?php
header('Content-type: text/plain; Charset=UTF-8'); 
$terminal=0;

$port="com8";

if (defined('STDIN')) {
  $terminal=1; $_REQUEST["DB"]=0; $_REQUEST["comm"]=1;
  $_REQUEST["temp"]=21; $_REQUEST["count"]=1;
  for ($i=1; $i<count($argv); $i++)   {

    $arg = $argv[$i];
    if ( stristr($arg,"comm=")){
      $comm=str_replace('comm=','', $arg);
      $_REQUEST["comm"]=$comm;
    }   else if ( stristr($arg,"temp="))  {
      $temp=str_replace('temp=','', $arg);
      $_REQUEST["temp"]=$temp;

    }   else if (stristr($arg,"db="))   {
      $db=str_replace('db=','', $arg);
      $_REQUEST["DB"]=$db;

    }   else if (stristr($arg,"count="))   {
      $count=str_replace('count=','', $arg);
      $_REQUEST["count"]=$count;
    }   else if (stristr($arg,"port="))   {
      $port=str_replace('port=','', $arg);
      $_REQUEST["port"]=$port;
    }                  
  }
}

$array = array(); $out="";

if (@$_REQUEST["DB"]==1)
  $conn = @sqlsrv_connect( "RASHIDOV10\SQLDEVELOP",  array("UID" => "ibs", 
    "PWD" => "123456", "Database"=>"ARDUINO","CharacterSet" => "UTF-8"));

$count=1; // брой измервания  ако не е зададено от POST[]

@shell_exec("mode $port: baud=9600 parity=n data=8 stop=1 xon=off");
//shell_exec("SetCommTimeouts $port");
$fd = @dio_open("$port:", O_RDWR  );
//usleep(400000);

if (!is_null($_REQUEST["comm"]) ) {
  $ctrl= $_REQUEST["comm"];

  switch  ($ctrl) { 

    case 3:  {
      $temp= $_REQUEST["temp"];
      // Задаване на операция 3 - задаване на стартова температура
      dio_write($fd,chr($ctrl).chr($temp));
      $a1="Задаване на прагова t=".$temp."°C \n";
      if ($terminal==1) $out.=$a1; 
      $array[] =array($a1);
      //$array[] =array("$temp");
      break;
    }    

    case 2: 
      // Задаване на операция 2 - стоп
      dio_write($fd,chr($ctrl));
      $a1="Спиране на измерванията\n";
      $a2="L: -1\n";
      if ($terminal==1) {
        $out.=$a1.$a2;
      } 

      $array[] =array($a1);
      $array[] =array($a2);

      break;    
    case 32:  // чете данни + чете прагова температура
    case 31:  // задава прагова температура + чете данни
    case 1:  
      if (!is_null($_REQUEST["count"]) ) $count= $_REQUEST["count"]; 
      // Задаване на операция 1 - четене на данни

      if ($ctrl==31) { 
        $temp= $_REQUEST["temp"]; 
        dio_write($fd,chr($ctrl).chr($temp).chr($count));
        $a1="Задаване на прагова t=".$temp."°C \n";
        if ($terminal==1) $out.=$a1; 
        $array[] =array($a1);                
      } 
      else  dio_write($fd,chr($ctrl).chr($count));

      $br=$count;//*3;  // четене на два реда
      $i=0; $buffer="";

      while ($i<$br) {

        $data=""; $error=0; $buffer="";
        while (!($data=="\n" || ($error==1) )) {

          $data = dio_read($fd,1);

          if ($data!="") {
            //echo $data;
            $buffer.=$data;
          }
          else {
            //usleep(500000); 
            //$buffer.="Err\n"; 
            $error=1; 
            //$array[] =array("Err\n");
            break; 
          }
        }

        // В случай, че е разрешено записване на данните в БД
        if (@$_REQUEST["DB"]==1) {
          $vl=""; $te="";
          $pos = strpos($buffer, "V:");
          if ($pos!==false) $vl =substr($buffer, $pos+3, 5);
          $pos = strpos($buffer, "T:"); 
          if ($pos!==false) $te =substr($buffer, $pos+3, 5);

          if ( $conn && ($vl!="")) {
            $query="insert into arduino values (getdate(), $vl, $te)";
            $result=@sqlsrv_query($conn,$query);
          }
        }

        if ($terminal==1) $out.=$buffer; 
        $array[] =array($buffer);
        $i++;
      }
      break;
    default: dio_write($fd,chr($ctrl)); break;
  }
}    

dio_close( $fd );
if ($terminal==1) { 
  shell_exec('chcp 866'); 
  echo  iconv("utf-8", "cp866", $out);
} else
  echo json_encode( $array ); 


?>