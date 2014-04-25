function edit(element) {
    element.removeAttribute("onclick");

    var input = window.document.createElement("input");
    input.setAttribute("onchange", "save(this)");
    input.setAttribute("onblur", "save(this)");
    input.value = element.textContent;

    element.textContent = "";
    element.appendChild(input);
    input.select();
}

function save(element) {
    element.removeAttribute("onchange");
    element.removeAttribute("onblur");

    element.parentNode.setAttribute("onclick", "edit(this)");
    element.parentNode.textContent = element.value;
}

function removeMember(element) {
    if (confirm("Person wirklich löschen?")) {
        element.parentNode.parentNode.parentNode.removeChild(element.parentNode.parentNode);
    }
}

function newMember() {
    var team = window.document.getElementById("team");

    var tr = window.document.createElement("tr");
    team.getElementsByTagName("tbody")[0].appendChild(tr);

    for (var i = 0; i < 6; i++) {
        var td = window.document.createElement("td");
        td.setAttribute("class", "left");
        td.setAttribute("onclick", "edit(this)");
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
    var div = window.document.createElement("div");
    div.textContent = "Löschen";
    div.setAttribute("class", "button");
    div.setAttribute("onclick", "removeMember(this)");
    td.appendChild(div);
    tr.appendChild(td);
}

function saveTable() {
    var response = document.getElementById("httpResponse");
    response.innerHTML = "";

    var teamTable = window.document.getElementById("team")

    var rows = teamTable.getElementsByTagName("tr");

    var content = "";
    content += (rows.length - 1) + "\n";

    for (var i = 1; i < rows.length; i++) {
        var data = rows[i].getElementsByTagName("td");
        for (var j = 0; j < data.length; j++) {
            if (j < 6) {
                content += data[j].textContent + "\n";
            }
            if (j == 6) {
                var checkBoxes = data[j].getElementsByTagName("input");
                var contentCheckBoxes = "";
                for (var k = 0; k < checkBoxes.length; k++) {
                    if (checkBoxes[k].checked == true) {
                        if (contentCheckBoxes != "") {
                            contentCheckBoxes += ";";
                        }
                        contentCheckBoxes += checkBoxes[k].value;
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