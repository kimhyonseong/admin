<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-04-01
 * Time: 오전 11:07
 */
session_start();
if (!isset($_SESSION['class']))
{
    echo '<script>alert("로그인이 필요합니다."); location.href="../index.php";</script>';
}
if (!isset($_GET['page']) || (int)$_GET['page']<1)
    $page=1;
else $page = (int)$_GET['page'];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/header.css">
    <title>검색</title>
    <link rel="stylesheet" href="../css/main.css?v=1">
</head>
<body>
<nav>
    <?php
    include_once 'nav.php';
    ?>
</nav>
<article>
    <label>
        <select name="order" class="select"
                onchange="location.href='<?=$_SERVER['PHP_SELF']?>?' +
                        '<?php
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

    <!--기사 리스트-->
    <table>
        <thead>
        <tr>
            <!--테이블 헤더-->
            <th>기사<br>번호</th><th>카테고리</th><th>제목</th> <th>기자</th> <th>날짜</th> <th>승인<br>여부</th>
            <!--//테이블 헤더-->
        </tr>
        </thead>
        <!--테이블 내용-->
        <tbody>
        <?php
        include_once __DIR__.'/../DB/DBconnect.php';

        //키워드가 있다면 찾기
        if (isset($_GET['key_word']) && $_GET['key_word']!='') {
            $query = 'select a.title,a.art_num,c.s_name,a.w_name,date_format(a.post_date,"%y.%m.%d") post_date,a.modi_date,a.ok from article a
                      join cate c on a.code=c.code
                      where (select min(a.art_num) from article a)<=art_num
                      and (a.text like "%'.mysqli_real_escape_string($conn,$_GET['key_word']).'%"
                           or a.title like "%'.mysqli_real_escape_string($conn,$_GET['key_word']).'%"
                           or a.sub_title like "%'.mysqli_real_escape_string($conn,$_GET['key_word']).'%"
                           or a.view_title like "%'.mysqli_real_escape_string($conn,$_GET['key_word']).'%") 
                           ';
            if (isset($_GET['order'])) $order = 'order by '.mysqli_real_escape_string($conn,$_GET['order']).' desc ';
            else $order = 'order by post_date desc ';
            $limit = 'limit ' . ($page-1)*15 . ', 15';
            $sql = mysqli_query($conn, $query.$order.$limit);
        }
        else{
            echo '<tr><td colspan="5">검색어를 입력해주세요.</td></tr>';
            $sql=false;
        }

        //쿼리 실패 및 조회되는 기사가 없을 시
        if ($sql==false || mysqli_num_rows($sql)==0) {
            echo '<tr><td colspan="5">없습니다.</td></tr>';
        }
        //조회되는 기사가 있을 시 제목 자르기, 정보 출력
        else {
            while ($sql_result = mysqli_fetch_array($sql)) {
                echo '<tr><td class="art_num">'.$sql_result['art_num'].'</td>';
                echo '<td class="cate_dt">'.$sql_result['s_name'].'</td>';
                if (mb_strlen($sql_result['title'],'UTF-8')>33)
                    $title = mb_substr($sql_result['title'],0,32,'UTF-8').'..';
                else
                    $title = $sql_result['title'];
                echo '<td class="title_td"><a href="article_view.php?art_num='.$sql_result['art_num'].'"> '.$title.'</a></td>';
                echo '<td class="name_td">'.$sql_result['w_name'].'</td>';
                if ($sql_result['modi_date']=='')
                    echo '<td class="date_td">'.$sql_result['post_date'].'</td>';
                else
                    echo '<td class="date_td">'.$sql_result['modi_date'].'</td>';
                if ($sql_result['ok']==1)
                    $ok='승인';
                else $ok='미승인';
                echo '<td class="ok_td">'.$ok.'</td></tr>';
            }
        }
        ?>
        </tbody>
        <!--//테이블 내용-->
    </table><br>
    <!--//기사 리스트-->


    <!-- 페이징 -->
    <?php
    //모든 페이지
    if (isset($_GET['key_word']) && $_GET['key_word']!='')
    {
        $all_page_query = 'select ceil(count(*)/15) all_page from article a
                           where (select min(a.art_num) from article a)<=art_num
                           and (a.text like "%'.mysqli_real_escape_string($conn,$_GET['key_word']).'%"
                                or a.title like "%'.mysqli_real_escape_string($conn,$_GET['key_word']).'%"
                                or a.sub_title like "%'.mysqli_real_escape_string($conn,$_GET['key_word']).'%"
                                or a.view_title like "%'.mysqli_real_escape_string($conn,$_GET['key_word']).'%")';
        $all_page = mysqli_fetch_array(mysqli_query($conn, $all_page_query))['all_page'];
    }
    else
    {
        $all_page=0;
    }

    //숫자 페이지 시작과 끝 계산
    $start_page = ceil($page/10)*10-9;
    if ($start_page<1)
        $start_page = 1;
    $end_page = ceil($page/10)*10;
    if ($end_page >= $all_page)
        $end_page = $all_page;

    echo '<div style="text-align: center;">';

    //이전 페이지
    echo '<a href="'.$_SERVER['PHP_SELF'].'?';
    if (isset($_GET['key_word'])) echo 'key_word='.htmlspecialchars($_GET['key_word']).'&';
    if (isset($_GET['order'])) echo 'order='.htmlspecialchars($_GET['order']).'&';
    if (floor($page/10)*10-9 <= 1)
        echo 'page=1">이전 </a>';
    else
        echo 'page='.(floor($page/10)*10-9).'">이전 </a>';

    //숫자 페이지
    for ($p = $start_page; $p<=$end_page; $p++){
        if ($all_page==0)
            echo '';
        else {
            if (isset($_GET['page']) && (int)$_GET['page']==$p){
                echo '<a href="' . $_SERVER['PHP_SELF'];
                if (isset($_GET['key_word'])) echo '?key_word=' . htmlspecialchars($_GET['key_word']) . '&';
                if (isset($_GET['order'])) echo 'order='.htmlspecialchars($_GET['order']).'&';
                echo 'page=' . $p . '"><strong>&nbsp;' . $p . '&nbsp;</strong></a>';
            }
            else{
                echo '<a href="' . $_SERVER['PHP_SELF'];
                if (isset($_GET['key_word'])) echo '?key_word=' . htmlspecialchars($_GET['key_word']) . '&';
                if (isset($_GET['order'])) echo 'order='.htmlspecialchars($_GET['order']).'&';
                echo 'page=' . $p . '">&nbsp;' . $p . '&nbsp;</a>';
            }
        }
    }

    //다음 페이지
    echo '<a href="'.$_SERVER['PHP_SELF'].'?';
    if (isset($_GET['key_word'])) echo 'key_word='.htmlspecialchars($_GET['key_word']).'&';
    if (isset($_GET['order'])) echo 'order='.htmlspecialchars($_GET['order']).'&';
    if (ceil($page/10)*10+1 >= $all_page) echo 'page='.$all_page.'"> 다음</a>';
    else
        echo 'page='.(ceil($page/10)*10+1).'"> 다음</a>';

    echo '</div>';
    ?>
    <!--// 페이징 //-->
</article>
</body>
</html>