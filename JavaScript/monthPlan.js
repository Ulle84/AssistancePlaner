function save(button, year, month) {
    button.disabled = true;

    var httpResponse = document.getElementById("httpResponse");

    httpResponse.innerHTML = "";

    var content = "";

    var notes = window.document.getElementById("notes");
    var data = window.document.getElementsByClassName("data");

    for (var i = 0; i < data.length; i++) {
        var dataContent = data[i].getElementsByTagName("td");
        content += dataContent[1].firstChild.value;
        content += " - ";
        content += dataContent[2].firstChild.value;
        content += "\n";
        content += dataContent[3].firstChild.value;
        content += "\n";
        content += dataContent[4].firstChild.value;
        content += "\n";
    }
    content += notes.value;

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            httpResponse.innerHTML = xmlhttp.responseText;
            button.disabled = false;
        }
    }

    xmlhttp.open("POST", "../PHP/monthPlanSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&content=" + content);
}

function notifyTeam(year, month) {
    var httpResponse = document.getElementById("httpResponse");

    httpResponse.innerHTML = "";

    var content = window.document.getElementById("notes").value.replace(new RegExp("\n", 'g'), "<br />");

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            httpResponse.innerHTML = xmlhttp.responseText;
        }
    }

    xmlhttp.open("POST", "../PHP/notifyTeam.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&content=" + content);
}

function validateString(element) {
    if (element.value.indexOf('&') > -1) {
        element.value = element.value.replace(new RegExp('&', 'g'), "");
        alert("Das Zeichen '&' ist ein unerlaubtes Sonderzeichen und wurde entfernt!");
    }
}