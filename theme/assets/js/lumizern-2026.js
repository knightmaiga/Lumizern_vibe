/* ==========================================================
   LUMIZERN VIBE – FINAL PATCHED VERSION (NO QUICK VIEW)
   ========================================================== */

let lumizernApp = null;

document.addEventListener('DOMContentLoaded', () => {
    if (!lumizernApp) {
        lumizernApp = new LumizernVibe();
        window.lumizernApp = lumizernApp;
    }

    initializePageSpecificFeatures();
});

class LumizernVibe {
    constructor() {
        if (typeof LumizernVibe._instance === 'object') {
            return LumizernVibe._instance;
        }
        LumizernVibe._instance = this;

        this.isInitialized = false;
        this.cartCount = 0;
        this.mobileToggle = null;
        this.mobileNav = null;

        this.init();
    }

    init() {
        if (this.isInitialized) return;

        this.injectStylesOnce();
        this.initPreloader();
        this.initDarkMode();
        this.initEnhancedMobileMenu();
        this.initSearchOverlay();
        this.initHomeInteractions();
        this.initCartFunctionality();
        this.initNewsletterForms();
        this.initPerformanceOptimizations();
        this.initHeaderScroll();
        this.initCardSpotlight();
        this.initAdaptiveStickyCTA();
        this.initVibeProfile();

        this.isInitialized = true;
    }

