<?php
if($recordList){
    foreach($recordList as $list){
        ?>
        API URL : <?php echo $list['api_url']; ?> <br />
        Post Data : <br />
        <?php echo $list['post_data']; ?> <br />
        Response : <br />
        <?php echo $list['api_response']; ?> <br />
        Datetime : <?php echo $list['created']; ?> <br />
        ===========================================================================================================<br />
        <?php
    }
}
?>