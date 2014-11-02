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
        case "bad":
            if (confirm("Der Assistent hat angegeben keine Zeit zu haben. \nMöchten Sie dennoch fortfahren?")) {
                element.setAttribute("class", "service");
                element.textContent = serviceDescription;
            }
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

function checkRoster(showErrorMessage, showSuccesMessage) {
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
            if (showErrorMessage) {
                alert("Der Dienstplan für den " + data[0].textContent + " ist nicht korrekt!");
            }
            return false;
        }
    }

    if (showSuccesMessage) {
        alert("Alles in Ordnung!");
    }
    return true;
}

function requestRoster(button, year, month) {
    button.disabled = true;

    // Zeiten zusammensuchen
    var rows = rosterTable.getElementsByClassName("rosterData");

    var workingTimes = "";

    for (var i = 1; i < rows.length; i++) {

        var selects = rows[i].getElementsByTagName("select");

        workingTimes += selects[0].value;
        workingTimes += " - ";
        workingTimes += selects[1].value + "\n";
    }


    // request mit zeiten an server stellen
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var roster = xmlhttp.responseText.split("\n");

            clearRoster();
            setNewRoster(roster);

            button.disabled = false;
        }
    }

    xmlhttp.open("POST", "../PHP/rosterGenerator.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&workingTimes=" + workingTimes);
}

function setNewRoster(roster) {
    var rows = rosterTable.getElementsByClassName("rosterData");
    var persons = rows[0].getElementsByTagName("th");

    for (var i = 1; i < rows.length; i++) {
        // check that we have ONE 'Dienst' and ONE 'Bereitschaft'
        var data = rows[i].getElementsByTagName("td");

        for (var j = columnOffsetLeft; j < data.length - columnOffsetRight; j++) {
            if (persons[j].textContent == roster[(i - 1) * 2]) {
                data[j].textContent = serviceDescription;
                data[j].setAttribute("baseClass", data[j].getAttribute("baseClass"));
                data[j].setAttribute("class", "service");
            }

            if (persons[j].textContent == roster[(i - 1) * 2 + 1]) {
                data[j].textContent = standbyDescription;
                data[j].setAttribute("baseClass", data[j].getAttribute("baseClass"));
                data[j].setAttribute("class", "standby");
            }
        }
    }

    calcHours();
}

function resetRoster() {
    if (!confirm("Möchten Sie den Dienstplan wirklich verwerfen?")) {
        return;
    }

    clearRoster();

    calcHours();
}

function clearRoster() {
    var services = window.document.getElementsByClassName("service");
    while (services.length > 0) {
        services[0].textContent = "";
        services[0].setAttribute("class", services[0].getAttribute("baseClass"));
    }

    var standbys = window.document.getElementsByClassName("standby");
    while (standbys.length > 0) {
        standbys[0].textContent = "";
        standbys[0].setAttribute("class", standbys[0].getAttribute("baseClass"));
    }
}

function save(button, year, month) {
    /*var saveRoster = true;
     if (!checkRoster(false)) {
     saveRoster = false;
     alert("Der Dienstplan ist nicht korrekt und wird daher nicht gespeichert!\n"
     + "Bitte Button 'Dienstplan prüfen' für Details drücken.")
     }*/
    button.disabled = true;

    var httpResponse = document.getElementById("httpResponse");

    httpResponse.innerHTML = "";

    var contentRoster = "";
    var rosterTable = window.document.getElementById("rosterTable");
    var rows = rosterTable.getElementsByClassName("rosterData");
    var persons = rows[0].getElementsByTagName("th");

    for (var i = 1; i < rows.length; i++) {

        var selects = rows[i].getElementsByTagName("select");
        var inputs = rows[i].getElementsByTagName("input");

        contentRoster += selects[0].value;
        contentRoster += " - ";
        contentRoster += selects[1].value + "\n";
        contentRoster += inputs[0].value + "\n";
        contentRoster += inputs[1].value + "\n";


        var personOfService = "";
        var personOfStandby = "";

        //if (saveRoster) {
        var data = rows[i].getElementsByTagName("td");

        for (var j = columnOffsetLeft; j < data.length - columnOffsetRight; j++) {
            if (data[j].textContent == serviceDescription) {
                personOfService = persons[j].textContent;
            }

            if (data[j].textContent == standbyDescription) {
                personOfStandby = persons[j].textContent;
            }
        }
        //}

        contentRoster += personOfService + "\n";
        contentRoster += personOfStandby + "\n";
    }

    var notes = window.document.getElementById("notes");
    contentRoster += notes.value;

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            httpResponse.innerHTML = xmlhttp.responseText;

            if (xmlhttp.responseText != "") {
                //TODO take last change of server!
                var now = new Date();
                var lastChange = window.document.getElementById("lastChange");
                lastChange.textContent = now.toStringDisplayWithTime();
            }

            button.disabled = false;
        }
    }

    xmlhttp.open("POST", "../PHP/rosterSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&content=" + contentRoster);
}

function createPdf(button, year, month) {
    if (!confirm("Haben Sie den Dienstplan gespeichert?")) {
        return;
    }


    if (!checkRoster(true, false)) {
        return;
    }

    if (window.document.getElementById("lastChange").textContent == "") {
        alert("Bitte erst den Dienstplan speichern!");
        return;
    }

    window.open("../PHP/rosterViewPdf.php?year=" + year + "&month=" + month);
}

function deleteRoster(button, year, month) {

    if (!confirm("Dienstplan wirklich löschen?")) {
        return;
    }

    button.disabled = true;

    var httpResponse = document.getElementById("httpResponse");

    httpResponse.innerHTML = "";

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            httpResponse.innerHTML = xmlhttp.responseText;

            var lastChange = window.document.getElementById("lastChange");
            lastChange.textContent = "";

            button.disabled = false;
        }
    }

    xmlhttp.open("POST", "../PHP/rosterEraser.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month);


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
        if (0 <= abs && abs <= 10) {
            element.parentNode.setAttribute("class", "good");
        } else if (10 < abs && abs <= 20) {
            element.parentNode.setAttribute("class", "okay");
        } else {
            element.parentNode.setAttribute("class", "bad");
        }
    }
}

function onEndTimeChanged(element) {
    recalculateHours(element.previousSibling.previousSibling, element);
}

function onStartTimeChanged(element) {
    recalculateHours(element, element.nextSibling.nextSibling);
}

function recalculateHours(startElement, stopElement) {
    /*var start = parseInt(startElement.value);
    var stop = parseInt(stopElement.value);*/

    var workingHours = timeToNumber(stopElement.value) + 24 - 6 - timeToNumber(startElement.value);
    var standbyHours = 1;

    if (workingHours <= 13) {
        standbyHours = 0.5;
    }

    startElement.parentNode.nextSibling.textContent = workingHours.toString();
    startElement.parentNode.nextSibling.nextSibling.textContent = standbyHours.toString();

    calcHours();
}

function timeToNumber(time) {
    var parts = time.split(":");

    return parseInt(parts[0]) + parseInt(parts[1]) / 60.0;
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

function notifyTeam(year, month) {
    if (!confirm("Möchten Sie wirklich eine E-Mail an das Team verschicken?")) {
        return;
    }

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

function init() {
    calcHours();
    checkAvailability();
}



