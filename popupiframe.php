<div class="popup">
    <div class="topbar">
        <div class="img" onclick="closeiframe()"></div>
    </div>
    <?php 
        $url = "login.php";
        if(isset($_GET["url"])){
            $url = $_GET["url"];
        }
    ?>
    <iframe src="<?php echo $url; ?>"></iframe>
</div>
<script type="text/javascript">
    function closeiframe(){
        var ifs = document.getElementsByClassName("popup");
        for(var i = 0; i < ifs.length; i++){
            var ifr = ifs[i];
            ifr.parentNode.removeChild(ifr);
        }
    }
</script>