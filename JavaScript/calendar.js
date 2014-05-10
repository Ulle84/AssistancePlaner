function dateClicked(element) {

    switch (element.getAttribute("class")) {
        case "bad":
            element.setAttribute("class", "good");
            break;
        case "good":
            element.setAttribute("class", "okay");
            break;
        case "okay":
            element.setAttribute("class", "bad");
            break;
    }
}

function markAllDates() {
    markAllBadDates();
    markAllOkayDates();
}

function markAllBadDates() {
    var notMarked = window.document.getElementsByClassName("bad");

    while (notMarked.length != 0) {
        notMarked[0].setAttribute("class", "good");
    }
}

function markAllOkayDates() {
    var notMarked = window.document.getElementsByClassName("okay");

    while (notMarked.length != 0) {
        notMarked[0].setAttribute("class", "good");
    }
}


function save(userName, year, month, id) {
    var httpResponse = document.getElementById("httpResponse");
    httpResponse.innerHTML = "";

    var content = "";

    var cells = window.document.getElementById(id).getElementsByTagName("td");

    var firstEntry = true;
    for (var i = 0; i < cells.length; i++) {
        if (cells[i].textContent != "")
        {
            if (firstEntry) {
                firstEntry = false;
            }
            else {
                content += ";";
            }

            switch (cells[i].getAttribute("class")) {
                case "bad":
                    content += "0";
                    break;
                case "good":
                    content += "10";
                    break;
                case "okay":
                    content += "1";
                    break;
            }
        }
    }

    /*var goodDates = window.document.getElementsByClassName("good");

    for (var i = 0; i < goodDates.length; i++) {
        if (i > 0) {
            content += ";";
        }
        content += goodDates[i].firstChild.textContent;
    }*/

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            httpResponse.innerHTML = xmlhttp.responseText;
        }
    }

    xmlhttp.open("POST", "../PHP/calendarSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&userName=" + userName + "&content=" + content);
}