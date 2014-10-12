/* ToDO
- style for printing
- refactoring
- create controls automatically
- english only!
 */


//options
var headingsSeparator = " | ";
var unit = "px";
var bodyMargin = 5;
var marginStep = 5;
var zoomFactor = 1.2;


var slideHeadings = null;
var actSlideNumber = 0;

// stlye stuff
var opacityActive = "1.00";
var opacityInactive = "0.30";
var slidesBackgroundColor = "whitesmoke";
var actHeadingColor = "red";

var bodyMarginBottom = bodyMargin;
var bodyMarginLeft = bodyMargin;
var bodyMarginRight = bodyMargin;
var bodyMarginTop = bodyMargin;

// elements
var body = null;
var control = null;
var tableOfContents = null;
var goFirst = null;
var goLast = null;
var goNext = null;
var goPrevious = null;
var headings = null;
var pageNumber = null;
var preferences = null;
var preferencesIcon = null;
var slides = null;

document.onkeydown = checkKey;

function checkKey(e) {

    e = e || window.event;

    if (e.keyCode == '37') {
        // left arrow
        goToPreviousSlide();
    }
    else if (e.keyCode == '39') {
        // right arrow
        goToNextSlide()
    }
}

function init() {
    initElements();
    setTitle();
    readCookie();
    setBackgroundColorOfSlides();
    makeImagesZoomable();
    setStartSlide();
    updatePageNumber();
    setSectionLinks();
    createControls();
    createSettingsMenu();
}

function initElements() {
    body = window.document.getElementsByTagName("body")[0];
    tableOfContents = window.document.getElementById("tableOfContents");
    headings = window.document.getElementById("headings").getElementsByTagName("span");
    pageNumber = window.document.getElementById("pageNumber");
    slides = window.document.getElementsByClassName("slide");
}

function setTitle() {
    window.document.getElementById("title").firstChild.nodeValue = window.document.getElementsByTagName("title")[0].text;
}

function readCookie() {
    if (document.cookie) {
        readCookie();
        setBodyMargins();
    }
}

function setBackgroundColorOfSlides() {
    for (var i = 0; i < slides.length; i++) {
        slides[i].style.backgroundColor = slidesBackgroundColor;
    }
}

function makeImagesZoomable() {
    for (var i = 0; i < slides.length; i++) {
        var images = slides[i].getElementsByTagName("img");
        for (var j = 0; j < images.length; j++) {
            images[j].setAttribute("onclick", "zoomIn(this)");
            images[j].setAttribute("class", "zoomImage");
        }
    }
}

function setStartSlide() {
    //TODO check if code is doubled...
    if (actSlideNumber < 0) {
        actSlideNumber = 0;
    }
    if (actSlideNumber >= slides.length) {
        actSlideNumber = slides.length - 1;
    }

    slides[actSlideNumber].style.display = "block";
}

function setSectionLinks() {
    slideHeadings = new Array(slides.length);
    for (var i = 0, j = -1; i < slides.length; i++) {
        var tempString = slides[i].getAttribute("section");
        if (tempString == "noHighlighting") {
            slideHeadings[i] = -1;
            continue;
        }
        if (tempString != null) {
            j += 2;
        }
        slideHeadings[i] = j;
    }

    // create table of contents
    var firstSection = true;
    var tempOl = null;
    for (var i = 0; i < slides.length; i++) {
        var tempString = slides[i].getAttribute("section");
        if ((tempString != null) && (tempString != "noHighlighting")) {
            if (!firstSection) {
                var tempSpan2 = window.document.createElement("span");
                tempSpan2.setAttribute("class", "headingsSeparator");
                tempSpan2.textContent = headingsSeparator;
                window.document.getElementById("headings").appendChild(tempSpan2);
            }
            var tempSpan = window.document.createElement("span");
            tempSpan.textContent = tempString;
            tempSpan.setAttribute("onclick", "goToSlide(" + i + ")");
            window.document.getElementById("headings").appendChild(tempSpan);
            if (firstSection) {
                tempOl = window.document.createElement("ol");
                tableOfContents.appendChild(tempOl);
            }
            var tempLi = window.document.createElement("li");
            tempLi.textContent = tempString;
            tempOl.appendChild(tempLi);
            firstSection = false;
        }
    }

    var actHeading = slideHeadings[actSlideNumber];
    if (actHeading > 0) {
        headings[actHeading - 1].style.color = actHeadingColor;
    }
}

