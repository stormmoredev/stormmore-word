window.addEventListener('load', function() {
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
                nameInput.value = selected.getElementsByClassName('title')[0].innerText;
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
});