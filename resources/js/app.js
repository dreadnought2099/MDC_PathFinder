import "./bootstrap";
import gsap from "gsap";

// Initialize cursor - optimized version with position persistence
const initCursor = () => {
    const cursorDot = document.querySelector(".cursor-dot");
    const cursorOutline = document.querySelector(".cursor-outline");
    const particleContainer = document.querySelector(".cursor-particles");

    if (!cursorDot || !cursorOutline || !particleContainer) return;

    // Store for current position
    let currentX = window.innerWidth / 2;
    let currentY = window.innerHeight / 2;

    // Get last cursor position from sessionStorage
    const getLastPosition = () => {
        try {
            const saved = sessionStorage.getItem("cursorPosition");
            if (saved) {
                const { x, y, timestamp } = JSON.parse(saved);
                // Only use saved position if it's from within the last 30 seconds
                if (Date.now() - timestamp < 30000) {
                    return { x, y };
                }
            }
        } catch (e) {
            console.warn("Failed to restore cursor position:", e);
        }
        return { x: currentX, y: currentY };
    };

    // Save cursor position to sessionStorage
    const savePosition = (x, y) => {
        try {
            sessionStorage.setItem(
                "cursorPosition",
                JSON.stringify({ x, y, timestamp: Date.now() })
            );
        } catch (e) {
            // Silently fail if storage is full or blocked
        }
    };

    const lastPos = getLastPosition();
    currentX = lastPos.x;
    currentY = lastPos.y;

    // Set initial styles - start at last known position
    gsap.set([cursorDot, cursorOutline], {
        xPercent: -50,
        yPercent: -50,
        position: "fixed",
        top: 0,
        left: 0,
        x: currentX,
        y: currentY,
        pointerEvents: "none",
        zIndex: 9999,
        opacity: 0,
    });

    gsap.set(particleContainer, {
        position: "fixed",
        top: 0,
        left: 0,
        width: "100%",
        height: "100%",
        pointerEvents: "none",
        zIndex: 9998,
    });

    let isVisible = false;
    let saveTimeout;

    // Mouse move handler - optimized with position saving
    const onMouseMove = (e) => {
        currentX = e.clientX;
        currentY = e.clientY;

        if (!isVisible) {
            gsap.to([cursorDot, cursorOutline], { opacity: 1, duration: 0.2 });
            isVisible = true;
        }

        // Dot follows instantly
        gsap.set(cursorDot, { x: currentX, y: currentY });

        // Outline follows with smooth animation
        gsap.to(cursorOutline, {
            x: currentX,
            y: currentY,
            duration: 0.1,
            ease: "power1.out",
        });

        // Debounced save to sessionStorage
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => savePosition(currentX, currentY), 100);
    };

    document.addEventListener("mousemove", onMouseMove, { passive: true });

    // Hover effect - reusable function
    const createHoverEffect = (el) => {
        if (el.dataset.cursorHover) return;
        el.dataset.cursorHover = "true";

        el.addEventListener("mouseenter", () => {
            gsap.to(cursorDot, {
                scale: 2,
                backgroundColor: "#16c47f",
                duration: 0.3,
                ease: "power2.out",
            });
            gsap.to(cursorOutline, {
                scale: 1.5,
                borderColor: "#16c47f",
                duration: 0.3,
                ease: "power2.out",
            });
        });

        el.addEventListener("mouseleave", () => {
            gsap.to(cursorDot, {
                scale: 1,
                backgroundColor: "#157ee1",
                duration: 0.3,
                ease: "power2.out",
            });
            gsap.to(cursorOutline, {
                scale: 1,
                borderColor: "#157ee1",
                duration: 0.3,
                ease: "power2.out",
            });
        });
    };

    // Apply hover effects
    const applyHoverEffects = () => {
        const selector =
            'a, button, input:not([type="file"]), textarea, select, [role="button"], [onclick], .clickable, label[for]';
        document.querySelectorAll(selector).forEach(createHoverEffect);
    };

    applyHoverEffects();

    // Optimized MutationObserver
    let observerTimeout;
    const observer = new MutationObserver(() => {
        clearTimeout(observerTimeout);
        observerTimeout = setTimeout(applyHoverEffects, 150);
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });

    // Click effect with particle burst
    document.addEventListener(
        "click",
        (e) => {
            gsap.to(cursorDot, {
                scale: 0.5,
                backgroundColor: "#FF2DD1",
                duration: 0.1,
                yoyo: true,
                repeat: 1,
                ease: "power2.inOut",
                onComplete: () => {
                    gsap.set(cursorDot, {backgroundColor: "#157ee1"});
                },
            });

            gsap.to(cursorOutline, {
                scale: 0.7,
                borderColor: "#FF2DD1",
                duration: 0.1,
                yoyo: true,
                repeat: 1,
                ease: "power2.inOut",
                onComplete: () => {
                    gsap.set(cursorOutline, { borderColor: "#157ee1" });
                },
            });

            createParticles(e.clientX, e.clientY);
        },
        { passive: true }
    );

    // Window enter/leave
    document.addEventListener("mouseleave", () => {
        gsap.to([cursorDot, cursorOutline], { opacity: 0, duration: 0.2 });
        isVisible = false;
        // Save position before leaving
        savePosition(currentX, currentY);
    });

    document.addEventListener("mouseenter", () => {
        gsap.to([cursorDot, cursorOutline], { opacity: 1, duration: 0.2 });
        isVisible = true;
    });

    // Save position before page unload
    window.addEventListener("beforeunload", () => {
        savePosition(currentX, currentY);
    });

    // Particle burst
    function createParticles(x, y) {
        const fragment = document.createDocumentFragment();

        for (let i = 0; i < 8; i++) {
            const particle = document.createElement("div");
            particle.className = "particle";
            fragment.appendChild(particle);

            const angle = (Math.PI * 2 * i) / 8;
            const distance = Math.random() * 40 + 20;

            gsap.set(particle, { x, y, position: "absolute" });
            gsap.to(particle, {
                x: x + Math.cos(angle) * distance,
                y: y + Math.sin(angle) * distance,
                opacity: 0,
                scale: 0,
                duration: 0.6,
                ease: "power2.out",
                onComplete: () => particle.remove(),
            });
        }

        particleContainer.appendChild(fragment);
    }
};

// Smart initialization - prevent double initialization
const init = () => {
    if (window.__cursorInitialized) return;
    window.__cursorInitialized = true;
    initCursor();
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}