function createControls() {
    control = window.document.createElement("div");
    control.setAttribute("id", "control");

    var img1 = window.document.createElement("img");
    img1.setAttribute("src", "../Images/Controls/preferences.png");
    img1.setAttribute("onclick", "showPreferences()");

    goFirst = window.document.createElement("img");
    goFirst.setAttribute("src", "../Images/Controls/goFirst.png");
    goFirst.setAttribute("onclick", "goToFirstSlide()");
    goFirst.setAttribute("id", "goFirst");

    goPrevious = window.document.createElement("img");
    goPrevious.setAttribute("src", "../Images/Controls/goPrevious.png");
    goPrevious.setAttribute("onclick", "goToPreviousSlide()");
    goPrevious.setAttribute("id", "goPrevious");

    goNext = window.document.createElement("img");
    goNext.setAttribute("src", "../Images/Controls/goNext.png");
    goNext.setAttribute("onclick", "goToNextSlide()");
    goNext.setAttribute("id", "goNext");

    goLast = window.document.createElement("img");
    goLast.setAttribute("src", "../Images/Controls/goLast.png");
    goLast.setAttribute("onclick", "goToLastSlide()");
    goLast.setAttribute("id", "goLast");

    control.appendChild(img1);
    control.appendChild(goFirst);
    control.appendChild(goPrevious);
    control.appendChild(goNext);
    control.appendChild(goLast);

    body.insertBefore(control, body.firstChild);

    styleControls();
}

function createSettingsMenu () {
    preferences = window.document.createElement("div");
    preferences.setAttribute("id", "preferences");

    var img1 = window.document.createElement("img");
    img1.setAttribute("src", "../Images/Controls/exit.png");
    img1.setAttribute("onclick", "hidePreferences()");

    var img2 = window.document.createElement("img");
    img2.setAttribute("src", "../Images/Controls/format-justify-fill.png");
    img2.setAttribute("onclick", "styleForPrint()");

    var table = window.document.createElement("table");
    for (var i = 0; i < 5; i++) {
        var tr = window.document.createElement("tr");
        table.appendChild(tr);
        for (var j = 0; j < 5; j++) {
            var td = window.document.createElement("td");
            tr.appendChild(td);

            if (i == 0 && j == 1) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goUp.png");
                img.setAttribute("onclick", "decrementBodyMargin('top')");
                td.appendChild(img);
            }

            if (i == 0 && j == 2) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goTop.png");
                img.setAttribute("onclick", "resetBodyMargin('top')");
                td.appendChild(img);
            }

            if (i == 0 && j == 3) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goDown.png");
                img.setAttribute("onclick", "incrementBodyMargin('top')");
                td.appendChild(img);
            }

            if (i == 1 && j == 0) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goPrevious.png");
                img.setAttribute("onclick", "decrementBodyMargin('left')");
                td.appendChild(img);
            }

            if (i == 1 && j == 4) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goNext.png");
                img.setAttribute("onclick", "decrementBodyMargin('right')");
                td.appendChild(img);
            }

            if (i == 2 && j == 0) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goFirst.png");
                img.setAttribute("onclick", "resetBodyMargin('left')");
                td.appendChild(img);
            }

            if (i == 2 && j == 2) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/edit-redo.png");
                img.setAttribute("onclick", "resetAllBodyMargins()");
                td.appendChild(img);
            }

            if (i == 2 && j == 4) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goLast.png");
                img.setAttribute("onclick", "resetBodyMargin('right')");
                td.appendChild(img);
            }

            if (i == 3 && j == 0) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goNext.png");
                img.setAttribute("onclick", "incrementBodyMargin('left')");
                td.appendChild(img);
            }

            if (i == 3 && j == 4) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goPrevious.png");
                img.setAttribute("onclick", "incrementBodyMargin('right')");
                td.appendChild(img);
            }

            if (i == 4 && j == 1) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goUp.png");
                img.setAttribute("onclick", "decrementBodyMargin('bottom')");
                td.appendChild(img);
            }

            if (i == 4 && j == 2) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goBottom.png");
                img.setAttribute("onclick", "resetBodyMargin('bottom')");
                td.appendChild(img);
            }

            if (i == 4 && j == 3) {
                var img = window.document.createElement("img");
                img.setAttribute("src", "../Images/Controls/goDown.png");
                img.setAttribute("onclick", "incrementBodyMargin('bottom')");
                td.appendChild(img);
            }
        }
    }

    preferences.appendChild(img1);
    preferences.appendChild(img2);
    preferences.appendChild(table);
    body.insertBefore(preferences, body.firstChild);
}

