/* ToDo
- �berblendeffekt zwischen Folien
- Punkte/Grafiken nacheinander einblenden
- Drop-Down-Men� zum direkten ansteuern von Folien
- Keys abfangen (links, rechts, hoch, runter, Pos1, Ende, Enter, Space) - Presenter testen
- Zeiten einblenden (Gesamtzeit - Zeit f�r Folie - Restzeit f�r Folie - Restgesamtzeit)
- Pr�sentation schw�rzen
- Drucker anzeigen
*/
var actSlideNumber = 0; /* 0 = 1. Seite usw. */
var actHeading = actSlideNumber;

var headingsSeperator = " | ";

// Array
var slideHeadings = null;

// stlye stuff
var active = "1.00";
var inactive = "0.30";
var slidesBackgroundColor = "#E6E6FA";
var actHeadingColor = "red";

var bodyMargin = 5; 
var bodyMarginBottom = bodyMargin;
var bodyMarginLeft = bodyMargin;
var bodyMarginRight = bodyMargin;
var bodyMarginTop = bodyMargin;
var marginStep = 5;
var zoomFactor = 1.2;

// elements
var abstandLinks = null;
var abstandOben = null;
var abstandRechts = null;
var abstandUntern = null;
var body = null;
var control = null;
var gliederung = null;
//var goDown = null;
var goFirst = null;
var goLast = null;
var goNext = null;
var goPrevious = null;
//var goUp = null;
var headings = null;
var pageNumber = null;
var preferences = null;
var preferencesIcon = null;
var slides = null;

function styleForPrint() {
	alert("noch nicht implementiert!");
}

function styleHeadings(newSlideNumber) {
	//alert("begin styleHeadings()");
	var actHeading = slideHeadings[actSlideNumber];
	var newHeading = slideHeadings[newSlideNumber];
	//alert("actHeading: " + actHeading + " newHeading: " + newHeading);
	if (actHeading != newHeading) {
		if (actHeading > 0) {
			headings[actHeading - 1].style.color = "black";
		}
		if (newHeading > 0) {
			headings[newHeading - 1].style.color = actHeadingColor;
		}
	}
	//alert("end styleHeadings()");
}

function changeSlideBackgroundColor(newBackgroundColor) {
	slidesBackgroundColor = newBackgroundColor;
	for (var i = 0; i < slides.length; i++) {
		slides[i].style.backgroundColor = slidesBackgroundColor;
	}
	/*for (var i = 0; i < select.length; i++) {
		if (select[i].selected == true) {
			= select[i].value;
			for (var j = 0; j < slides.length; j++) {
				slides[j].style.backgroundColor = select[i].value;
			}
		}
	}*/
}

function updatePageNumber() {
	pageNumber.firstChild.nodeValue = "Seite: " + (actSlideNumber + 1) + "/" + slides.length;
}

