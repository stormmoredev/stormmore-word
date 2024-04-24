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

function toggleCommentPanel() {
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

function openProfilePhotoDialog() {
    document.getElementById('upload-photo').click();
}

function changedUploadedProfilePhoto(input) {
    const maxPhotoSize = document.getElementById('max-photo-size').value;
    const filename = document.getElementById('profile-photo-name');
    const invalidFile = document.getElementById('profile-photo-invalid');

    if (input.files.length > 0) {
        const size = Math.round(input.files[0].size / 1024);  // in KB
        if (size > maxPhotoSize) {
            filename.innerText = '';
            invalidFile.classList.remove('hidden');
            input.value = null;
        }
        else {
            filename.innerText = input.files[0].name + " (" + size + " KB)";
            invalidFile.classList.add('hidden');
        }
    }
    else {
        filename.innerText = '';
        invalidFile.classList.add('hidden');
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
        if (user.photo != null && user.photo != '') {
            profile = document.getElementById('profile-photo');
            profile.src = "/media/profile/" + user.photo;
        } else {
            profile = document.getElementById('profile-initials');
        }
        profile.classList.remove('hidden');
    }
    else {
        const anonymousUserEl = document.getElementById('user-anonymous');
        if (anonymousUserEl != null) {
            anonymousUserEl.classList.remove('hidden');
        }
    }
};
