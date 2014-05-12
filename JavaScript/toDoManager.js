var doneSection;

function init() {
    doneSection = window.document.getElementById("done");
    checkSections();
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