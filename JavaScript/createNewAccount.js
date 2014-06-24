function createNewAccount() {
    var response = document.getElementById("httpResponse");
    response.innerHTML = "";

    var clientName = window.document.getElementById("clientName").value;
    var password = window.document.getElementById("password");
    var passwordRepetition = window.document.getElementById("passwordRepetition");

    if (password.value == "") {
        alert("Passwort darf nicht leer sein!");
        return;
    }

    if (password.value != passwordRepetition.value) {
        alert("Passwort und Wiederholung des Passwortes sind verschieden!\nBitte erneut eingeben!");
        password.value = "";
        passwordRepetition.value = "";
        return;
    }

    //TODO Criterias for password?

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            response.innerHTML = xmlhttp.responseText;
        }
    }

    xmlhttp.open("POST", "../PHP/createNewAccountHandler.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("clientName=" + clientName + "&password=" + password.value);
}

function validateString(element) {
    if (element.value.indexOf('&') > -1) {
        element.value = element.value.replace(new RegExp('&', 'g'), "");
        alert("Das Zeichen '&' ist ein unerlaubtes Sonderzeichen und wurde entfernt!");
    }
}