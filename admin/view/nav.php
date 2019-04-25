<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-04-04
 * Time: 오후 2:53
 */
?>

    <!--왼쪽 카테고리-->
    <ul class="menu">
        <li><a href="main.php">Home</a></li>
        <li>
            <!----------- 로그인 정보 --------------->
            <div style="border:1px solid #A6B2BE; width: 130px; padding: 10px;">
                <p>내 정보</p>
                <p class="my_inf"><?= $_SESSION['class'] ?>
                    <?= $_SESSION['name'] ?><br></p>
                <input class="logout" type="button" onclick="location.href='../member/log_out.php'" value="Log out">
            </div>
            <!---------// 로그인 정보 --------------->
        </li>
        <li>
            <!----------- 검색창 --------------->
            <form method="get" action="search.php">
                <input class="search" style="float: right;" type="text" name="key_word" placeholder="search">
            </form>
            <!---------// 검색창 --------------->
        </li>
        <li><a href="post_article.php">글쓰기</a></li>
        <?php
        include_once __DIR__ . '/../DB/DBconnect.php';

        for ($i = 0; $i < 3; $i++) {
            $big_cate = mysqli_query($conn, 'select b_name,big_num from big_cate');
            echo '<li><a href="main.php?ok=' . $i . '"';
            if (isset($_GET['ok']) && $_GET['ok']==(string)$i)
                echo 'style="color: white"';
            if ($i == 1)        echo '>승인 기사</a>';
            else if ($i == 0)   echo '>미승인 기사</a>';
            elseif ($i == 2)    echo '>전체기사</a>';

            //승인 미승인일 때 드롭다운 메뉴 생성
            if ($i == 0 || $i == 1) {
                if (isset($_GET['ok']))
                {
                    if ($_GET['ok']==$i && isset($_GET['big_num']))      //스타일을 교체하여 드롭다운 보이기
                        echo '<ul class="under_clicked">';
                    else    echo '<ul class="under">';
                }
                else echo '<ul class="under">';

                //쿼리 실패 또는 결과값이 0일 경우
                if ($big_cate == false || mysqli_num_rows($big_cate) == 0)
                    echo '카테고리 이상';

                //그 외 (쿼리 성공 시)
                else {   //카테고리 별 이름과 경로 설정 (big_num은 대분류 번호, b_name은 대분류 명칭)
                    while ($big_cate_con = mysqli_fetch_array($big_cate)) {
                        //선택되었다면 하위 카테고리까지 보이기
                        if (isset($_GET['ok']) && $_GET['ok']==$i && isset($_GET['big_num']) && $_GET['big_num']==$big_cate_con['big_num']) {
                            echo '<li><a style="color: white" href="main.php?ok=' . $i . '&big_num=' . $big_cate_con['big_num'] . '">&nbsp' . $big_cate_con['b_name'] . '</a></li>';
                            $small_cate = mysqli_query($conn,'select * from cate where big_num='.$big_cate_con['big_num']);
                            while ($small_cate_con=mysqli_fetch_array($small_cate))
                            {
                                echo '<ul>';
                                echo '<li><a href="main.php?ok=' . $i . '&big_num=' . $big_cate_con['big_num'] . '&code='.$small_cate_con['code'].'">&nbsp&nbsp&nbsp' . $small_cate_con['s_name'] . '</a></li>';
                                echo '</ul>';
                            }
                        }
                        //선택 되지 않은 카테고리들 표기
                        else {
                            echo '<li><a href="main.php?ok=' . $i . '&big_num=' . $big_cate_con['big_num'] . '">&nbsp' . $big_cate_con['b_name'] . '&nbsp&nbsp&nbsp</a></li>';
                        }
                    }
                }
                echo '</ul>';
                echo '</li>';
            }
        }
        ?>
    </ul>
    <!--//왼쪽 카테고리-->
