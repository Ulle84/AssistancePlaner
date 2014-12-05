var serviceDescription = "Dienst";
var standbyDescription = "Bereitschaft"
var columnOffsetLeft = 5;
var columnOffsetRight = 2;
var const_year;
var const_month;

function entryClicked(element) {
    if (element.parentNode.firstChild.nextSibling.firstChild.value == "--") {
        alert("An diesem Tag wird kein Dienst benötigt!");
        return;
    }

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

        var selects = rows[i].getElementsByTagName("select");

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

        if (selects[0].value == "--") {
            if (countOfService != 0 || countOfStandby != 0) {
                if (showErrorMessage) {
                    alert("Am " + data[0].textContent + " ist kein Dienst!");
                }
                return false;
            }
        }
        else {
            if (countOfService != 1 || countOfStandby != 1) {
                if (showErrorMessage) {
                    alert("Der Dienstplan für den " + data[0].textContent + " ist nicht korrekt!");
                }
                return false;
            }
        }
    }

    if (showSuccesMessage) {
        alert("Alles in Ordnung!");
    }
    return true;
}

function checkAvailability() {
    var rosterTable = window.document.getElementById("rosterTable");

    var rows = rosterTable.getElementsByClassName("rosterData");

    var badDaysCount = 0;
    var badDays = "";

    for (var i = 1; i < rows.length; i++) {
        var selects = rows[i].getElementsByTagName("select");

        var availableGood = rows[i].getElementsByClassName("good");
        var availableOkay = rows[i].getElementsByClassName("okay");
        var service = rows[i].getElementsByClassName("service");
        var standby = rows[i].getElementsByClassName("standby");

        if (selects[0].value != "--") {
            if (availableGood.length + availableOkay.length + service.length + standby.length < 2) {
                badDaysCount++;
                if (badDays != "") {
                    badDays += "   ";
                }
                badDays += rows[i].firstChild.textContent;
            }
        }
    }

    if (badDaysCount == 1) {
        alert("Am " + badDays + " haben nicht genug Assistenten Zeit!");
    }
    else if (badDaysCount > 1) {
        alert("Es gibt Tage, an denen nicht genug Assistenten Zeit haben:\n" + badDays);
    }
    else {
        alert("An allen Tagen sind genug Assistenten verfügbar.");
    }

}

function requestRoster(button, year, month) {
    button.disabled = true;

    var rosterTable = window.document.getElementById("rosterTable");

    // Zeiten zusammensuchen
    var rows = rosterTable.getElementsByClassName("rosterData");

    var workingTimes = "";

    for (var i = 1; i < rows.length; i++) {

        var selects = rows[i].getElementsByTagName("select");

        workingTimes += selects[0].value + ":" + selects[1].value;
        workingTimes += " - ";
        workingTimes += selects[2].value + ":" + selects[3].value + "\n";
    }

    // request mit zeiten an server stellen
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var roster = xmlhttp.responseText.split("\n");

            clearRoster();
            setNewRoster(roster);

            button.disabled = false;
            save(year, month);
        }
    }

    xmlhttp.open("POST", "../PHP/rosterGenerator.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&workingTimes=" + workingTimes);
}

function publishRoster(button, year, month) {
    if (!confirm("Möchten Sie wirklich den Dienstplan veröffentlichen?")) {
        return;
    }

    button.disabled = true;

    var now = new Date();

    var publishedDate = window.document.getElementById("publishedDate");
    publishedDate.textContent = now.toStringDisplayWithTime();

    publishedDate.parentNode.setAttribute("class", "");

    var action = "newRoster";

    var content = "";

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            save(year, month);
            button.disabled = false;
        }
    }

    xmlhttp.open("POST", "../PHP/notifyTeam.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&action=" + action+ "&content=" + content);
}

function closeRoster(button, year, month) {
    if (!confirm("Möchten Sie wirklich den Dienstplan abschließen?\nDanach sind keine Änderungen möglich.")) {
        return;
    }

    button.disabled = true;

    var now = new Date();

    var closedDate = window.document.getElementById("closedDate");
    closedDate.textContent = now.toStringDisplayWithTime();

    closedDate.parentNode.setAttribute("class", "");

    save(year, month);

    var action = "notifyProvider";

    var content = "";

    var uniqueID = window.document.getElementById("uniqueID").textContent;

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            location.reload(true);
        }
    }

    xmlhttp.open("POST", "../PHP/notifyTeam.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&action=" + action + "&content=" + content + "&uniqueID=" + uniqueID);
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

    save(const_year, const_month);
}

