function ToDo(description, dueDate, repetition) {
    this.id = toDoId++;
    this.description = description;
    this.dueDate = dueDate;
    this.repetition = repetition;
    this.copiedFromId = 0;
    this.dueDateDisplay = this.getDueDateDisplay(dueDate);
    this.doneOn = '';
    this.doneBy = '';

    var repetitionDetails = repetition.split(" ");
    this.repeatIntervalNumber = repetitionDetails[0];
    this.repeatIntervalType = repetitionDetails[1];
    this.repeatFrom = repetitionDetails[2];
}

ToDo.prototype.createRepetition = function () {
    var copy = new ToDo(this.description, this.dueDate, this.repetition);
    copy.copiedFromId = this.id;

    if (copy.repetition == "") {
        return copy;
    }

    var date = new Date();
    if (this.repeatFrom == "d") {
        date.fromStringSortable(this.dueDate)
    }

    switch (this.repeatIntervalType) {
        case 'd':
            date.addDays(this.repeatIntervalNumber);
            break;
        case 'w':
            date.addWeeks(this.repeatIntervalNumber);
            break;
        case 'm':
            date.addMonth(this.repeatIntervalNumber);
            break;
        case 'y':
            date.addYears(this.repeatIntervalNumber);
            break;
    }

    copy.dueDate = date.toStringSortable();
    copy.dueDateDisplay = copy.getDueDateDisplay(copy.dueDate);

    return copy;
};

ToDo.prototype.getInfo = function () {
    var info = "id: " + this.id;
    info += "\ndescription: " + this.description;
    info += "\ndueDate: " + this.dueDate;
    info += "\ndueDateDisplay: " + this.dueDateDisplay;
    info += "\nrepetition: " + this.repetition;
    info += "\ncopiedFromId: " + this.copiedFromId;

    return info;
};

ToDo.prototype.getDueDateDisplay = function (dueDate) {
    var dueDateDisplay = dueDate.substr(8, 2) + '.';
    dueDateDisplay += dueDate.substr(5, 2) + '.';
    dueDateDisplay += dueDate.substr(0, 4);

    return dueDateDisplay;
};

var sections;
var toDos;
var today;
var tomorrow;
var dayAfterTomorrow;
var toDoId;

function init() {
    toDoId = 1;
    initDates();
    initSections();
    readData();
    checkSections();

    dp_init();
}

function initDates() {
    var date = new Date();
    today = date.toStringSortable();
    date.addDays(1);
    tomorrow = date.toStringSortable();
    date.addDays(1);
    dayAfterTomorrow = date.toStringSortable();
}

function initSections() {
    sections = new Array();
    sections['unsorted'] = window.document.getElementById("unsorted");
    sections['done'] = window.document.getElementById("done");
    sections['overdue'] = window.document.getElementById("overdue");
    sections['today'] = window.document.getElementById("today");
    sections['tomorrow'] = window.document.getElementById("tomorrow");
    sections['dayAfterTomorrow'] = window.document.getElementById("dayAfterTomorrow");
    sections['future'] = window.document.getElementById("future");
    sections['noDueDate'] = window.document.getElementById("noDueDate");
}

function toDoItemChanged(item) {
    var destination;
    if (item.checked) {
        item.nextSibling.setAttribute("style", "text-decoration: line-through");
        item.nextSibling.nextSibling.setAttribute("style", "text-decoration: line-through");
        item.nextSibling.nextSibling.nextSibling.setAttribute("style", "text-decoration: line-through");
        destination = sections['done'];

        var toDo = getToDoById(item.parentNode.getAttribute("toDoId"));

        var date = new Date();
        toDo.doneOn = date.toStringWithTime();
        toDo.doneBy = window.document.getElementById("username").textContent;

        if (toDo.repetition != "") {
            var repetition = toDo.createRepetition();
            toDos.push(repetition);
            generateToDo(repetition);
        }
    }
    else {
        item.nextSibling.setAttribute("style", "text-decoration: none");
        item.nextSibling.nextSibling.setAttribute("style", "text-decoration: none");
        item.nextSibling.nextSibling.nextSibling.setAttribute("style", "text-decoration: none");
        var toDo = getToDoById(item.parentNode.getAttribute("toDoId"));
        toDo.doneBy = "";
        toDo.doneOn = "";
        destination = sections[getDueSectionName(toDo.dueDate)];

        if (toDo.repetition != "") {
            removeToDoCopiedFromId(toDo.id);
        }
    }
    destination.appendChild(item.parentNode);
    checkSections();
}

function checkSections() {
    var toDoSections = window.document.getElementsByClassName("toDoSection");

    for (var i = 0; i < toDoSections.length; i++) {
        var toDos = toDoSections[i].getElementsByClassName("toDo");
        if (toDos.length > 0) {
            toDoSections[i].style.display = "block";
        }
        else {
            toDoSections[i].style.display = "none";
        }
    }
}

