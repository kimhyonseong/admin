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
    unset($_SESSION['img_name']);
    unset($_SESSION['img_route']);
    $art_num = (int)mysqli_real_escape_string($conn, $_GET['art_num']);
    mysqli_query($conn, 'select w_name from article where art_num = ' . $art_num);
    $img_time_query = mysqli_query($conn, 'select date_format(now(),"%Y") Y,
                                              date_format(now(),"%m") m,
                                              date_format(now(),"%d") d,
                                              date_format(now(),"%H") H,
                                              date_format(now(),"%i") i,
                                              date_format(now(),"%S") S,
                                              img_url from img where art_num = ' . $art_num );
    if (mysqli_num_rows($img_time_query) != 0) {
        $i=0;
        while ($img_time = mysqli_fetch_array($img_time_query)) {
            $dir = 'img/' . $img_time['Y'] . '/' . $img_time['m'] . '/' . $img_time['d'];
            $_SESSION['img_route'] = 'http://localhost/intern/china_focus/admin/DB/' . $dir;

            //strpos($img_time['img_url'],$art_num.'_')
            //이미지는 기사번호_분초이름.jpg 형식으로 저장되어있음
            $img_file_names[$i] = substr($img_time['img_url'],strpos($img_time['img_url'],$art_num.'_'));

            echo '<script>alert("'.$img_file_names[$i].'")</script>';
            if ( mysqli_num_rows($img_time_query) ==$i+1 ){
                $_SESSION['img_name'] = serialize($img_file_names);
            }
            $i++;
        }
    }
    //$_SESSION['img_name']=serialize('여기에 파일 이름 배열');
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
        #body {
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

        .under_table {
            z-index: 1;
            position: relative;
            left: -25px;
            top: 30px;
            width: 100px;
        }

        .under_table td {
            width: 15px;
            height: 15px;
        }
    </style>
    <script>
        function htmledit(id, first, second = '0') {
            //첫번째 인자만 있을 시--------------- 글에 적용, 다른 열려있는 메뉴들 다 접기
            if (second == null || second === '0') {
                htmlframe.document.execCommand(first);
                if (first === 'redo' || first === 'undo')
                    code_edt.document.execCommand(first);
                else if (first === 'copy') {
                    var copy_string = htmlframe.getSelection().toString();
                    htmlframe.document.execCommand("copy");
                    sessionStorage.setItem('copy', copy_string);
                } else if (first === 'paste') {
                    var copy_session = sessionStorage.getItem('copy');
                    htmlframe.document.execCommand('insertText', false, copy_session);
                    htmlframe.document.body.focus();
                } else htmlframe.document.body.focus(); //포커스 됨
            }
            //-------------------------------------------------------------두번째 인자 있을 시 메뉴 펼치기
            else {
                var under = document.getElementById("under" + id);

                if (under.style.display === 'none')
                    under.style.display = 'inline-block';
                else if (under.style.display === 'inline-block') {
                    htmlframe.document.execCommand(first, false, second);
                    under.style.display = 'none';
                    htmlframe.document.body.focus(); //포커스 됨
                }
            }
        }

        //----------누르면 보이고 안보이고
        function toggle(id) {
            if (document.getElementById('under' + id).style.display === 'none')
                document.getElementById('under' + id).style.display = 'inline-block';
            else
                document.getElementById('under' + id).style.display = 'none';
        }

        //------------------아이프레임을 서밋으로 옮길때 text라는 name을 가진 div에 값 전달
        function data_submit() {
            form.text.value = htmlframe.document.body.innerHTML;
        }

        //------------------------다른 곳 클릭 시 아래로 나온거 닫기
        function close_() {
            var target = event.srcElement.id;
            var target2 = event.srcElement.tagName;
            var class_under = document.getElementsByClassName("under");

            for (var i = 0; i < class_under.length; i++) {  //-------------------자신,예외 항목 설정/ 모든 under 클래스 닫기
                if (target - 1 === i || target2 === 'INPUT') {
                    continue;
                }
                class_under[i].style.display = 'none';
            }
            console.log(event.srcElement.id);
            console.log(class_under.length);
        }

        //-------------------모든 under 클래스 닫기 (아이프레임 클릭 시 사용)
        function close_all() {
            var class_under = document.getElementsByClassName("under");
            for (var a = 0; a < class_under.length; a++) {
                class_under[a].style.display = 'none';
            }
            console.log(class_under.length);
        }

        //------------------------------------  표 만드는 함수
        function makeTable(id, row = 0, data = 0) {
            var under = document.getElementById("under" + id);

            var table = "<table style='border: 1px solid black; border-collapse: collapse;'>";
            var num = 1;

            for (var i = 0; i < row; i++) {
                table += "<tr style='border: 1px solid black;'>";

                for (var a = 0; a < data; a++) {
                    table += "<td style='border: 1px solid black;'>" + num + "</td>";
                    num++;
                }
                table += "</tr>";
            }
            table += "</table><br>";
            htmlframe.document.execCommand('insertHTML',false,table);
            htmlframe.document.body.focus(); //포커스 됨

        }

        //----------------------특수문자 삽입
        function insert_text(id, text = 0) {

            htmlframe.document.execCommand('insertText', false, text);
            htmlframe.document.body.focus();

        }

        //------------------이모티콘 삽입
        function insert_emt(id, src = 0) {

            htmlframe.document.execCommand('insertImage', false, src);
            document.getElementById("under" + id).style.display = 'none';
            htmlframe.document.body.focus();

        }

        //------------------------- 줌 함수
        var scale = 1;
        function zoom1(id, scale = 0) {
            var under = document.getElementById("under" + id);

            htmlframe.document.body.style.zoom = scale;
            htmlframe.document.body.focus();

        }

        //--------------------------- html 편집기 사용
        var decode = 1;

        function changeDecode() {
            if (decode === 1) {
                document.getElementById('decode_des').innerText = 'HTML편집 상태';
                decode = 0;
                document.getElementById('code_edt').style.width = '100%';
                document.getElementById('code_edt').style.height = '500px';
                document.getElementById('code_edt').style.backgroundColor = 'black';
                document.getElementById('code_edt').style.color = 'white';
                document.getElementById('code_edt').innerText = htmlframe.document.body.innerHTML;
                //$(document.getElementById('decode').innerText).text(htmlframe.document.body.innerHTML);
                document.getElementById('code_edt').style.display = 'block';

            } else {
                decode = 1;
                document.getElementById('decode_des').innerText = '';
                document.getElementById('code_edt').style.display = 'none';
            }
        }

        //-----------------iframe에 있는 값 편집기로 가져오기( 검은 화면으로)
        function update_html() {
            code_edt.document.body.innerText = (htmlframe.document.body.innerHTML);
        }

        //------------------편집기에 있는 값 iframe으로 보내기(흰 화면으로)
        function update_text() {
            (htmlframe.document.body.innerHTML) = (code_edt.document.body.innerText);
        }

        //-------------------------이미지 파일 업로드 관련 함수들
        var count = 1;

        function add_file() {
            count++;
            Files.innerHTML = Files.innerHTML +
                '<br id="br' + count + '">' + '<span id="des' + count + '">이미지' +
                count + ' </span><input name="file[]" id="file' + count + '" type="file">';
        }

        function delete_file() {
            console.log(document.getElementById('file' + count));
            console.log(count);
            if (count < 2) {
                alert('파일 최소 1개');
            } else {
                document.getElementById('des' + count).remove();
                document.getElementById('file' + count).remove();
                document.getElementById('br' + count).remove();
                count--;
            }
        }

        function insert_img(img) {
            var add;
            add = img;
            parent.htmlframe.document.execCommand('insertHTML', false, add);
        }


        //----------------------미디어 넣기 (유튜브만...)
        function insert_video(id, url) {

            if (url.replace(/(\s*)/g, "") !== '') {
                //https://youtu.be/10WmU0CqMaM
                //https://www.youtube.com/watch?v=10WmU0CqMaM&feature=youtu.be
                //위에 두개가 아래처럼 되야함
                //https://www.youtube.com/embed/10WmU0CqMaM
                if (url.indexOf('youtu.be') !== -1 || url.indexOf('youtube.com') !== -1) //유튜브 영상 넣기
                {
                    if (url.indexOf('?v=') !== -1) {
                        var start = url.indexOf('?v='); //시작 위치
                        if (url.indexOf('&feature') !== -1) {
                            var end = url.indexOf('&feature') - url.indexOf('?v='); //끝부분 위치
                            var end_url = url.substr(start, end).replace('?v=', '');
                        }
                        else
                        {
                            end_url = url.substr(start).replace('?v=', '');
                        }
                    } else {
                        end_url = url.substr(url.indexOf('youtu.be/')).replace('youtu.be/', '');
                    }
                    url = 'https://www.youtube.com/embed/' + end_url;
                    alert(end_url);
                }
                var video = '<br><iframe width="640" height="360" src="' + url + '" frameborder="0"' +
                    ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><br><br>';
                htmlframe.document.execCommand('insertHTML', false, video);
                document.getElementById(id).style.display = 'none';
            } else {
                alert('주소를 확인해주세요.');
                document.getElementById('video').focus();
            }
        }

        //----------------------미리보기
        function pre_view() {
            window.open("", 'pre_view', "width=1200px;,height=800px;", true);
            document.form.action = 'pre_view.php';
            document.form.target = 'pre_view';
            document.form.submit();
        }

        function delete_event() {
            if (confirm("정말 삭제하시겠습니까?") === true) {
                document.form.action = '../DB/delete_article.php';
                document.form.submit();
            } else return;
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
                <input type="hidden" name="name" value="<?= $art_content['w_name'] ?>">
                <input type="hidden" value="<?= (int)mysqli_real_escape_string($conn, $_GET['art_num']) ?>"
                       name="art_num">
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
                                                      value="<?= $art_content['view_title'] ?>"></h4>* 노출제목은 필수가
                    아닙니다.<br><br>
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
                </div>
                <!--// 기사뷰 타이틀 -->
                <!-- 기사뷰 텍스트 -->
                <div class='view_txt'>
                    <div style="font-size: 15px;">
                        주의사항<br>
                        &nbsp;&nbsp;이미지 확장자는 (jpg,jpeg,png,gif)만 가능합니다.<br>
                        &nbsp;&nbsp;이미지는 한꺼번에 첨부해주세요.<br>
                        &nbsp;&nbsp;이전 이미지를 첨부하지 않으면 이미지 손상이 일어납니다.<br>
                    </div>
                    <?php
                    include_once 'editor.php';
                    ?>
                    <!--// 기사뷰 텍스트 -->

                    <!--서밋-->
                    <input type="submit" formaction="../DB/modify_article.php" value="글 수정 완료" onclick="data_submit();">
                    <input type="button" onclick="delete_event()" value="글 삭제"><br><br>
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
