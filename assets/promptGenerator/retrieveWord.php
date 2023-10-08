<?php
$request = $_GET["request"];
$condition = $_GET["condition"];

function unpackFilter($filter){
    $filters = explode("~", $filter);
    
    if ($filters[1] === "forbid"){
        if ($_GET["request"] === "adjective"){
            $answer = "NOT type = 'magnifier'";
        } else {
            $answer = "NOT type = 'placeholder'";
        }
        foreach ($filters as $value){
            if ($value === "filter" || $value === "forbid"){
                continue;
            } else {
                $answer .= " AND NOT type = '" . $value . "'";
            }
        }
    } else if ($filters[1] === "allow"){
        $answer = "type = 'placeholder'";
        foreach ($filters as $value){
            if ($value === "filter" || $value === "forbid"){
                continue;
            } else {
                $answer .= " OR type = '" . $value . "'";
            }
        }
    }
    return $answer;
}
function unpackFilterQuant($filter){
    $filters = explode("~", $filter);
    
    if ($filters[1] === "forbid"){

        if ($filters[2] === "count"){
            $answer = "NOT allowed = 'count'";
        } else {
            $answer = "NOT allowed = 'noncount'";
        }
        foreach ($filters as $value){
            if ($value === "filter" || $value === "forbid"){
                continue;
            } else {
                $answer .= " AND NOT type = '" . $value . "'";
            }
        }
    } else if ($filters[1] === "allow"){
        $answer = "type = 'placeholder'";
        foreach ($filters as $value){
            if ($value === "filter" || $value === "forbid"){
                continue;
            } else {
                $answer .= " OR type = '" . $value . "'";
            }
        }
    }
    return $answer; 
}

switch ($request){
    case "noun":
         if (strpos($condition, "filter") === 0){
            $filter = unpackFilter($condition);
            $result = $conn->query(
            "SELECT singular, plural, type, article, countable FROM nouns WHERE " . $filter . " ORDER BY RAND() LIMIT 2"
            );
        } else {
            $result = $conn->query(
            "SELECT singular, plural, type, article, countable FROM nouns ORDER BY RAND() LIMIT 1"
            );
        }
        $word = $result->fetch_assoc();
        echo json_encode($word);
        break;
    case "adjective":
        if (strpos($condition, "filter") === 0){
            $filter = unpackFilter($condition);
            $result = $conn->query(
            "SELECT entry, type, form FROM qualifiers WHERE form = 'adjective' AND " . $filter . " ORDER BY RAND() LIMIT 2"
            );
        } else {
            $result = $conn->query(
            "SELECT entry, type, form FROM qualifiers WHERE form = 'adjective' AND NOT type = 'magnifier' ORDER BY RAND() LIMIT 2"
            );
        }
        $word = $result->fetch_assoc();
        $word2 = $result->fetch_assoc();
        $result = $conn->query(
            "SELECT entry, type, form FROM qualifiers WHERE form = 'adjective' AND type = 'magnifier' ORDER BY RAND() LIMIT 1"
            );
        $word3 = $result->fetch_assoc();
        echo "[" . json_encode($word) . ", " . json_encode($word2) . ", " . json_encode($word3) . "]";
        break;
    case "quantifier":
        if (strpos($condition, "filter") === 0){
            $filter = unpackFilterQuant($condition);
            $result = $conn->query(
                "SELECT singular, plural, allowed, type FROM quantifiers WHERE " . $filter . " ORDER BY RAND() LIMIT 1"
            );
        } else if ($condition === "simple"){
            $result = $conn->query(
                "SELECT singular, plural, allowed, type FROM quantifiers WHERE type = '" . $condition . "' ORDER BY RAND() LIMIT 1"
            );
        } else {
            $result = $conn->query(
                "SELECT singular, plural, allowed, type FROM quantifiers WHERE NOT allowed = 'noncount' AND NOT type = 'fusion' ORDER BY RAND() LIMIT 1"
            );
        }
        $word = $result->fetch_assoc();
        echo json_encode($word);
        break;
    case "connector":
        if (strpos($condition, "filter") === 0){
            $filter = unpackFilter($condition);
            $result = $conn->query(
                "SELECT main, preposition, passive, adverb, req, type FROM connectors WHERE " . $filter . " OR type = 'simple' ORDER BY RAND() LIMIT 1"
            );
        } else {
            $result = $conn->query(
                "SELECT main, preposition, passive, adverb, req, type FROM connectors ORDER BY RAND() LIMIT 1"
            );
        }
        $word = $result->fetch_assoc();
        echo json_encode($word);
        break;
    case "adverb":
        if (strpos($condition, "filter") === 0){
            $filter = unpackFilter($condition);
            $result = $conn->query(
            "SELECT entry, type, form FROM qualifiers WHERE form = 'adverb' AND (" . $filter . ") ORDER BY RAND() LIMIT 1"
            );
        } else {
            $result = $conn->query(
            "SELECT entry, type, form FROM qualifiers WHERE form = 'adverb' ORDER BY RAND() LIMIT 1"
            );
        }
        $word = $result->fetch_assoc();
        echo json_encode($word);
        break;
}
?>