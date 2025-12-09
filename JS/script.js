(function () {

    let toggleBtn = document.getElementById("themeToggle");
    //console.log("Theme toggle script loaded.", toggleBtn);


    if (!toggleBtn) {
        const navRight = document.querySelector('.nav-right') || document.querySelector('.nav-container');
        if (navRight) {
            toggleBtn = document.createElement('button');
            toggleBtn.id = 'themeToggle';
            toggleBtn.className = 'theme-toggle';
            toggleBtn.setAttribute('aria-label', 'Toggle theme');
            navRight.appendChild(toggleBtn);
        }
    }


    if (!toggleBtn) return;

    // Initialize from localStorage
    const saved = localStorage.getItem('theme');
    if (saved === 'dark') {
        document.body.classList.add('dark-mode');
        toggleBtn.textContent = '☀️';
    } else {
        document.body.classList.remove('dark-mode');
        toggleBtn.textContent = '🌙';
    }

    // Click handler
    toggleBtn.addEventListener('click', () => {
        const isDark = document.body.classList.toggle('dark-mode');
        //console.log("Toggled theme. Dark mode?", isDark);
        if (isDark) {
            localStorage.setItem('theme', 'dark');
            toggleBtn.textContent = '☀️';
        } else {
            localStorage.setItem('theme', 'light');
            toggleBtn.textContent = '🌙';
        }
    });
})();
