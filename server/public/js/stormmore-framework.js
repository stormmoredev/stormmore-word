//noinspection JSUnusedGlobalSymbols

class StormEvent {
    constructor(event) {
        this.ori = event;
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

class StormNode {
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

    getAttribute(name) {
        return this.ori.getAttribute(name);
    }

    /**
     * return url for A elements
     * @returns string href
     */
    getUrl() {
        return this.ori.getAttribute('href');
    }

    /**
     * returns action url
     * @returns {string}
     */
    getAction() {
        return this.ori.getAttribute('action');
    }

    getMethod() {
        return this.ori.getAttribute('method');
    }

    show() {
        this.ori.style.removeProperty('display');
    }

    hide() {
        this.ori.style.display = 'none';
    }

    /**
     * @returns {FormData|null}
     */
    formData() {
        if (!this.is('form')) {
            $.logW(`Can't serialize ${this.tag()} element to FormData`);
            return null;
        }

        const formData = new FormData();
        const elements = this.ori.querySelectorAll("input, select, textarea");
        for(let element of elements) {
            const type = element.getAttribute('type');
            const checked = element.checked;
            const name = element.name;
            const value = element.value;
            if (type === 'file') {
                if (value) {
                    formData.append(name, value);
                    formData.append(name + "_data", element.files[0]);
                }
                continue
            }
            if ((type === 'radio' || type === 'checkbox') && !checked)
                continue;
            if(name) {
                formData.append(name, value);
            }
        }
        return formData;
    }

    formAction() {
        if (!this.is('form')) {
            $.logW(`Can't get action url from ${this.tag()} element.`);
            return;
        }
        return this.ori.getAttribute('action');
    }

    setValue(value) {
        const tagName = this.ori.tagName.toLowerCase();
        if (tagName === 'input' || tagName === 'textarea') {
            this.ori.value = value;
        }
        if (tagName === 'img') {
            this.ori.setAttribute('src', value);
        }
        else {
            this.ori.innerText = value;
        }
    }

    getValue() {
        const tagName = this.ori.tagName.toLowerCase();
        if (tagName === 'input' || tagName === 'textarea') {
            return this.ori.value;
        }
        if (tagName === 'img') {
            return this.ori.getAttribute('src');
        }
        else {
            return this.ori.innerText.trim();
        }
    }

    /**
     *
     * @param {string|array} name or names in case of array
     */
    is(name) {
        if (typeof name == 'string' && this.ori.tagName.toLowerCase() === name) {
            return true;
        }
    }

    /**
     * return tag lowered tag name
     * @returns {string}
     */
    tag() {
        return this.ori.tagName.toLowerCase();
    }

    setAttribute(name, value) {
        this.ori.setAttribute(name, value);
    }

    find(path) {
        const htmlElement = this.ori.querySelector(path);
        if (htmlElement == null) return;

        return new StormNode(htmlElement);
    }

    findAll(path) {
        const elements = [];
        const htmlElements = this.ori.querySelectorAll(path);
        htmlElements.forEach((htmlElement) => {
            elements.push(new StormNode(htmlElement));
        });
        return elements;
    }
}

class StormResponse {
    constructor(response) {
        this.response = response;
    }

    json() {
        return JSON.parse(this.response);
    }
}

class StormComponent {
    toHtmlBindings = { }
    fromHtmlBindings = { }
    properties = { }

    init() { }

    setNode(node) {
        this.node = node;
    }

    remove() { this.node.ori.remove(); }

    /**
     * return form by query selector or closest one
     * @param {String} formQuerySelector
     * @returns {StormNode}
     */
    getForm(formQuerySelector = '') {
        if (formQuerySelector !== '') {
            return this.node.find(formQuerySelector);
        }
        if (this.node.is('form')) {
            return this.node;
        }
        return this.node.find('form');
    }

    getFormData(formQuerySelector = '') {
        const form = this.getForm(formQuerySelector);
        if (form === null) {
            return null;
        }

        return {
            action: form.getAction(),
            method: form.getMethod(),
            data: form.formData()
        }
    }

    /**
     * Fetch form in component. Url is taken from action.
     * @param {Object} opt - Options for fetching form. Optional.
     * @param {string} opt.querySelector - form query selector.
     * @param {string} opt.action - action url for request.
     * @param {string} opt.method - request method e.g. get/post
     * @param {FormData} opt.data  - form date. Optional.
     * @returns {*}
     */
    submitForm(opt = {}) {
        const querySelector = $.getFromOptional('querySelector', opt, '');
        const formData  = this.getFormData(querySelector);

        const action = $.getFromOptional('action', opt, formData.action);
        const method = $.getFromOptional('method', opt, formData.method);
        const data   = $.getFromOptional('data', opt, formData.data)

        return this.fetch(action, {
            method: method,
            body: data
        });
    }

    broadcastHtmlBindings(property, broadcaster) {
        if (this.toHtmlBindings.hasOwnProperty(property)) {
            for (const node of this.toHtmlBindings[property]) {
                if (broadcaster !== node) {
                    node.setValue(broadcaster.getValue());
                }
            }
        }
    }

    addToBinding(propertyName, attributeValue, element, action) {
        if  (!this.toHtmlBindings.hasOwnProperty(propertyName)) {
            this.toHtmlBindings[propertyName] = [];
        }
        this.toHtmlBindings[propertyName].push({
            element: element,
            attributeValue: attributeValue,
            action: action
        });
    }

    /**
     * @param {object} obj
     */
    set(obj) {
        for(let propertyName of Object.keys(obj)) {
            this.properties[propertyName] = obj[propertyName];
            if (this.toHtmlBindings.hasOwnProperty(propertyName)) {
                for (const binding of this.toHtmlBindings[propertyName]) {
                    binding.action(this, binding.element, propertyName, binding.attributeValue);
                }
            }
        }
    }

    get(obj) {

    }

    on(property, callback) {
        if (!this.fromHtmlBindings.hasOwnProperty(property)) {
            this.fromHtmlBindings[property] = callback;
        }
    }

    /**
     * @param {StormComponent} component
     */
    append(component) {
        const componentName = component.constructor.name;
        this.#loadTemplateDocFragment(componentName).
        then((templateDocFragment) => {
            component.setNode(new StormNode(templateDocFragment));
            Mounter.mount(component);
            component.init();
            this.node.ori.appendChild(templateDocFragment);
        }).
        catch(e => {
            $.logError(e);
        })
    }

    /**
     * @param {string} componentName
     * @returns {Promise<Element>}
     */
    #loadTemplateDocFragment(componentName) {
        return new Promise((resolve, reject) => {
            const templateNode = $.findByAttribute(document, 'for', componentName);
            if (templateNode == null) { reject(`Template for '${componentName}' not found.`); }
            if (templateNode.content.children.length === 1) {
                resolve(templateNode.content.children[0].cloneNode(true));
            } else {
                const div = document.createElement("div");
                for(let child of templateNode.content.children) {
                    div.appendChild(child.cloneNode(true));
                }
                resolve(div);
            }
        });
    }
}


class Mounter {
    static #frameworkElements(component) {
        let frameworkElements = Array
            .from(component.node.ori.querySelectorAll('*'))
            .filter(e => this.#frameworkAttributes(e).length);
        if (this.#frameworkAttributes(component.node.ori).length) {
            frameworkElements.push(component.node.ori);
        }
        return frameworkElements;
    }

