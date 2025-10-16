import "./bootstrap";
import gsap from "gsap";

document.addEventListener("DOMContentLoaded", () => {
    // Get cursor elements
    const cursorDot = document.querySelector(".cursor-dot");
    const cursorOutline = document.querySelector(".cursor-outline");
    const particleContainer = document.querySelector(".cursor-particles");

    if (!cursorDot || !cursorOutline) return;

    // Set transform origin for accurate positioning
    gsap.set([cursorDot, cursorOutline], { xPercent: -50, yPercent: -50 });

    // --- Cursor follows mouse instantly ---
    document.addEventListener("mousemove", (e) => {
        gsap.set(cursorDot, { x: e.clientX, y: e.clientY }); // dot follows instantly
        gsap.set(cursorOutline, { x: e.clientX, y: e.clientY }); // outline follows instantly
    });

    // --- Hover effect: grow dot, outline stays normal ---
    document.querySelectorAll("a, button").forEach((el) => {
        el.addEventListener("mouseenter", () => {
            gsap.set(cursorDot, { scale: 2, backgroundColor: "#00ffe0" }); // dot grows
            gsap.set(cursorOutline, { scale: 1, borderColor: "#157ee1" }); // outline fixed
        });
        el.addEventListener("mouseleave", () => {
            gsap.set(cursorDot, { scale: 1, backgroundColor: "#157ee1" }); // reset dot
            gsap.set(cursorOutline, { scale: 1, borderColor: "#157ee1" }); // reset outline
        });
    });

    // --- Click pulse + particle burst ---
    document.addEventListener("click", (e) => {
        gsap.to(cursorOutline, {
            scale: 0.7,
            borderColor: "#ffae00",
            duration: 0.1,
            yoyo: true,
            repeat: 1,
        });
        createParticles(e.clientX, e.clientY);
    });

    // --- Section-based colors (instant) ---
    const sections = document.querySelectorAll("section");
    document.addEventListener("mousemove", (e) => {
        const mouseY = e.clientY;
        sections.forEach((sec) => {
            const rect = sec.getBoundingClientRect();
            if (mouseY >= rect.top && mouseY <= rect.bottom) {
                if (sec.classList.contains("section-alt")) {
                    gsap.set(cursorDot, { backgroundColor: "#ff00ff" });
                    gsap.set(cursorOutline, {
                        borderColor: "rgba(255,0,255,0.6)",
                    });
                } else {
                    gsap.set(cursorDot, { backgroundColor: "#157ee1" });
                    gsap.set(cursorOutline, {
                        borderColor: "rgba(21,126,225,0.6)",
                    });
                }
            }
        });
    });

    // --- Hide cursor when leaving window ---
    document.addEventListener("mouseleave", () =>
        gsap.set([cursorDot, cursorOutline], { opacity: 0 })
    );
    document.addEventListener("mouseenter", () =>
        gsap.set([cursorDot, cursorOutline], { opacity: 1 })
    );

    // --- Particle burst function ---
    function createParticles(x, y) {
        for (let i = 0; i < 6; i++) {
            const p = document.createElement("div");
            p.classList.add("particle");
            particleContainer.appendChild(p);

            const angle = Math.random() * Math.PI * 2;
            const distance = Math.random() * 30 + 10;

            gsap.set(p, { x, y });
            gsap.to(p, {
                x: x + Math.cos(angle) * distance,
                y: y + Math.sin(angle) * distance,
                opacity: 0,
                duration: 0.5,
                ease: "power1.out",
                onComplete: () => p.remove(),
            });
        }
    }
});
