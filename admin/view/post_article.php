<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-03-25
 * Time: 오후 3:32
 */
session_start();
if (!isset($_SESSION['class']))
    header('location: ../index.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글쓰기</title>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type='text/javascript' src="http://news.mk.co.kr/v4/lib/js/header.js"></script>
    <link rel='stylesheet' type='text/css' href='http://common.mk.co.kr/common/css/2017/chinafocus_ver2.css'>
    <link rel="stylesheet" href="../css/header.css?v=2">
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

        function F5(){
            //alert(event.keyCode);
            if (event.keyCode === 116 || event.ctrlKey===1 && (event.keyCode ===82))
            {
                var check=confirm('변경사항이 저장되지 않습니다.');
                if (check===true)
                {
                    //여기에 파일 지우기 넣으면 됨
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        //document.onkeydown=F5;
        window.onbeforeunload = function (e) {
            e = e || window.event;
            // For IE<8 and Firefox prior to version 4
            if (e) {
                e.returnValue = '페이지를 닫습니다.';
            }
            // For Chrome, Safari, IE8+ and Opera 12+
            return '페이지를 닫습니다.';
        };
        function htmledit(id, first, second = '0') {
            //첫번째 인자만 있을 시--------------- 글에 적용, 다른 열려있는 메뉴들 다 접기
            if (second == null || second === '0') {
                htmlframe.document.execCommand(first);
                if (first === 'redo' || first === 'undo')
                    code_edt.document.execCommand(first);
                else if (first ==='copy') {
                    var copy_string = htmlframe.getSelection().toString();
                    htmlframe.document.execCommand("copy");
                    sessionStorage.setItem('copy', copy_string);
                }
                else if (first ==='paste') {
                    var copy_session = sessionStorage.getItem('copy');
                    htmlframe.document.execCommand('insertText',false,copy_session);
                    htmlframe.document.body.focus();
                }
                else htmlframe.document.body.focus(); //포커스 됨
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
            htmlframe.document.body.innerHTML = htmlframe.document.body.innerHTML + table;
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
                        var end = url.indexOf('&feature') - url.indexOf('?v='); //끝부분 위치
                        var end_url = url.substr(start, end).replace('?v=', '');
                    } else {
                        end_url = url.substr(url.indexOf('youtu.be/')).replace('youtu.be/', '');
                    }
                    url = 'https://www.youtube.com/embed/' + end_url;
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

        //----------------------붙여넣기

    </script>
</head>
<body id="body" onclick="close_();">
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

        <form method="post" name="form" id="form" enctype="multipart/form-data">

            <div class="content_left">
                <br><br>
                요청 중요도 : <input type="number" min="0" max="100" name="import" placeholder="중요도(숫자만)">
                <br>0이상 100이하
                <!-- 기사뷰 타이틀 -->
                <div class="view_title">
                    <h3>제목 : <input  class="input_title" type="text" name="title" placeholder="메인 제목"></h3>
                    <h4 class='sub_tit'> 부제목 : <input class="input_title" type="text" name="sub_title"
                                                      placeholder="보조 제목"></h4>
                    <h4 class='sub_tit'>노출제목 : <input class="input_title" type="text" name="view_title"
                                                      placeholder="실제로 보이게 할 제목(짧게)"></h4><br>
                    카테고리 : <select name="code">
                        <?php
                        include_once __DIR__ . '/../DB/DBconnect.php';
                        $cate = mysqli_query($conn, 'select * from cate');
                        while ($cate_content = mysqli_fetch_array($cate)) {
                            echo '<option value="' . $cate_content['code'] . '">' . $cate_content['s_name'] . '</option>';
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
                </div>
                <!--// 기사뷰 텍스트 -->
                <br>
                <input type="submit" formaction="../DB/test.php" onclick="data_submit();"
                       value="글쓰기 완료"><br><br>
            </div>
        </form>

        <!--// 기사에 들어갈 내용들-->
        <!--// left -->
    </div>
    <!--// container -->
</div>
</body>
</html>