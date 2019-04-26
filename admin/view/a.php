<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-03-27
 * Time: 오후 3:40
 */
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
        var count = 1;
        $("#append").click(function(){
            if(count >= 10){
                alert("최대 업로드수는 " + count + "개입니다.");
            } else {
                count++;
                $("#fileArea").append("<input type='file' name='userfile[]' id='userfile" + count + "' />");
            }
        });
        $("#delete").click(function(){
            if(count <= 1){
                alert("최소 업로드수는 " + count + "개입니다.");
            } else {
                $("#userfile" + count).remove();
                count--;
            }
        });


    });
</script>

<button id="append">Append</button>
<button id="delete">Delete</button>

<div id="fileArea">
    <input type="file" name="userfile[]" id="userfile" />
</div>