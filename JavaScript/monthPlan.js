function save() {

    var httpResponse = document.getElementById("httpResponse");

    httpResponse.innerHTML = "";

    var content = "";

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

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            httpResponse.innerHTML = xmlhttp.responseText;
        }
    }

    var year = window.document.getElementById("year").textContent;
    var month = window.document.getElementById("month").textContent;

    xmlhttp.open("POST", "../PHP/monthPlanSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&content=" + content);
}