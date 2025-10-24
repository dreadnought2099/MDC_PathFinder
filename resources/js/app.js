import "./bootstrap";
import gsap from "gsap";

// Make GSAP available globally
window.gsap = gsap;

class ImgReveal extends HTMLElement {}
customElements.define("img-reveal", ImgReveal);

const initCursor = () => {
    const cursorDot = document.querySelector(".cursor-dot");
    const cursorOutline = document.querySelector(".cursor-outline");
    const particleContainer = document.querySelector(".cursor-particles");

    if (!cursorDot || !cursorOutline || !particleContainer) {
        console.warn(
            "Custom cursor elements not found. Default cursor will be used."
        );
        document.body.classList.remove("custom-cursor-enabled");
        return;
    }

    document.body.classList.add("custom-cursor-enabled");

    let currentX = window.innerWidth / 2;
    let currentY = window.innerHeight / 2;

    const getLastPosition = () => {
        try {
            const saved = sessionStorage.getItem("cursorPosition");
            if (saved) {
                const { x, y, timestamp } = JSON.parse(saved);
                if (Date.now() - timestamp < 30000) return { x, y };
            }
        } catch {}
        return { x: currentX, y: currentY };
    };

    const savePosition = (x, y) => {
        try {
            sessionStorage.setItem(
                "cursorPosition",
                JSON.stringify({ x, y, timestamp: Date.now() })
            );
        } catch {}
    };

    const lastPos = getLastPosition();
    currentX = lastPos.x;
    currentY = lastPos.y;

    // Initial placement
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

    const onMouseMove = (e) => {
        currentX = e.clientX;
        currentY = e.clientY;

        if (!isVisible) {
            gsap.to([cursorDot, cursorOutline], { opacity: 1, duration: 0.2 });
            isVisible = true;
        }

        // Smooth movement with natural trailing
        gsap.to(cursorDot, {
            x: currentX,
            y: currentY,
            duration: 0.12,
            ease: "power3.out",
        });

        gsap.to(cursorOutline, {
            x: currentX,
            y: currentY,
            duration: 0.18,
            ease: "power3.out",
        });

        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => savePosition(currentX, currentY), 100);
    };

    document.addEventListener("mousemove", onMouseMove, { passive: true });

    const createHoverEffect = (el) => {
        if (el.dataset.cursorHover) return;
        el.dataset.cursorHover = "true";
        el.addEventListener("mouseenter", () => {
            gsap.to(cursorDot, {
                scale: 2,
                backgroundColor: "#16c47f",
                duration: 0.05,
                ease: "power1.out",
            });
            gsap.to(cursorOutline, {
                scale: 1.5,
                borderColor: "#16c47f",
                duration: 0.05,
                ease: "power1.out",
            });
        });
        el.addEventListener("mouseleave", () => {
            gsap.to(cursorDot, {
                scale: 1,
                backgroundColor: "#157ee1",
                duration: 0.05,
                ease: "power1.out",
            });
            gsap.to(cursorOutline, {
                scale: 1,
                borderColor: "#157ee1",
                duration: 0.05,
                ease: "power1.out",
            });
        });
    };

    const applyHoverEffects = () => {
        const selector =
            'a, button, input:not([type="file"]), textarea, select, [role="button"], [onclick], .clickable, label[for]';
        document.querySelectorAll(selector).forEach(createHoverEffect);
    };

    const applyGlightboxEffects = () => {
        // Specific selectors for GLightbox elements
        const glightboxSelectors =
            ".gclose, .gnext, .gprev, .gslide-image, .gslide-description";
        document
            .querySelectorAll(glightboxSelectors)
            .forEach(createHoverEffect);
    };

    applyHoverEffects();

    // Update observer to watch for GLightbox container specifically
    const observer = new MutationObserver((mutations) => {
        const hasGlightbox = mutations.some((mutation) =>
            Array.from(mutation.addedNodes).some(
                (node) =>
                    node.classList &&
                    node.classList.contains("glightbox-container")
            )
        );

        if (hasGlightbox) {
            // Apply both general and GLightbox-specific effects
            setTimeout(() => {
                applyHoverEffects();
                applyGlightboxEffects();
            }, 150);
        } else {
            applyHoverEffects();
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });

    document.addEventListener("click", (e) => {
        // Purple pulse effect on click
        gsap.to(cursorDot, {
            scale: 0.6,
            backgroundColor: "#FF2DD1",
            duration: 0.12,
            ease: "power2.inOut",
            yoyo: true,
            repeat: 1,
        });

        gsap.to(cursorOutline, {
            scale: 0.8,
            borderColor: "#FF2DD1",
            duration: 0.12,
            ease: "power2.inOut",
            yoyo: true,
            repeat: 1,
        });

        // Particle burst
        createParticles(e.clientX, e.clientY);
    });

    document.addEventListener("mouseleave", () => {
        gsap.to([cursorDot, cursorOutline], { opacity: 0, duration: 0.2 });
        isVisible = false;
        savePosition(currentX, currentY);
    });

    document.addEventListener("mouseenter", () => {
        gsap.to([cursorDot, cursorOutline], { opacity: 1, duration: 0.2 });
        isVisible = true;
    });

    window.addEventListener("beforeunload", () =>
        savePosition(currentX, currentY)
    );

    // Particle creation with slight delay to sync with trailing outline
    function createParticles(x, y) {
        gsap.delayedCall(0.08, () => {
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
        });
    }
};

