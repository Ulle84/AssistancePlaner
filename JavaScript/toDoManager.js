//TODO generate 'class'

var sections;
var toDos;
var today;
var tomorrow;
var dayAfterTomorrow;
var toDoId;

function init() {
    toDoId = 0;
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
        destination = sections['done'];

        // create copy if repeated
        /*var copy = item.parentNode.cloneNode(true);
         copy.setAttribute("dueDate", "2014-05-14");
         copy.firstChild.checked = false;
         copy.firstChild.nextSibling.setAttribute("style", "text-decoration: none");
         sections[getDueSectionName("2014-05-14")].appendChild(copy);*/
    }
    else {
        item.nextSibling.setAttribute("style", "text-decoration: none");
        destination = sections[getDueSectionName(item.parentNode.getAttribute("dueDate"))];
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
        var toDo = new Array();
        toDo['id'] = toDoId++;
        toDo['description'] = cells[0].textContent;
        toDo['dueDate'] = cells[1].textContent;
        toDos.push(toDo);
    }

    toDos.sort(sortfunction);
    generateToDoItems();
}

function sortfunction(a, b) {

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

function generateToDoItems() {
    for (var i = 0; i < toDos.length; i++) {

        var div = window.document.createElement("div");
        var input = window.document.createElement("input");
        var span = window.document.createElement("span");

        sections[getDueSectionName(toDos[i]["dueDate"])].appendChild(div);

        div.appendChild(input);
        div.appendChild(span);

        div.setAttribute("class", "toDo");
        div.setAttribute("dueDate", toDos[i]['dueDate']);
        div.setAttribute("toDoId", toDos[i]['id']);

        input.setAttribute("type", "checkbox");
        input.setAttribute("onchange", "toDoItemChanged(this)");

        span.textContent = toDos[i]['description'];
    }
}

