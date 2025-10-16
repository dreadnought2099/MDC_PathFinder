import "./bootstrap";
import gsap from "gsap";

document.addEventListener("DOMContentLoaded", () => {
    const cursorDot = document.querySelector(".cursor-dot");
    const cursorOutline = document.querySelector(".cursor-outline");

    if (!cursorDot || !cursorOutline) {
        console.error("Cursor elements not found!");
        return;
    }

    let mouseX = 0,
        mouseY = 0;

    document.addEventListener("mousemove", (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;

        // Move small dot instantly
        gsap.to(cursorDot, {
            x: mouseX,
            y: mouseY,
            duration: 0.1,
            ease: "power2.out",
        });

        // Smoothly move the outer circle
        gsap.to(cursorOutline, {
            x: mouseX,
            y: mouseY,
            duration: 0.25,
            ease: "power3.out",
        });
    });

    // Magnetic hover effect for links and buttons
    document.querySelectorAll("a, button").forEach((el) => {
        el.addEventListener("mouseenter", () => {
            gsap.to(cursorOutline, {
                scale: 1.8,
                borderColor: "#ff00ff",
                duration: 0.3,
                ease: "power2.out",
            });
            gsap.to(cursorDot, {
                scale: 0.5,
                backgroundColor: "#ff00ff",
                duration: 0.3,
                ease: "power2.out",
            });
        });

        el.addEventListener("mouseleave", () => {
            gsap.to(cursorOutline, {
                scale: 1,
                borderColor: "#00ff99",
                duration: 0.3,
                ease: "power2.out",
            });
            gsap.to(cursorDot, {
                scale: 1,
                backgroundColor: "#00ff99",
                duration: 0.3,
                ease: "power2.out",
            });
        });
    });

    // Click feedback
    document.addEventListener("click", () => {
        gsap.to(cursorOutline, {
            scale: 0.7,
            duration: 0.1,
            yoyo: true,
            repeat: 1,
            ease: "power1.inOut",
        });
    });

    // Hide cursor when leaving window
    document.addEventListener("mouseleave", () => {
        gsap.to([cursorDot, cursorOutline], { opacity: 0 });
    });
    document.addEventListener("mouseenter", () => {
        gsap.to([cursorDot, cursorOutline], { opacity: 1 });
    });
});