function styleForPrint() {
    alert("currently not implemented!");
}

function styleHeadings(newSlideNumber) {
    var actHeading = slideHeadings[actSlideNumber];
    var newHeading = slideHeadings[newSlideNumber];
    if (actHeading != newHeading) {
        if (actHeading > 0) {
            headings[actHeading - 1].style.color = "black";
        }
        if (newHeading > 0) {
            headings[newHeading - 1].style.color = actHeadingColor;
        }
    }
}

function changeSlideBackgroundColor(newBackgroundColor) {
    slidesBackgroundColor = newBackgroundColor;
    for (var i = 0; i < slides.length; i++) {
        slides[i].style.backgroundColor = slidesBackgroundColor;
    }
}

function updatePageNumber() {
    pageNumber.firstChild.nodeValue = "Seite: " + (actSlideNumber + 1) + "/" + slides.length;
}

function readCookie() {
    var myCookie = document.cookie;
    actSlideNumber = parseInt(myCookie.substr(myCookie.search('actSlideNumber=') + 15));
    bodyMarginBottom = parseInt(myCookie.substr(myCookie.search('bodyMarginBottom=') + 17));
    bodyMarginLeft = parseInt(myCookie.substr(myCookie.search('bodyMarginLeft=') + 15));
    bodyMarginRight = parseInt(myCookie.substr(myCookie.search('bodyMarginRight=') + 16));
    bodyMarginTop = parseInt(myCookie.substr(myCookie.search('bodyMarginTop=') + 14));
    slidesBackgroundColor = myCookie.substr((myCookie.search('slidesBackgroundColor=') + 22), 7);
}

function writeCookie() {
    var now = new Date();
    var expireDate = new Date(now.getTime() + 1000 * 60 * 60 * 24 * 365); // Cookie expires in one year
    document.cookie = "actSlideNumber=" + actSlideNumber + ";";
    document.cookie = "bodyMarginBottom=" + bodyMarginBottom + ";";
    document.cookie = "bodyMarginLeft=" + bodyMarginLeft + ";";
    document.cookie = "bodyMarginRight=" + bodyMarginRight + ";";
    document.cookie = "bodyMarginTop=" + bodyMarginTop + ";";
    document.cookie = "slidesBackgroundColor=" + slidesBackgroundColor + ";";
    document.cookie = "expires=" + expireDate.toGMTString() + ";";
}

