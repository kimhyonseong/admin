<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-04-17
 * Time: 오후 3:02
 */
    session_start();
    include_once __DIR__ . '/../DB/DBconnect.php';

    if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['name'] != "") {
        $file_count = count($_FILES['file']['name']);

        //파일 갯수만큼 수행, 파일이 이름이 있다면 파일 검사, 오류 시 페이지 자체를 빠져나감
        for ($i = 0; $i < $file_count; $i++) {
            if (isset($_FILES['file']['tmp_name'][$i]) && $_FILES['file']['name'][$i] != "") { //파일 이름 있는지 확인
                $ext_array = explode('.', 'jpg.gif.jpeg.png'); //확장자 확인
                $file_ext = pathinfo($_FILES['file']['name'][$i]);

                if (!in_array($file_ext['extension'], $ext_array)) {
                    echo '<script>parent.alert("확장자가 부적절합니다."); window.close();</script>';
                    exit();
                } else if ($_FILES['file']['error'][$i] != UPLOAD_ERR_OK) { //그 외의 에러 확인
                    switch ($_FILES['file']['error'][$i]) {
                        case UPLOAD_ERR_INI_SIZE:
                            echo '<script>parent.alert("사진 용량 초과");</script>';
                            exit();
                            break;
                        case UPLOAD_ERR_FORM_SIZE:
                            echo '<script>parent.alert("사진 용량은 2MB까지 입니다.");</script>';
                            exit();
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            echo '<script>parent.alert("파일이 없습니다.");</script>';
                            exit();
                            break;
                        default:
                            echo '<script>parent.alert("파일이 정상적으로 첨부되지 않았습니다.");</script>';
                            exit();
                    }
                }
            } else { //파일 이름이 없다면
                echo '<script>parent.alert("파일이 없습니다.");</script>';
                exit();
            }
        }

        //이전에 이미지를 올렸는지 확인하기
        if (isset($_SESSION['img_name']))
        {
            $old_files = unserialize($_SESSION['img_name']);
        }
        else {
            $file_name = array('');
            $file_old_count = 0;
        }

        //오늘날짜 디렉토리 만들기
        $time_query = mysqli_query($conn, 'select date_format(now(),"%Y") Y,
                                              date_format(now(),"%m") m,
                                              date_format(now(),"%d") d,
                                              date_format(now(),"%H") H,
                                              date_format(now(),"%i") i,
                                              date_format(now(),"%S") S
                                             ');
        $time = mysqli_fetch_array($time_query);
        if (!is_dir(__DIR__.'/img/'.$time['Y'].'/'.$time['m'].'/'.$time['d']))
            mkdir(__DIR__ . '/img/' . $time['Y'].'/'.$time['m'].'/'.$time['d'],0777,true);


        //정상적으로 수행 시 이쪽으로 진입, 파일이름 다시 검사 후 서버 경로에 업로드
        for ($i = 0;$i < $file_count; $i++) {
            $dir = 'img/'.$time['Y'].'/'.$time['m'].'/'.$time['d'];
            $img_route = 'http://localhost/intern/china_focus/admin/DB/'.$dir;

            if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['name'] != '') {
                // img/월/일/none_file
                $file_name[$i] = 'none_'. $time['H'] .$time['i'] .$time['S'] .$_FILES['file']['name'][$i];
                move_uploaded_file($_FILES['file']['tmp_name'][$i], './' .$dir . '/'. $file_name[$i]);

                $img =
                    "<br><div class='center_image' style='width:500px;'><img alt='본문 첨부 이미지' src='http://localhost/intern/china_focus/admin/DB/"
                    .$dir.'/'.$file_name[$i] . "' border='0' hspace='0' vspace='0' width='100%'><br><div class='img_conti'>이미지 설명</div></div><br>";
                echo '<script>parent.insert_img("'.$img.'");</script>';
            }
            if ($i + 1 == $file_count) {
                $_SESSION['img_route'] = $img_route;
                if (isset($old_files)) {
                    $all_files = array_merge($old_files, $file_name);
                    $_SESSION['img_name'] = serialize($all_files);  // 이전 이미지와 이번에 올린 이미지 둘다 저장
                }
                else $_SESSION['img_name'] = serialize($file_name);  //이미지 파일 이름만 세션 배열 저장
            }
        }
    }
?>