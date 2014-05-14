function ToDo(description, dueDate, repetition) {
    this.id = toDoId++;
    this.description = description;
    this.dueDate = dueDate;
    this.repetition = repetition;
    this.copiedFromId = 0;
    this.dueDateDisplay = this.getDueDateDisplay(dueDate);
    this.doneOn = '';
    this.doneBy = '';

    var repetitionDetails = repetition.split(";");
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
        var toDo = getToDoById(item.parentNode.getAttribute("toDoId"));
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
            toDoSections[i].setAttribute("style", "display: block;");
        }
        else {
            toDoSections[i].setAttribute("style", "display: none;");
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

    sections[getDueSectionName(toDo.dueDate)].appendChild(div);

    div.appendChild(input);
    div.appendChild(spanDescription);
    div.appendChild(spanDueDate);

    div.setAttribute("class", "toDo");
    div.setAttribute("toDoId", toDo.id);

    input.setAttribute("type", "checkbox");
    input.setAttribute("onchange", "toDoItemChanged(this)");

    spanDescription.textContent = toDo.description;

    spanDueDate.textContent = " - " + toDo.dueDateDisplay;
    spanDueDate.setAttribute("class", "dueDate");
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
    for (var i = 0; i < toDos.length; i++) {
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

function test1() {
    var date = new Date();
    alert(date.toStringWithTime());
}

function test2() {
    var array = [2, 5, 9, 10, 11];

    for (var i = 0; i < array.length; i++) {
        if (array[i] == 5 || array[i] == 9) {
            array.splice(i, 1);
            i--;
        }
    }

    for (var i = 0; i < array.length; i++) {
        alert(array[i]);
    }

}