function init() {
	// Elemente initialisieren
	abstandLinks = window.document.getElementById("abstandLinks");
	abstandOben = window.document.getElementById("abstandOben");
	abstandRechts = window.document.getElementById("abstandRechts");
	abstandUnten = window.document.getElementById("abstandUnten");
	body = window.document.getElementById("body");
	control = window.document.getElementById("control");
	gliederung = window.document.getElementById("gliederung");
	goFirst = window.document.getElementById("goFirst");
	goLast = window.document.getElementById("goLast");
	goNext = window.document.getElementById("goNext");
	goPrevious = window.document.getElementById("goPrevious");
	headings = window.document.getElementById("headings").getElementsByTagName("span");
	pageNumber = window.document.getElementById("pageNumber");
	preferences = window.document.getElementById("preferences");
	slides = window.document.getElementsByClassName("slide");
	
	// Titel = Titel des HMTL-Dokuments
	window.document.getElementById("title").firstChild.nodeValue = window.document.getElementsByTagName("title")[0].text;
	
	// Cookie auslesen
	if(document.cookie){
		readCookie();
		setBodyMargins();
	}
	
	// Hintergrundfarbe der Slides
	for (var i = 0; i < slides.length; i++) {
		slides[i].style.backgroundColor = slidesBackgroundColor;
	}
	
	// Bilder in slides zoombar machen
	for (var i = 0; i < slides.length; i++) {
		var images = slides[i].getElementsByTagName("img");
		for (var j = 0; j < images.length; j++) {
			images[j].setAttribute("onclick", "zoomIn(this)");
			images[j].setAttribute("class", "zoomImage");
		}
	}
	
	// Startseite aktiv schalten
	slides[actSlideNumber].style.display = "block";
	
	// sonstige Vorbereitungen
	//createControls();
	updatePageNumber();
	styleControls();
	
	// Gliederungs-Array erstellen
	slideHeadings = new Array(slides.length);
	for (var i = 0, j = -1; i < slides.length; i++) {
		var tempString = slides[i].getAttribute("gliederung");
		if (tempString == "noHighlighting") {
			slideHeadings[i] = -1;
			continue;
		}
		if (tempString != null) {
			j += 2;
		}
		slideHeadings[i] = j;
	}
	
	// Gliederung erstellen
	var firstRun = true;
	var tempOl = null;
	for (var i = 0; i < slides.length; i++) {
		var tempString = slides[i].getAttribute("gliederung");
		if ((tempString != null) && (tempString != "noHighlighting")) {
			if (!firstRun) {
				var tempSpan2 = window.document.createElement("span");
				tempSpan2.textContent = headingsSeperator;
				window.document.getElementById("headings").appendChild(tempSpan2);
			}
			var tempSpan = window.document.createElement("span");
			tempSpan.textContent = tempString;
			// onclick="goToSlide(2)"
			tempSpan.setAttribute("onclick", "goToSlide(" + i + ")");
			window.document.getElementById("headings").appendChild(tempSpan);
			if (firstRun) {
				tempOl = window.document.createElement("ol");
				gliederung.appendChild(tempOl);
			}
			var tempLi = window.document.createElement("li");
			tempLi.textContent = tempString;
			//tempLi.
			tempOl.appendChild(tempLi);
			firstRun = false;
		}
	}
	
	// aktuelle �berschrift aktiv schalten
	var actHeading = slideHeadings[actSlideNumber];
	if (actHeading > 0) {
		headings[actHeading - 1].style.color = actHeadingColor;
	}
}

function readCookie() {
	myCookie = document.cookie;
	actSlideNumber = parseInt(myCookie.substr(myCookie.search('actSlideNumber=')+15));
	bodyMarginBottom = parseInt(myCookie.substr(myCookie.search('bodyMarginBottom=')+17));
	bodyMarginLeft = parseInt(myCookie.substr(myCookie.search('bodyMarginLeft=')+15));
	bodyMarginRight = parseInt(myCookie.substr(myCookie.search('bodyMarginRight=')+16));
	bodyMarginTop = parseInt(myCookie.substr(myCookie.search('bodyMarginTop=')+14));
	slidesBackgroundColor = myCookie.substr((myCookie.search('slidesBackgroundColor=')+22), 7);
}

function writeCookie() {
	var now = new Date();
	var expireDate = new Date(now.getTime() +1000*60*60*24*365); // Cookie ein Jahr gültig
	document.cookie = "actSlideNumber=" + actSlideNumber + ";";
	document.cookie = "bodyMarginBottom=" + bodyMarginBottom + ";";
	document.cookie = "bodyMarginLeft=" + bodyMarginLeft + ";";
	document.cookie = "bodyMarginRight=" + bodyMarginRight + ";";
	document.cookie = "bodyMarginTop=" + bodyMarginTop + ";";
	document.cookie = "slidesBackgroundColor=" + slidesBackgroundColor + ";";
	document.cookie = "expires=" + expireDate.toGMTString() + ";";
}

function goToSlide(newSlideNumber) {
	if ((newSlideNumber == actSlideNumber) || (newSlideNumber < 0) || (newSlideNumber + 1 > slides.length)) {
		// slide wird schon gezeigt
		// oder number ist nicht sinnvoll gew�hlt
		return;
	}
	slides[actSlideNumber].style.display = "none";
	slides[newSlideNumber].style.display = "block";
	
	styleHeadings(newSlideNumber);
	actSlideNumber = newSlideNumber;
	styleControls();
	updatePageNumber();
}

