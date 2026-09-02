/* ============================================================
   GSAP + ScrollTrigger Animations & Interactive Logic
   Croisade Que Ton Règne Vienne
   ============================================================ */

// ─── GSAP Registration ───────────────────────────────────────
gsap.registerPlugin(ScrollTrigger);

// ─── UTILITY ─────────────────────────────────────────────────
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

// ─── DOM READY ───────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }

    initNavbar();
    initHeroAnimations();
    initScrollReveal();
    initParallax();
    initCounters();
    initHoverCards();
    initMouseParallax();
    initScrollTop();
    initMobileNav();
    initForm();
});

/* ═══════════════════════════════════════════════════════════
   NAVBAR
═══════════════════════════════════════════════════════════ */
function initNavbar() {
    const navbar = $('#navbar');
    if (!navbar) return;

    // Scroll state
    const onScroll = () => {
        if (window.scrollY > 60) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // GSAP entrance
    gsap.from(navbar, {
        y: -80,
        opacity: 0,
        duration: 1,
        ease: 'power3.out',
        delay: 0.2
    });
}

/* ═══════════════════════════════════════════════════════════
   HERO ANIMATIONS
═══════════════════════════════════════════════════════════ */
function initHeroAnimations() {
    const tl = gsap.timeline({ delay: 0.5 });

    // Hero bg zoom
    const heroBgImg = $('.hero-bg img');
    if (heroBgImg) {
        gsap.to(heroBgImg, {
            scale: 1,
            duration: 8,
            ease: 'power1.out'
        });
    }

    // Content entrance
    const badge = $('.hero-badge');
    const slogan = $('.hero-slogan');
    const title = $('.hero-title');
    const desc = $('.hero-desc');
    const btns = $('.hero-btns');
    const infoCard = $('.hero-info-card');
    const rightImg = $('.hero-visual-wrap');
    const fc1 = $('.hero-float-card-1');
    const fc2 = $('.hero-float-card-2');

    tl.from([badge, slogan].filter(Boolean), {
            y: 30,
            opacity: 0,
            duration: 0.7,
            ease: 'power3.out',
            stagger: 0.1
        })
        .from(title, {
            y: 40,
            opacity: 0,
            duration: 0.9,
            ease: 'power3.out'
        }, '-=0.3')
        .from(desc, {
            y: 30,
            opacity: 0,
            duration: 0.7,
            ease: 'power3.out'
        }, '-=0.5')
        .from(btns, {
            y: 20,
            opacity: 0,
            duration: 0.6,
            ease: 'power3.out'
        }, '-=0.4')
        .from(infoCard, {
            y: 20,
            opacity: 0,
            duration: 0.6,
            ease: 'power3.out'
        }, '-=0.3');

    if (rightImg) {
        tl.from(rightImg, {
            x: 60,
            opacity: 0,
            duration: 1,
            ease: 'power3.out'
        }, '-=1');
    }
    if (fc1) {
        tl.from(fc1, {
            x: -30,
            opacity: 0,
            duration: 0.6,
            ease: 'back.out(1.7)'
        }, '-=0.5');
    }
    if (fc2) {
        tl.from(fc2, {
            x: 30,
            opacity: 0,
            duration: 0.6,
            ease: 'back.out(1.7)'
        }, '-=0.5');
    }
}

/* ═══════════════════════════════════════════════════════════
   SCROLL REVEAL (IntersectionObserver)
═══════════════════════════════════════════════════════════ */
function initScrollReveal() {
    const revealEls = $$('.reveal, .reveal-left, .reveal-right, .reveal-scale');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.05,
        rootMargin: '50px 0px 0px 0px'
    });

    revealEls.forEach(el => observer.observe(el));
}