function getDueSectionName(dueDate) {
    if (dueDate == "") {
        return 'noDueDate';
    }
    else if (dueDate < today) {
        return 'overdue';
    }
    else if (dueDate == today) {
        return 'today';
    }
    else if (dueDate == tomorrow) {
        return 'tomorrow';
    }
    else if (dueDate == dayAfterTomorrow) {
        return 'dayAfterTomorrow';
    }
    else {
        return 'future';
    }
}

function readData() {
    var toDoData = window.document.getElementById("toDoData");

    toDos = new Array();

    var rows = toDoData.getElementsByTagName("tr");
    for (var i = 1; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName("td");
        var toDo = new ToDo(cells[0].textContent, cells[1].textContent, cells[2].textContent);
        toDos.push(toDo);
    }

    toDos.sort(sortFunction);
    generateAllToDos();
}

function sortFunction(a, b) {

    if (a['dueDate'] == b['dueDate']) {
        if (a['description'] == b['description']) {
            return 0;
        }
        return a['description'] < b['description'] ? -1 : +1;
    }

    if (a['dueDate'] == "") {
        return +1;
    }

    if (b['dueDate'] == "") {
        return -1;
    }

    return a['dueDate'] < b['dueDate'] ? -1 : +1;
}

function generateAllToDos() {
    for (var i = 0; i < toDos.length; i++) {
        generateToDo(toDos[i]);
    }
}

function generateToDo(toDo) {
    var div = window.document.createElement("div");
    var input = window.document.createElement("input");
    var spanDescription = window.document.createElement("span");
    var spanDueDate = window.document.createElement("span");
    var spanRepetition = window.document.createElement("span");

    sections[getDueSectionName(toDo.dueDate)].appendChild(div);

    div.appendChild(input);
    div.appendChild(spanDescription);
    div.appendChild(spanDueDate);
    div.appendChild(spanRepetition);

    div.setAttribute("class", "toDo");
    div.setAttribute("toDoId", toDo.id);

    input.setAttribute("type", "checkbox");
    input.setAttribute("onchange", "toDoItemChanged(this)");

    spanDescription.textContent = toDo.description;

    spanDueDate.textContent = " - fällig am " + toDo.dueDateDisplay;
    spanDueDate.setAttribute("class", "dueDate");

    if (toDo.repetition != "") {
        var repetitionText = " - wiederholt sich ";
        if (toDo.repeatIntervalNumber == 1) {
            switch (toDo.repeatIntervalType) {
                case 'd':
                    repetitionText += "jeden Tag ";
                    break;
                case 'w':
                    repetitionText += "jede Woche ";
                    break;
                case 'm':
                    repetitionText += "jeden Monat ";
                    break;
                case 'y':
                    repetitionText += "jedes Jahr ";
                    break;
            }
        }
        else {
            repetitionText += "alle " + toDo.repeatIntervalNumber;
            switch (toDo.repeatIntervalType) {
                case 'd':
                    repetitionText += " Tage ";
                    break;
                case 'w':
                    repetitionText += " Wochen ";
                    break;
                case 'm':
                    repetitionText += " Monate ";
                    break;
                case 'y':
                    repetitionText += " Jahre ";
                    break;
            }

        }

        switch (toDo.repeatFrom) {
            case 'd':
                repetitionText += "ab dem Fälligkeitsdatum";
                break;
            case 'c':
                repetitionText += "ab dem Erledigungsdatum";
                break;
        }

        spanRepetition.textContent = repetitionText;
    }
}

function getToDoById(id) {
    for (var i = 0; i < toDos.length; i++) {
        if (toDos[i].id == id) {
            return toDos[i];
        }
    }
}

function removeToDoCopiedFromId(id) {
    var idOfCopy = 0;
    for (var i = 0; i < toDos.length; i++) {
        if (toDos[i].copiedFromId == id) {
            idOfCopy = toDos[i].id;
            toDos.splice(i, 1);
            break;
        }
    }
    var toDoItems = window.document.getElementsByClassName("toDo");
    for (var i = 0; i < toDoItems.length; i++) {
        if (toDoItems[i].getAttribute("toDoId") == idOfCopy) {
            toDoItems[i].parentNode.removeChild(toDoItems[i]);
            break;
        }
    }
    checkSections();
}

function save(button) {
    button.disabled = true;
    var httpResponse = document.getElementById("httpResponse");

    toDos.sort(sortFunction);

    var toDo = "";
    var done = "";

    for (var i = 0; i < toDos.length; i++) {
        if (toDos[i].doneOn == "") {
            toDo += toDos[i].description + '\n';
            toDo += toDos[i].dueDate + '\n';
            toDo += toDos[i].repetition + '\n\n';
        }
        else {
            done += toDos[i].description + '\n';
            done += toDos[i].dueDate + '\n';
            done += toDos[i].repetition + '\n';
            done += toDos[i].doneOn + '\n';
            done += toDos[i].doneBy + '\n\n';
        }
    }

    httpResponse.innerHTML = "";

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            httpResponse.innerHTML = xmlhttp.responseText;

            for (var i = 0; i < toDos.length; i++) {
                if (toDos[i].doneOn != "") {
                    toDos.splice(i, 1);
                    i--;
                }
            }

            var done = sections['done'].getElementsByClassName("toDo")
            while (done.length > 0) {
                sections['done'].removeChild(done[0]);
            }

            checkSections();

            button.disabled = false;
        }
    }

    xmlhttp.open("POST", "../PHP/toDoSaver.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("toDo=" + toDo + "&done=" + done);
}