// --- Ensure cursor stays above GLightbox ---
const bringCursorToFront = () => {
    const cursorEls = document.querySelectorAll(
        ".cursor-dot, .cursor-outline, .cursor-particles"
    );
    cursorEls.forEach((el) => document.body.appendChild(el));
};

// When GLightbox opens, re-append cursor elements to the top and reapply hover effects
document.addEventListener("glightbox_open", () => {
    setTimeout(() => {
        bringCursorToFront();

        // Reapply cursor hover effects for GLightbox buttons
        const cursorDot = document.querySelector(".cursor-dot");
        const cursorOutline = document.querySelector(".cursor-outline");

        const glightboxSelectors =
            ".gclose, .gnext, .gprev, .gslide-image, .gslide-description";
        document.querySelectorAll(glightboxSelectors).forEach((el) => {
            el.addEventListener("mouseenter", () => {
                gsap.to(cursorDot, {
                    scale: 2,
                    backgroundColor: "#16c47f",
                    duration: 0.05,
                    ease: "power1.out",
                });
                gsap.to(cursorOutline, {
                    scale: 1.5,
                    borderColor: "#16c47f",
                    duration: 0.05,
                    ease: "power1.out",
                });
            });
            el.addEventListener("mouseleave", () => {
                gsap.to(cursorDot, {
                    scale: 1,
                    backgroundColor: "#157ee1",
                    duration: 0.05,
                    ease: "power1.out",
                });
                gsap.to(cursorOutline, {
                    scale: 1,
                    borderColor: "#157ee1",
                    duration: 0.05,
                    ease: "power1.out",
                });
            });
        });

        // Smooth fade-in if needed
        gsap.to([cursorDot, cursorOutline], { opacity: 1, duration: 0.3 });
    }, 150);
});

// --- Fallback-safe initialization ---
const init = () => {
    if (window.__cursorInitialized) return;
    window.__cursorInitialized = true;

    const media = window.matchMedia("(max-width: 768px)");

    const handleChange = (e) => {
        if (e.matches) {
            document.body.classList.remove("custom-cursor-enabled");
            cursorElements().forEach((el) => (el.style.display = "none"));
        } else {
            try {
                cursorElements().forEach((el) => (el.style.display = ""));
                initCursor();
            } catch (err) {
                console.error("Cursor initialization failed:", err);
                document.body.classList.remove("custom-cursor-enabled");
            }
        }
    };

    const cursorElements = () =>
        document.querySelectorAll(
            ".cursor-dot, .cursor-outline, .cursor-particles"
        );

    handleChange(media);
    media.addEventListener("change", handleChange);
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
