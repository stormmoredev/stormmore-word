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

function showOnLoad() {
    const elements = document.getElementsByClassName('show-on-load');
    for (let element of elements) {
        element.classList.remove('hidden');
    }
}

function handleInputWithComboboxes() {
    const comboboxes = document.getElementsByClassName('input-with-combobox');
    for (let combobox of comboboxes) {
        const valueInput = combobox.querySelectorAll('input[type="hidden"]')[0];
        const nameInput = combobox.querySelectorAll('input[type="text"]')[0];
        const list = combobox.getElementsByTagName('ul')[0];
        const items = list.getElementsByTagName('li');
        const button = combobox.getElementsByTagName('button')[0];
        function closeList() {
            list.classList.add('hidden');
            const selectedId = valueInput.value;
            const selected = list.querySelector('li[data-value="' + selectedId + '"]');
            if (selected) {
                const name = selected.getElementsByClassName('title')[0].innerText;
                nameInput.value = name.trim();
            } else {
                nameInput.value = "";
            }
            for (let item of items) {
                item.classList.remove('hidden');
            }
        }
        nameInput.addEventListener('focus', function () {
            list.classList.remove('hidden');
        });
        nameInput.addEventListener('focusout', function () {
            setTimeout(function() { closeList(); }, 150);
        });
        nameInput.addEventListener('keyup', function () {
            valueInput.value = null;
            const q = nameInput.value.toLowerCase();
            for (let item of items) {
                if (item.innerText.toLowerCase().includes(q)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            }
        });
        button.addEventListener('click', function () {
            if (list.classList.contains('hidden')) {
                list.classList.remove('hidden');
            } else {
                list.classList.add('hidden');
            }
        })
        for(let item of items) {
            item.addEventListener('click', function() {
                valueInput.value = item.getAttribute('data-value');
            });
        }
    }
}

document.addEventListener("DOMContentLoaded", showOnLoad);
document.addEventListener("DOMContentLoaded", handleInputWithComboboxes);