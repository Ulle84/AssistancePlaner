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
        element.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.removeChild(element.parentNode.parentNode.parentNode.parentNode.parentNode);
    }
}

function resetPassword(element) {
    var response = document.getElementById("httpResponse");
    response.innerHTML = "";

    var userName = element.parentNode.parentNode.parentNode.firstChild.childNodes[2].textContent;

    //confirmation
    if (!confirm("Passwort von " + userName + " wirklich zurücksetzen?")) {
        return;
    }

    element.disabled = true;

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            element.disabled = false;
        }
    }

    xmlhttp.open("POST", "../PHP/resetPassword.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("userName=" + userName);
}

function newMember() {
    var team = window.document.getElementById("team");

    var div = window.document.createElement("div");
    team.appendChild(div);
    div.setAttribute("class", "businessCard");

    var h1 = window.document.createElement("h1");
    div.appendChild(h1);

    var table = window.document.createElement("table");
    div.appendChild(table);

    var tbody = window.document.createElement("tbody");
    table.appendChild(tbody);

    var tr0 = window.document.createElement("tr");
    var tr1 = window.document.createElement("tr");
    var tr2 = window.document.createElement("tr");
    var tr3 = window.document.createElement("tr");
    var tr4 = window.document.createElement("tr");
    var tr5 = window.document.createElement("tr");
    var tr6 = window.document.createElement("tr");
    var tr7 = window.document.createElement("tr");
    var tr8 = window.document.createElement("tr");
    var tr9 = window.document.createElement("tr");

    tbody.appendChild(tr0);
    tbody.appendChild(tr1);
    tbody.appendChild(tr2);
    tbody.appendChild(tr3);
    tbody.appendChild(tr4);
    tbody.appendChild(tr5);
    tbody.appendChild(tr6);
    tbody.appendChild(tr7);
    tbody.appendChild(tr8);
    tbody.appendChild(tr9);

    var td00 = window.document.createElement("td");
    var td01 = window.document.createElement("td");
    var td02 = window.document.createElement("td");
    var td03 = window.document.createElement("td");
    var td04 = window.document.createElement("td");
    var td05 = window.document.createElement("td");
    var td06 = window.document.createElement("td");
    var td07 = window.document.createElement("td");
    var td08 = window.document.createElement("td");
    var td09 = window.document.createElement("td");
    var td10 = window.document.createElement("td");
    var td11 = window.document.createElement("td");
    var td12 = window.document.createElement("td");
    var td13 = window.document.createElement("td");
    var td14 = window.document.createElement("td");
    var td15 = window.document.createElement("td");
    var td16 = window.document.createElement("td");
    var td17 = window.document.createElement("td");
    var td18 = window.document.createElement("td");
    var td19 = window.document.createElement("td");

    tr0.appendChild(td00);
    tr0.appendChild(td01);
    tr1.appendChild(td02);
    tr1.appendChild(td03);
    tr2.appendChild(td04);
    tr2.appendChild(td05);
    tr3.appendChild(td06);
    tr3.appendChild(td07);
    tr4.appendChild(td08);
    tr4.appendChild(td09);
    tr5.appendChild(td10);
    tr5.appendChild(td11);
    tr6.appendChild(td12);
    tr6.appendChild(td13);
    tr7.appendChild(td14);
    tr7.appendChild(td15);
    tr8.appendChild(td16);
    tr8.appendChild(td17);
    tr9.appendChild(td18);
    tr9.appendChild(td19);

    td00.textContent = "ID";
    td02.textContent = "Vorname";
    td04.textContent = "Nachname";
    td06.textContent = "E-Mail Adresse";
    td08.textContent = "Telefonnummer";
    td10.textContent = "Stichwörter";
    td12.textContent = "Stundenkontigent";
    td14.textContent = "Priorisierung";
    td16.textContent = "Bevorzugte Tage";
    td18.textContent = "Aktionen";

    var input0 = window.document.createElement("input");
    var input1 = window.document.createElement("input");
    var input2 = window.document.createElement("input");

    input0.setAttribute("type", "button");
    input1.setAttribute("type", "button");
    input2.setAttribute("type", "button");

    input0.setAttribute("value", "Editieren");
    input1.setAttribute("value", "Löschen");
    input2.setAttribute("value", "Passwort zurücksetzen");

    input0.setAttribute("onclick", "editMember(this)");
    input1.setAttribute("onclick", "removeMember(this)");
    input2.setAttribute("onclick", "resetPassword(this)");

    td19.appendChild(input0);
    td19.appendChild(input1);
    td19.appendChild(input2);
}

function editMember(button) {
    var cells = button.parentNode.parentNode.parentNode.getElementsByTagName("td");

    var id = cells[1].textContent;
    var firstName = cells[3].textContent;
    var lastName =  cells[5].textContent;
    var eMailAddress =  cells[7].textContent;
    //...

    var inputId = window.document.createElement("input");
    inputId.setAttribute("type", "text");
    inputId.setAttribute("size", "20");
    cells[1].appendChild(inputId);
}

function checkLoginNames() {
    var teamTable = window.document.getElementById("team")
    var businessCards = teamTable.getElementsByClassName("businessCard");

    var loginNames = [];
    for (var i = 0; i < businessCards.length; i++) {
        loginNames.push(businessCards.getAttribute("id"));
    }

    loginNames.sort();
    var last = loginNames[0];
    for (var i = 1; i < loginNames.length; i++) {
        if (loginNames[i] == last) {
            alert("Die Tabelle kann nicht gespeichert werden!\nDie Kennung " + last + " ist doppelt enthalten.");
            return false;
        }
        last = loginNames[i];
    }
    return true;

}


//TODO rework
function saveTeam(button) {
    if (!checkLoginNames()) {
        return;
    }
    button.disabled = true;

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
            button.disabled = false;
        }
    }

    xmlhttp.open("POST", "../PHP/teamSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("content=" + content);
}