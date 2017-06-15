<?php
 
use \GatewayWorker\Lib\Gateway;
use \Workerman\Worker;
use \Workerman\Autoloader;

global $mysqli;
$mysqli = new mysqli("localhost", "root", "hebeifangju", "wifi");
if(mysqli_connect_errno())
{
    echo mysqli_connect_error();
}
else{
    var_dump("chenggong");
}
/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{

    public static function onConnect($client_id)
    {
        //检查端口APP
      if($_SERVER['GATEWAY_PORT']=="8282")
      {
        var_dump("websocketchenggong");
        //给手机设备一个注册密钥


      }

    }    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message)
   {
      // self::$db->select('*')->from('infos')->query();
    global $mysqli;
    global $zhuceMIYAO;
    global $ws_worker;
    global $jiaoyan;

   if($_SERVER['GATEWAY_PORT']=="2348"){
    //判断设备发送的命令,设备注册
     $f=substr($message, 1, 1);
     $e=SetToHexString($f);
      $riqi=date("Y/m/d H:i:sa");
      var_dump($riqi);
     var_dump(SetToHexString($message));

    if($e=="01")
    {
        $ip=$_SERVER['REMOTE_ADDR'];
        var_dump($_SERVER['REMOTE_ADDR']);
        $port=$_SERVER['REMOTE_PORT'];
        $clients= $ip.':'.$port;
        var_dump($clients);
        //获得设备ID字段$date
        $getID=SetToHexString(substr($message, 10, 4));
        var_dump($getID);


        //从数据库中查询是否有这个设备
         $sql_isin="SELECT * FROM yonghu where shebeiID='$getID'";
         $insert_id = $mysqli->query($sql_isin);
         // 如果用户表存在就算了
         if ($insert_id->num_rows > 0) {
            $rows_sb= $insert_id->fetch_assoc();
            $zhuceMIYAO=$rows_sb["zhuceMIYAO"];
            $jiaoyan=$rows_sb["jiaoyan"];
            var_dump($zhuceMIYAO);
           Gateway::sendToCurrentClient("~"."q".$zhuceMIYAO.$jiaoyan."~");

          }
           //如果不存在就新建一个数据表并且插入到用户表中
          else{
         //将设备ID号随机生成一个32位数
          $zhuceMIYAO=getRandom("4");
          //将ID字段和生成的随机码存放到数组中
          //将32位注册码发送给设备
          $jiaoyan=jiaoyan($zhuceMIYAO);      
        
          $sql = "INSERT INTO yonghu (shebeiID,zhuceMIYAO,jiaoyan,IPP) VALUES($getID,'$zhuceMIYAO','$jiaoyan','$clients')";
          $insert_id = $mysqli->query($sql);
          //创建新的数据表
          // mysql_select_db("wifi",$mysqli);//选择数据库
           $sql_creat = "CREATE TABLE s".$getID."
          (
           riqi datetime,
          shebeiID varchar(16),
          szwd varchar(16),
          dqwd varchar(16),
          kgzt varchar(16),
          fazt varchar(16),
          baojing varchar(16),
          jrzt varchar(16)
          )default character set utf8"; 
          $creat=$mysqli->query($sql_creat);
          var_dump($sql_creat);
          $mysqli->close();
        //将ID和秘钥存贮到数据库中
        //  var_dump($getID);
        //  var_dump($zhuceMIYAO);
        //  var_dump(jiaoyan($zhuceMIYAO));
         Gateway::sendToCurrentClient("~"."q".$zhuceMIYAO.$jiaoyan."~");

    }

   }
         
    //设备发送信息命令
    if($e=="02")
    {
        // $getID=SetToHexString(substr($message, 10, 4));
        $a= SetToHexString($message);
        var_dump("02");
        $getdate=array();
        for ($i=0; $i <strlen($a) ; $i++) { 
            $getdate[$i]=substr($a, $i*2, 2);
        }
        $shezhiwd=hexdec($getdate[7]);  
        $wd=hexdec($getdate[8]);
        $jrzt=hexdec($getdate[9]);
        $kgzt=hexdec($getdate[10]);
        $baojing=hexdec($getdate[11]);
        $fazt=hexdec($getdate[12]);

        //查询用户数据表中的密钥
        $zhuceMIYAO=substr($message, 2, 4);
        var_dump($zhuceMIYAO);
        $sql_miyao = "SELECT shebeiID from yonghu where zhuceMIYAO='$zhuceMIYAO'";
        $miyao = $mysqli->query($sql_miyao); 
        $row_miyao = $miyao->fetch_assoc();
        $sid=$row_miyao["shebeiID"];
        var_dump("miyao"); 
        var_dump($row_miyao["shebeiID"]);       

        //将信息存储到数据库中
        $riqi=date("Y/m/d H:i:sa");

        $sql = "INSERT INTO s".$row_miyao["shebeiID"]." (dqwd,jrzt,shebeiID,kgzt,baojing,fazt,szwd,riqi) VALUES($wd,$jrzt,$sid,$kgzt,$baojing,$fazt,$shezhiwd,'$riqi')";
        $insert_id = $mysqli->query($sql);
        // $jiaoyan=dangwei_four($zhuceMIYAO);
        // // Gateway::sendToAll("~"."s".$zhuceMIYAO."\x02"."\x02\x03".$jiaoyan."~");
        // var_dump($zhuceMIYAO);

    }

   }
      if($_SERVER['GATEWAY_PORT']=="8282"){
        var_dump($message);
        //随机生成一个手机密钥
        // global  $sjmiyao;
        // $sjmiyao=getRandom("4");
        // var_dump($sjmiyao);
        // Gateway::sendToCurrentClient("my".$sjmiyao);

        $ml=substr($message, 2,2);
        var_dump($ml);
        $sjid=substr($message, 4,8);
        var_dump("shoujiwd");
        var_dump($sjid);
        if($ml=="ms"){
           // $dqriqi=date("Y/m/d h:i:sa");
            if(substr($message, 4,6)=="lixian")
            {
              Gateway::sendToCurrentClient(0);
            
            var_dump(substr($message, 4,6));
            }
            else{
            $sql_wendu="SELECT * FROM s".$sjid."  order by riqi desc limit 1";
            $result_wd = $mysqli->query($sql_wendu);
            $rowwd = $result_wd->fetch_assoc();
            $nowwd=$rowwd["dqwd"];
            if($nowwd==null) $nowwd=0;
            var_dump($nowwd);
             // var_dump($rowwd["szwd"]);
             Gateway::sendToCurrentClient($nowwd);
             }
        }
        if($ml=="sz"){
           // $dqriqi=date("Y/m/d h:i:sa");
            $sql_wendu="SELECT * FROM s".$sjid."  order by riqi desc limit 1";
            $result_wd = $mysqli->query($sql_wendu);
               $rowwd = $result_wd->fetch_assoc();
               $szwd=$rowwd["szwd"];
              var_dump($szwd);
             Gateway::sendToCurrentClient($szwd);
        }
        if($ml=="kg"){
           // $dqriqi=date("Y/m/d h:i:sa");
            $sql_kg="SELECT * FROM s".$sjid."   order by riqi desc limit 1";
            $result_kg = $mysqli->query($sql_kg);
               $rowwd = $result_kg->fetch_assoc();
               $kgzt=$rowwd["kgzt"];
              var_dump($kgzt);
             Gateway::sendToCurrentClient($kgzt);
        }
        if($ml=="ct"){
         static $i=0;
          //从数据库中获得注册密钥
         $sql_sj="SELECT * FROM yonghu where shebeiID='$sjid'";
         $sjid = $mysqli->query($sql_sj);
         $rows_sj= $sjid->fetch_assoc();
         $zhuceMIYAO=$rows_sj["zhuceMIYAO"];
         $jiaoyan=$rows_sj["jiaoyan"];
         var_dump("jiaoyan");
         var_dump($jiaoyan);
         var_dump($zhuceMIYAO);
         // $jiaoyan=jiaoyan($zhuceMIYAO);
        //$mysqli->close();
        //参数地址
        $WENDU="\x01";  //设置温度
        $GONGLV="\x02"; //功率档位
        $KAIGUAN="\x03"; //开关状态
        $FANGWU="\x04"; //房屋模式
        //参数值
        $YIDANG="\x00"; //1档
        $ERDANG="\x01"; //2档
        $SANDANG="\x02";//3档
        $SIDANG="\x03"; //4档

        $OPEN="\x01";   //OPEN
        $CLOSE="\x00";  //CLOSE

        $DAN="\x00";    //单屋
        $DUO="\x01";    //多屋

         //开关校验
          if($i%2==0){
          //发送开机命令
           $jiaoyan=jiaoyan_kai($zhuceMIYAO);
           var_dump("kai");
         var_dump($jiaoyan);
           Gateway::sendToAll("~"."s".$zhuceMIYAO."\x02"."\x03\x01".$jiaoyan."~");
           var_dump($i);
           $i++;
          }
          else{
           // //发送关机命令
           $jiaoyan=jiaoyan_guan($zhuceMIYAO);
            var_dump("guan");
          var_dump($jiaoyan);
           Gateway::sendToAll("~"."s".$zhuceMIYAO."\x02"."\x03\x00".$jiaoyan."~");
           var_dump($i);
           $i++;
          }


        //Gateway::sendToAll("~"."s".$zhuceMIYAO."\x02"."\x02\x03".$jiaoyan."~");
          // $jiaoyan=dangwei_four($zhuceMIYAO);
          // Gateway::sendToAll("~"."s".$zhuceMIYAO."\x02"."\x02\x03".$jiaoyan."~");

        }
      if($ml=="yi"){
          //从数据库中获得注册密钥
         $sql_sj="SELECT * FROM yonghu where shebeiID='$sjid'";
         $sjid = $mysqli->query($sql_sj);
         $rows_sj= $sjid->fetch_assoc();
         $zhuceMIYAO=$rows_sj["zhuceMIYAO"];
         $jiaoyan=$rows_sj["jiaoyan"];
         $jiaoyan=dangwei_one($zhuceMIYAO);
         var_dump("yi");
         var_dump($jiaoyan);
         Gateway::sendToAll("~"."s".$zhuceMIYAO."\x02"."\x02\x00".$jiaoyan."~");
         var_dump("dangwei");
         var_dump("~"."s".$zhuceMIYAO.$jiaoyan."~");

        }
      if($ml=="er"){
         static $i=0;
          //从数据库中获得注册密钥
         $sql_sj="SELECT * FROM yonghu where shebeiID='$sjid'";
         $sjid = $mysqli->query($sql_sj);
         $rows_sj= $sjid->fetch_assoc();
         $zhuceMIYAO=$rows_sj["zhuceMIYAO"];
         $jiaoyan=$rows_sj["jiaoyan"];
         $jiaoyan=dangwei_two($zhuceMIYAO);
         var_dump("er");
         var_dump($jiaoyan);
         Gateway::sendToAll("~"."s".$zhuceMIYAO."\x02"."\x02\x01".$jiaoyan."~");


        }
      if($ml=="sa"){
         static $i=0;
          //从数据库中获得注册密钥
         $sql_sj="SELECT * FROM yonghu where shebeiID='$sjid'";
         $sjid = $mysqli->query($sql_sj);
         $rows_sj= $sjid->fetch_assoc();
         $zhuceMIYAO=$rows_sj["zhuceMIYAO"];
         $jiaoyan=$rows_sj["jiaoyan"];
         $jiaoyan=dangwei_three($zhuceMIYAO);
         var_dump("san");
         var_dump($jiaoyan);
         Gateway::sendToAll("~"."s".$zhuceMIYAO."\x02"."\x02\x02".$jiaoyan."~");


        }
      if($ml=="si"){
         static $i=0;
          //从数据库中获得注册密钥
         $sql_sj="SELECT * FROM yonghu where shebeiID='$sjid'";
         $sjid = $mysqli->query($sql_sj);
         $rows_sj= $sjid->fetch_assoc();
         $zhuceMIYAO=$rows_sj["zhuceMIYAO"];
         $jiaoyan=$rows_sj["jiaoyan"];
         $jiaoyan=dangwei_four($zhuceMIYAO);
         var_dump("si");
         var_dump($jiaoyan);
         Gateway::sendToAll("~"."s".$zhuceMIYAO."\x02"."\x02\x03".$jiaoyan."~");


        }
      if($ml=="wd"){
         static $i=0;
          //从数据库中获得注册密钥
         $sql_sj="SELECT * FROM yonghu where shebeiID='$sjid'";
         $sjid = $mysqli->query($sql_sj);
         $rows_sj= $sjid->fetch_assoc();
         $zhuceMIYAO=$rows_sj["zhuceMIYAO"];
         $jiaoyan=$rows_sj["jiaoyan"];
         var_dump("jiaoyan");
         var_dump($jiaoyan);
         var_dump($zhuceMIYAO);         
         $wendu=substr($message, 12,2);
          // $wd=stripcslashes(preg_replace('/[A-Fa-f0-9]{2}/', '\\x$0' , $wendu));
         $wd=SetToHexString($wendu); //解码1，php>=5.4
         // $wd*=1.6;
         var_dump("wendushi");
         var_dump($wendu);
         var_dump($wd);

         $jiaoyan=set_wendu($zhuceMIYAO,$wd);
         var_dump($jiaoyan);
         Gateway::sendToAll("~"."s".$zhuceMIYAO."\x02"."\x01".$wendu.$jiaoyan."~");


        }


    }
   }

   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id)
   {
       // 向所有人发送 
       GateWay::sendToAll("$client_id logout\r\n");
   }




}
// 向所有验证的用户推送数据
function broadcast($message)
{
   global $ws_worker;
   foreach($ws_worker->uidConnections as $connection)
   {
        echo '1/';
        $connection->send($message);
   }
}
// 针对uid推送数据
function sendMessageByUid($uid, $message)
{
    global $ws_worker;
    if(isset($ws_worker->uidConnections[$uid]))
    {
        $connection = $ws_worker->uidConnections[$uid];
        $connection->send($message);
    }
}
//16进制转换字符串
function SingleDecToHex($dec)
{
    $tmp="";
    $dec=$dec%16;
    if($dec<10)
        return $tmp.$dec;
    $arr=array("a","b","c","d","e","f");
    return $tmp.$arr[$dec-10];
}
function SingleHexToDec($hex)
{
    $v=ord($hex);
    if(47<$v&$v<58)
        return $v-48;
    if(96<$v&$v<103)
        return $v-87;
}
function SetToHexString($str)
{
    if(!$str)return false;
    $tmp="";
    for($i=0;$i<strlen($str);$i++)
    {
        $ord=ord($str[$i]);
        $tmp.=SingleDecToHex(($ord-$ord%16)/16);
        $tmp.=SingleDecToHex($ord%16);
    }
    return $tmp;
}
function UnsetFromHexString($str)
{
    if(!$str)return false;
    $tmp="";
    for($i=0;$i<strlen($str);$i+=2)
    {
        $tmp.=chr(SingleHexToDec(substr($str,$i,1))*16+SingleHexToDec(substr($str,

$i+1,1)));
    }
    return $tmp;
}
//随机生成32位数用于注册秘钥
function getRandom($param){
    $str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $key = "";
    for($i=0;$i<$param;$i++)
     {
         $key .= $str{mt_rand(0,32)};    //生成php随机数
     }
     return $key;
 }
