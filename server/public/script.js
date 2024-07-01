function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
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


function onAboutMeTextChanged(e) {
    return;
    const allowedKeys = [8, 46, 37, 38, 39, 40];
    const input = e.target;
    const maxWords = input.getAttribute('data-maxwords');
    let stats = getTextStatistics(input.value);
    if (stats.words === maxWords) {
       const selectedChars = input.selectionEnd - input.selectionStart;

       /*
       if (!allowedKeys.includes(e.keyCode) && selectedChars == 0) {
            e.preventDefault();
            e.stopPropagation();
       }
        */
    }
    console.log(input.value);

    document.getElementById('about-me-count-words').innerText = stats.words;
}

function getTextStatistics(text) {
    let prev = ' ';
    let characters = '';
    let length = text.length;
    let stats = {
        words: 0
    };

    for(let i = 0; i < length; i++) {
        if (text[i] !== ' ' && prev === ' ') {
            characters += text[i];
            stats.words++;
        } else {
            characters += text[i]
        }

        prev = text[i];
    }

    return stats;
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

function buildAuthentication() {
    if (isAuthenticated()) {
        const user = getUser();
        let usernameInitials = '';
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
        if (user.photo != null && user.photo !== '') {
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
}

class Reply {
    static initialize(containerName) {
        const writeCommentEl = document.getElementById(containerName);
        if (writeCommentEl == null) return;

        let element =  containerName + (isAuthenticated() ? '-authorized' : '-unauthorized');
        writeCommentEl.innerHTML = document.getElementById(element).innerHTML;
    }
}

const forumEntry = {
    onTitleKeydown: function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            document.getElementById('content').focus();
        }
    },
    onContentChange: function(textarea) {
        textarea.style.height = textarea.scrollHeight + "px";
    }
}
const blogEntry = {
    onTitleKeydown: function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            document.getElementById('content').focus();
        }
    },
    onContentChange: function(textarea) {
        textarea.style.height = textarea.scrollHeight + "px";
    }
}

class PostGratitude {
    static initialize() {
        document.addEventListener('click', function(event) {
            const closest = event.target.closest('.post-gratitude');
            if (!closest) return;
            event.preventDefault();
            fetch(closest.href, {
                method: 'POST'
            })
            .then((data) => {
                return data.text();
            })
            .then((json) => {
            })
        });
    }
}

document.addEventListener("DOMContentLoaded", function() {
    replaceDateTimeDiff();
    replaceDatetime();
    buildAuthentication();

    PostGratitude.initialize();
    Reply.initialize('write-reply');
    Reply.initialize('write-comment');
});
