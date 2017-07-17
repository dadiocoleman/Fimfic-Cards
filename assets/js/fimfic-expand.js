var hideElements = document.getElementsByClassName("hide");
for (i=0; i<hideElements.length; i++) {
hideElements[i].style.display = "none";
}
function hide(elem) {
var hideElement = document.getElementById(elem);
if (hideElement.style.display == "block") {
hideElement.style.display = "none";
} else {
hideElement.style.display = "block";
}
}function show(elem) {var showElement = document.getElementById(elem);if (showElement.style.display == "none") {showElement.style.display = "block";} else {showElement.style.display = "none";}}

function toggle(t) {
	if (t.childNodes[0].innerHTML == "Less") {
		t.childNodes[0].innerHTML = "More";
	} else {
		t.childNodes[0].innerHTML = "Less";
	}
}

function toggleClass(el){
	if(el.className == "fulldesc-expanded"){
		el.className = "fulldesc-hidden";
	} else {
		el.className = "fulldesc-expanded";
	}
}