function goToSlide(newSlideNumber) {
    if ((newSlideNumber == actSlideNumber) || (newSlideNumber < 0) || (newSlideNumber >= slides.length)) {
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
        goFirst.style.opacity = opacityInactive;
        goPrevious.style.opacity = opacityInactive;
    } else {
        goFirst.style.opacity = opacityActive;
        goPrevious.style.opacity = opacityActive;
    }

    if (actSlideNumber + 1 == slides.length) {
        goNext.style.opacity = opacityInactive;
        goLast.style.opacity = opacityInactive;
    } else {
        goNext.style.opacity = opacityActive;
        goLast.style.opacity = opacityActive;
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

function showPreferences() {
    preferences.style.display = "block";
    preferencesIcon.style.opacity = opacityInactive;
}

function hidePreferences() {
    preferences.style.display = "none";
    preferencesIcon.style.opacity = opacityActive;
}

function setBodyMargins() {
    body.style.marginLeft = bodyMarginLeft + unit;
    body.style.marginRight = bodyMarginRight + unit;
    body.style.marginTop = bodyMarginTop + unit;
    body.style.marginBottom = bodyMarginBottom + unit;
}

function incrementBodyMargin(edge) {
    switch (edge) {
        case "left":
            bodyMarginLeft += marginStep;
            body.style.marginLeft = bodyMarginLeft + unit;
            break;
        case "right":
            bodyMarginRight += marginStep;
            body.style.marginRight = bodyMarginRight + unit;
            break;
        case "top":
            bodyMarginTop += marginStep;
            body.style.marginTop = bodyMarginTop + unit;
            break;
        case "bottom":
            bodyMarginBottom += marginStep;
            body.style.marginBottom = bodyMarginBottom + unit;
            break;
        default:
            alert("wrong parameter\n" +
                "incrementBodyMargin('left' || 'right' || 'top' || 'bottom')");
    }
}

function decrementBodyMargin(edge) {
    switch (edge) {
        case "left":
            bodyMarginLeft -= marginStep;
            body.style.marginLeft = bodyMarginLeft + unit;
            break;
        case "right":
            bodyMarginRight -= marginStep;
            body.style.marginRight = bodyMarginRight + unit;
            break;
        case "top":
            bodyMarginTop -= marginStep;
            body.style.marginTop = bodyMarginTop + unit;
            break;
        case "bottom":
            bodyMarginBottom -= marginStep;
            body.style.marginBottom = bodyMarginBottom + unit;
            break;
        default:
            alert("wrong parameter\n" +
                "decrementBodyMargin('left' || 'right' || 'top' || 'bottom')");
    }
}

function resetBodyMargin(edge) {
    switch (edge) {
        case "left":
            bodyMarginLeft = bodyMargin;
            body.style.marginLeft = bodyMarginLeft + unit;
            break;
        case "right":
            bodyMarginRight = bodyMargin;
            body.style.marginRight = bodyMarginRight + unit;
            break;
        case "top":
            bodyMarginTop = bodyMargin;
            body.style.marginTop = bodyMarginTop + unit;
            break;
        case "bottom":
            bodyMarginBottom = bodyMargin;
            body.style.marginBottom = bodyMarginBottom + unit;
            break;
        default:
            alert("wrong parameter\n" +
                "resetBodyMargin('left' || 'right' || 'top' || 'bottom')");
    }
}

function resetAllBodyMargins() {
    resetBodyMargin("left");
    resetBodyMargin("right");
    resetBodyMargin("top");
    resetBodyMargin("bottom");
}

function changeSlideNumber() {
    var newSlideNr = prompt("To which page would you like to jump? ", "");
    if (isNaN(newSlideNr)) {
        alert("A page number between 1 and " + slides.length + " is required!");
        return;
    }
    newSlideNr--;
    if (newSlideNr == actSlideNumber) {
        return;
    }
    if ((newSlideNr < 0) || (newSlideNr + 1 > slides.length)) {
        alert("A page number between 1 and " + slides.length + " is required!");
        return;
    }
    goToSlide(newSlideNr);
}

function zoomIn(image) {
    image.width *= zoomFactor;
}