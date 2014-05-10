function changePassword() {
    var response = document.getElementById("httpResponse");
    response.innerHTML = "";

    var userName = window.document.getElementById("userName").textContent;
    var oldPassword = window.document.getElementById("oldPassword");
    var newPassword = window.document.getElementById("newPassword");
    var newPasswordRepetition = window.document.getElementById("newPasswordRepetition");

    if (newPassword.value == "") {
        alert("Neues Passwort darf nicht leer sein!");
        return;
    }

    if (newPassword.value != newPasswordRepetition.value) {
        alert("Neues Passwort und Wiederholung des neuen Passwortes sind verschieden!\nBitte erneut eingeben!");
        newPassword.value = "";
        newPasswordRepetition.value = "";
        return;
    }

    //TODO Criterias for password?

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            response.innerHTML = xmlhttp.responseText;
        }
    }

    xmlhttp.open("POST", "../PHP/changePasswordSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("userName=" + userName + "&oldPassword=" + oldPassword.value + "&newPassword=" + newPassword.value);
}

function validateString(element) {
    if (element.value.contains("&")) {
        element.value = element.value.replace(new RegExp("&", 'g'), "");
        alert("Das Zeichen '&' ist ein unerlaubtes Sonderzeichen und wurde entfernt!");
    }
}