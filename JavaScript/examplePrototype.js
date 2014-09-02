Date.prototype.addYears = function (number) {
    var year = this.getFullYear();
    year += parseInt(number);
    this.setFullYear(year);
}