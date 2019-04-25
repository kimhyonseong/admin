<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-03-25
 * Time: 오전 9:14
 */
include_once __DIR__ . '/../DB/DBconnect.php';

//특수문자 패턴
$special = "/[~!@#$%^&*()_\-\+\=\\\'\"\<>,\.\|;:`\/]/";

//변수 선언
if (isset($_POST['ID']))
    $ID = mysqli_real_escape_string($conn, htmlspecialchars($_POST['ID']));
if (isset($_POST['name']))
    $name = mysqli_real_escape_string($conn, htmlspecialchars($_POST['name']));
if (isset($_POST['PW']))
    $PW = mysqli_real_escape_string($conn, $_POST['PW']);
if (isset($_POST['PWC']))
    $PWC = mysqli_real_escape_string($conn, $_POST['PWC']);
if (isset($_POST['Email']))
    $Email = mysqli_real_escape_string($conn, $_POST['Email']);


if (!strcmp($ID,'') || !strcmp($name,'') || !strcmp($PW,'') || !strcmp($PWC,'') || !strcmp($Email,''))
    echo '<script>alert("빈칸을 채워주세요."); history.back();</script>';

else if (preg_match($special, $ID))
    echo '<script>alert("아이디에 특수문자 사용 불가"); history.back();</script>';

else if (trim($ID) != $ID)
    echo '<script>alert("아이디 공백 사용 불가능"); history.back();</script>';

elseif ($PW != $PWC)
    echo '<script>alert("비밀번호를 확인하세요."); history.back();</script>';

else {
    mysqli_query($conn, 'insert into member(id,pw,name,email) values("' . $ID . '","' . $PW . '","' . $name . '","' . $Email . '")');
    echo '<script>alert("'.$name.'기자님 가입되었습니다."); location.href="../index.php"</script>';
}
?>