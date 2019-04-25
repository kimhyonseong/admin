<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-03-25
 * Time: 오전 9:08
 */
session_start();
if (isset($_SESSION['class']))
    header('location: view/main.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Admin_index</title>
    <style>
        #index
        {
            margin-top: 15vh;
            height: 100%;
            width: 100%;
        }
        .login
        {
            width: 500px;
            height: 400px;
            margin: auto;
            text-align: center;
        }
        .login_text
        {
            width: 250px;
            height: 30px;
            margin: 15px;
            border: none;
            border-bottom: #dcdcdc 2px solid;
            font-size: 20px;
            padding: 5px;
            background-color: transparent;
        }
        .login_text:focus
        {
            border-bottom: pink 2px solid;
            outline: none;
        }
        .index_submit
        {
            width: 120px;
            height: 50px;
            margin: 10px;
            border-radius: 10px;
            /*border: #585858 4px solid;*/
            border: none;
            font-size: 20px;
            background-color: #585858;
            color: white;
            margin-top: 30px;
            transition: background-color .3s;
        }
        .index_submit:hover
        {
            /*border: pink 4px solid;*/
            background-color: pink;
        }
        body
        {
            background-color: #F6F6F6;
        }
        h1
        {
            color: #585858;
        }
    </style>
</head>
<body id="index">
<div class="login">
    <br><br>
    <h1>China Focus<br>Admin page</h1>
    <form method="post">
        <input class="login_text" type="text" placeholder="ID" name="ID" autocomplete="off"><br>
        <input class="login_text" type="password" placeholder="password" name="PW"><br>
        <input class="index_submit" type="submit" formaction="member/login.php" value="Log in"><input type="submit" value="Join" formaction="join_form.php" class="index_submit">
    </form>
</div>
</body>
</html>