function styleControls() {
	if (actSlideNumber == 0) {
		goFirst.style.opacity = inactive;
		goPrevious.style.opacity = inactive;
	} else {
		goFirst.style.opacity = active;
		goPrevious.style.opacity = active;
	}	
		
	if (actSlideNumber + 1 == slides.length) {
		goNext.style.opacity = inactive;
		goLast.style.opacity = inactive;
	} else {
		goNext.style.opacity = active;
		goLast.style.opacity = active;
	}
}

function goToNextSlide() {
	goToSlide(actSlideNumber + 1);
}

function goToPreviousSlide() {
	goToSlide(actSlideNumber - 1);
}

function goToFirstSlide() {
	goToSlide(0);
}

function goToLastSlide() {
	goToSlide(slides.length - 1);
}

function showPreferences () {
	preferences.style.display = "block";
	preferencesIcon.style.opacity = inactive;
}

function hidePreferences() {
	preferences.style.display = "none";
	preferencesIcon.style.opacity = active;
}

function setBodyMargins() {
	body.style.marginLeft = bodyMarginLeft;
	body.style.marginRight = bodyMarginRight;
	body.style.marginTop = bodyMarginTop;
	body.style.marginBottom = bodyMarginBottom;
}

function incrementBodyMargin(edge) {
	switch (edge) {
		case "left":
			bodyMarginLeft += marginStep;
			body.style.marginLeft = bodyMarginLeft;
			break;
		case "right":
			bodyMarginRight += marginStep;
			body.style.marginRight = bodyMarginRight;
			break;
		case "top":
			bodyMarginTop += marginStep;
			body.style.marginTop = bodyMarginTop;
			break;
		case "bottom":
			bodyMarginBottom += marginStep;
			body.style.marginBottom = bodyMarginBottom;
			break;
		default:
			alert("Falscher Parameter\n" + 
				"incrementBodyMargin('left' || 'right' || 'top' || 'bottom')");
	}
}

function decrementBodyMargin(edge) {
	switch (edge) {
		case "left":
			bodyMarginLeft -= marginStep;
			body.style.marginLeft = bodyMarginLeft;
			break;
		case "right":
			bodyMarginRight -= marginStep;
			body.style.marginRight = bodyMarginRight;
			break;
		case "top":
			bodyMarginTop -= marginStep;
			body.style.marginTop = bodyMarginTop;
			break;
		case "bottom":
			bodyMarginBottom -= marginStep;
			body.style.marginBottom = bodyMarginBottom;
			break;
		default:
			alert("Falscher Parameter\n" + 
				"decrementBodyMargin('left' || 'right' || 'top' || 'bottom')");
	}
}

function resetBodyMargin(edge) {
	switch (edge) {
		case "left":
			bodyMarginLeft = bodyMargin;
			body.style.marginLeft = bodyMarginLeft;
			break;
		case "right":
			bodyMarginRight = bodyMargin;
			body.style.marginRight = bodyMarginRight;
			break;
		case "top":
			bodyMarginTop = bodyMargin;
			body.style.marginTop = bodyMarginTop;
			break;
		case "bottom":
			bodyMarginBottom = bodyMargin;
			body.style.marginBottom = bodyMarginBottom;
			break;
		default:
			alert("Falscher Parameter\n" + 
				"resetBodyMargin('left' || 'right' || 'top' || 'bottom')");
	}
}

function changeSlideNumber() {
	var newSlideNr = prompt("Welche Seite soll angezeigt werden? ", "");
	// (number == actSlideNumber) || (number < 0) || (number + 1 > slides.length)
	if(isNaN(newSlideNr)) {
		alert("Es muss eine Zahl zwischen 1 und " + slides.length + " eingeben werden!");
		return;
	}
	newSlideNr--;
	if (newSlideNr == actSlideNumber) {
		return;
	}
	if ((newSlideNr < 0) || (newSlideNr + 1 > slides.length)) {
		alert("Zahl muss zwischen 1 und " + slides.length + " liegen");
		return;
	}
	goToSlide(newSlideNr);
}

function zoomIn(image) {
	image.width *= zoomFactor;
}