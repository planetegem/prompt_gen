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
    listIndex, formIndex;
    
function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function checkCookie() {
    let index = getCookie("listIndex");
    if (index === "") {
        listIndex = 0;
        formIndex = 0;
        document.cookie = "listIndex=0;";
        document.cookie = "formIndex=0;";
        console.log("found");
    }
}
checkCookie();
function selectList(){
    listIndex = getCookie("listIndex");
    document.getElementById("listHeader").innerText = "list of " + tables[listIndex];
    fetchTable(tables[listIndex]);
}
selectList();
function listRight(){
    listIndex = getCookie("listIndex");
    if (listIndex < tables.length - 1){
        listIndex++;
    } else {
        listIndex = 0;
    }
    document.cookie = "listIndex=" + listIndex + ";";
    selectList();
}
document.getElementById("listRight").addEventListener("click", listRight);
function listLeft(){
    listIndex = getCookie("listIndex");
    if (listIndex > 0){
        listIndex--;
    } else {
        listIndex = tables.length - 1;
    }
    document.cookie = "listIndex=" + listIndex + ";";
    selectList();
}
document.getElementById("listLeft").addEventListener("click", listLeft);