
let forms = document.getElementById("inputPanel").getElementsByTagName("form");
function switchInput(){
    formIndex = getCookie("formIndex");
    for (let form of forms){
        form.style.display = "none";
    }
    forms[formIndex].style.display = "grid";

}
switchInput();
function inputRight(){
    formIndex = getCookie("formIndex");
    if (formIndex < forms.length-1){
        formIndex++;
        document.cookie = "formIndex=" + formIndex + ";";
        switchInput();
    } else {
        formIndex = 0;
        document.cookie = "formIndex=" + formIndex + ";";
        switchInput();
    }
}
let rightButtons = document.getElementsByClassName("inputRight");
for (let button of rightButtons){
    button.addEventListener("click", inputRight);
}
function inputLeft(){
    formIndex = getCookie("formIndex");
    if (formIndex > 0){
        formIndex--;
        document.cookie = "formIndex=" + formIndex + ";";
        switchInput();
    } else {
        formIndex = forms.length - 1;
        document.cookie = "formIndex=" + formIndex + ";";
        switchInput();
    } 
}
let leftButtons = document.getElementsByClassName("inputLeft");
for (let button of leftButtons){
    button.addEventListener("click", inputLeft);
}