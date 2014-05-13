var unsortedSection;
var doneSection;
var overdueSection;
var todaySection;
var tomorrowSection;
var dayAfterTomorrowSection;
var futureSection;
var noDueDateSection;
var sections;

function init() {
    initSections();
    sortToDos();
    checkSections();
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
    //TODO
    unsortedSection = window.document.getElementById("unsorted");
    doneSection = window.document.getElementById("done");
    overdueSection = window.document.getElementById("overdue");
    todaySection = window.document.getElementById("today");
    tomorrowSection = window.document.getElementById("tomorrow");
    dayAfterTomorrowSection = window.document.getElementById("dayAfterTomorrow");
    futureSection = window.document.getElementById("future");
    noDueDateSection = window.document.getElementById("noDueDate");
}

function toDoItemChanged(item) {
    if (item.checked) {
        item.nextSibling.setAttribute("style", "text-decoration: line-through");
        item.setAttribute("origin", item.parentNode.parentNode.getAttribute("id"));
        doneSection.appendChild(item.parentNode);
    }
    else {
        item.nextSibling.setAttribute("style", "text-decoration: none");
        var destination = window.document.getElementById(item.getAttribute("origin"));
        destination.appendChild(item.parentNode);
    }
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

function sortToDos() {
    var toDos = sections['unsorted'].getElementsByClassName("toDo");

    var date = new Date();

    var today = date.toStringSortable();
    date.addDays(1);
    var tomorrow = date.toStringSortable();
    date.addDays(1);
    var dayAfterTomorrow = date.toStringSortable();

    while (toDos.length > 0) {
        var dueDate = toDos[0].getAttribute("dueDate");
        if (dueDate == "") {
            noDueDateSection.appendChild(toDos[0]);
        }
        else if (dueDate < today) {
            overdueSection.appendChild(toDos[0]);
        }
        else if (dueDate == today) {
            todaySection.appendChild(toDos[0]);
        }
        else if (dueDate == tomorrow) {
            tomorrowSection.appendChild(toDos[0]);
        }
        else if (dueDate == dayAfterTomorrow) {
            dayAfterTomorrowSection.appendChild(toDos[0]);
        }
        else {
            futureSection.appendChild(toDos[0]);
        }
    }


}