/* ═══════════════════════════════════════════════════════════
   PARALLAX (GSAP ScrollTrigger)
═══════════════════════════════════════════════════════════ */
function initParallax() {
    // Hero background parallax
    const heroBg = $('.hero-bg img');
    if (heroBg) {
        gsap.to(heroBg, {
            yPercent: 20,
            ease: 'none',
            scrollTrigger: {
                trigger: '.hero',
                start: 'top top',
                end: 'bottom top',
                scrub: true
            }
        });
    }

    // Mission section parallax
    const missionBg = $('.mission-bg-img img');
    if (missionBg) {
        gsap.to(missionBg, {
            yPercent: 15,
            ease: 'none',
            scrollTrigger: {
                trigger: '.mission-section',
                start: 'top bottom',
                end: 'bottom top',
                scrub: true
            }
        });
    }

    // Vision images parallax
    const visionMain = $('.vision-img-main img');
    if (visionMain) {
        gsap.to(visionMain, {
            yPercent: -8,
            ease: 'none',
            scrollTrigger: {
                trigger: '.vision-section',
                start: 'top bottom',
                end: 'bottom top',
                scrub: 1.5
            }
        });
    }

    // ScrollTrigger animations for cards are now handled by IntersectionObserver (.reveal class)
}

/* ═══════════════════════════════════════════════════════════
   ANIMATED COUNTERS
═══════════════════════════════════════════════════════════ */
function initCounters() {
    const counters = $$('.counter-number[data-target]');
    if (!counters.length) return;

    counters.forEach(counter => {
        const target = parseInt(counter.dataset.target, 10);
        const suffix = counter.dataset.suffix || '';
        const obj = { val: 0 };

        ScrollTrigger.create({
            trigger: counter,
            start: 'top 85%',
            once: true,
            onEnter: () => {
                gsap.to(obj, {
                    val: target,
                    duration: 2.5,
                    ease: 'power2.out',
                    onUpdate: () => {
                        counter.textContent = Math.round(obj.val).toLocaleString() + suffix;
                    }
                });
            }
        });
    });
}

/* ═══════════════════════════════════════════════════════════
   HOVER CARDS — glow follow effect
═══════════════════════════════════════════════════════════ */
function initHoverCards() {
    const cards = $$('.pilier-card, .mission-stat-card');

    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const cx = rect.width / 2;
            const cy = rect.height / 2;
            const rx = (y - cy) / cy * -8;
            const ry = (x - cx) / cx * 8;

            gsap.to(card, {
                rotateX: rx,
                rotateY: ry,
                duration: 0.3,
                ease: 'power2.out',
                transformPerspective: 800
            });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                rotateX: 0,
                rotateY: 0,
                duration: 0.5,
                ease: 'elastic.out(1, 0.5)',
                transformPerspective: 800
            });
        });
    });
}

/* ═══════════════════════════════════════════════════════════
   MOUSE PARALLAX (Hero)
═══════════════════════════════════════════════════════════ */
function initMouseParallax() {
    const hero = $('.hero');
    if (!hero) return;

    const orb1 = $('.hero-orb-1');
    const orb2 = $('.hero-orb-2');
    const visualWrap = $('.hero-visual-wrap');

    hero.addEventListener('mousemove', (e) => {
        const rect = hero.getBoundingClientRect();
        const mx = (e.clientX - rect.left) / rect.width - 0.5; // -0.5 → 0.5
        const my = (e.clientY - rect.top) / rect.height - 0.5;

        if (orb1) {
            gsap.to(orb1, { x: mx * 60, y: my * 40, duration: 1.5, ease: 'power2.out' });
        }
        if (orb2) {
            gsap.to(orb2, { x: mx * -40, y: my * -30, duration: 1.5, ease: 'power2.out' });
        }
        if (visualWrap) {
            gsap.to(visualWrap, { x: mx * 20, y: my * 12, duration: 1.5, ease: 'power2.out' });
        }
    });
}

/* ═══════════════════════════════════════════════════════════
   SCROLL TO TOP
═══════════════════════════════════════════════════════════ */
function initScrollTop() {
    const btn = $('#scrollTop');
    if (!btn) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    }, { passive: true });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