//异或校验
 function jiaoyan($num)
 {
    $tmp='q';
    //$mingling="7e81"."$num".$tmp."7e";
   for($i=0;$i<strlen($num);$i++){
    $tmp=$tmp ^ substr($num,$i,1);
   }    
   return $tmp;
 }
 //异或校验
 function jiaoyan_kai($num)
 {
    $tmp='s';
    //$mingling="7e81"."$num".$tmp."7e";
   for($i=0;$i<strlen($num);$i++){
    $tmp=$tmp ^ substr($num,$i,1);
   }  
   $tmp=$tmp^"\x02";
   $tmp=$tmp^"\x03";  

   $tmp=$tmp^"\x01";  

   return $tmp;
 }
  function jiaoyan_guan($num)
 {
    $tmp='s';
    //$mingling="7e81"."$num".$tmp."7e";
   for($i=0;$i<strlen($num);$i++){
    $tmp=$tmp ^ substr($num,$i,1);
   }  
   $tmp=$tmp^"\x02";
   $tmp=$tmp^"\x03";  

   $tmp=$tmp^"\x00";  

   return $tmp;
 }
    function dangwei_one($num)
 {
    $tmp='s';
    //$mingling="7e81"."$num".$tmp."7e";
   for($i=0;$i<strlen($num);$i++){
    $tmp=$tmp ^ substr($num,$i,1);
   }  
   $tmp=$tmp^"\x02";
   $tmp=$tmp^"\x02";  

   $tmp=$tmp^"\x00";  

   return $tmp;
 }
    function dangwei_two($num)
 {
    $tmp='s';
    //$mingling="7e81"."$num".$tmp."7e";
   for($i=0;$i<strlen($num);$i++){
    $tmp=$tmp ^ substr($num,$i,1);
   }  
   $tmp=$tmp^"\x02";
   $tmp=$tmp^"\x02";  

   $tmp=$tmp^"\x01";  

   return $tmp;
 }
    function dangwei_three($num)
 {
    $tmp='s';
    //$mingling="7e81"."$num".$tmp."7e";
   for($i=0;$i<strlen($num);$i++){
    $tmp=$tmp ^ substr($num,$i,1);
   }  
   $tmp=$tmp^"\x02";
   $tmp=$tmp^"\x02";  

   $tmp=$tmp^"\x02";  

   return $tmp;
 }
   function dangwei_four($num)
 {
    $tmp='s';
    //$mingling="7e81"."$num".$tmp."7e";
   for($i=0;$i<strlen($num);$i++){
    $tmp=$tmp ^ substr($num,$i,1);
   }  
   $tmp=$tmp^"\x02";
   $tmp=$tmp^"\x02";  

   $tmp=$tmp^"\x03";  

   return $tmp;
 }
    function set_wendu($num,$wd)
 {
    $tmp='s';
    //$mingling="7e81"."$num".$tmp."7e";
   for($i=0;$i<strlen($num);$i++){
    $tmp=$tmp ^ substr($num,$i,1);
   }
   // $tmp=$tmp^"\x02";
 $tmp=$tmp^"\x02";
   $tmp=$tmp^"\x01";
   // $pt=stripcslashes(preg_replace('/[A-Fa-f0-9]{2}/', '\\x$0' , $wendu));
  // $pt=hex2bin($wd); //解码1，php>=5.4

   // $tmp=$tmp^$wd; 
      for($i=0;$i<strlen($wd);$i++){
    $tmp=$tmp ^ substr($wd,$i,1);
   } 

   return $tmp;
 }

