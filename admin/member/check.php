<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-04-01
 * Time: 오후 2:14
 */

include_once __DIR__.'/../DB/DBconnect.php';

//공백 제거
$rID=trim(mysqli_real_escape_string($conn,htmlspecialchars($_GET['ID'])));
$special = "/[~!@#$%^&*()_\-\+\=\\\'\"\<>,\.\|;:`\/]/";

//아이디가 이미 있는지 확인
$ch=mysqli_query($conn,'select * from member where id=\''.$rID.'\'');
$ch_id=mysqli_fetch_array($ch);


if (strpos($rID, ' ') !==false || empty($rID) )
    echo "공백 사용이 안됩니다.";

else if (!strcmp($rID, $ch_id['ID']) || mysqli_num_rows($ch)>=1)
    echo "중복된 아이디입니다.";

else if (preg_match($special, $rID))
    echo "특수문자는 사용이 안됩니다.";

else
    echo $rID . "(은)는 사용할 수 있는 아이디입니다.";

?>