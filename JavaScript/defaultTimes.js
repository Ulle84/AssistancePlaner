function saveWorkingTimes(button) {
    button.disabled = true;

    var response = document.getElementById("httpResponseWorkingTimes");
    response.innerHTML = "";

    var teamTable = window.document.getElementById("defaultTimes")

    var rows = teamTable.getElementsByTagName("tr");

    var content = "";

    for (var i = 1; i < rows.length; i++) {
        var data = rows[i].getElementsByTagName("td");
        content += data[1].firstChild.value;
        content += " - ";
        content += data[2].firstChild.value;
        content += "\n";
    }


    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        button.disabled = false;
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            response.innerHTML = xmlhttp.responseText;
        }
    }

    xmlhttp.open("POST", "../PHP/defaultTimesSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("content=" + content);
}

function validateString(element) {
    if (element.value.indexOf('&') > -1) {
        element.value = element.value.replace(new RegExp('&', 'g'), "");
        alert("Das Zeichen '&' ist ein unerlaubtes Sonderzeichen und wurde entfernt!");
    }
}