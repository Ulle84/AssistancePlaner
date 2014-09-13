Date.prototype.addYears = function (numberOfYears) {
    var year = this.getFullYear();
    year += parseInt(numberOfYears);
    this.setFullYear(year);
}