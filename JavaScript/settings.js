function saveSettings(button) {

    button.disabled = true;

    var response = document.getElementById("httpResponse");
    response.innerHTML = "";

    var content = window.document.getElementById("firstName").value;
    content += "\n" + window.document.getElementById("lastName").value;

    if (window.document.getElementById("showToDoManager").checked) {
        content += "\n1";
    }
    else {
        content += "\n0";
    }

    content += "\n" + window.document.getElementById("mailAddress").value;
    content += "\n" + window.document.getElementById("standardPassword").value;

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            response.innerHTML = xmlhttp.responseText;
            button.disabled = false;
        }
    }

    xmlhttp.open("POST", "../PHP/settingsSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("content=" + content);
}