<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-03-25
 * Time: 오전 9:14
 */
session_start();

//이미 로그인이 되어있다면 메인으로 이동
if (isset($_SESSION['class']))
    header('location: ../view/main.php');

include_once __DIR__ . '/../DB/DBconnect.php';

//빈칸 검사
if ($_POST['ID'] == '' || $_POST['PW'] == '') {
    echo '<script>alert("빈칸을 입력해주세요"); history.back();</script>';
}

//빈칸 채워졌을 시
else {
    //변수 선언
    $ID = mysqli_real_escape_string($conn, $_POST['ID']);
    $PW = mysqli_real_escape_string($conn, $_POST['PW']);

    //아이디와 비밀번호가 있는지 확인
    $login = mysqli_query($conn, 'select * from member where id=\'' . $ID . '\' and pw = \'' . $PW . '\'');

    //존재하지 않거나 쿼리 실패 시
    if ($login == false || mysqli_num_rows($login) == 0)
        echo '<script>alert("로그인 실패"); history.back();</script>';

    //쿼리 성공시
    else {
        $login_result = mysqli_fetch_array($login);

        //아이디와 비밀번호 일치 시
        if ($login_result['id'] == $ID && $login_result['pw'] == $PW) {
            $_SESSION['name'] = $login_result['name'];
            $_SESSION['ID'] = $login_result['ID'];
            $_SESSION['email'] = $login_result['email'];

            //클래스에 따라 역할 나누기
            if ($login_result['class'] == 1)
                $_SESSION['class'] = '관리자';
            else $_SESSION['class'] = '기자';
            header('location: ../view/main.php');
        }
        else echo '<script>alert("불일치"); history.back();</script>';
    }
}

?>