<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-04-04
 * Time: 오전 9:15
 */
session_start();
if (!isset($_SESSION['class'])) {
    echo '<script>alert("로그인이 필요합니다."); location.href="../index.php";</script>';
}
if (!isset($_GET['page']) || (int)$_GET['page'] < 1)
    $page = 1;
else $page = (int)$_GET['page'];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 메인페이지</title>
    <link rel="stylesheet" href="../css/main.css?v=0">
</head>
<body>
<header>
</header>
<nav>
    <?php
    include_once 'nav.php';
    ?>
</nav>
<article>
    <!--오른쪽 내용들-->
    <?php
    //-----------------URL을 통한 오른쪽 컨텐츠
    if (isset($_GET['ok']) || isset($_GET['big_num']) || isset($_GET['code'])) {
    ?>
    <label>
        <select name="order" class="select"
                onchange="location.href='<?=$_SERVER['PHP_SELF']?>?' +
                '<?php
                if (isset($_GET['ok'])) echo 'ok='.$_GET['ok'].'&';
                if (isset($_GET['new'])) echo 'new='.$_GET['new'].'&';
                if (isset($_GET['big_num'])) echo 'big_num='.$_GET['big_num'].'&';
                if (isset($_GET['key_word'])) echo 'key_word='.$_GET['key_word'].'&';
                ?>order='+this.options[this.selectedIndex].value">
            <option value="post_date">정렬</option>
            <option value="art_num"
                <?php if (isset($_GET['order']) && $_GET['order']=='art_num')
                echo 'selected'; ?>
            >기사 번호 순</option>
            <option value="import"
                <?php if (isset($_GET['order']) && $_GET['order']=='import')
                    echo 'selected'; ?>
            >중요도 순</option>
        </select>
    </label>
    <br>
    <!--기사 리스트-->
    <table>
        <thead>
        <tr>
            <!--테이블 헤더-->
            <th>기사<br>번호</th>
            <th>카테고리</th>
            <th>제목</th>
            <th>작성자</th>
            <th>날짜</th>
            <th>승인<br>여부</th>
            <!--//테이블 헤더-->
        </tr>
        </thead>
        <!--테이블 내용-->
        <tbody>
        <?php
        include_once __DIR__ . '/../DB/DBconnect.php';

        /*승인 상태와 카테고리가 존재할 경우
        표 내용 쿼리가 해당 페이지 쿼리*/
        //-------------------------------------------------------------ok가 있을때
        if (isset($_GET['ok'])) {
            if ($_GET['ok']==(string)2) {   //----------------ok가 2면 모두 출력
                $query = 'select a.art_num,a.title,c.s_name,a.w_name,date_format(a.post_date,"%y.%m.%d") post_date,
                                  date_format(a.modi_date,"%y.%m.%d") modi_date,a.ok from article a
                          join cate c on a.code=c.code
                          where (select max(art_num) from article)>=a.art_num'; // 다른 조건이 없어서 서브쿼리 없으면 현저히 느려짐
                $all_page_query = 'select ceil(count(*)/15) count from article a join cate c on a.code=c.code';
            }
            elseif (isset($_GET['code'])) {     //-----------------코드가 있을 때
                $query = 'select a.art_num,a.title,c.s_name,a.w_name,date_format(a.post_date,"%y.%m.%d") post_date,
                                  date_format(a.modi_date,"%y.%m.%d") modi_date,a.ok from article a
                          join cate c on a.code=c.code 
                                       and c.code=' . (int)mysqli_real_escape_string($conn, $_GET['code']) . '
                          where ok=' . (int)mysqli_real_escape_string($conn, $_GET['ok']);
                $all_page_query = 'select ceil(count(*)/15) count from article a join cate c on a.code=c.code
                                  and c.code=' . (int)mysqli_real_escape_string($conn, $_GET['code']).'
                                  where ok=' . (int)mysqli_real_escape_string($conn, $_GET['ok']);
            }
            elseif (isset($_GET['big_num'])) {      //------------------큰 카테고리가 있을 때
                $query = 'select a.art_num,a.title,c.s_name,a.w_name,date_format(a.post_date,"%y.%m.%d") post_date,
                                  date_format(a.modi_date,"%y.%m.%d") modi_date,a.ok from article a
                          join cate c on a.code=c.code 
                                       and c.big_num=' . (int)mysqli_real_escape_string($conn, $_GET['big_num']) . '
                          where ok=' . (int)mysqli_real_escape_string($conn, $_GET['ok']);
                $all_page_query = 'select ceil(count(*)/15) count from article a
                                   join cate c on a.code=c.code 
                                                and c.big_num=' . (int)mysqli_real_escape_string($conn, $_GET['big_num']) . '
                                   where ok=' . (int)mysqli_real_escape_string($conn, $_GET['ok']);
            }

            /*----------------------------------------------------ok만 있을때
                    전체, 신규, 업데이트로 나뉨 총 쿼리 3개 new=1은 신규 new=0은 업데이트*/
            else
            {
                $query = 'select a.art_num,a.title,c.s_name,a.w_name,date_format(a.post_date,"%y.%m.%d") post_date,
                                  date_format(a.modi_date,"%y.%m.%d") modi_date,a.ok from article a
                          join cate c on a.code=c.code
                          where ok=' . (int)mysqli_real_escape_string($conn, $_GET['ok']);
                $all_page_query = 'select ceil(count(*)/15) count from article a
                                   join cate c on a.code=c.code
                                   where ok=' . (int)mysqli_real_escape_string($conn, $_GET['ok']);

                //new는 신규와 업데이트된 기사를 구분함
                if (isset($_GET['new'])) {
                    if ($_GET['new'] == (string)1) {
                        $query = $query . ' and modi_date is null';
                    } elseif ($_GET['new'] == (string)0) {
                        $query .= ' and modi_date is not null';
                    }
                    else //3이나 4 등 1,2를 제외한 숫자 들어가면 쿼리의 결과 값이 0 row 또는 쿼리 실패하게
                        $query = ' ';
                }
            }
        }
        //--승인이 있고 코드가 없을때 기사들 출력 -- 끝
        //나머지 이상한거는 실패 처리
        else {
            $query = ' ';
            echo '<tr><td colspan="5">카테고리를 선택해주세요</td></tr>';
        }
        //--------------------------------정렬--------------
        if (isset($_GET['order'])) $order=' order by '.mysqli_real_escape_string($conn,$_GET['order']).' desc';
        else $order=' order by post_date desc';
        $limit=' limit ' . ($page - 1) * 15 . ', 15';

        $sql = mysqli_query($conn,$query.$order.$limit) or die('<script>alert("안되요");</script>');
        $all_page = mysqli_fetch_array(mysqli_query($conn, $all_page_query))['count'];
        //$all_page=ceil(mysqli_num_rows(mysqli_query($conn,$query))/15); --> 속도 느려짐
        if ($all_page==false)
            $all_page =0;

        //쿼리 실패 및 조회되는 기사가 없을 시
        if (mysqli_num_rows($sql) == 0) {
            echo '<tr><td colspan="5">없습니다.</td></tr>';
        } //--------------------------조회되는 기사가 있을 시 기사 리스트 출력
        else {
            //echo $query.$order.$limit;
            while ($sql_result = mysqli_fetch_array($sql))
            {
                echo '<tr><td class="art_num">' . $sql_result['art_num'] . '</td>';
                echo '<td class="cate_dt">'.$sql_result['s_name'].'</td>';
                if (mb_strlen($sql_result['title'], 'UTF-8') > 33)
                    $title = mb_substr($sql_result['title'], 0, 32, 'UTF-8') . '..';
                else
                    $title = $sql_result['title'];
                echo '<td class="title_td"><a href="article_view.php?art_num=' . $sql_result['art_num'] . '"> ' . $title . '</a></td>';
                echo '<td class="name_td">' . $sql_result['w_name'] . '</td>';
                if ($sql_result['modi_date'] == '')
                    echo '<td class="date_td">' . $sql_result['post_date'] . '</td>';
                else
                    echo '<td class="date_td">' . $sql_result['modi_date'] . '</td>';
                if ($sql_result['ok'] == 1)
                    $ok = '승인';
                else $ok = '미승인';
                echo '<td class="ok_td">' . $ok . '</td></tr>';
            }
        }   //----------------------------------------------------------------------------

        echo '</tbody>';
        //<!--//테이블 내용-->
        echo '</table><br>';
        //<!--//기사 리스트-->

        //-=======================================페이징===============================================================

        //숫자 페이지 시작과 끝 계산
        $start_page = ceil($page / 10) * 10 - 9;
        $end_page = ceil($page / 10) * 10;
        if ($start_page < 1)  $start_page = 1;
        if ($start_page >= $all_page)  $start_page = $all_page;
        if ($end_page >= $all_page) $end_page = $all_page;

        echo '<div class="page_num" style="text-align: center;">';

        //이전 페이지
        echo '<a href="' . $_SERVER['PHP_SELF'] . '?';
        if (isset($_GET['ok']))  echo 'ok=' . (int)htmlspecialchars($_GET['ok']) . '&';
        if (isset($_GET['new'])) echo 'new=' . (int)htmlspecialchars($_GET['new']) . '&';
        if (isset($_GET['code'])) echo 'code=' . (int)htmlspecialchars($_GET['code']) . '&';
        if (isset($_GET['big_num']))  echo 'big_num=' . (int)htmlspecialchars($_GET['big_num']) . '&';
        if (isset($_GET['order'])) echo 'order='.htmlspecialchars($_GET['order']).'&';
        if (floor($page / 10) * 10 - 9 <= 1)
            echo 'page=1">이전 </a>';
        else
            echo 'page=' . (floor($page / 10) * 10 - 9) . '">이전 </a>';

        //숫자 페이지
        for ($p = $start_page; $p <= $end_page; $p++) {
            if ($all_page == 0)
                echo '';
            else {
                echo '<a href="' . $_SERVER['PHP_SELF'] . '?';
                if (isset($_GET['page']) && (int)$_GET['page'] == $p) {
                    if (isset($_GET['ok'])) echo 'ok=' . (int)htmlspecialchars($_GET['ok']) . '&';
                    if (isset($_GET['new'])) echo 'new=' . (int)htmlspecialchars($_GET['new']) . '&';
                    if (isset($_GET['code'])) echo 'code=' . (int)htmlspecialchars($_GET['code']) . '&';
                    if (isset($_GET['big_num'])) echo 'big_num=' . (int)htmlspecialchars($_GET['big_num']) . '&';
                    if (isset($_GET['order'])) echo 'order='.htmlspecialchars($_GET['order']).'&';
                    echo 'page=' . $p . '">&nbsp;<strong>' . $p . '</strong>&nbsp;</a>';
                } else {
                    if (isset($_GET['ok'])) echo 'ok=' . (int)htmlspecialchars($_GET['ok']) . '&';
                    if (isset($_GET['new'])) echo 'new=' . (int)htmlspecialchars($_GET['new']) . '&';
                    if (isset($_GET['code'])) echo 'code=' . (int)htmlspecialchars($_GET['code']) . '&';
                    if (isset($_GET['big_num'])) echo 'big_num=' . (int)htmlspecialchars($_GET['big_num']) . '&';
                    if (isset($_GET['order'])) echo 'order='.htmlspecialchars($_GET['order']).'&';
                    echo 'page=' . $p . '">&nbsp;' . $p . '&nbsp;</a>';
                }
            }
        }

        //다음 페이지
        echo '<a href="' . $_SERVER['PHP_SELF'] . '?';
        if (isset($_GET['ok'])) echo 'ok=' . (int)htmlspecialchars($_GET['ok']) . '&';
        if (isset($_GET['new'])) echo 'new=' . (int)htmlspecialchars($_GET['new']) . '&';
        if (isset($_GET['code'])) echo 'code=' . (int)htmlspecialchars($_GET['code']) . '&';
        if (isset($_GET['big_num'])) echo 'big_num=' . (int)htmlspecialchars($_GET['big_num']) . '&';
        if (isset($_GET['order'])) echo 'order='.htmlspecialchars($_GET['order']).'&';
        if (ceil($page / 10) * 10 + 1 >= $all_page) echo 'page=' . $all_page . '"> 다음</a>';
        else        echo 'page=' . (ceil($page / 10) * 10 + 1) . '"> 다음</a>';
        echo '</div>';
        //-=======================================페이징 끝===============================================================
        }

        //처음 들어왔을 때 보여주는 화면
        else {
            $count_article = mysqli_fetch_array(mysqli_query($conn,
                'select count(*) count from article'));
            $new_article = mysqli_fetch_array(mysqli_query($conn,
                'select count(*) count from article where ok=0 and modi_date is null'));
            $update_article = mysqli_fetch_array(mysqli_query($conn,
                'select count(*) count from article where ok=0 and modi_date is not null;'));
            $no_ok = mysqli_fetch_array(mysqli_query($conn,
                'select count(*) count from article where ok=0'));
            ?>

            <span class="main_box1" id="post" onclick="location.href='post_article.php'">
            <br>
            <img src="icon/new-24-128.png"><br><br><br>
            <p class="font">글쓰기</p>
        </span>

            <span class="main_box1" id="all_article" onclick="location.href='main.php?ok=2'">
            <br>
            <img src="icon/paper.png"><br><br><br>
            <p class="font">전체 기사<br>
            <?= $count_article['count'] ?>건</p>
        </span>


            <div style="top: 470px; position: relative;width: 100%; height: 300px;">
                <div class="main_box2" id="no_ok" onclick="location.href='main.php?ok=0'">
                    <br>
                    <img src="icon/x.png" width="100"><br>
                    <p class="font2">미승인 기사<br>
                <?= $no_ok['count'] ?>건</p>
                </div>

                <div class="main_box2" id="update_art" onclick="location.href='main.php?ok=0&new=0'">
                    <br>
                    <img src="icon/Update-128.png"><br><br><br>
                    <p class="font2">결재 진행 중<br>
                        <?= $update_article['count'] ?>건</p>
                </div>

                <div class="main_box2" id="new_art" onclick="location.href='main.php?ok=0&new=1'">
                    <br>
                    <img src="icon/new10-128.png"><br><br><br>
                    <p class="font2">신규 기사<br>
                        <?= $new_article['count'] ?>건</p>
                </div>
            </div>
            <?php
        }
        ?>
</article>
</body>
</html>