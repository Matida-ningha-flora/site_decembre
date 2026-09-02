/* ============================================================
   GSAP + ScrollTrigger Animations — École des Adorateurs
   Centre de Formation Missionnaire PROSKUNEO
   (Mêmes patterns d'animation que main.js)
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
    initMissionCards();
});

/* ═══════════════════════════════════════════════════════════
   NAVBAR
═══════════════════════════════════════════════════════════ */
function initNavbar() {
    const navbar = $('#navbar');
    if (!navbar) return;

    const onScroll = () => {
        if (window.scrollY > 60) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

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

    const missionBg = $('.ea-mission-bg img');
    if (missionBg) {
        gsap.to(missionBg, {
            yPercent: 15,
            ease: 'none',
            scrollTrigger: {
                trigger: '.ea-mission-section',
                start: 'top bottom',
                end: 'bottom top',
                scrub: true
            }
        });
    }

    const visionMain = $('.ea-vision-img-main img');
    if (visionMain) {
        gsap.to(visionMain, {
            yPercent: -8,
            ease: 'none',
            scrollTrigger: {
                trigger: '.ea-definition-section',
                start: 'top bottom',
                end: 'bottom top',
                scrub: 1.5
            }
        });
    }
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
   HOVER CARDS — glow follow effect (3D tilt)
═══════════════════════════════════════════════════════════ */
function initHoverCards() {
    const cards = $$('.ea-mission-card, .ea-formation-card, .ea-stat-card');

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
        const mx = (e.clientX - rect.left) / rect.width - 0.5;
        const my = (e.clientY - rect.top) / rect.height - 0.5;

        if (orb1) gsap.to(orb1, { x: mx * 60, y: my * 40, duration: 1.5, ease: 'power2.out' });
        if (orb2) gsap.to(orb2, { x: mx * -40, y: my * -30, duration: 1.5, ease: 'power2.out' });
        if (visualWrap) gsap.to(visualWrap, { x: mx * 20, y: my * 12, duration: 1.5, ease: 'power2.out' });
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
    $$('a', mobileNav).forEach(link => link.addEventListener('click', close));
}

/* ═══════════════════════════════════════════════════════════
   MISSION CARDS — staggered entrance on scroll
═══════════════════════════════════════════════════════════ */
function initMissionCards() {
    const cards = $$('.ea-mission-card');
    if (!cards.length) return;

    ScrollTrigger.create({
        trigger: '.ea-missions-grid',
        start: 'top 80%',
        once: true,
        onEnter: () => {
            gsap.from(cards, {
                y: 60,
                opacity: 0,
                duration: 0.7,
                stagger: 0.15,
                ease: 'power3.out'
            });
        }
    });

    const formCards = $$('.ea-formation-item');
    if (formCards.length) {
        ScrollTrigger.create({
            trigger: '.ea-formation-list',
            start: 'top 85%',
            once: true,
            onEnter: () => {
                gsap.from(formCards, {
                    x: 40,
                    opacity: 0,
                    duration: 0.6,
                    stagger: 0.12,
                    ease: 'power3.out'
                });
            }
        });
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