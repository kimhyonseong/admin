<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-03-25
 * Time: 오후 2:28
 */
session_start();
if (!isset($_SESSION['class']))
    header('location: ../index.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>기사 뷰</title>
    <script type='text/javascript' src="http://news.mk.co.kr/v4/lib/js/header.js"></script>
    <link rel='stylesheet' type='text/css' href='http://common.mk.co.kr/common/css/2017/chinafocus_ver2.css'>
    <link rel="stylesheet" href="../css/header.css?v=0">
</head>
<style>
    #body{
        margin: auto;
        width: 750px;
    }
</style>
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
        <div class="content_left">

            <?php
            include_once __DIR__ . '/../DB/DBconnect.php';

            //기사 번호가 없으면 잘못된 경로
            if (isset($_GET['art_num']))
                $art_num = mysqli_real_escape_string($conn, htmlspecialchars($_GET['art_num']));
            else
                echo '<script>alert("잘못된 페이지입니다."); history.back();</script>';


            //쿼리문 실패 및 결과가 없을때 경고문 출력
            $sql = mysqli_query($conn, 'select * from article a where a.art_num=' . $art_num);
            if ($sql == false || mysqli_num_rows($sql) == 0) {
                echo '<script>alert("해당 기사가 존재하지 않습니다.."); history.back();</script>';
            }

            //쿼리 성공 시
            else
            {
                $sql_result = mysqli_fetch_array($sql);

                //중요도 표기
                if ($sql_result['ok']==1)
                    echo '<h1>중요도 : '.$sql_result['import'].'</h1>';
                else
                    echo '<h1>요청 중요도 : '.$sql_result['import'].'</h1>';

                //--------------------------------헤드라인처럼 보이기(썸네일 확인용)
                {
                    $head_img = mysqli_fetch_array(mysqli_query($conn, 'select img_url from img where art_num=' . $sql_result['art_num'] . ' and img_order=0'))['img_url'];
                    echo '<div class="main_headline">';
                    echo '<div class="headline_thumb">';
                    echo '<img src=\'' . $head_img . '\' alt=\'헤드라인기사 포토\' width="450" height="265" ><span class="frame_g"></span></div>';
                    echo '<div class="headline_art">';
                    echo '<h2 class="main_tit">' . $sql_result['title'] . '</h2>';

                    //첫번째 줄바꿈 찾고 그 줄바꿈의 위치까지만 보여줌
                    $summary = $sql_result['text'];
                    $summary = strip_tags($summary);

                    if (mb_strlen($summary,'UTF-8')>20)
                        $summary = mb_substr($summary, 0, strpos($summary, "\n",20));

                    //글자수가 150이상일 시 ..찍어서 보여줌
                    if (mb_strlen($summary,'UTF-8') >= 150) {
                        $summary = mb_substr($summary, 0, 150,'UTF-8') . '..';
                    }
                    else $summary = $summary.'..';

                    //서머리 보이기
                    echo '<span class="main_txt">' . $summary . '</span> </div>';
                    echo '</div>';
                }
                //----------------------------------헤드라인 끝

                //-------카테고리 보이기
                $category=mysqli_fetch_array(mysqli_query($conn,
                    'select c.code,c.s_name,a.art_num from cate c 
                            join article a on c.code=a.code 
                            and a.art_num='.(int)mysqli_real_escape_string($conn,$_GET['art_num'])));
                echo '<br><h2>카테고리: '.$category['s_name'].'</h2>';

                //--------<!-- 기사뷰 타이틀 -->
                echo '<div class="view_title">';
                echo '<h3>' . $sql_result['title'] . '</h3>';
                echo '<h4 class=\'sub_tit\'>' . $sql_result['sub_title'] . '</h4>';
                echo '<div class="view_top">';
                echo '<div class="inputtime"> <span class=\'sm_tit\'>기사입력</span> <span class=\'sm_num\'>' . $sql_result['post_date'] . '</span> <span style=\'color:#dfddde;padding:0 2px 0 2px\'>|</span>';
                if ($sql_result['modi_date'] != null) {
                    echo '<span class=\'sm_tit\'>최종수정</span> <span class=\'sm_num\'>' . $sql_result['modi_date'] . '</span></div></div>';
                } else
                    echo '<span class=\'sm_tit\'>최종수정</span> <span class=\'sm_num\'>' . $sql_result['post_date'] . '</span></div></div>';

                //<!-- 공유 버튼 -->
                echo '<div class="sns_right">
				    <ul>
					<li><a href="#"><img src="http://img.mk.co.kr/main/2015/mk_new/ic_print.gif" alt="프린트"></a></li>
					<li><a href="#"><img src="http://img.mk.co.kr/main/2015/mk_new/ic_facebook.gif" alt="페이스북" class="sns_section_li" sns_name="facebook"></a></li>
					<li><a href="#"><img src="http://img.mk.co.kr/main/2015/mk_new/ic_twitter.gif" alt="트위터" class="sns_section_li" sns_name="twitter"></a></li>
					<li><a href="#"><img src="http://img.mk.co.kr/main/2015/mk_new/ic_castory.gif" alt="카카오스토리"></a></li>
					<li><a href="#"><img src="http://img.mk.co.kr/main/2015/mk_new/ic_other.gif" alt="공유"></a></li>
				</ul>
			        </div>';
                //<!--// 공유 버튼 -->

                echo '</div>';
                //-----------<!--// 기사뷰 타이틀 -->

                //----------------------------------------------------기사 본문 시작
                echo '<div class=\'view_txt\'>';
                $text = $sql_result['text'];
                $img_sql = mysqli_query($conn, 'select * from img where art_num=' . $art_num . ' order by img_order asc');

                if ($img_sql == false || mysqli_num_rows($img_sql)==0) {
                    echo '';
                }
                else {
                    //--------------------------이미지 보이기
                    $i = 0;
                    while ($img_result = mysqli_fetch_array($img_sql)) {
                        if ($i==$img_result['img_order']) {
                            $text = str_replace("<!--img$i-->",
                                "<div class='center_image' style='width:500px;'><img src='" . $img_result['img_url'] . "' border='0' hspace='0' vspace='0' width='100%' alt='본문 첨부 이미지'><br><div class='img_conti'>" . $img_result['description'] . "</div></div>", $text);
                        }
                        $i++;
                    }
                    //--------------------------//이미지 보이기 끝
                }
                //------------------------------------------------줄 바꿈 고치고 본문 보이기
                echo str_replace("\n", '<br>', $text) . '</div>';
                echo '<!--태그 수정으로 인한 안보임 방지//-->';
                //-------------------------------기사 본문 끝
                //------------------------------------------------작성자 또는 관리자라면 수정하기 버튼 보이기, 관리자는 승인까지 보이기
                if ($_SESSION['class'] == '관리자') {
                    echo '<input type="button" value="수정 및 삭제" onclick="location.href=\'modify.php?art_num=' . $sql_result['art_num'] .'\'"><br>';
                    echo '<form method="post" >';
                    echo '중요도 : <input type="number" min="0" max="100" value="'.$sql_result['import'].'" name="import" placeholder="숫자">';
                    echo '&nbsp;<input type="submit" value="중요도 수정 및 승인" formaction="../DB/admission.php">';
                    if ($sql_result['novel']==1)
                        echo '&nbsp;<input type="submit" value="소설 연재 취소" formaction="../DB/novel_no_admission.php">';
                    else
                        echo '&nbsp;<input type="submit" value="소설 연재 승인" formaction="../DB/novel_admission.php">';
                    echo '<input type="hidden" name="art_num" value="'.$art_num.'">';
                    echo '</form>';
                }
                elseif ($sql_result['w_name']!='') {
                    if (strpos($_SESSION['name'], $sql_result['w_name'])!==false) {
                        echo '<input type="button" value="수정 및 삭제" onclick="location.href=\'modify.php?art_num=' . $sql_result['art_num'] . '\'"><br>';
                    }
                }
            }

            ?>
        </div>
        <!--// left -->
    </div>
    <!--// container -->
</div>
</body>
</html>
