function sendFeedback(button) {
    button.disabled = true;
    var httpResponse = document.getElementById("httpResponse");
    httpResponse.innerHTML = "";

    var feedback = window.document.getElementById("feedback").value;

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            httpResponse.innerHTML = xmlhttp.responseText;
            button.disabled = false;
        }
    }

    xmlhttp.open("POST", "../PHP/feedbackSender.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("feedback=" + feedback);
}

function validateString(element) {
    if (element.value.indexOf('&') > -1) {
        element.value = element.value.replace(new RegExp('&', 'g'), "");
        alert("Das Zeichen '&' ist ein unerlaubtes Sonderzeichen und wurde entfernt!");
    }
}