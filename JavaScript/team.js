function validateInteger(element, minValue, maxValue) {
    var integerValue = parseInt(element.value);
    if (isNaN(integerValue)) {
        element.value = 0;
        alert("Die Eingabe '" + value + "' ist ungültig und wurde auf 0 gesetzt!");
        return;
    }

    if (integerValue < minValue) {
        element.value = minValue;
        alert("Werte kleiner als " + minValue + " sind nicht zulässig.\nDer Wert wurde auf " + minValue + " gesetzt!");
        return;
    }

    if (integerValue > maxValue) {
        element.value = maxValue;
        alert("Werte größer als " + maxValue + " sind nicht zulässig.\nDer Wert wurde auf " + maxValue + " gesetzt!");
        return;
    }

    element.value = integerValue;
}

function validateString(element) {
    if (element.value.contains("&")) {
        element.value = element.value.replace(new RegExp("&", 'g'), "");
        alert("Das Zeichen '&' ist ein unerlaubtes Sonderzeichen und wurde entfernt!");
    }
}

function removeMember(element) {
    if (confirm("Person wirklich löschen?")) {
        element.parentNode.parentNode.parentNode.removeChild(element.parentNode.parentNode);
    }
}

function resetPassword(element) {
    var response = document.getElementById("httpResponse");
    response.innerHTML = "";

    var userName = element.parentNode.parentNode.firstChild.textContent;

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            response.innerHTML = xmlhttp.responseText;
        }
    }

    xmlhttp.open("POST", "../PHP/resetPassword.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("userName=" + userName);
}

function newMember() {
    var team = window.document.getElementById("team");

    var tr = window.document.createElement("tr");
    team.getElementsByTagName("tbody")[0].appendChild(tr);

    for (var i = 0; i < 8; i++) {
        var td = window.document.createElement("td");
        var input = window.document.createElement("input");

        var size = ["12", "12", "18", "15", "15", "18", "18", "11"];
        var maxLength = "50";

        if (i > 5) {
            maxLength = "3";
        }

        var functionCall = "validateString(this)";
        if (i == 6) {
            functionCall = "validateInteger(this, 0, 999)";
        }

        if (i == 7) {
            functionCall = "validateInteger(this, 1, 999)";
        }

        input.setAttribute("onchange", functionCall);
        input.setAttribute("onblur", functionCall);
        input.setAttribute("type", "text");
        input.setAttribute("size", size[i]);
        input.setAttribute("maxlength", maxLength);
        input.setAttribute("value", "");
        if (i > 5) {
            input.setAttribute("style", "text-align: right");
        }
        td.setAttribute("class", "left");
        td.appendChild(input);
        tr.appendChild(td);
    }

    var weekdays = new Array("Mo", "Di", "Mi", "Do", "Fr", "Sa", "So");
    var tdCheckBoxes = window.document.createElement("td");
    for (var i = 0; i < weekdays.length; i++) {
        var input = window.document.createElement("input");
        input.setAttribute("type", "checkbox");
        input.value = weekdays[i];
        var span = window.document.createElement("span");
        span.textContent = weekdays[i] + " ";
        tdCheckBoxes.appendChild(input);
        tdCheckBoxes.appendChild(span);
    }
    tr.appendChild(tdCheckBoxes);

    var td = window.document.createElement("td");
    td.setAttribute("class", "left");
    var input = window.document.createElement("input");
    input.setAttribute("value", "Löschen");
    input.setAttribute("type", "button");
    input.setAttribute("onclick", "removeMember(this)");
    td.appendChild(input);
    var input2 = window.document.createElement("input");
    input2.setAttribute("value", "Passwort zurücksetzen");
    input2.setAttribute("type", "button");
    input2.setAttribute("onclick", "resetPassword(this)");
    td.appendChild(input2);
    tr.appendChild(td);
}

function checkLoginNames() {
    var teamTable = window.document.getElementById("team")
    var rows = teamTable.getElementsByTagName("tr");

    var loginNames = [];
    for (var i = 1; i < rows.length; i++) {
        var data = rows[i].getElementsByTagName("td");
        loginNames.push(data[0].firstChild.value);
    }

    var forbiddenNames = window.document.getElementsByClassName("forbiddenName");
    for (var i = 0; i < forbiddenNames.length; i++) {
        if (loginNames.indexOf(forbiddenNames[i].textContent) > 1) {
            alert(forbiddenNames[i].textContent + " ist kein erlaubter Login-Name!\nBitte neuen Namen vergeben!");
            return false;
        }
    }

    loginNames.sort();
    var last = loginNames[0];
    for (var i = 1; i < loginNames.length; i++) {
        if (loginNames[i] == last) {
            alert("Die Tabelle kann nicht gespeichert werden!\nDer Login-Name " + last + " ist doppelt enthalten.");
            return false;
        }
        last = loginNames[i];
    }
    return true;

}

function saveTable() {
    if (!checkLoginNames()) {
        return;
    }

    var response = document.getElementById("httpResponse");
    response.innerHTML = "";

    var teamTable = window.document.getElementById("team")

    var rows = teamTable.getElementsByTagName("tr");

    var content = "";
    content += (rows.length - 1) + "\n";

    for (var i = 1; i < rows.length; i++) {
        var data = rows[i].getElementsByTagName("td");
        for (var j = 0; j < data.length; j++) {
            if (j < 8) {
                content += data[j].firstChild.value + "\n";
            }
            if (j == 8) {
                var checkBoxes = data[j].getElementsByTagName("input");
                var contentCheckBoxes = "";
                for (var k = 0; k < checkBoxes.length; k++) {
                    if (contentCheckBoxes != "") {
                        contentCheckBoxes += ";";
                    }
                    if (checkBoxes[k].checked == true) {
                        contentCheckBoxes += 1;
                    }
                    else {
                        contentCheckBoxes += 0;
                    }

                }
                content += contentCheckBoxes + "\n";
            }
        }
    }


    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            response.innerHTML = xmlhttp.responseText;
        }
    }

    xmlhttp.open("POST", "../PHP/teamSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("content=" + content);
}