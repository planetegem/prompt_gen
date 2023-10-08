<?php
if (!isset($_SESSION["tryingDB"])){
    unset($_SESSION["feedback"]);
}
if (array_key_exists("noun", $_POST)){
    // 1. CHECK IF MINIMUM SINGULAR IS FILLED IN 
    if (empty($_POST["singular"])){
        $_SESSION["feedback"] = "Enter at least a singular form. If only plural form: insert as singular with type 'plural'";
    } else {
        // 2. DETERMINE ARTICLE
        if (array_key_exists("definite", $_POST) && array_key_exists("indefinite", $_POST)){
            $article = "any";
        } else if (array_key_exists("definite", $_POST)){
            $article = "definite";
        } else if (array_key_exists("indefinite", $_POST)){
            $article = "indefinite";
        } else {
            $article = "none";
        }
        // 3. VALIDATE OTHER VALUES
        $singular = validateInput($_POST["singular"]);
        $plural = validateInput($_POST["plural"]);
        $type = validateInput($_POST["nounType"]);
        
        if (array_key_exists("countable", $_POST)){
            $countable = "true";
        } else {
            $countable = "false";
        }

        // 4. CHECK IF ENTRY EXISTS AND UPDATE
        $query = $conn->query("SELECT singular FROM nouns WHERE singular = '" . $singular . "'");
        if ($query->num_rows > 0){
            $sql = "UPDATE nouns SET plural = '" . $plural . "', type = '" . $type . "', 
                    article = '" . $article . "', countable = '" . $countable . "', user = '" . $_SESSION["user"] . "'
                    WHERE singular = '" . $singular . "'";
            if ($conn->query($sql) === TRUE){
                $_SESSION["feedback"] = "Record updated.";
                $_SESSION["nounType"] = $type;
            } else {
                $_SESSION["feedback"] = "Error updating record: " . $conn->error;
            }
        } else {
            // 5. IF NOT, ADD AS NEW
            $query = $conn->query(
                    "INSERT INTO nouns(singular, plural, type, article, countable, user)
                    VALUES ('" . $singular . "', '" . $plural . "', '" . $type . "', '" . $article . "', countable = '" . $countable . "', '" . $_SESSION["user"] . "')"
                    );
            if ($query === TRUE){
                $_SESSION["feedback"] = "Record added.";
                $_SESSION["nounType"] = $type;
            } else {
                $_SESSION["feedback"] = "Error adding record: " . $conn->error;
            }
        }   
    }
    $delete = $conn->query("DELETE FROM nouns WHERE type='delete'");
    
    $_SESSION["tryingDB"] = true;
    header("location: index.php");
    exit;
}
if (array_key_exists("qualifier", $_POST)){
    // 1. CHECK IF SOMETHING IS FILLED IN
    if (empty($_POST["qualEntry"])){
        $_SESSION["feedback"] = "No value has been entered.";
    } else {
        // 2. VALIDATE INPUT
        $entry = validateInput($_POST["qualEntry"]);
        $form = $_POST["form"];
        $type = validateInput($_POST["qualType"]);
        
        // 3. CHECK IF ENTRY EXISTS AND UPDATE
        $query = $conn->query("SELECT entry FROM qualifiers WHERE entry = '" . $entry ."'");
        if ($query->num_rows > 0){
            $sql = "UPDATE qualifiers 
                    SET form = '" . $form . "', type = '" . $type . "', user = '" . $_SESSION["user"] . "'
                    WHERE entry = '" . $entry . "'";
            if ($conn->query($sql) === TRUE){
                $_SESSION["feedback"] = "Record updated.";
            } else {
                $_SESSION["feedback"] = "Error updating record: " . $conn->error;
            }
        } else { 
            // 4. IF NOT, ADD AS NEW
            $query = $conn->query(
                "INSERT INTO qualifiers(entry, form, type, user)
                VALUES ('" . $entry . "', '" . $form . "', '" . $type . "', '" . $_SESSION["user"] . "')"
                );
            if ($query === TRUE){
                $_SESSION["feedback"] = "Record added.";
            } else {
                $_SESSION["feedback"] = "Error adding record: " . $conn->error;
            }
        }
    }
    $delete = $conn->query("DELETE FROM qualifiers WHERE type='delete'");
    
    $_SESSION["tryingDB"] = true;
    header("location: index.php");
    exit;
}
if (array_key_exists("quantifier", $_POST)){
    // 1. CHECK IF SOMETHING IS FILLED IN
    if (empty($_POST["quantSingular"])){
        $_SESSION["feedback"] = "Enter at least a singular form.";
    } else if(!array_key_exists("count", $_POST) && !array_key_exists("noncount", $_POST)) {
        $_SESSION["feedback"] = "Quantifier needs to be allowed with at least one noun type.";
    } else {
        // 2. VALIDATE INPUT
        $singular = validateInput($_POST["quantSingular"]);
        $plural = validateInput($_POST["quantPlural"]);
        $type = validateInput($_POST["quantType"]);

        if (array_key_exists("count", $_POST) && array_key_exists("noncount", $_POST)){
            $allowed = "any";
        } else if (array_key_exists("count", $_POST)){
            $allowed = "count";
        } else if (array_key_exists("noncount", $_POST)){
            $allowed = "noncount";
        }
        
        // 3. CHECK IF ENTRY EXISTS AND UPDATE
        $query = $conn->query("SELECT singular FROM quantifiers WHERE singular = '" . $singular ."'");
        if ($query->num_rows > 0){
            $sql = "UPDATE quantifiers 
                    SET plural = '" . $plural . "', allowed = '" . $allowed . "', type = '" . $type . "', user = '" . $_SESSION["user"] . "'
                    WHERE singular = '" . $singular . "'";
            if ($conn->query($sql) === TRUE){
                $_SESSION["feedback"] = "Record updated.";
            } else {
                $_SESSION["feedback"] = "Error updating record: " . $conn->error;
            }
        } else { 
            // 4. IF NOT, ADD AS NEW
            $query = $conn->query(
                "INSERT INTO quantifiers(singular, plural, allowed, type, user)
                VALUES ('" . $singular . "', '" . $plural . "', '" . $allowed . "', '" . $type . "', '" . $_SESSION["user"] . "')"
                );
            if ($query === TRUE){
                $_SESSION["feedback"] = "Record added.";
            } else {
                $_SESSION["feedback"] = "Error adding record: " . $conn->error;
            }
        }
    }
    $delete = $conn->query("DELETE FROM quantifiers WHERE type='delete'");
    
    $_SESSION["tryingDB"] = true;
    header("location: index.php");
    exit;
}