    /**
     * @param element
     * @returns {Attr[]}
     */
    static #frameworkAttributes(element) {
        return Array
            .from(element.attributes)
            .filter(({name}) => name.startsWith($.prefix + "-"));
    }

    static getProperty(component, propertyName) {
        return component.properties[propertyName];
    }

    static setProperty(component, propertyName, value) {
        component.properties[propertyName] = value;
    }

    static mount(component) {
        const bindable = {
            'if': (component, element, propertyName) => {
                const propertyValue = this.getProperty(component, propertyName);
                if (propertyValue === false) element.hide();
                if (propertyValue === true) element.show();
            },
            'if-not': (component, element, propertyName) => {
                const propertyValue = this.getProperty(component, propertyName);
                if (propertyValue === false) element.show();
                if (propertyValue === true) element.hide();
            },
            'bind': (component, element, propertyName) => {
                const propertyValue = this.getProperty(component, propertyName);
                if (propertyValue == undefined) return;
                element.setValue(propertyValue);
            },
            'bind-up': (component, element, propertyName) => {
                const propertyValue = this.getProperty(component, propertyName);
                if (propertyValue == undefined) return;
                element.setValue(propertyValue);
            },
            'disabled': (component, element, propertyName) => {
                const propertyValue = this.getProperty(component, propertyName);
                if (typeof propertyValue == 'boolean') {
                    element.ori.disabled =  propertyValue;
                }
            },
            'class': (component, element, propertyName, attributeValue) => {

            }
        };
        const events = ['click', 'direct-click']
        const elements = this.#frameworkElements(component);
        for(let element of elements) {
            for(let attribute of this.#frameworkAttributes(element)) {
                const sElement = new StormNode(element);
                const attributeName = attribute.name.replace($.prefix + "-", '');
                const attributeValue = attribute.value;
                const propertyName = attribute.value;

                if (bindable.hasOwnProperty(attributeName)) {
                    if (attributeName == 'bind-up') {
                        this.setProperty(component, propertyName, sElement.getValue());
                        component.broadcastHtmlBindings(attributeValue, element.ori);
                    }
                    const action = bindable[attributeName];
                    action(component, sElement, propertyName, attributeValue);
                    component.addToBinding(propertyName, attributeValue, sElement, action);
                    /*
                    if (element.is('input') || element.is('textarea')) {
                        element.addEventListener('input', () => {
                            const value = element.getValue();
                            this[propName] = value;
                            if (this.fromHtmlBindings.hasOwnProperty(propName)) {
                                this.fromHtmlBindings[propName](value);
                            }
                            this.broadcastHtmlBindings(propName, element);
                        })
                    }
                     */
                    continue;
                }

                const isDirect = attributeName.startsWith('direct-');
                const eventName = isDirect ? attributeName.replace('direct-', '') : attributeName;
                if (events.includes(eventName)) {
                    const callback = component[attributeValue];
                    if (callback === undefined) {
                        $.logError(`Callback '${eventName}' is undefined.`);
                        return;
                    }
                    sElement.addEventListener(eventName, e => {
                        if (eventName === 'click') {
                            if (sElement.is('a') || sElement.is('button')) {
                                e.preventDefault();
                            }
                        }
                        if (isDirect && element !== e.ori.target) {
                            return;
                        }
                        component[attributeValue](new StormEvent(e));
                    });
                }
            }
        }
    }
}