function save(year, month) {
    var contentRoster = "";
    var rosterTable = window.document.getElementById("rosterTable");
    var rows = rosterTable.getElementsByClassName("rosterData");
    var persons = rows[0].getElementsByTagName("th");

    for (var i = 1; i < rows.length; i++) {

        var selects = rows[i].getElementsByTagName("select");
        var inputs = rows[i].getElementsByTagName("input");

        contentRoster += selects[0].value;
        contentRoster += ":";
        contentRoster += selects[1].value;
        contentRoster += " - ";
        contentRoster += selects[2].value;
        contentRoster += ":";
        contentRoster += selects[3].value + "\n";
        contentRoster += inputs[0].value + "\n";
        contentRoster += inputs[1].value + "\n";

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

        contentRoster += personOfService + "\n";
        contentRoster += personOfStandby + "\n";
    }

    var notes = window.document.getElementById("notes");
    contentRoster += notes.value;

    var now = new Date();
    var lastChangeTime =  now.toStringDisplayWithTime();

    var lastChange = window.document.getElementById("lastChange");
    var publishedDate = window.document.getElementById("publishedDate").textContent;
    var closedDate = window.document.getElementById("closedDate").textContent;
    var uniqueID = window.document.getElementById("uniqueID").textContent;

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            lastChange.textContent = xmlhttp.responseText;
            lastChange.parentNode.setAttribute("class", "");
        }
    }

    xmlhttp.open("POST", "../PHP/rosterSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&lastChangeTime=" + lastChangeTime + "&publishedDate=" + publishedDate + "&closedDate=" + closedDate + "&content=" + contentRoster + "&uniqueID=" + uniqueID);
}

function createPdf(button, year, month, checkRoster) {
    if (checkRoster) {
        if (!checkRoster(true, false)) {
            return;
        }
     }

    var uniqueID = window.document.getElementById("uniqueID").textContent;
    var clientName = window.document.getElementById("clientName").textContent;

    window.open("../PHP/rosterViewPdf.php?year=" + year + "&month=" + month + "&clientName=" + clientName + "&id=" + uniqueID);
}

function calcHours() {
    var rosterTable = window.document.getElementById("rosterTable");
    var hourTable = window.document.getElementById("hourTable");

    var serviceHoursIndex = 3;
    var standbyHoursIndex = 4;


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

function onStartTimeHourChanged(element) {
    if (element.value == "--") {
        var services = element.parentNode.parentNode.getElementsByClassName("service");
        var standbys = element.parentNode.parentNode.getElementsByClassName("standby");

        while (services.length > 0) {
            services[0].textContent = "";
            services[0].setAttribute("class", services[0].getAttribute("baseClass"));
        }

        while (standbys.length > 0) {
            standbys[0].textContent = "";
            standbys[0].setAttribute("class", standbys[0].getAttribute("baseClass"));
        }
    }

    recalculateHours(element);
}

function onStartTimeMinuteChanged(element) {
    recalculateHours(element.previousSibling);
}

function onEndTimeHourChanged(element) {
    recalculateHours(element.parentNode.previousSibling.firstChild);
}

function onEndTimeMinuteChanged(element) {
    recalculateHours(element.parentNode.previousSibling.firstChild);
}



function recalculateHours(startHoursElement) {
    var startMinutesElement = startHoursElement.nextSibling;
    var stopHoursElement = startHoursElement.parentNode.nextSibling.firstChild;
    var stopMinutesElement = startHoursElement.parentNode.nextSibling.firstChild.nextSibling;


    var startTime = startHoursElement.value + ":" + startMinutesElement.value;
    var stopTime = stopHoursElement.value + ":" + stopMinutesElement.value;

    var workingHours = timeToNumber(stopTime) + 24 - 6 - timeToNumber(startTime);
    var standbyHours = 1;

    if (workingHours <= 13) {
        standbyHours = 0.5;
    }

    startHoursElement.parentNode.nextSibling.nextSibling.textContent = workingHours.toString();
    startHoursElement.parentNode.nextSibling.nextSibling.nextSibling.textContent = standbyHours.toString();

    if (startHoursElement.value == "--") {
        startHoursElement.parentNode.nextSibling.nextSibling.textContent = "0";
        startHoursElement.parentNode.nextSibling.nextSibling.nextSibling.textContent = "0";

        startMinutesElement.style.display = "none";
        stopHoursElement.style.display = "none";
        stopMinutesElement.style.display = "none";
    }
    else {
        startMinutesElement.style.display = "inline";
        stopHoursElement.style.display = "inline";
        stopMinutesElement.style.display = "inline";
    }

    calcHours();
}

function timeToNumber(time) {
    var parts = time.split(":");

    return parseInt(parts[0]) + parseInt(parts[1]) / 60.0;
}

function notifyTeam(year, month) {
    if (!confirm("Möchten Sie wirklich eine E-Mail an das Team verschicken?")) {
        return;
    }

    var action = "requestDates";

    var content = window.document.getElementById("notes").value.replace(new RegExp("\n", 'g'), "<br />");

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            alert(xmlhttp.responseText);
        }
    }

    xmlhttp.open("POST", "../PHP/notifyTeam.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("year=" + year + "&month=" + month + "&action=" + action+ "&content=" + content);
}

function init(year, month) {
    const_year = year;
    const_month = month;
    doDirtyRepositionHack();
    calcHours();
}

function doDirtyRepositionHack() {
    // really dirty positioning hack ;-)
    var smallElement = window.document.getElementById("dirtyPositionHack");
    var main = window.document.getElementById("main");

    main.style.position = "absolute";
    main.style.left = smallElement.offsetLeft + "px";
    main.style.top = smallElement.offsetTop + "px";
}

/*window.addEventListener("beforeunload", function (e) {
    save(const_year, const_month, true);
});*/