if (array_key_exists("connector", $_POST)){
    // 1. CHECK IF SOMETHING IS FILLED IN
    if (empty($_POST["mainEntry"])){
        $_SESSION["feedback"] = "No value has been entered.";
    } else {
        // 2. VALIDATE INPUT
        $mainEntry = validateInput($_POST["mainEntry"]);
        $type = validateInput($_POST["connectorType"]);
        $req = validateInput($_POST["connectorRequirement"]);
        $keyEntry = $mainEntry;

        if (array_key_exists("passive", $_POST)){
            $passive = "true";
        } else {
            $passive = "false";
        }
        if (array_key_exists("adverb", $_POST)){
            $adverb = "true";
        } else {
            $adverb = "false";
        }
        
        // 3. CHECK IF ENTRY EXISTS AND UPDATE
        $query = $conn->query("SELECT keyEntry FROM connectors WHERE keyEntry = '" . $keyEntry . "'");
        if ($query->num_rows > 0){
            $sql = "UPDATE connectors 
                    SET main = '" . $mainEntry . "', passive = '" . $passive . "', adverb = '" . $adverb . "', req = '" . $req . "', type = '" . $type . "', user = '" . $_SESSION["user"] . "'
                    WHERE keyEntry = '" . $keyEntry . "'";
            if ($conn->query($sql) === TRUE){
                $_SESSION["feedback"] = "Record updated.";
            } else {
                $_SESSION["feedback"] = "Error updating record: " . $conn->error;
            }
        } else { 
            // 4. IF NOT, ADD AS NEW
            $query = $conn->query(
                "INSERT INTO connectors(keyEntry, main, type, req, user, passive, adverb)
                VALUES ('" . $keyEntry . "', '" . $mainEntry . "', '" . $type . "', '" . $req . "', '" . $_SESSION["user"] . "', '" . $passive . "', '" . $adverb . "')"
                );
            if ($query === TRUE){
                $_SESSION["feedback"] = "Record added.";
            } else {
                $_SESSION["feedback"] = "Error adding record: " . $conn->error;
            }
        }
    }
    $delete = $conn->query("DELETE FROM connectors WHERE type='delete'");
    
    $_SESSION["tryingDB"] = true;
    header("location: index.php");
    exit;
}

?>