function fetchTable(table){
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function(){
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("wordTable").innerHTML = this.responseText;
        }
    };
    xml.open("GET", "assets/vocabList.php?table="+table, true);
    xml.send();
}

let tables = ["nouns", "qualifiers", "quantifiers", "connectors"],
    listIndex = 0;
    

function selectList(){
    document.getElementById("listHeader").innerText = "list of " + tables[listIndex];
    fetchTable(tables[listIndex]);
}
selectList();
function listRight(){
    if (listIndex < tables.length - 1){
        listIndex++;
    } else {
        listIndex = 0;
    }
    selectList();
}
document.getElementById("listRight").addEventListener("click", listRight);
function listLeft(){
    if (listIndex > 0){
        listIndex--;
    } else {
        listIndex = tables.length - 1;
    }
    selectList();
}
document.getElementById("listLeft").addEventListener("click", listLeft);