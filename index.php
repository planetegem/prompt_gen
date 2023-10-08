<?php session_start();?>
<?php
	include "connection.php";
    function validateInput($key){
        $key = trim($key);
        $key = stripslashes($key);
        $key = htmlspecialchars($key);
        return $key;
    }
    if (!isset($_SESSION["connecting"])){
        unset($_SESSION["logStatus"]); 
    }
    if (array_key_exists("connect", $_POST)){
        $name = validateInput($_POST["username"]);
        $pass = validateInput($_POST["password"]);
        
        $result = $conn->query(
                "SELECT password FROM AdminList
                WHERE username = '" . $name . "'"
                );
        
        if($result->num_rows > 0){ //FOUND USERNAME
            $result = $result->fetch_assoc();
            if ($result["password"] === $pass){
                $_SESSION["logStatus"] = "connection established";
                $_SESSION["connected"] = true;
                $_SESSION["connecting"] = true;
            } else {
                $_SESSION["logStatus"] = "username and/or password incorrect";
                $_SESSION["connected"] = false;
                $_SESSION["connecting"] = true;
            }
        } else {
            $_SESSION["logStatus"] = "username and/or password incorrect";
            $_SESSION["connected"] = false;
            $_SESSION["connecting"] = true;
        }
        header("location: index.php");
        exit;
    } else if (array_key_exists("disconnect", $_POST)){
        $_SESSION["connected"] = false;
        $_SESSION["logStatus"] = "connection severed";
        $_SESSION["connecting"] = true;
        
        header("location: index.php");
        exit;
    }
    if ($_SESSION["connected"]){
        include "assets/inputPanelHeader.php";
    }
    
?> 

<!DOCTYPE html>
<html xmlns="https://www.w3.org/1999/xhtml" lang="nl" xml:lang="nl">
<head>
    <meta http-equiv='content-type' content='text/html; charset=UTF-8'>
    <meta name="description" content="Planetegem">
    <meta name="keywords" content="keywords">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="googlebot" content="notranslate">
    <title>Planetegem</title>
    <link rel="stylesheet" href="css.css">
</head>
<body>
<main>
    <section id="promptField">
        <div class="container">
            <div id="targetField"></div>
            <button id="getPrompt" class="button" onclick="getPrompt()">get prompt</button>
            <div id="footer">
                <hr class="divider">
                <button id="returnbutton" onclick="location.href='http://www.planetegem.be'" type="button">
                    <img id="return">
                    <h4>return&nbsp;to<br>planetegem</h4>
                </button>
                <p>&#169; 2023 Niels Van Damme | info@planetegem.be | <a href="https://www.instagram.com/planetegem/" style="text-decoration:none;color:black;">www.instagram.com/planetegem</a></p>
            </div>
        </div>

    </section>
    <section id="wordList">
        <div class="header">
            <div id="listLeft">
                <img src="assets/images/leftArrow.svg">
            </div>
            <h2 id="listHeader">list of nouns</h2>
            <div id="listRight">
                <img src="assets/images/rightArrow.svg">
            </div>
        </div>
        <div id="wordTable"></div>
        <div id="devSection">
            <form id="connection" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <?php if ($_SESSION["connected"]){
                    echo '<input type="submit" value="disconnect" name="disconnect" class="button" id="disconnect">';
                } else {
                    echo '<div>';
                    echo '<label for="username">username:</label>';
                    echo '<input type="text" name="username" id="username" size="9">';
                    echo '</div>';
                    echo '<div>';
                    echo '<label for="password">password:</label>';
                    echo '<input type="password" name="password" id="password" size="9">';
                    echo '</div>';
                    echo '<input type="submit" value="connect" name="connect" class="button" id="connect">';
                }?>
                <span id="logStatus"><?php echo $_SESSION["logStatus"];?></span>
            </form>
            <?php if($_SESSION["connected"]){include "assets/inputPanelBody.php";}?>
        </div>
    </section>
</main>
<script src="assets/promptGenerator/commons.js"></script>
<script src="assets/promptGenerator/promptGenerator.js"></script>
<?php 
    if($_SESSION["connected"]){
        echo '<script src="assets/vocabList.js"></script>';
        echo '<script src="assets/inputPanel.js"></script>';
    } else {
        echo '<script src="assets/vocabList-nocookie.js"></script>';
    }
?>
</body>
<?php unset($_SESSION["connecting"]);?>
<?php unset($_SESSION["tryingDB"]);?>