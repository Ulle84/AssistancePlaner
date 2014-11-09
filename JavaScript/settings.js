function saveSettings() {
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

    xmlhttp.open("POST", "../PHP/settingsSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("content=" + content);
}

function saveWorkingTimes() {
    var teamTable = window.document.getElementById("defaultTimes")

    var rows = teamTable.getElementsByTagName("tr");

    var content = "";

    for (var i = 1; i < rows.length; i++) {
        var data = rows[i].getElementsByTagName("td");
        content += data[1].firstChild.value + ":" + data[1].firstChild.nextSibling.value;
        content += " - ";
        content += data[2].firstChild.value + ":" + data[2].firstChild.nextSibling.value;
        content += "\n";
    }

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.open("POST", "../PHP/defaultTimesSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("content=" + content);
}

window.addEventListener("beforeunload", function (e) {
    saveSettings();
    saveWorkingTimes();
});