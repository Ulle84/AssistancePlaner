function validateInteger(element, minValue, maxValue) {
    var value = element.value;
    var integerValue = parseInt(element.value);
    if (isNaN(integerValue)) {
        element.value = minValue;
        alert("Die Eingabe '" + value + "' ist ungültig und wurde auf " + minValue + " gesetzt!");
        return;
    }

    if (integerValue < minValue) {
        element.value = minValue;
        alert("Werte kleiner als " + minValue + " sind nicht zulässig.\nDer Wert wurde auf " + minValue + " gesetzt!");
        return;
    }

    if (integerValue > maxValue) {
        element.value = maxValue;
        alert("Werte größer als " + maxValue + " sind nicht zulässig.\nDer Wert wurde auf " + maxValue + " gesetzt!");
        return;
    }

    element.value = integerValue;
}

function validateString(element) {
    if (element.value.contains("&")) {
        element.value = element.value.replace(new RegExp("&", 'g'), "");
        alert("Das Zeichen '&' ist ein unerlaubtes Sonderzeichen und wurde entfernt!");
    }
}