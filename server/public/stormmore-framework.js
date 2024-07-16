//noinspection JSUnusedGlobalSymbols

class StormEvent {
    constructor(event) {
        this.ori = event;
        this.target = new StormElement(event.target);
    }

    preventDefault() {
        this.ori.preventDefault();
    }
    preventWhenKeyIsPressed(keyCode) {
        if (this.ori.keyCode === keyCode)
            this.ori.preventDefault();
    }
    preventWhenKeysArePressed() {
        for(const keyCode of arguments) {
            console.log(keyCode);
            this.preventWhenKeyIsPressed(keyCode);
        }
    }

    isKeyPressed(keyCode) {
        return this.ori.keyCode === keyCode;
    }
}

class StormElement {
    constructor(htmlElement) {
        this.ori = htmlElement;
    }

    addEventListener(eventName, listener) {
        this.ori.addEventListener(eventName, e => { listener(new StormEvent(e), this) });
        return this;
    }
    addEventListenerOn(eventName, path, listener) {
        const el = this.find(path);
        if (el == null) return this;

        el.addEventListener(eventName, listener);
        return this;
    }
    addEventListenerOnAll(eventName, path, listener) {
        const elements = this.findAll(path);
        elements.forEach(el => {
            el.addEventListener(eventName, listener);
        });
        return this;
    }

    keyPress(listener) {
        return this.addEventListener('keypress', listener);
    }
    keyPressOn(path, listener) {
        return this.addEventListenerOn('keypress', path, listener);
    }
    keyPressOnAll(path, listener) {
        return this.addEventListenerOnAll('keypress', path, listener);
    }

    click(listener) {
        return this.addEventListener('click', listener);
    }
    clickOn(path, listener) {
        return this.addEventListenerOn('click', path, listener);
    }
    clickOnAll(path, listener) {
        return this.addEventListenerOnAll('click', path, listener);
    }

    input(listener) {
        return this.addEventListener('input', listener);
    }
    inputOn(path, listener) {
        return this.addEventListenerOn('input', path, listener);
    }
    inputOnAll(path, listener) {
        return this.addEventListenerOnAll('input', path, listener);
    }

    focus() {
        this.ori.focus();
        return this;
    }

    setHeight(height, unit = 'px') {
        this.ori.style.height = height + unit;
        return this;
    }

    getHeight() {
        return this.ori.style.height;
    }

    find(path) {
        const htmlElement = this.ori.querySelector(path);
        if (htmlElement == null) return;

        return new StormElement(htmlElement);
    }

    findAll(path) {
        const elements = [];
        const htmlElements = this.ori.querySelectorAll(path);
        htmlElements.forEach((htmlElement) => {
            elements.push(new StormElement(htmlElement));
        });
        return elements;
    }
}

class $ {
    static in(path, callback) {
        const el = this.find(document, path);
        if (el && callback) {
            callback(el);
        }
    }

    static find(context, path) {
        const el = context.querySelector(path);
        if (el) {
            return new StormElement(el);
        }
        return null;
    }

    static findAll(context, path) {
        return context.querySelectorAll(path);
    }
}