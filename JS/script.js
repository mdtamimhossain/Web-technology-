(function () {
    // Ensure a theme toggle exists and works across pages.
    let toggleBtn = document.getElementById("themeToggle");

    // If no toggle in HTML, inject one into the navbar (if present)
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

    // If still no toggle, gracefully exit (site may not have a navbar)
    if (!toggleBtn) return;

    // Initialize from localStorage
    const saved = localStorage.getItem('theme');
    if (saved === 'dark') {
        document.body.classList.add('dark-mode');
        toggleBtn.textContent = '☀️';
    } else {
        // default: light
        document.body.classList.remove('dark-mode');
        toggleBtn.textContent = '🌙';
    }

    // Click handler
    toggleBtn.addEventListener('click', () => {
        const isDark = document.body.classList.toggle('dark-mode');
        if (isDark) {
            localStorage.setItem('theme', 'dark');
            toggleBtn.textContent = '☀️';
        } else {
            localStorage.setItem('theme', 'light');
            toggleBtn.textContent = '🌙';
        }
    });

    // Optional: respond to system preference changes
    try {
        const mq = window.matchMedia('(prefers-color-scheme: dark)');
        mq.addEventListener && mq.addEventListener('change', (e) => {
            // Only apply system change when user hasn't explicitly chosen
            if (!localStorage.getItem('theme')) {
                if (e.matches) {
                    document.body.classList.add('dark-mode');
                    toggleBtn.textContent = '☀️';
                } else {
                    document.body.classList.remove('dark-mode');
                    toggleBtn.textContent = '🌙';
                }
            }
        });
    } catch (e) {
        // older browsers: ignore
    }
})();
/* validation logic moved to JS/validation.js */