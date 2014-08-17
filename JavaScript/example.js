function init() {
    var date = new Date();

    var element = window.document.getElementById("createContentWithJavaScript");
    element.textContent = date.toLocaleString();
}