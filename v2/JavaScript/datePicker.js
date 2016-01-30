//Todo Declare Class
var dp_currentYear;
var dp_currentMonth;
var dp_displayYear;
var dp_displayMonth;
var dp_monthDescriptions;
var dp_datePicker;
var dp_dateDisplay;

function dp_init() {
    var date = new Date();
    dp_currentYear = date.getFullYear();
    dp_currentMonth = date.getMonthCorrected();
    dp_displayYear = dp_currentYear;
    dp_displayMonth = dp_currentMonth;

    dp_datePicker = window.document.getElementById("myDatePicker");
    dp_dateDisplay = window.document.getElementById("dueDateInput");

    dp_monthDescriptions = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"]

    updateMonth();

    /*var calendarInput = window.document.getElementById("calendarInput");
     calendarInput.appendChild(arrayToTable(createMonthArray(dp_currentYear, dp_currentMonth), 7, true));*/
}

function arrayToTable(array, width, firstRowIsHeader) {
    var table = window.document.createElement("table");

    var row;
    for (var i = 0; i < array.length; i++) {
        if (i % width == 0) {
            row = window.document.createElement("tr");
            table.appendChild(row);
        }
        var cell = window.document.createElement((firstRowIsHeader && i < width) ? "th" : "td");
        cell.textContent = array[i];
        if (parseInt(array[i]) > 0) {
            cell.setAttribute("onclick", "dateClicked(" + dp_displayYear + ", " + dp_displayMonth + ", " + array[i] + ")");
        }
        row.appendChild(cell);
    }
    return table;
}

function dateClicked(year, month, day) {
    hideDatePicker();

    var date = new Date(year, month - 1, day);

    dp_dateDisplay.value = date.toStringDisplay();

    dueDateChanged();
}

function createMonthArray(year, month) {
    var date = new Date(year, month - 1, 1);
    var numberOfDays = date.getNumberOfDaysInThisMonth();

    var firstDay = date.getDay(); // So = 0; Mo = 1; Di = 2 ...
    var firstDayOffset = (firstDay + 6) % 7;

    var totalCount = firstDayOffset + numberOfDays;

    var rowCount = totalCount / 7;
    if (totalCount % 7 != 0) {
        rowCount++;
    }

    var monthArray = ["Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"];

    for (var i = 0; i < firstDayOffset; i++) {
        monthArray.push("");
    }

    for (var i = 1; i <= numberOfDays; i++) {
        monthArray.push(i);
    }

    while (monthArray.length % 7 != 0) {
        monthArray.push("");
    }

    return monthArray;
}

function goToPreviousMonth() {
    dp_displayMonth--;
    if (dp_displayMonth == 0) {
        dp_displayMonth = 12;
        dp_displayYear--;
    }
    updateMonth();
}

function goToCurrentMonth() {
    dp_displayMonth = dp_currentMonth;
    dp_displayYear = dp_currentYear;
    updateMonth();
}

function goToNextMonth() {
    dp_displayMonth++;
    if (dp_displayMonth == 13) {
        dp_displayMonth = 1;
        dp_displayYear++;
    }
    updateMonth();
}

function updateMonth() {
    var calendarInput = window.document.getElementById("calendarInput");
    if (calendarInput.hasChildNodes()) {
        while (calendarInput.childNodes.length > 0) {
            calendarInput.removeChild(calendarInput.firstChild);
        }
    }
    calendarInput.appendChild(arrayToTable(createMonthArray(dp_displayYear, dp_displayMonth), 7, true));

    var monthDescription = window.document.getElementById("monthDescription");
    monthDescription.textContent = dp_monthDescriptions[dp_displayMonth - 1] + " " + dp_displayYear;
}

function showDatePicker() {
    dp_datePicker.style.display = "block";
}

function hideDatePicker() {
    dp_datePicker.style.display = "none";
}

function removeDate() {
    dp_dateDisplay.value = "";
    hideDatePicker();
    dueDateChanged();
}