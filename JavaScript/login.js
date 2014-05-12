function login() {
    var response = document.getElementById("httpResponse");
    response.innerHTML = "";

    var username = window.document.getElementById("username").value;
    var password = window.document.getElementById("password").value;

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if (xmlhttp.responseText == "changePassword") {
                window.location.href = "../PHP/changePassword.php";
                response.innerHTML = "Login erfolgreich - Bitte Passwort wechseln!"
                return;
            }
            if (xmlhttp.responseText == "OK") {
                window.location.href = "../PHP/index.php";
                response.innerHTML = "Login erfolgreich!"
                return;
            }

            response.innerHTML = xmlhttp.responseText;
        }
    }

    xmlhttp.open("POST", "../PHP/loginHandler.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("username=" + username + "&password=" + password);
}