/* ═══════════════════════════════════════════════════════════
   MOBILE NAV
═══════════════════════════════════════════════════════════ */
function initMobileNav() {
    const hamburger = $('#hamburger');
    const mobileNav = $('#mobileNav');
    const closeBtn = $('#mobileNavClose');

    if (!hamburger || !mobileNav) return;

    hamburger.addEventListener('click', () => {
        mobileNav.classList.add('open');
        document.body.style.overflow = 'hidden';
    });

    const close = () => {
        mobileNav.classList.remove('open');
        document.body.style.overflow = '';
    };

    if (closeBtn) closeBtn.addEventListener('click', close);

    // Close on link click
    $$('a', mobileNav).forEach(link => {
        link.addEventListener('click', close);
    });
}

/* ═══════════════════════════════════════════════════════════
   FORM — AJAX Submit to PHP
═══════════════════════════════════════════════════════════ */
function initForm() {
    const form = $('#inscriptionForm');
    if (!form) return;

    const submitBtn = form.querySelector('.form-submit');
    const msgSuccess = form.querySelector('.form-message.success');
    const msgError = form.querySelector('.form-message.error');

    form.addEventListener('submit', async(e) => {
        e.preventDefault();

        // Reset messages
        msgSuccess && (msgSuccess.style.display = 'none');
        msgError && (msgError.style.display = 'none');

        // Loading state
        submitBtn.classList.add('btn-loading');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Envoi en cours...';

        try {
            const formData = new FormData(form);
            const response = await fetch('php/register.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Success animation
                if (msgSuccess) {
                    msgSuccess.textContent = result.message || '✅ Inscription enregistrée avec succès ! Que Dieu vous bénisse.';
                    msgSuccess.style.display = 'block';
                    gsap.from(msgSuccess, { y: 10, opacity: 0, duration: 0.5, ease: 'power3.out' });
                }
                form.reset();

                // Animate success
                gsap.to(submitBtn, {
                    scale: 1.05,
                    duration: 0.2,
                    yoyo: true,
                    repeat: 1,
                    ease: 'power2.out'
                });
            } else {
                if (msgError) {
                    msgError.textContent = result.message || '❌ Une erreur est survenue. Veuillez réessayer.';
                    msgError.style.display = 'block';
                    gsap.from(msgError, { y: 10, opacity: 0, duration: 0.5, ease: 'power3.out' });
                }
            }
        } catch (err) {
            if (msgError) {
                msgError.textContent = '❌ Erreur réseau. Vérifiez votre connexion et réessayez.';
                msgError.style.display = 'block';
            }
        } finally {
            submitBtn.classList.remove('btn-loading');
            submitBtn.textContent = originalText;
        }
    });

    // Real-time validation
    $$('.form-control', form).forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => {
            if (input.classList.contains('invalid')) validateField(input);
        });
    });
}

function validateField(input) {
    const value = input.value.trim();
    let valid = true;

    if (input.required && !value) valid = false;
    if (input.type === 'email' && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) valid = false;
    if (input.type === 'tel' && value && !/^[\d\s\+\-\(\)]{6,15}$/.test(value)) valid = false;

    if (!valid) {
        input.style.borderColor = '#ef4444';
        input.style.boxShadow = '0 0 0 4px rgba(239,68,68,0.1)';
        input.classList.add('invalid');
    } else {
        input.style.borderColor = 'rgba(34,197,94,0.5)';
        input.style.boxShadow = '0 0 0 4px rgba(34,197,94,0.1)';
        input.classList.remove('invalid');
    }
}

/* ═══════════════════════════════════════════════════════════
   SMOOTH SCROLL for anchor links
═══════════════════════════════════════════════════════════ */
document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        if (href === '#') return;
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            const top = target.getBoundingClientRect().top + window.scrollY - 80;
            window.scrollTo({ top, behavior: 'smooth' });
        }
    });
});