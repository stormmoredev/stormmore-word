function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return null;
}

function isAuthenticated() {
    return getCookie('user') != null
}

function getUser() {
    const cookie = getCookie('user');
    const decodedCookie = decodeURIComponent(cookie);
    return JSON.parse(decodedCookie);
}

function handleComments() {
    const writeCommentEl = document.getElementById('write-comment');
    if (writeCommentEl != null) {
        const isAuth = isAuthenticated();
        let element;
        if (isAuth) {
            element = document.getElementById('write-comment-authorized');
        }
        if (!isAuth) {
            element = document.getElementById('write-comment-not-authorized');
        }
        writeCommentEl.innerHTML = element.innerHTML;
    }
}

window.onload = function() {
    if (isAuthenticated()) {
        const user = getUser();
        const el = document.getElementById('user-authenticated');
        let authenticatedTemplate = document.getElementById('user-authenticated-template').innerHTML;
        authenticatedTemplate = authenticatedTemplate.replace('%username%', user.name);
        el.innerHTML = authenticatedTemplate;
        if (user.panel) {
            document.getElementById('backend-panel').classList.remove('hidden');
        }
    } else {
        const anonymousUserEl = document.getElementById('user-anonymous');
        if (anonymousUserEl != null) {
            anonymousUserEl.classList.remove('hidden');
        }
    }
};
