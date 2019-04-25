<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-04-01
 * Time: 오후 2:09
 */
session_start();
if (isset($_SESSION['class']))
    header('location: view/main.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Join</title>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <script>
        function C1() {
            if (document.getElementById('ID').value) {
                window.open('member/check.php?ID=' + document.getElementById('ID').value, '', 'width=500px,height=250px');
            }
            else {
                console.log(document.getElementById('ID'));
                alert('빈칸을 채워주세요.');
            }
        }
    </script>
    <style>
        input[type="text"],input[type="password"]
        {
            width: 300px;
            height: 60px;
            font-size: 27px;
            margin: 10px;
            padding-left: 10px;
            border-radius: 5px;
            border: 4px solid #dcdcdc;
        }
        input[type="text"]:focus,input[type="password"]:focus
        {
            width: 300px;
            height: 60px;
            font-size: 27px;
            margin: 10px;
            padding-left: 10px;
            border-radius: 5px;
            border: 4px solid black;
        }
        input[type="button"],input[type="submit"]
        {
            background-color: transparent;
            border: 3px solid #dcdcdc;
            border-radius: 10px;
            width: 170px;
            height: 60px;
            font-size: 25px;
        }
        input[type="button"]:hover,input[type="submit"]:hover
        {
            background-color: transparent;
            border: 3px solid black;
            border-radius: 10px;
            width: 170px;
            height: 60px;
            font-size: 25px;
        }
    </style>
</head>
<body>
<article>
    <h1>회원가입</h1><br>
    <form method="post" action="member/join.php">
        <input autocomplete="off" type="text" placeholder="ID" name="ID" id="ID">
        &nbsp;&nbsp;<input type="button" value="중복확인" onclick="C1();"><br>
        <input type="password" placeholder="Password" name="PW" id="PW"><br>
        <input type="password" placeholder="Password Check" name="PWC" id="PWC"><br>
        <input type="text" placeholder="name" name="name"><br>
        <input type="text" placeholder="E-mail" name="Email" id="Email" autocomplete="off"><br><br>
        <input type="hidden" value="<?= $_SERVER['HTTP_REFERER'] ?>" name="pre_page">
        <input type="submit" value="Join Us">
    </form>
</article>
</body>
</html>