function addToDo() {
    var descriptionInput = window.document.getElementById("descriptionInput");
    var dueDateInput = window.document.getElementById("dueDateInput");
    var intervalNumberSelection = window.document.getElementById("intervalNumberSelection");
    var intervalTypeSelection = window.document.getElementById("intervalTypeSelection");
    var repeatFromSelection = window.document.getElementById("repeatFromSelection");

    var intervalTypes = [" d", " w", " m", " y"];
    var repeatFromTypes = [" c", " d"]

    if (descriptionInput.value == "") {
        return;
    }

    var repetition = "";
    if (dueDateInput.value != "" && intervalNumberSelection.selectedIndex != 0) {
        repetition += intervalNumberSelection.selectedIndex;
        repetition += intervalTypes[intervalTypeSelection.selectedIndex];
        repetition += repeatFromTypes[repeatFromSelection.selectedIndex];
    }

    var convertedDate = "";
    if (dueDateInput.value != "") {
        convertedDate = convertDisplayDateToSortableDate(dueDateInput.value);
    }

    var toDo = new ToDo(descriptionInput.value, convertedDate, repetition);

    toDos.push(toDo);
    generateToDo(toDo);
    checkSections();

    descriptionInput.value = "";
    dueDateInput.value = "";
    intervalNumberSelection.selectedIndex = 0;
    intervalTypeSelection.selectedIndex = 0;
    repeatFromSelection.selectedIndex = 0;
    descriptionChanged();
}

function intervalNumberChanged() {
    var intervalNumberSelection = window.document.getElementById("intervalNumberSelection");
    var intervalTypeSelection = window.document.getElementById("intervalTypeSelection");
    if (intervalNumberSelection.selectedIndex == 1) {
        window.document.getElementById("day").textContent = "Tag";
        window.document.getElementById("week").textContent = "Woche";
        window.document.getElementById("month").textContent = "Monat";
        window.document.getElementById("year").textContent = "Jahr";
    }
    else {
        window.document.getElementById("day").textContent = "Tage";
        window.document.getElementById("week").textContent = "Wochen";
        window.document.getElementById("month").textContent = "Monate";
        window.document.getElementById("year").textContent = "Jahre";
    }

    var intervalType = window.document.getElementById("intervalType");
    var repeatFrom = window.document.getElementById("repeatFrom");

    if (intervalNumberSelection.selectedIndex == 0) {
        intervalType.setAttribute("class", "hidden");
        repeatFrom.setAttribute("class", "hidden");
    }
    else {
        intervalType.setAttribute("class", "");
        repeatFrom.setAttribute("class", "");
    }
}

function intervalTypeChanged() {
    var intervalTypeSelection = window.document.getElementById("intervalTypeSelection");
    var every = window.document.getElementById("every");

    switch (intervalTypeSelection.selectedIndex) {
        case 0:
            every.textContent = "jeden";
            break;
        case 1:
            every.textContent = "jede";
            break;
        case 2:
            every.textContent = "jeden";
            break;
        case 3:
            every.textContent = "jedes";
            break;
    }
}

function dueDateChanged() {
    var dueDateInput = window.document.getElementById("dueDateInput");

    var intervalNumber = window.document.getElementById("intervalNumber");
    var intervalType = window.document.getElementById("intervalType");
    var repeatFrom = window.document.getElementById("repeatFrom");

    if (dueDateInput.value == "") {
        intervalNumber.setAttribute("class", "hidden");
        intervalType.setAttribute("class", "hidden");
        repeatFrom.setAttribute("class", "hidden");
    }
    else {
        intervalNumber.setAttribute("class", "");
        intervalNumberChanged();
    }
}

function descriptionChanged() {
    var descriptionInput = window.document.getElementById("descriptionInput");

    var dueDate = window.document.getElementById("dueDate");
    var intervalNumber = window.document.getElementById("intervalNumber");
    var intervalType = window.document.getElementById("intervalType");
    var repeatFrom = window.document.getElementById("repeatFrom");

    if (descriptionInput.value == "") {
        dueDate.setAttribute("class", "hidden");
        intervalNumber.setAttribute("class", "hidden");
        intervalType.setAttribute("class", "hidden");
        repeatFrom.setAttribute("class", "hidden");
    }
    else {
        dueDate.setAttribute("class", "");
        dueDateChanged();
    }
}