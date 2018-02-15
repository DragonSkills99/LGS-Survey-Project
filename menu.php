<?php //class="active" ?>
<?php 
$path = explode("?", $_SERVER['REQUEST_URI'])[0];
?>
<ul class="menu">
    <li>
        <a href="/sp/survey" <?php if($path == "/sp/survey" || $path == "/sp/survey/" || $path == "/sp/survey/index.php") echo 'class="active"'; ?>>
            <span>
                <img alt="home" src="home.png">
                Home
            </span>
        </a>
    </li>
    <?php 
    if(loggedIn() && checkForAdmin()) 
    { ?> 
        <li>
            <a href="/sp/survey/surveyedit.php<?php if(isset($_GET["ID"]) && $path != "/sp/survey/surveyedit.php") echo "?ID=".$_GET["ID"]; ?>" <?php if($path == "/sp/survey/surveyedit.php") echo 'class="active"'; ?>>
                <span>
                    <img alt="edit survey" src="edit.png">
                    Umfrage bearbeiten
                </span>
            </a>
        </li> <?php 
    } 
    ?>
    <?php if(!loggedIn()) 
    { ?> 
        <li>
            <a href="/sp/survey/login.php" <?php if($path == "/sp/survey/login.php") echo 'class="active"'; ?>>
                <span>
                    <img alt="login" src="login.png">
                    Einloggen
                </span>
            </a>
        </li> <?php 
    } 
    ?>
    <?php if(!loggedIn()) 
    { ?> 
        <li>
            <a href="/sp/survey/register.php" <?php if($path == "/sp/survey/register.php") echo 'class="active"'; ?>>
                <span>
                    <img alt="register" src="register.png">
                    Registrieren
                </span>
            </a>
        </li> <?php 
    } 
    ?>
    <?php if(loggedIn()) 
    { ?> 
        <li>
            <a href="/sp/survey/logout.php" <?php if($path == "/sp/survey/logout.php") echo 'class="active"'; ?>>
                <span>
                    <img alt="logout" src="logout.png">
                    Ausloggen
                </span>
            </a>
        </li> <?php
    } 
    ?>
</ul>
<div style="height: 30px;"></div>