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
    return getCookie('storm-user') != null
}

function getUser() {
    const cookie = getCookie('storm-user');
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
        let usernameInitials = 'BG';
        if (user.name.includes(' ')) {
            const letters = user.name.split(' ');
            usernameInitials = letters[0][0] + letters[1][0];
        } else {
            usernameInitials = user.name.slice(0, 1);
        }
        const el = document.getElementById('user-authenticated');
        let authenticatedTemplate = document.getElementById('user-authenticated-template').innerHTML;
        authenticatedTemplate = authenticatedTemplate.replace('%username%', usernameInitials);
        el.innerHTML = authenticatedTemplate;
        if (user.panel) {
            document.getElementById('panel').classList.remove('hidden');
        }
        let profile = null;
        if (user.photo != null) {
            profile = document.getElementById('profile-photo');
            profile.src = "/media/profile/" + user.photo;
        } else {
            profile = document.getElementById('profile-initials');
        }
        profile.classList.remove('hidden');
    } else {
        const anonymousUserEl = document.getElementById('user-anonymous');
        if (anonymousUserEl != null) {
            anonymousUserEl.classList.remove('hidden');
        }
    }
};
