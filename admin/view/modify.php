<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-03-25
 * Time: 오후 3:58
 */
session_start();
if (!isset($_SESSION['class']))
    header('location: ../index.php');
include_once __DIR__ . '/../DB/DBconnect.php';

//사용자 및 기사 체크 쿼리 (기사의 작성자 이름 조회) GET이 안들어오면 0으로 처리함함
$check = mysqli_query($conn, 'select w_name from article where art_num = ' . (int)mysqli_real_escape_string($conn, $_GET['art_num']));

//쿼리 결과 0 또는 실패 시 뒤로, 성공시 값 가져오기
if ($check == false || mysqli_num_rows($check) == 0)
    echo '<script>alert("기사가 없습니다."); history.back();</script>';
else $writer_check = mysqli_fetch_array($check);


//현재 세션과 작성자의 이름이 불일치하면서 관리자가 아닐 시
if ($writer_check['w_name'] != $_SESSION['name'] && $_SESSION['class'] != '관리자')
    echo '<script>alert("작성자가 아닙니다."); history.back();</script>';
//그 외 (작성자 이름 일치 또는 관리자일 경우)
else {
    echo '수정 가능';
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>수정페이지</title>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type='text/javascript' src="http://news.mk.co.kr/v4/lib/js/header.js"></script>
    <link rel='stylesheet' type='text/css' href='http://common.mk.co.kr/common/css/2017/chinafocus_ver2.css'>
    <link rel="stylesheet" href="../css/header.css?v=0">
    <style>
        #body{
            margin: auto;
            width: 750px;
        }
        input[type="number"] {
            width: 100px;
            height: 30px;
        }

        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .input_title {
            width: 600px;
            height: 30px;
        }

        .File {
            /*apperance: none;*/
            /*-webkit-apperance: none;*/
            width: 100%;
            height: 80%;
            padding: auto;
            text-align: center;
        }

        .file_name {
            font-size: 15px;
        }
    </style>
    <script type="text/javascript">
        <?php
        include_once __DIR__ . '/../DB/DBconnect.php';
        $img_count = mysqli_fetch_array(mysqli_query($conn,
                                            'select count(art_num) count from img 
                                                   where art_num=' . (int)mysqli_real_escape_string($conn, $_GET['art_num']) . ' 
                                                   and not img_url=""'));
        ?>


        //글을 텍스트 에어리어에 삽입하는 함수
        function delete_event()
        {
            if(confirm("정말 삭제하시겠습니까?")===true)
            {
                document.form.action='../DB/delete_article.php';
                document.form.submit();
            }
            else return;
        }
    </script>
</head>
<body id="body">
<header>
    <?php
    include_once 'header.php';
    ?>
</header>
<div>
    <!-- container -->
    <div>
        <!-- left -->
        <!--기사에 들어갈 내용들-->
        <?php
        include_once __DIR__ . '/../DB/DBconnect.php';
        $article = mysqli_query($conn, 'select * from article where art_num=' . (int)mysqli_real_escape_string($conn, $_GET['art_num']));
        if ($article == false)
            $art_content = '';
        else
            $art_content = mysqli_fetch_array($article);
        ?>

        <div class="content_left">
            <form method="post" name="form" enctype="multipart/form-data">
                <br><br>
                <input type="hidden" name="name" value="<?=$art_content['w_name']?>">
                <input type="hidden" value="<?=(int)mysqli_real_escape_string($conn, $_GET['art_num'])?>" name="art_num">
                중요도 : <input type="number" min="0" max="100" name="import" placeholder="중요도(숫자만)">
                <br>0이상 100이하
                <!-- 기사뷰 타이틀 -->
                <div class="view_title">
                    <h3>제목 : <input required class="input_title" type="text" name="title" placeholder="메인 제목"
                                    value="<?= $art_content['title'] ?>"></h3>
                    <h4 class='sub_tit'> 부제목 : <input required class="input_title" type="text" name="sub_title"
                                                      placeholder="보조 제목" value="<?= $art_content['sub_title'] ?>"></h4>
                    <h4 class='sub_tit'>노출제목 : <input class="input_title" type="text" name="view_title"
                                                      placeholder="실제로 보이게 할 제목(짧게)"
                                                      value="<?= $art_content['view_title'] ?>"></h4><br>
                    카테고리 : <select name="code">
                        <?php
                        include_once __DIR__ . '/../DB/DBconnect.php';
                        $cate = mysqli_query($conn, 'select * from cate');
                        while ($cate_content = mysqli_fetch_array($cate)) {
                            if ($art_content['code'] == $cate_content['code'])
                                echo '<option value="' . $cate_content['code'] . '" selected>' . $cate_content['s_name'] . '</option>';
                            else echo '<option value="' . $cate_content['code'] . '">' . $cate_content['s_name'] . '</option>';
                        }
                        ?>
                    </select>
                    <br>* 노출제목은 필수가 아닙니다.
                </div>
                <!--// 기사뷰 타이틀 -->
                <!-- 기사뷰 텍스트 -->
                <div class='view_txt'>
                    <div style="font-size: 15px;">
                        주의사항<br>
                        &nbsp;&nbsp;이미지 확장자는 (jpg,jpeg,png,gif)만 가능합니다.<br>
                    </div>
                    <?php
                    include_once 'editor.php';
                    ?>

                <!--// 기사뷰 텍스트 -->

                <!-- 이미지 업로드-->
                이미지를 추가하지 않으면 기존에 있던 이미지가 사라집니다.<br>
                <!--서밋-->
                <input type="submit" formaction="../DB/modify_article.php" value="글 수정 완료"> <input type="button" onclick="delete_event()"  value="글 삭제"><br><br>
                <!--//서밋--></div>
            </form>
        </div>

        <!--// 기사에 들어갈 내용들-->
        <!--// left -->
    </div>
    <!--// container -->
</div>
</body>
</html>