class $ {
    static mode = "prod";
    static prefix = 'x';

    static getCookie(cookieName) {
        let name = cookieName + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
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

    /**
     * return obj property or default if undefined
     * @param name - parameter name
     * @param obj - object
     * @param def - default value
     */
    static getFromOptional(name, obj, def) {
        if (obj != null && obj[name] !== undefined) {
            return obj[name];
        }
        return def;
    }

    static log(level, msg) {
        if (level === 'debug' && this.mode === 'debug') {
            console.log(msg);
        }
        if (level === 'debug') {
            console.log(msg);
        }
        if (level === 'warning') {
            console.warn(msg);
        }
        if (level === 'error') {
            console.error(msg);
        }
    }

    static logDebug(msg) {
        this.log('debug', msg);
    }

    static logError(msg) {
        this.log('error', msg);
    }

    static logW(msg) {
        this.log('warning', msg);
    }

    static prefixFrmAttribute(name = '') {
        return this.prefix + '-' + name;
    }

    /**
     *
     * @param context
     * @param {string} name
     * @param {string} value
     * @returns {Element|null}
     */
    static findByAttribute(context, name, value = undefined) {
        let search = `[`;
        search += this.prefixFrmAttribute(name);
        if (value !== undefined)
            search += `= "${value}"`;
        search += ']';

        return context.querySelector(search);
    }

    static app(options = null) {
        if (options !== null) {

        }
        const frmAttrName = this.prefixFrmAttribute('component');
        const elements = document.querySelectorAll(`[${frmAttrName}]`);
        for(let element of elements) {
            const name = element.getAttribute(frmAttrName);
            const component = this.#instantiateComponent(name, element);
            component.init();
            Mounter.mount(component);
        }
    }

    /**
     * @param {string} name
     * @param {Element} element
     * @returns {StormComponent|null}
     */
    static #instantiateComponent(name, element) {
        try {
            let component = eval(`new ${name}()`);
            if (component instanceof StormComponent) {
                component.setNode(new StormNode(element));
                return component;
            }
        } catch(e) {
            this.logError(`Can't instantiate ${name} component.`);
            return null;
        }
    }
}