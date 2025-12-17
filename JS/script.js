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

// Product Details Tab Switching
function openTab(evt, tabName) {
    // Hide all tab content
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });

    // Remove active class from all tab links
    const tabLinks = document.querySelectorAll('.tab-link');
    tabLinks.forEach(link => {
        link.classList.remove('active');
    });

    // Show the selected tab content
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }

    // Add active class to the clicked tab link
    if (evt && evt.currentTarget) {
        evt.currentTarget.classList.add('active');
    }
}

// Size button selection
document.addEventListener('DOMContentLoaded', function() {
    const sizeButtons = document.querySelectorAll('.size-btn');
    sizeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active from all size buttons in the same group
            const parent = this.closest('.size-options');
            if (parent) {
                parent.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            }
            this.classList.add('active');
        });
    });

    // Color selection
    const colorOptions = document.querySelectorAll('.color-options .color');
    colorOptions.forEach(color => {
        color.addEventListener('click', function() {
            const parent = this.closest('.color-options');
            if (parent) {
                parent.querySelectorAll('.color').forEach(c => c.classList.remove('active'));
            }
            this.classList.add('active');
        });
    });

    // Thumbnail image selection
    const thumbnails = document.querySelectorAll('.thumbnail-images img');
    const mainImage = document.querySelector('.main-image');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            if (mainImage) {
                mainImage.src = this.src;
            }
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
