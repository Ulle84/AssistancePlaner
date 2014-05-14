//Extend the functionality of date

Date.prototype.addDays = function (number) {
    var year = this.getFullYear();
    var month = this.getMonthCorrected();
    var day = this.getDate();

    for (var i = 0; i < number; i++) {
        day++;
        if (day > this.getNumberOfDays(year, month)) {
            day = 1;
            month++;
            if (month == 13) {
                month = 1;
                year++;
            }
        }
    }

    this.setFullYear(year);
    this.setMonth(month - 1);
    this.setDate(day);
}

Date.prototype.addWeeks = function (number) {
    this.addDays(number * 7);
}

Date.prototype.addMonth = function (number) {
    var year = this.getFullYear();
    var month = this.getMonthCorrected();

    for (var i = 0; i < number; i++) {
        month++;
        if (month == 13) {
            month = 1;
            year++;
        }
    }

    this.setFullYear(year);
    this.setMonth(month - 1);
}

Date.prototype.addYears = function (number) {
    var year = this.getFullYear();
    year += parseInt(number);
    this.setFullYear(year);
}

Date.prototype.toStringSortable = function () {
    var year = this.getFullYear();
    var month = this.getMonth() + 1;
    var day = this.getDate();

    var result = "";
    result += year;
    result += "-";
    result += month < 10 ? "0" + month : month;
    result += "-";
    result += day < 10 ? "0" + day : day;

    return result;
}

Date.prototype.toStringWithTime = function () {
    var hour = this.getHours();
    var minute = this.getMinutes();

    var result = this.toStringSortable();
    result += " ";
    result += hour < 10 ? "0" + hour : hour;
    result += ":";
    result += minute < 10 ? "0" + minute : minute;

    return result;
}

Date.prototype.fromStringSortable = function (string) {
    var data = string.split('-');
    this.setFullYear(data[0]);
    this.setMonth(data[1] - 1);
    this.setDate(data[2]);
}

Date.prototype.toStringDisplay = function () {
    var year = this.getFullYear();
    var month = this.getMonthCorrected();
    var day = this.getDate();

    var result = "";
    if (day < 10) {
        result += "0";
    }
    result += day;
    result += ".";

    if (month < 10) {
        result += "0";
    }
    result += month;
    result += ".";

    result += year;

    return result;
}

Date.prototype.getMonthCorrected = function () {
    return this.getMonth() + 1;
}

Date.prototype.isInLeapYear = function () {
    var year = this.getFullYear();
    return ((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0);
}

Date.prototype.getNumberOfDaysInThisMonth = function () {
    var month = this.getMonthCorrected();
    return /4|6|9|11/.test(month) ? 30 : month == 2 ? this.isInLeapYear() ? 29 : 28 : 31;
}

Date.prototype.getNumberOfDays = function (year, month) {
    return /4|6|9|11/.test(month) ? 30 : month == 2 ? ((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0) ? 29 : 28 : 31;
}