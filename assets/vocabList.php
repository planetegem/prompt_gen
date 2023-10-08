<?php
include "connect.php";
$table = $_GET["table"];

switch ($table){
    case "nouns":
        getNouns();
        break;
    case "qualifiers":
        getQualifiers();
        break;
    case "quantifiers":
        getQuantifiers();
        break;
    case "connectors":
        getConnectors();
        break;
}

function getNouns(){
    global $conn;
    $sql = "SELECT * FROM nouns ORDER BY type, singular";
    $result = $conn->query($sql);
    
    echo "<div class='tableHeader'>entry</div>";
    echo "<div class='tableHeader'>article</div>";
    echo "<div class='tableHeader'>type</div>";
    while ($row = $result->fetch_assoc()){
        echo "<div class='cell'>" . $row["singular"];
        if ($row["plural"] === ""){
            echo "</div>";
        } else {
            echo " / " . $row["plural"] . "</div>";
        }
        
        if ($row["countable"] === "false"){
            echo "<div class='cell'>noncount</div>";
        } else {
            echo "<div class='cell'>" . $row["article"] . "</div>";
        }

        echo "<div class='cell'>" . $row["type"] . "</div>";
    }
}
function getQualifiers(){
    global $conn;
    $sql = "SELECT * FROM qualifiers ORDER BY form, type, entry";
    $result = $conn->query($sql);
    
    echo "<div class='tableHeader'>entry</div>";
    echo "<div class='tableHeader'>form</div>";
    echo "<div class='tableHeader'>type</div>";
    while ($row = $result->fetch_assoc()){
        echo "<div class='cell'>" . $row["entry"]. "</div>";
        echo "<div class='cell'>" . $row["form"] . "</div>";
        echo "<div class='cell'>" . $row["type"] . "</div>";
    }
}
function getQuantifiers(){
    global $conn;
    $sql = "SELECT * FROM quantifiers ORDER BY type, singular";
    $result = $conn->query($sql);
    
    echo "<div class='tableHeader'>entry</div>";
    echo "<div class='tableHeader'>allowed with</div>";
    echo "<div class='tableHeader'>type</div>";
    while ($row = $result->fetch_assoc()){
        echo "<div class='cell'>" . $row["singular"];
        if ($row["plural"] === ""){
            echo "</div>";
        } else {
            echo " / " . $row["plural"] . "</div>";
        }
        echo "<div class='cell'>" . $row["allowed"] . " nouns</div>";
        echo "<div class='cell'>" . $row["type"] . "</div>";
    }
}
function getConnectors(){
    global $conn;
    $sql = "SELECT * FROM connectors ORDER BY type, req, main";
    $result = $conn->query($sql);
    
    echo "<div class='tableHeader'>entry</div>";
    echo "<div class='tableHeader'>allowed</div>";
    echo "<div class='tableHeader'>type / need</div>";
    while ($row = $result->fetch_assoc()){
        echo "<div class='cell'>" . $row["main"] . "</div>";
        echo "<div class='cell'>";
        if ($row["passive"] === "true" && $row["adverb"] === "true"){
            echo "passive + adverb";
        } else if ($row["passive"] === "true"){
            echo "passive";
        } else if ($row["adverb"] === "true"){
            echo "adverb";
        } else {
            echo "none";
        }
        echo "</div>";
        echo "<div class='cell'>" . $row["type"];
        if ($row["req"] === ""){
            echo "</div>";
        } else {
            echo " / " . $row["req"] . "</div>";
        }
    }
}
?>