<?php
    /**
     * Created by PhpStorm.
     * User: mk
     * Date: 2019-04-12
     * Time: 오전 10:27
     */ ?>
<div style="margin: 10px;" onclick="close_(); update_html();">
    <div>
        <?php
            $edt = array('0', 'cut', 'outdent', 'indent', 'undo',
                'redo', 'justifyLeft', 'justifyCenter', 'justifyRight', 'insertHorizontalRule',
                'insertOrderedList', 'insertUnorderedList', 'copy', 'paste');  //클릭 한번으로 사용 가능한 기능 (에디터 기능1)

            $item1 = array('bold', 'italic', 'strikeThrough', 'underline');  //클릭 한번으로 사용 가능한 기능 (에디터 기능2)

            $item2 = array('foreColor', 'hiliteColor', 'fontName', 'fontSize', 'createLink');  //클릭 시 아래에 메뉴가 나와야하는 기능

            $color = array('black', 'white', 'red', 'blue', 'green', 'yellow');
            $exec_font = array('돋움', '돋움체', '굴림', '굴림체', '바탕', '바탕체', 'serif', 'sans-serif', 'cursive', 'fantasy');

            $id_count = 0;
            for ($j = 1; $j < count($edt); $j++) {
                ?>
                <!-------------------에디터 기능1---------------->
                <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt=""
                                                                     style="position: relative" title="<?= $edt[$j] ?>"
                                                                     src="edt_img/edit_<?= $j ?>.gif"
                                                                     onclick="htmledit(this.id,'<?= $edt[$j] ?>','0');">
                </div>
                <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;"></div>
                <?php
            }
        ?>
        <br>
        <?php
            for ($j = 0; $j < count($item1); $j++) {
                ?>
                <!-------------------에디터 기능2---------------->
                <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt=""
                                                                     style="position: relative"
                                                                     title="<?= $item1[$j] ?>"
                                                                     src="edt_img/item_<?= $j + 1 ?>.gif"
                                                                     onclick="htmledit(this.id,'<?= $item1[$j] ?>','0');">
                </div>
                <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;"></div>
                <?php
            }
        ?>
        <!--------------------------------------------글 색깔--------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="글자색" src="edt_img/item_5.gif"
                                                             onclick="htmledit(this.id,'foreColor','1')"></div>
        <!--display: inline-block 디스플레이 바꿔줘야함-->
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;">
            <div class="under_table">
                <table style="background-color: white; ">
                    <tr>
                        <?php
                            for ($i = 0; $i < count($color); $i++) { ?>
                                <td onclick="htmledit('<?= $id_count ?>','foreColor','<?= $color[$i] ?>');"
                                    bgcolor="<?= $color[$i] ?>"></td>
                            <?php } ?>
                    </tr>
                </table>
            </div>
        </div>
        <!--------------------------------------------글 배경색--------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="글배경색" src="edt_img/item_6.gif"
                                                             onclick="htmledit(this.id,'hiliteColor','1')"></div>
        <!--display: inline-block 디스플레이 바꿔줘야함-->
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;">
            <div class="under_table" style="">
                <table style="background-color: white; ">
                    <tr>
                        <?php
                            for ($i = 0; $i < count($color); $i++) {
                                ?>
                                <td onclick="htmledit('<?= $id_count ?>','hiliteColor','<?= $color[$i] ?>');"
                                    bgcolor="<?= $color[$i] ?>"></td>
                            <?php } ?>
                    </tr>
                </table>
            </div>
        </div>
        <!--------------------------------------------글꼴--------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="글꼴" src="edt_img/item_7.gif"
                                                             onclick="htmledit(this.id,'fontName','1');"></div>
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;">
            <div class="under_table">
                <table style="background-color: white; width: 300px; text-align: center;">
                    <tr>
                        <?php
                            for ($i = 0; $i < 10; $i++) {
                                if ($i % 3 == 0 && $i != 0)
                                    echo '</tr><tr>';
                                ?>
                                <td onclick="htmledit('<?= $id_count ?>','fontName','<?= $exec_font[$i] ?>');"><?= $exec_font[$i] ?>
                                </td>
                            <?php } ?>
                    </tr>
                </table>
            </div>
        </div>

        <!--------------------------------------------글 사이즈--------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="foreColor" src="edt_img/item_8.gif"
                                                             onclick="htmledit(this.id,'fontsize','1');"></div>
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;">
            <div class="under_table">
                <table style="background-color: white;">
                    <tr><?php
                            for ($i = 0; $i < 7; $i++) { ?>
                                <td onclick="htmledit('<?= $id_count ?>','fontsize','<?= $i + 1 ?>');"><font
                                            size="<?= $i + 1 ?>">가</font></td>
                            <?php } ?>
                    </tr>
                </table>
            </div>
        </div>

        <!--------------------------------------------링크 만들기--------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="createLink" src="edt_img/item_9.gif"
                                                             onclick="htmledit(this.id,'createLink','1');">
        </div>
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;">
            <div class="under_table">
                <table style="background-color: white; width: 300px;">
                    <tr>
                        <td><input placeholder="링크" type="text" name="link" id="link"></td>
                        <td><input type="button" value="링크추가"
                                   onclick="htmledit('<?= $id_count ?>','createLink',document.getElementById('link').value)">
                        </td>
                    </tr>
                </table>
            </div>
        </div>


        <!--------------------------------------------표 만들기--------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="table"
                                                             src="edt_img/item_10.gif" onclick="toggle(this.id);">
        </div>
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;">
            <div class="under_table">
                <table style="background-color: white; width: 300px;">
                    <tr>
                        <td><input placeholder="행" type="text" id="row"></td>
                    </tr>
                    <tr>
                        <td><input placeholder="열" type="text" id="data"></td>
                    </tr>
                    <tr>
                        <td><input type="button" name="createTable" value="만들기"
                                   onclick="makeTable('<?= $id_count ?>',parseInt(document.getElementById('row').value),parseInt(document.getElementById('data').value))">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <!-------------------------------------------- 특수문자(★같은거) 삽입 -------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="img" src="edt_img/item_11.gif"
                                                             onclick="toggle(this.id);"></div>
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;">
            <div class="under_table">
                <table style="background-color: white; width: 300px;">
                    <tr>
                        <td onclick="insert_text(this.id,'&amp;')">&amp;</td>
                        <td onclick="insert_text(this.id,'>')">></td>
                        <td onclick="insert_text(this.id,'<')"><</td>
                        <td onclick="insert_text(this.id,'△')">△</td>
                        <td onclick="insert_text(this.id,'▲')">▲</td>
                        <?php
                            for ($i = 0; $i < 255; $i++) {
                                if ($i % 10 == 5)
                                    echo '</tr><tr>';
                                ?>
                                <td onclick="insert_text(this.id,'&#<?= $i ?>')">&#<?= $i ?></td>
                                <?php
                            }
                        ?>
                    </tr>
                </table>
            </div>
        </div>
        <!--------------------------------------------이모티콘 삽입-------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="emoticon" src="edt_img/item_12.gif"
                                                             onclick="toggle(this.id);"></div>
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;">
            <div class="under_table">
                <table style="background-color: white; width: 300px;">
                    <tr>
                        <?php
                            for ($i = 1; $i <= 30; $i++) {
                                if ($i % 10 == 1 && $i != 1)
                                    echo '</tr><tr>';
                                ?>
                                <td><img src="edt_img/emotions/<?= $i ?>.gif"
                                         onclick="insert_emt('<?= $id_count ?>','edt_img/emotions/<?= $i ?>.gif')"></td>
                                <?php
                            }
                        ?>
                    </tr>
                </table>
            </div>
        </div>

        <!-------------------------------------------- 줌 --------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="zoom"
                                                             src="edt_img/item_131.gif" onclick="toggle(this.id)"></div>
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;">
            <div class="under_table">
                <table style="background-color: white; width: 300px;">
                    <tr>
                        <td><input placeholder="줌 퍼센트" type="text" id="zoom"></td>
                        <td><input type="button" value="확인"
                                   onclick="zoom('<?= $id_count ?>',parseInt(document.getElementById('zoom').value)/100)">
                        </td>
                    </tr>
                </table>
            </div>
        </div>


        <!-------------------------------------------- html 편집기 --------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="html" src="edt_img/item_141.gif"
                                                             onclick="changeDecode();"></div>
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;"></div>


        <!-------------------------------------------- 이미지 삽입 -------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="img" src="edt_img/item_13.gif"
                                                             onclick="toggle(this.id);">
        </div>
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;">
            <div class="under_table">
                <table style="background-color: white; width: 300px;">
                    <tr>
                        <td><input type="button" onclick="add_file();" value="파일 추가"></td>
                        <td><input type="button" onclick="delete_file();" value="파일 삭제"></td>
                    </tr>
                    <tr>
                        <td>
                            <div id="Files">
                                <span id="des1">이미지1 </span><input id="file1" name="file[]" type="file">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" formaction="../DB/insert_img.php" formtarget="another"
                                   formenctype="multipart/form-data"></td>
                    </tr>
                </table>
            </div>
        </div>
        <iframe name="another" src="../DB/insert_img.php" style="display: none"></iframe>
        <!-------------------------------------------- 미디어 삽입 -------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="video" src="edt_img/item_121.gif"
                                                             onclick="toggle(this.id);">
        </div>
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;">
            <div class="under_table" style="">
                <form action="../DB/insert_img.php" method="post" target="another" enctype="multipart/form-data">
                    <table style="background-color: white; width: 300px;">
                        <tr>
                            <td><input type="text" placeholder="영상 주소" id="video"></td>
                            <td><input type="button" value="영상넣기"
                                       onclick="insert_video(document.getElementById('under11').id,document.getElementById('video').value);">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <!-------------------------------------------- 미리보기 -------------------------------------->
        <div style="margin:auto; display: inline-block"><img id="<?= ++$id_count ?>" alt="" style="position: relative"
                                                             title="img" src="edt_img/item_151.gif"
                                                             onclick="data_submit(); pre_view();"></div>
        <div class="under" id="under<?= $id_count ?>" style="display:none; position: absolute;"></div>
    </div>


    <!-------------------------------------------- 폼 --------------------------------------->
    <div>

        <iframe id="htmlframe" name="htmlframe" style="width: 100%; height: 500px;">
        </iframe>
        <script>
            htmlframe.document.designMode = "On";
            htmlframe.document.body.setAttribute('id', 'body');
            htmlframe.document.body.setAttribute('onkeyup', 'parent.update_html(); parent.F5();');
            htmlframe.document.body.setAttribute('onclick', 'parent.close_all(); parent.update_html(); ');
            htmlframe.document.head.innerHTML = "<link rel='stylesheet' type='text/css' href='http://common.mk.co.kr/common/css/2017/chinafocus_ver2.css?1'>";
            <?php
            if (isset($_GET['art_num']) && isset($art_content))
            {
            $img_query = mysqli_query($conn, 'select * from img where art_num=' . (int)mysqli_real_escape_string($conn, $_GET['art_num']));
            $i = 0;

            while ($img_content = mysqli_fetch_array($img_query)) {
                $art_content['text'] = str_replace("<!--img$i-->",
                    ("<br><div class='center_image' style='width:500px;'>
                    <img alt='본문 첨부 이미지' src='".$img_content['img_url']."' border='0' hspace='0' vspace='0' width='100%'>
                    <br><div class='img_conti'>".$img_content['description']."</div></div><br>"),
                    $art_content['text']);
                $i++;
            }
            //$art_content['text'] = str_replace("'","\'",$art_content['text']);
            $art_content['text'] = str_replace("\n","<br>",$art_content['text']);
            ?>
            htmlframe.document.body.innerHTML = <?=json_encode($art_content['text'])?>;
            <?php }   ?>
        </script>
        <input type="hidden" name="text">


        <div id="decode_des"></div>
        <iframe id="code_edt" name="code_edt" style="display: none"></iframe>
        <script>
            code_edt.document.designMode = "On";
            code_edt.document.body.setAttribute('id', 'body2');
            code_edt.document.body.setAttribute('onkeyup', 'parent.update_text();');
            code_edt.document.body.setAttribute('onclick', 'parent.close_all(); parent.update_text();');
            code_edt.document.head.innerHTML = "<style>body{color: white; background-color: black;}</style>";
        </script>
    </div>
</div>

