class StormUserProvider {
    static user;

    static getUser() {
        if (this.user == null) {
            this.user = { };
            this.user.authenticated = false
            if ($.getCookie('storm-user') != null) {
                const cookie = $.getCookie('storm-user');
                const decodedCookie = decodeURIComponent(cookie);
                this.user = JSON.parse(decodedCookie);
                this.user.authenticated  = true;
            }
        }
        return this.user;
    }
}

class HeaderComponent extends StormComponent {
    init() {
        const user = StormUserProvider.getUser();
        this.set({authenticated: user.authenticated});
        if (user.authenticated) {
            let initials = user.name.slice(0, 1)
            if (user.name.includes(' ')) {
                let letters = user.name.split(' ');
                let initials = letters[0][0] + letters[1][0];
            }
            this.set({
                hasAccessToPanel: user.panel,
                hasPhoto: user.photo !== null && user.photo !== '',
                initials: initials,
                photo: `/media/profile/${user.photo}`
            })
        }
    }
}

class PostComponent extends StormComponent {
    init() {
        const user = StormUserProvider.getUser();
        this.set({isAuthenticated: user.authenticated});
    }

    follow() {
        if (!this.properties.isAuthenticated) {
            this.showAuthenticationModal()
        }
    }

    like(e) {
        if (!this.properties.isAuthenticated) {
            this.showAuthenticationModal()
            return;
        }
        fetch(`/b/vote/${this.properties.id}`, {method: 'post'}).
            then(r => r.json()).
            then(r => {
                if (r.status === 1) {
                    this.set({ likes: parseInt(this.properties.likes) + 1 });
                }
            });
    }

    unlike() {
        fetch(`/b/rmvote/${this.properties.id}`, { method: 'post' }).
        then(r => r.json()).
        then(r => {
            this.set({ likes: this.properties.likes - 1 });
        });
    }

    comment() {
        if (!this.isAuthenticated) {
            this.showAuthenticationModal()
        }
    }

    showAuthenticationModal() {
        const authenticationModal = new AuthenticationModalComponent();
        this.append(authenticationModal);
    }
}

class AuthenticationModalComponent extends StormComponent{
    init() {
        this.gotoSigninForm();
        this.set({
            loginFailed: false
        });
    }

    close() {
        this.remove();
    }

    submit(e) {
        this.set({ disabled: true });
        this.submitForm()
            .then(r => {
                let json = r.json();
                if (json.status == 1) {
                   window.location.reload();
                } else {
                    this.set({disabled:false});
                    this.set({
                        loginFailed: true,
                        password: '',
                        email: ''
                    })
                }
        });
    }

    gotoSignupForm() {
        this.set({
            showSigninForm: false,
            showSignupForm: true
        });
    }

    gotoSigninForm() {
        this.set({
            showSigninForm: true,
            showSignupForm: false
        });
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
        } else {
            filename.innerText = input.files[0].name + " (" + size + " KB)";
            invalidFile.classList.add('hidden');
        }
    } else {
        filename.innerText = '';
        invalidFile.classList.add('hidden');
    }
}


const forumEntry = {
    onTitleKeydown: function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            document.getElementById('content').focus();
        }
    },
    onContentChange: function (textarea) {
        textarea.style.height = textarea.scrollHeight + "px";
    }
}

class BlogEntry {
    static initialize() {
        /*
        $.in('#add-post-form', (form) => {
            form.keyPressOn('#title', e => {
                if (e.isKeyPressed(13)) {
                    e.preventDefault();
                    form.find('#subtitle').focus();
                }
            });
            form.keyPressOn('#subtitle', e => {
                if (e.isKeyPressed(13)) {
                    e.preventDefault();
                    form.find('#content').focus();
                }
            });
            form.inputOn('#content', (e, content) => {
                const scrollHeight = content.ori.scrollHeight;
                content.setHeight(scrollHeight);
            })
        });
        */
    }
}


window.addEventListener("load", function () {
    replaceDateTimeDiff();
    replaceDatetime();
});

document.addEventListener("DOMContentLoaded", function () {
    BlogEntry.initialize();
    const div = document.querySelector('div')

});
