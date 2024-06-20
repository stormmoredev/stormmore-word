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


function onAboutMeTextChanged(e) {
    return;
    const allowedKeys = [8, 46, 37, 38, 39, 40];
    const input = e.target;
    const maxWords = input.getAttribute('data-maxwords');
    let stats = getTextStatistics(input.value);
    if (stats.words == maxWords) {
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


function dateFormat(format, date) {

    if(!date || date === "")
    {
        date = new Date();
    }
    else if(typeof(date) !== 'object')
    {
        date = new Date(date.replace(/-/g,"/"));
    }

    let string = '',
        mo = date.getMonth(),
        m1 = mo+1,
        dow = date.getDay(),
        d = date.getDate(),
        y = date.getFullYear(),
        h = date.getHours(),
        mi = date.getMinutes(),
        s = date.getSeconds();

    for (let i = 0, len = format.length; i < len; i++) {
        switch(format[i])
        {
            case 'j':
                string+= d;
                break;
            case 'd':
                string+= (d < 10) ? "0"+d : d;
                break;
            case 'l':
                let days = Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
                string+= days[dow];
                break;
            case 'w':
                string+= dow;
                break;
            case 'D':
                days = Array("Sun","Mon","Tue","Wed","Thr","Fri","Sat");
                string+= days[dow];
                break;
            case 'm':
                string+= (m1 < 10) ? "0"+m1 : m1;
                break;
            case 'n':
                string+= m1;
                break;
            case 'F':
                let months = Array("January","February","March","April","May","June","July",
                    "August","September","October","November","December");
                string+= months[mo];
                break;
            case 'M':
                months = Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
                string+= months[mo];
                break;
            case 'Y':
                string+= y;
                break;
            case 'y':
                string+= y.toString().slice(-2);
                break;
            case 'H':
                string+= (h < 10) ? "0"+h : h;
                break;
            case 'g':
                let hour = (h===0) ? 12 : h;
                string+= (hour > 12) ? hour -12 : hour;
                break;
            case 'h':
                hour = (h===0) ? 12 : h;
                hour = ( hour > 12) ? hour -12 : hour;
                string+= (hour < 10) ? "0"+hour : hour;
                break;
            case 'a':
                string+= (h < 12) ? "am" : "pm";
                break;
            case 'i':
                string+= (mi < 10) ? "0"+mi : mi;
                break;
            case 's':
                string+= (s < 10) ? "0"+s : s;
                break;
            case 'c':
                string+= date.toISOString();
                break;
            default:
                string+= format[i];
        }
    }

    return string;
}

const dateDifferenceI18n = [];

function dateDifference(actualDate) {
    const diffInSeconds = Math.abs(new Date() - actualDate) / 1000;
    const days = Math.floor(diffInSeconds / 60 / 60 / 24);
    const hours = Math.floor(diffInSeconds / 60 / 60 % 24);
    const minutes = Math.floor(diffInSeconds / 60 % 60);
    const months = Math.floor(days / 30.4);
    const years = Math.floor(months / 12);

    if (years == 1) {
        return dateDifferenceI18n['date_interval_y_singular'];
    }
    else if (years > 1) {
        return years + dateDifferenceI18n['date_interval_y_plural'].replace('%s', years);
    }
    else if (months == 1) {
        return dateDifferenceI18n['date_interval_m_singular'];
    }
    else if (months > 1 ) {
        return dateDifferenceI18n['date_interval_m_plural'].replace('%s', months);
    }
    else if (days == 1) {
        return dateDifferenceI18n['date_interval_d_singular']
    }
    else if (days > 1) {
        return dateDifferenceI18n['date_interval_d_plural'].replace('%s', days);
    }
    else if (hours == 1) {
        return dateDifferenceI18n['date_interval_h_singular']
    }
    else if (hours > 1) {
        return dateDifferenceI18n['date_interval_h_plural'].replace('%s', hours);
    }
    else if (minutes == 1) {
        return dateDifferenceI18n['date_interval_i_singular'];
    }
    else if (minutes > 1) {
        return dateDifferenceI18n['date_interval_i_plural'].replace('%s', minutes);
    }
    else {
        return dateDifferenceI18n['date_interval_seconds_ago'];
    }
}

function replaceDatetime() {
    let elements = document.getElementsByClassName('convert-to-datetime');
    for(let i = 0; i < elements.length; i++) {
        let el = elements[i];
        let date  = el.getAttribute('data-date');
        let format = el.getAttribute('data-format');
        el.innerHTML = dateFormat(format, date);
    }
}

function replaceDateTimeDiff() {
    let elements = document.getElementsByClassName('convert-to-datetime-diff');
    for(let i = 0; i < elements.length; i++) {
        let el = elements[i];
        let date  = new Date(el.getAttribute('data-date'));

        el.setAttribute('title', date.toLocaleDateString() + " " + date.toLocaleTimeString());
        el.innerHTML = dateDifference(date);
    }
}

function buildAuthentication() {
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
        if (e.keyCode == 13) {
            e.preventDefault();
            document.getElementById('content').focus();
        }
    },
    onContentChange: function(textarea) {
        textarea.style.height = textarea.scrollHeight + "px";
    }
}

window.addEventListener('load', function() {
    replaceDateTimeDiff();
    replaceDatetime();
    buildAuthentication();

    Reply.initialize('write-reply');
    Reply.initialize('write-comment');
});
