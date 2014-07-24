var serviceDescription = "Dienst";
var standbyDescription = "Bereitschaft"
var columnOffsetLeft = 4;
var columnOffsetRight = 2;

function entryClicked(element) {

    switch (element.getAttribute("class")) {
        case "good":
            element.setAttribute("class", "service");
            element.textContent = serviceDescription;
            break;
        case "okay":
            element.setAttribute("class", "service");
            element.textContent = serviceDescription;
            break;
        case "service":
            element.setAttribute("class", "standby");
            element.textContent = standbyDescription;
            break;
        case "standby":
            element.setAttribute("class", element.getAttribute("baseClass"));
            element.textContent = "";
            break;
    }

    calcHours(); //TODO only one column is affected - do not calculate the whole table
}

function checkRoster(showSuccess) {
    var rosterTable = window.document.getElementById("rosterTable")

    var rows = rosterTable.getElementsByClassName("rosterData");

    for (var i = 1; i < rows.length; i++) {
        var countOfService = 0;
        var countOfStandby = 0;

        // check that we have ONE 'Dienst' and ONE 'Bereitschaft'
        var data = rows[i].getElementsByTagName("td");

        for (var j = columnOffsetLeft; j < data.length - columnOffsetRight; j++) {
            if (data[j].textContent == serviceDescription) {
                countOfService++;
            }

            if (data[j].textContent == standbyDescription) {
                countOfStandby++;
            }
        }

        if (countOfService != 1 || countOfStandby != 1) {
            alert("Der Dienstplan für den " + data[0].textContent + " ist nicht korrekt!");
            return false;
        }
    }

    if (showSuccess) {
        alert("Alles in Ordnung!");
    }
    return true;
}

function save(button, year, month) {
    if (!checkRoster(false)) {
        return;
    }
    button.disabled = true;

    var httpResponse = document.getElementById("httpResponse");

    httpResponse.innerHTML = "";


    var content = "";

    var rosterTable = window.document.getElementById("rosterTable")

    var rows = rosterTable.getElementsByClassName("rosterData");

    var persons = rows[0].getElementsByTagName("th");

    for (var i = 1; i < rows.length; i++) {
        var personOfService = "";
        var personOfStandby = "";

        var data = rows[i].getElementsByTagName("td");

        for (var j = columnOffsetLeft; j < data.length - columnOffsetRight; j++) {
            if (data[j].textContent == serviceDescription) {
                personOfService = persons[j].textContent;
            }

            if (data[j].textContent == standbyDescription) {
                personOfStandby = persons[j].textContent;
            }
        }

        content += personOfService + ";" + personOfStandby + "\n";
    }


    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            httpResponse.innerHTML = xmlhttp.responseText;

            var now = new Date();
            var lastChange = window.document.getElementById("lastChange");
            lastChange.textContent = now.toStringDisplayWithTime();

            button.disabled = false;
        }
    }

    xmlhttp.open("POST", "../PHP/rosterSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&content=" + content);
}

function createPdf(button, year, month) {

    if (!checkRoster(false)) {
        return;
    }

    window.open("../PHP/rosterViewPdf.php?year=" + year + "&month=" + month);
}

function calcHours() {
    var rosterTable = window.document.getElementById("rosterTable");
    var hourTable = window.document.getElementById("hourTable");

    var serviceHoursIndex = 2;
    var standbyHoursIndex = 3;


    var rows = rosterTable.getElementsByClassName("rosterData");

    var persons = rows[0].getElementsByTagName("th");

    var hours = new Array();

    for (var i = columnOffsetLeft; i < persons.length - columnOffsetRight; i++) {
        hours[persons[i].textContent] = 0.0;
    }

    for (var i = 1; i < rows.length; i++) {
        var data = rows[i].getElementsByTagName("td");

        for (var j = columnOffsetLeft; j < data.length - columnOffsetRight; j++) {
            if (data[j].textContent == serviceDescription) {
                hours[persons[j].textContent] += parseFloat(data[serviceHoursIndex].textContent);
            }

            if (data[j].textContent == standbyDescription) {
                hours[persons[j].textContent] += parseFloat(data[standbyHoursIndex].textContent);
            }
        }
    }

    for (var i = columnOffsetLeft; i < persons.length - columnOffsetRight; i++) {
        var element = window.document.getElementById("hours" + persons[i].textContent);
        element.textContent = hours[persons[i].textContent];

        var targetHours = parseFloat(element.nextSibling.textContent);
        var calculatedHours = parseFloat(hours[persons[i].textContent]);
        var percentage = calculatedHours / targetHours * 100 - 100;
        var diff = calculatedHours - targetHours;

        element.nextSibling.nextSibling.textContent = diff;
        element.nextSibling.nextSibling.nextSibling.textContent = Math.round(percentage) + " %";
        var abs = Math.abs(percentage);
        if (0 <= abs && abs <= 5) {
            element.parentNode.setAttribute("class", "good");
        } else if (5 < abs && abs <= 10) {
            element.parentNode.setAttribute("class", "okay");
        } else {
            element.parentNode.setAttribute("class", "bad");
        }
    }
}

function checkAvailability() {
    var rosterTable = window.document.getElementById("rosterTable");

    var rows = rosterTable.getElementsByClassName("rosterData");

    var badDaysCount = 0;
    var badDays = "";

    for (var i = 1; i < rows.length; i++) {
        var availableGood = rows[i].getElementsByClassName("good");
        var availableOkay = rows[i].getElementsByClassName("okay");
        var service = rows[i].getElementsByClassName("service");
        var standby = rows[i].getElementsByClassName("standby");

        if (availableGood.length + availableOkay.length + service.length + standby.length < 2) {
            badDaysCount++;
            if (badDays != "") {
                badDays += "   ";
            }
            badDays += rows[i].firstChild.textContent;
        }
    }

    if (badDaysCount == 1) {
        alert("Am " + badDays + " haben nicht genug Assistenten Zeit!");
    }
    if (badDaysCount > 1) {
        alert("Es gibt Tage, an denen nicht genug Assistenten Zeit haben:\n" + badDays);
    }

}

function init() {
    calcHours();
    checkAvailability();
}