    injectStylesOnce() {
        if (document.getElementById('lz-notification-styles')) return;

        const styles = document.createElement('style');
        styles.id = 'lz-notification-styles';
        styles.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
            .notification { animation: slideInRight .3s ease-out; }
            .fade-in { animation: fadeIn .6s ease-in; }
        `;
        document.head.appendChild(styles);
    }

    initPreloader() {
        if (document.querySelector('.lumizern-preloader')) return;
        if (sessionStorage.getItem('lumizern_initial_load')) return;

        sessionStorage.setItem('lumizern_initial_load', 'true');
        const pre = document.createElement('div');
        pre.className = 'lumizern-preloader';
        pre.innerHTML = `
            <div class="preloader-content">
                <span class="loader-title">LUMIZERN</span>
                <div class="loading-bar"><div class="loading-progress"></div></div>
            </div>
        `;
        document.body.prepend(pre);

        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            const bar = pre.querySelector('.loading-progress');
            if (bar) bar.style.width = `${Math.min(progress, 100)}%`;

            if (progress >= 100) {
                clearInterval(interval);
                pre.style.opacity = '0';
                setTimeout(() => pre.remove(), 500);
            }
        }, 100);
    }

    initDarkMode() {
        const toggle = document.getElementById('dark-mode-switch');
        if (!toggle) return;

        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
            toggle.checked = true;
        }

        toggle.addEventListener('change', () => {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', String(document.body.classList.contains('dark-mode')));
        });
    }

    initEnhancedMobileMenu() {
        this.mobileToggle = document.querySelector('.mobile-menu-toggle, .lz-mobile-toggle, [data-lz-mobile-toggle]');
        this.mobileNav = document.querySelector('.mobile-nav, .lz-mobile-nav');

        if (!this.mobileToggle || !this.mobileNav) return;

        this.mobileToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isActive = this.mobileNav.classList.contains('active');
            this.mobileNav.classList.toggle('active', !isActive);
            this.mobileToggle.classList.toggle('active', !isActive);
            this.mobileToggle.setAttribute('aria-expanded', String(!isActive));
            document.body.style.overflow = !isActive ? 'hidden' : '';
        });

        this.mobileNav.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                this.mobileNav.classList.remove('active');
                this.mobileToggle.classList.remove('active');
                this.mobileToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            });
        });
    }

    initSearchOverlay() {
        const trigger = document.querySelector('.search-trigger, .lz-search-trigger, [data-lz-search-open]');
        const overlay = document.querySelector('.search-overlay, .lz-search-overlay');
        const closeBtn = document.querySelector('.search-close, .lz-search-close, [data-lz-search-close]');
        if (!trigger || !overlay) return;

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            const input = overlay.querySelector("input[type='search']");
            if (input) setTimeout(() => input.focus(), 100);
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    initHomeInteractions() {
        if (!document.querySelector('.homepage-main')) return;
        this.initScrollAnimations();
    }

    initScrollAnimations() {
        const selectors = '.vibe-section, .trending-section, .social-proof-vibe';
        const elements = document.querySelectorAll(selectors);
        if (!elements.length) return;

        if (!('IntersectionObserver' in window)) {
            elements.forEach((el) => el.classList.add('fade-in'));
            return;
        }

        const obs = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '50px' });

        elements.forEach((el) => obs.observe(el));
    }

    initCartFunctionality() {
        this.cartCount = parseInt(localStorage.getItem('lumizern_cart_count'), 10) || 0;
        this.updateCartCount(this.cartCount);

        document.querySelectorAll('.qty').forEach((qty) => {
            qty.addEventListener('change', function () {
                if (this.form) this.form.submit();
            });
        });

        if (window.jQuery && typeof wc_cart_fragments_params !== 'undefined') {
            jQuery(document.body).on('added_to_cart updated_cart', (event, fragments) => {
                if (fragments && fragments['.lz-cart-count']) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = fragments['.lz-cart-count'];
                    const count = parseInt(tempDiv.textContent || '', 10) || 0;
                    this.updateCartCount(count);
                }
            });
        }
    }

    updateCartCount(count) {
        this.cartCount = count;
        localStorage.setItem('lumizern_cart_count', String(count));
        document.querySelectorAll('.cart-count, .lz-cart-count').forEach((el) => {
            el.textContent = count;
            el.style.display = count > 0 ? 'flex' : 'none';
        });
    }

    initNewsletterForms() {
        document.querySelectorAll('.newsletter-form').forEach((form) => {
            form.addEventListener('submit', (e) => this.handleNewsletterSubmit(e));
        });
    }

    handleNewsletterSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const emailInput = form.querySelector("input[type='email']");
        const email = emailInput?.value.trim();
        const btn = form.querySelector('button');

        if (!email || !this.validateEmail(email)) {
            this.showNotification('Please enter a valid email address', 'error');
            emailInput?.focus();
            return;
        }

        this.showButtonLoading(btn, 'Subscribing...');
        setTimeout(() => {
            this.showNotification('Welcome to the Lumizern vibe! 🎉', 'success');
            form.reset();
            this.resetButton(btn);
        }, 1000);
    }

    validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    initPerformanceOptimizations() {
        const imgs = document.querySelectorAll('img[data-src]');
        if (!imgs.length) return;

        if (!('IntersectionObserver' in window)) {
            imgs.forEach((img) => {
                img.src = img.dataset.src;
                img.classList.remove('lazy');
            });
            return;
        }

        const obs = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        }, { rootMargin: '50px' });

        imgs.forEach((img) => obs.observe(img));
    }

    initHeaderScroll() {
        const header = document.querySelector('.site-header, .lz-header');
        if (!header) return;

        let lastY = window.scrollY;
        let ticking = false;

        const updateHeader = () => {
            const currentY = window.scrollY;
            if (currentY > 100) {
                header.classList.add('scrolled');
                header.style.transform = currentY > lastY && currentY > 200 ? 'translateY(-100%)' : 'translateY(0)';
            } else {
                header.classList.remove('scrolled');
                header.style.transform = 'translateY(0)';
            }
            lastY = currentY;
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateHeader);
                ticking = true;
            }
        }, { passive: true });
    }


    initCardSpotlight() {
        const cards = document.querySelectorAll('.product, .clean-card, .trending-product-card, .vibe-card');
        if (!cards.length) return;

        cards.forEach((card) => {
            card.addEventListener('pointermove', (event) => {
                const rect = card.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width) * 100;
                const y = ((event.clientY - rect.top) / rect.height) * 100;
                card.style.setProperty('--lx', `${x}%`);
                card.style.setProperty('--ly', `${y}%`);
                card.style.backgroundImage = 'radial-gradient(circle at var(--lx) var(--ly), rgba(255,255,255,.15), rgba(255,255,255,.02) 45%)';
            });

            card.addEventListener('pointerleave', () => {
                card.style.backgroundImage = '';
            });
        });
    }

    initAdaptiveStickyCTA() {
        const cta = document.querySelector('#place_order, .submit-btn-enhanced, .action-btn-primary');
        if (!cta) return;

        const onScroll = () => {
            const y = window.scrollY || 0;
            cta.style.transform = y > 280 ? 'translateY(0)' : 'translateY(0)';
            cta.style.boxShadow = y > 280 ? '0 18px 36px rgba(138,43,226,.35)' : '';
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    initVibeProfile() {
        const saveButton = document.querySelector('[data-vibe-profile-save]');
        const status = document.querySelector('[data-vibe-profile-status]');
        const chips = document.querySelectorAll('[data-vibe-profile-option]');

        if (!saveButton || !chips.length || !window.lumizernConfig) return;

        let selected = document.querySelector('[data-vibe-profile-option].is-active')?.dataset.vibeProfileOption || chips[0].dataset.vibeProfileOption;

        chips.forEach((chip) => {
            chip.addEventListener('click', () => {
                chips.forEach((c) => c.classList.remove('is-active'));
                chip.classList.add('is-active');
                selected = chip.dataset.vibeProfileOption || selected;
            });
        });

        saveButton.addEventListener('click', async () => {
            saveButton.disabled = true;
            if (status) status.textContent = 'Saving your vibe profile...';

            const body = new URLSearchParams({
                action: 'lumizern_save_quiz_profile',
                nonce: lumizernConfig.quiz_profile_nonce || '',
                primary: selected,
                secondary: '',
                tertiary: '',
                email_opt_in: ''
            });

            try {
                const response = await fetch(lumizernConfig.ajax_url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                    body: body.toString()
                });
                const data = await response.json();
                if (data && data.success) {
                    if (status) status.textContent = 'Saved. Your store experience is now personalized.';
                } else if (status) {
                    status.textContent = 'Unable to save right now. Please try again.';
                }
            } catch (error) {
                if (status) status.textContent = 'Unable to save right now. Please try again.';
            } finally {
                saveButton.disabled = false;
            }
        });
    }

    showButtonLoading(btn, text) {
        if (!btn) return;
        btn.disabled = true;
        btn.dataset.originalText = btn.textContent;
        btn.textContent = text;
    }

    resetButton(btn) {
        if (!btn) return;
        btn.disabled = false;
        btn.textContent = btn.dataset.originalText || 'Subscribe';
    }

    showNotification(msg, type = 'success') {
        document.querySelectorAll('.notification').forEach((n) => n.remove());
        const box = document.createElement('div');
        box.className = `notification notification-${type}`;
        box.style.cssText = `
            position: fixed; right: 20px; top: 100px; padding: 16px 24px;
            background: ${type === 'success' ? '#4CAF50' : (type === 'error' ? '#f44336' : '#2196F3')};
            color: white; border-radius: 8px; z-index: 10000; max-width: 320px;
        `;
        box.textContent = msg;
        document.body.appendChild(box);
        setTimeout(() => box.remove(), 3500);
    }
}

function initializePageSpecificFeatures() {
    const elementsToAnimate = document.querySelectorAll('.vibe-card, .proof-stat-vibe');
    if (!elementsToAnimate.length) return;

    if (!('IntersectionObserver' in window)) {
        elementsToAnimate.forEach((el) => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
        return;
    }

    const obs = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '50px' });

    elementsToAnimate.forEach((el) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        obs.observe(el);
    });
}
