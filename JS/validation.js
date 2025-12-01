
(function(){
    // helpers
    function hasUpper(str){ return /[A-Z]/.test(str); }
    function hasLower(str){ return /[a-z]/.test(str); }
    function isEmail(str){ return /^\S+@\S+\.\S+$/.test(str); }

    function ensureErrorElem(input){
        let next = input.nextElementSibling;
        if (!next || !next.classList || !next.classList.contains('error-msg')){
            const span = document.createElement('div');
            span.className = 'error-msg';
            input.parentNode.insertBefore(span, input.nextSibling);
            return span;
        }
        return next;
    }

    function setValid(input, msg){
        input.classList.remove('invalid');
        input.classList.add('valid');
        const err = ensureErrorElem(input);
        err.textContent = msg || '';
        err.style.display = msg ? 'none' : 'none';
    }

    function setInvalid(input, msg){
        input.classList.remove('valid');
        input.classList.add('invalid');
        const err = ensureErrorElem(input);
        err.textContent = msg || 'Invalid';
        err.style.display = 'block';
    }

    // validation functions
    function validateUsernameField(input){
        const v = input.value || '';
        if (v.length < 5) { setInvalid(input, 'Username must be at least 5 characters'); return false; }
        if (!hasUpper(v) || !hasLower(v)) { setInvalid(input, 'Username must contain upper and lower case letters'); return false; }
        setValid(input);
        return true;
    }

    function validatePasswordField(input){
        const v = input.value || '';
        if (v.length < 10){ setInvalid(input, 'Password must be at least 10 characters'); return false; }
        setValid(input);
        return true;
    }

    function validatePasswordMatch(pwInput, confirmInput){
        const pw = pwInput.value || '';
        const c = confirmInput.value || '';
        if (c === ''){ setInvalid(confirmInput, 'Please repeat the password'); return false; }
        if (pw !== c){ setInvalid(confirmInput, 'Passwords do not match'); return false; }
        setValid(confirmInput);
        return true;
    }

    function validateEmailField(input){
        const v = input.value || '';
        if (!isEmail(v)){ setInvalid(input, 'Enter a valid email address'); return false; }
        setValid(input);
        return true;
    }

    // wire up registration form (by id if present)
    const regForm = document.getElementById('registrationForm');
    const regUsername = document.getElementById('username');
    const regPassword = document.getElementById('password');
    const regConfirm = document.getElementById('confirm-password');
    if (regForm && regUsername && regPassword && regConfirm){
        // attach listeners
        regUsername.addEventListener('input', function(){ validateUsernameField(regUsername); });
        regPassword.addEventListener('input', function(){ validatePasswordField(regPassword); if (regConfirm.value) validatePasswordMatch(regPassword, regConfirm); });
        regConfirm.addEventListener('input', function(){ validatePasswordMatch(regPassword, regConfirm); });
        // live email validation if present
        const regEmail = regForm.querySelector('input[type="email"]');
        if (regEmail) regEmail.addEventListener('input', function(){ validateEmailField(regEmail); });

        regForm.addEventListener('submit', function(e){
            const uOk = validateUsernameField(regUsername);
            const pOk = validatePasswordField(regPassword);
            const mOk = validatePasswordMatch(regPassword, regConfirm);
            const email = regForm.querySelector('input[type="email"]');
            const eOk = email ? validateEmailField(email) : true;
            if (!(uOk && pOk && mOk && eOk)){
                e.preventDefault();
                const firstInvalid = regForm.querySelector('.invalid');
                if (firstInvalid) firstInvalid.focus();
            }
        });
    }

    // wire up login form (by id if present, fallback to heuristic)
    const loginForm = document.getElementById('loginForm') || (function(){
        const forms = document.querySelectorAll('form');
        for(const f of forms){
            const btn = f.querySelector('button[type="submit"]');
            if (!btn) continue;
            const txt = (btn.textContent||'').toLowerCase();
            if (txt.includes('login') || txt.includes('sign in')) return f;
        }
        return null;
    })();

    if (loginForm){
        const pw = loginForm.querySelector('#password');
        const em = loginForm.querySelector('input[type="email"]') || loginForm.querySelector('#email');
        if (pw){ pw.addEventListener('input', function(){ validatePasswordField(pw); }); }
        if (em){ em.addEventListener('input', function(){ validateEmailField(em); }); }
        loginForm.addEventListener('submit', function(e){
            const okPw = pw ? validatePasswordField(pw) : true;
            const okEm = em ? validateEmailField(em) : true;
            if (!(okPw && okEm)){
                e.preventDefault();
                const firstInvalid = loginForm.querySelector('.invalid');
                if (firstInvalid) firstInvalid.focus();
            }
        });
    }

    // personal information form validation (by id)
    const personalForm = document.getElementById('personalForm');
    if (personalForm){
        const first = document.getElementById('firstName');
        const last = document.getElementById('lastName');
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');

        function validateNameField(input){
            if (!input) return true;
            const v = (input.value||'').trim();
            if (v.length < 2){ setInvalid(input, 'Please enter at least 2 characters'); return false; }
            setValid(input); return true;
        }

        function validatePhoneField(input){
            if (!input) return true;
            const v = (input.value||'').trim();
            if (!v) { setValid(input); return true; } // optional
            if (!/^[-+()\d\s]{6,}$/.test(v)){ setInvalid(input, 'Enter a valid phone number'); return false; }
            setValid(input); return true;
        }

        if (first) first.addEventListener('input', () => validateNameField(first));
        if (last) last.addEventListener('input', () => validateNameField(last));
        if (email) email.addEventListener('input', () => validateEmailField(email));
        if (phone) phone.addEventListener('input', () => validatePhoneField(phone));

        personalForm.addEventListener('submit', function(e){
            const okFirst = validateNameField(first);
            const okLast = validateNameField(last);
            const okEmail = validateEmailField(email);
            const okPhone = validatePhoneField(phone);
            if (!(okFirst && okLast && okEmail && okPhone)){
                e.preventDefault();
                const firstInvalid = personalForm.querySelector('.invalid');
                if (firstInvalid) firstInvalid.focus();
            }
        });
    }
})();
