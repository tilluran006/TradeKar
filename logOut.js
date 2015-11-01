
function deleteCookie(cname) {
	var expires = "expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
	var text = cname + "=000; " + expires;
	document.cookie = text;
}

function logout() {
	var cookies = ['id', 'sessionId'];
	for(var i in cookies) {
		deleteCookie(cookies[i]);
	}
	window.location.assign('login.php');
}

