function goodbye(e) {
    "use strict";
	if (!e) {
        e = window.event;
    }
	//e.cancelBubble is supported by IE - this will kill the bubbling process.
	e.cancelBubble = true;

	//e.stopPropagation works in Firefox.
	if (e.stopPropagation) {
		e.stopPropagation();
		e.preventDefault();
	}
}
window.onbeforeunload = goodbye;
