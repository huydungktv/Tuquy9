<?php 
//include_once '../class/Config.php';
//include_once '../class/Connection.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');

$name = $_POST['uid'];
$betmoney = $_POST['betmoney'];
$bettype = $_POST['bettype'];

$name = tokenDecode($name);



$mt = explode(' ', microtime());
$milliseconds = $mt[0]*1000000;

$res = array();
$res['uid'] = $name;
$res['betmoney'] = $_POST['betmoney'];
$res['bettype'] = $_POST['bettype'];
$res['status'] = 0;
$res['time'] = date("YmdHis").$milliseconds;

if (count ($_POST) != 3 || $name == '' || $betmoney == '' || $bettype == '' || $betmoney < 100 || ($bettype != 'realmoney' && $bettype != 'virtualmoney') ) {
    $res['info'] = 'Invalid parameters';
}else{
    $sql = "SELECT * FROM player where username='$name'";
    $rs = mysql_query($sql);
    $row = mysql_fetch_array($rs);        
 
    if (!$row) {
        $res['exits'] = 0;
    }else{
        $res['exits'] = 1;
        if ($bettype == 'realmoney') {            
            if ($row['realmoney'] - $betmoney >= 0) {
                $sql = "UPDATE player SET realmoney = realmoney - $betmoney WHERE username='$name'";
                $result = mysql_query($sql);                
                $res['status'] = 1;
                $res['remainmoney'] = $row['realmoney'] - $betmoney;
                
                //history
                $sqlh = "INSERT INTO bethistory (username, gameid, betmoney, bettype, remainmoney, time, status) VALUES (
                '$name','minipoker',$betmoney,'realmoney','".$res['remainmoney']."',".time().", 1)";
                mysql_query($sqlh);
                
            }else{
                $res['status'] = 0;    
                $res['info'] = "Not enough money";
            }
        }elseif ($bettype == 'virtualmoney'){            
            if ($row['virtualmoney'] - $betmoney >= 0) {
                $sql = "UPDATE player SET virtualmoney = virtualmoney - $betmoney WHERE username='$name'";
                $result = mysql_query($sql);
                $res['status'] = 1;
                $res['remainmoney'] = $row['virtualmoney'] - $betmoney;
                
                //history
                $sqlh = "INSERT INTO bethistory (username, gameid, betmoney, bettype, remainmoney, time, status) VALUES (
                '$name','minipoker',$betmoney,'virtualmoney','".$res['remainmoney']."',".time().", 1)";
                mysql_query($sqlh);
                
            }else{
                $res['status'] = 0;
                $res['info'] = "Not enough money";
            }
        }        
    }
}
print json_encode($res);

die;