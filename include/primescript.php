<script>
    // prime.js

// ========== HERO SECTION ANIMATION ==========
document.addEventListener('DOMContentLoaded', function () {
    // Animate hero content on load
    const heroContent = document.querySelector('.hero-content');
    if (heroContent) {
        heroContent.style.opacity = 0;
        heroContent.style.transform = 'translateY(40px)';
        setTimeout(() => {
            heroContent.style.transition = 'opacity 1s ease, transform 1s cubic-bezier(0.34, 1.56, 0.64, 1)';
            heroContent.style.opacity = 1;
            heroContent.style.transform = 'translateY(0)';
        }, 200);
    }

    // ========== SIGN UP BUTTON SCROLL ==========
    const signUpBtn = document.querySelector('.btn-primary');
    if (signUpBtn) {
        signUpBtn.addEventListener('click', function () {
            // You can scroll to a signup section or open a modal.
            // For now, we'll show a fun popup.
            showToast('Sign up form coming soon! Stay tuned. 🚀');
        });
    }

    // ========== PROGRESS BARS ANIMATION ==========
    const progressBars = document.querySelectorAll('.progress-bar-fill');
    if (progressBars.length > 0) {
        progressBars.forEach(bar => {
            bar.style.width = '0';
        });
        setTimeout(() => {
            progressBars.forEach(bar => {
                bar.style.transition = 'width 1.2s cubic-bezier(0.25,1,0.5,1)';
                const finalWidth = bar.getAttribute('style').match(/width:\s*(\d+)%/);
                if (finalWidth) {
                    bar.style.width = finalWidth[1] + '%';
                }
            });
        }, 400);
    }

    // ========== BENEFIT CARDS HOVER EFFECT ==========
    const benefitCards = document.querySelectorAll('.benefit-card');
    benefitCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.classList.add('active');
            // subtle scale and shadow
            card.style.transform = 'scale(1.035)';
            card.style.boxShadow = '0 8px 24px rgba(37, 99, 235, 0.10)';
        });
        card.addEventListener('mouseleave', () => {
            card.classList.remove('active');
            card.style.transform = '';
            card.style.boxShadow = '';
        });
    });

    // ========== HERO OVERLAY PARALLAX EFFECT ==========
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            heroSection.style.backgroundPosition = `center ${scrollY * 0.2}px`;
        });
    }

    // ========== FAKE COOKIE PLACEHOLDER DEMO ==========
    const cookiePlaceholder = document.querySelector('.cookie-placeholder');
    if (cookiePlaceholder) {
        cookiePlaceholder.addEventListener('click', () => {
            cookiePlaceholder.innerHTML = '<p>🎉 Functional cookies accepted! Content loaded.</p>';
        });
    }

    // ========== QUIZ BUTTON ==========
    const quizBtn = document.querySelector('.symptoms-about-section .btn.btn-primary');
    if (quizBtn) {
        quizBtn.addEventListener('click', function (e) {
            e.preventDefault();
            showFunQuizModal();
        });
    }
});

// ========== TOAST (POPUP) NOTIFICATION ==========
function showToast(message) {
    let toast = document.createElement('div');
    toast.className = 'prime-toast';
    toast.innerText = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 2200);
}

// ========== FUN QUIZ MODAL ==========
function showFunQuizModal() {
    // Remove existing if present
    const oldModal = document.getElementById('prime-quiz-modal');
    if (oldModal) oldModal.remove();

    // Create modal overlay
    const modal = document.createElement('div');
    modal.id = 'prime-quiz-modal';
    modal.style.position = 'fixed';
    modal.style.top = 0;
    modal.style.left = 0;
    modal.style.width = '100vw';
    modal.style.height = '100vh';
    modal.style.background = 'rgba(0,0,0,0.5)';
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modal.style.zIndex = 9999;

    // Quiz content
    modal.innerHTML = `
        <div style="
            background:#fff; 
            border-radius:1rem; 
            padding:2.5rem 2rem 2rem 2rem; 
            box-shadow:0 8px 32px rgba(0,0,0,0.13); 
            max-width:350px;
            width:100%;
            text-align:center;
            position:relative;">
            <button id="prime-quiz-close" style="
                position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.4rem;cursor:pointer;color:#aaa;">&times;</button>
            <h3 style="margin-bottom:1.2rem;color:#2563eb;">How do you feel today?</h3>
            <p style="font-size:1rem;">Pick an option:</p>
            <div style="margin-bottom:1rem;">
                <button class="quiz-opt" style="margin:.35rem .3rem;padding:.5rem 1rem;border-radius:.5rem;border:none;background:#e0f2f7;color:#2563eb;font-weight:600;cursor:pointer;">Energetic</button>
                <button class="quiz-opt" style="margin:.35rem .3rem;padding:.5rem 1rem;border-radius:.5rem;border:none;background:#e0f2f7;color:#2563eb;font-weight:600;cursor:pointer;">Tired</button>
                <button class="quiz-opt" style="margin:.35rem .3rem;padding:.5rem 1rem;border-radius:.5rem;border:none;background:#e0f2f7;color:#2563eb;font-weight:600;cursor:pointer;">Sick</button>
                <button class="quiz-opt" style="margin:.35rem .3rem;padding:.5rem 1rem;border-radius:.5rem;border:none;background:#e0f2f7;color:#2563eb;font-weight:600;cursor:pointer;">Stressed</button>
                <button class="quiz-opt" style="margin:.35rem .3rem;padding:.5rem 1rem;border-radius:.5rem;border:none;background:#e0f2f7;color:#2563eb;font-weight:600;cursor:pointer;">Curious</button>
            </div>
            <div id="quiz-result" style="min-height:24px;"></div>
        </div>
    `;

    document.body.appendChild(modal);

    modal.querySelectorAll('.quiz-opt').forEach(btn => {
        btn.addEventListener('click', function () {
            let msg = '';
            switch (btn.innerText) {
                case 'Energetic': msg = "Great! Keep up the healthy habits. 💪"; break;
                case 'Tired': msg = "Consider a walk, a glass of water, and an early night! 😴"; break;
                case 'Sick': msg = "If symptoms persist, contact a healthcare provider. 💬"; break;
                case 'Stressed': msg = "Take a deep breath. Try a 2-min mindfulness exercise. 🧘"; break;
                case 'Curious': msg = "Explore our Prime resources for tips and tools! 🕵️"; break;
                default: msg = "Stay positive!"; break;
            }
            modal.querySelector('#quiz-result').innerHTML = `<span style="color:#2563eb;font-weight:500">${msg}</span>`;
        });
    });

    modal.querySelector('#prime-quiz-close').onclick = () => modal.remove();

    // Close modal on outside click
    modal.addEventListener('click', function(e){
        if(e.target === modal) modal.remove();
    });
}

// ========== TOAST STYLE ==========
(function injectToastCSS() {
    const style = document.createElement('style');
    style.innerHTML = `
    .prime-toast {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) scale(0.95);
        background: #2563eb;
        color: #fff;
        padding: 1rem 2rem;
        border-radius: 1.5rem;
        font-size: 1.1rem;
        font-family: inherit;
        opacity: 0;
        pointer-events: none;
        box-shadow: 0 4px 24px rgba(37, 99, 235, 0.15);
        z-index: 9999;
        transition: opacity 0.4s, transform 0.4s;
    }
    .prime-toast.show {
        opacity: 1;
        pointer-events: auto;
        transform: translateX(-50%) scale(1);
    }
    `;
    document.head.appendChild(style);
})();
</script>