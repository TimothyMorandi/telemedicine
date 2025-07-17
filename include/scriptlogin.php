<script>
// scriptlogin.js
// Advanced login page script: handles password show/hide with icon toggle, error message close, and responsive hamburger menu

document.addEventListener('DOMContentLoaded', function () {
    // =====================
    // Password show/hide logic with icon toggle
    // =====================
    const showHideBtns = document.querySelectorAll('.show-hide-btn');
    showHideBtns.forEach(btn => {
        // Find the icon element inside the button (if present)
        let icon = btn.querySelector('i');
        btn.addEventListener('click', function () {
            const targetInputId = btn.getAttribute('data-target');
            const targetInput = document.getElementById(targetInputId);
            if (targetInput) {
                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    if (icon) {
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        // Fallback for iconless buttons, update innerHTML to show eye-slash
                        btn.innerHTML = '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
                        icon = btn.querySelector('i');
                    }
                } else {
                    targetInput.type = 'password';
                    if (icon) {
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                    } else {
                        // Fallback for iconless buttons, update innerHTML to show eye
                        btn.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i>';
                        icon = btn.querySelector('i');
                    }
                }
            }
        });
    });

    // =====================
    // Error message close button (if you add a .close-btn to your error HTML)
    // =====================
    document.querySelectorAll('.error-message .close-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            this.parentElement.style.display = 'none';
        });
    });

    // =====================
    // Hamburger menu for mobile nav
    // =====================
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const closeMenuBtn = document.getElementById('closeMenuBtn');

    if (hamburgerIcon && mobileMenuOverlay) {
        hamburgerIcon.addEventListener('click', function () {
            mobileMenuOverlay.classList.add('active');
        });
    }
    if (closeMenuBtn && mobileMenuOverlay) {
        closeMenuBtn.addEventListener('click', function () {
            mobileMenuOverlay.classList.remove('active');
        });
    }

    // =====================
    // Optional: click outside mobile menu closes it
    // =====================
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', function (e) {
            if (e.target === mobileMenuOverlay) {
                mobileMenuOverlay.classList.remove('active');
            }
        });
    }
});
</script>