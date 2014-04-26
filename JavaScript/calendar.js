function dateClicked(element) {

    if (element.getAttribute("class") == "bad") {
        element.setAttribute("class", "good");
    }
    else {
        element.setAttribute("class", "bad");
    }
}

function markAllDates() {
    var notMarked = window.document.getElementsByClassName("bad");

    while (notMarked.length != 0) {
        notMarked[0].setAttribute("class", "good");
    }


    //this code works not properly, because var notMarked is updated during cycles - so only every second element is touched
    /*
     for (var i = 0; i < notMarked.length; i++) {
     notMarked[i].setAttribute("class", "good");
     }
     */
}

function save() {
    var httpResponse = document.getElementById("httpResponse");
    httpResponse.innerHTML = "";

    var name = window.document.getElementById("name").value;

    var content = "";

    var goodDates = window.document.getElementsByClassName("good");

    for (var i = 0; i < goodDates.length; i++) {
        if (i > 0) {
            content += ";";
        }
        content += goodDates[i].firstChild.textContent;
    }

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            httpResponse.innerHTML = xmlhttp.responseText;
        }
    }

    var year = window.document.getElementById("year").textContent;
    var month = window.document.getElementById("month").textContent;

    xmlhttp.open("POST", "../PHP/calendarSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&name=" + name + "&content=" + content);
}