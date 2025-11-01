import gsap from "gsap";
import { Draggable } from "gsap/Draggable";
import { MotionPathPlugin } from "gsap/MotionPathPlugin";

// Register the plugins
gsap.registerPlugin(Draggable, MotionPathPlugin);

export function initLandingAnimations() {
    // Helper: Animate only if element exists
    function animateIfExists(target, vars) {
        if (document.querySelector(target)) {
            gsap.to(target, vars);
        }
    }

    // Smooth infinite circular movement for SVG circles
    function createInfiniteMovement(target, radius, duration, offset = 0) {
        const element = document.querySelector(target);
        if (!element) return;

        gsap.to(target, {
            motionPath: {
                path: [
                    {
                        x: radius * Math.cos(offset),
                        y: radius * Math.sin(offset),
                    },
                    {
                        x: radius * Math.cos(offset + Math.PI / 2),
                        y: radius * Math.sin(offset + Math.PI / 2),
                    },
                    {
                        x: radius * Math.cos(offset + Math.PI),
                        y: radius * Math.sin(offset + Math.PI),
                    },
                    {
                        x: radius * Math.cos(offset + (3 * Math.PI) / 2),
                        y: radius * Math.sin(offset + (3 * Math.PI) / 2),
                    },
                    {
                        x: radius * Math.cos(offset + 2 * Math.PI),
                        y: radius * Math.sin(offset + 2 * Math.PI),
                    },
                ],
                curviness: 1.5,
            },
            duration: duration,
            repeat: -1,
            ease: "none",
            yoyo: false,
        });
    }

    // Alternative: Smooth figure-8 or custom path movement
    function createSmoothWandering(target, duration, scale = 1) {
        const element = document.querySelector(target);
        if (!element) return;

        const width = window.innerWidth * scale;
        const height = window.innerHeight * scale;

        gsap.to(target, {
            motionPath: {
                path: [
                    { x: -width * 0.3, y: -height * 0.2 },
                    { x: width * 0.2, y: -height * 0.3 },
                    { x: width * 0.4, y: height * 0.1 },
                    { x: width * 0.1, y: height * 0.4 },
                    { x: -width * 0.2, y: height * 0.3 },
                    { x: -width * 0.4, y: -height * 0.1 },
                    { x: -width * 0.3, y: -height * 0.2 },
                ],
                curviness: 2,
            },
            duration: duration,
            repeat: -1,
            ease: "none",
        });
    }

    // Initialize smooth infinite movement for circles
    createSmoothWandering(".svg-circle-1", 20, 0.4);
    createSmoothWandering(".svg-circle-2", 25, 0.45);
    createInfiniteMovement(".svg-circle-3", 150, 15, Math.PI / 4);

    // SVG Path Animations
    animateIfExists(".svg-path-1", {
        attr: { d: "M 0 100 Q 250 150 500 100" },
        duration: 4,
        repeat: -1,
        yoyo: true,
        ease: "sine.inOut",
    });

    animateIfExists(".svg-path-2", {
        attr: { d: "M 500 200 Q 750 250 1000 200" },
        duration: 5,
        repeat: -1,
        yoyo: true,
        ease: "sine.inOut",
    });

    // Main Timeline (fade/slide elements in sequence)
    const tl = gsap.timeline({
        defaults: { ease: "power3.out" },
    });

    if (document.querySelector(".main-title")) {
        tl.to(
            ".main-title",
            {
                opacity: 1,
                y: 0,
                duration: 0.8,
                ease: "power2.out",
            },
            "-=0.2"
        );
    }

    if (document.querySelector(".subtitle")) {
        tl.to(
            ".subtitle",
            {
                opacity: 1,
                y: 0,
                duration: 0.6,
            },
            "-=0.4"
        );
    }

    if (document.querySelector(".cta-button")) {
        tl.to(
            ".cta-button",
            {
                opacity: 1,
                scale: 1,
                duration: 0.6,
                ease: "back.out(1.4)",
            },
            "-=0.3"
        );
    }

    // Enhanced draggable title - simplified and faster
    const titleElement = document.querySelector(".main-title");
    if (titleElement) {
        const originalPosition = { x: 0, y: 0 };

        Draggable.create(".main-title", {
            type: "x,y",
            edgeResistance: 0.65,
            bounds: "body",
            inertia: true,
            cursor: "grab",
            activeCursor: "grabbing",

            onPress: function () {
                gsap.to(this.target, {
                    scale: 1.05,
                    duration: 0.2,
                    ease: "power2.out",
                });
            },

            onDragEnd: function () {
                gsap.to(this.target, {
                    scale: 1,
                    duration: 0.2,
                    ease: "power2.out",
                });
            },
        });

        // Simple hover effects
        titleElement.style.cursor = "grab";
        titleElement.style.userSelect = "none";
        titleElement.style.touchAction = "none";

        titleElement.addEventListener("mouseenter", () => {
            gsap.to(titleElement, {
                textShadow: "0 0 20px rgba(21, 126, 225, 0.4)",
                duration: 0.3,
            });
        });

        titleElement.addEventListener("mouseleave", () => {
            gsap.to(titleElement, {
                textShadow: "0 0 0px rgba(21, 126, 225, 0)",
                duration: 0.3,
            });
        });

        // Double-click to snap back to center
        titleElement.addEventListener("dblclick", () => {
            gsap.to(titleElement, {
                x: originalPosition.x,
                y: originalPosition.y,
                duration: 0.8,
                ease: "power2.out",
            });
        });
    }

    // Subtle parallax on mouse move (only affects subtitle)
    const mm = gsap.matchMedia();

    mm.add("(min-width: 768px)", () => {
        document.addEventListener("mousemove", (e) => {
            const moveX = (e.clientX - window.innerWidth / 2) * 0.003;
            const moveY = (e.clientY - window.innerHeight / 2) * 0.003;

            animateIfExists(".subtitle", {
                x: moveX * 15,
                y: moveY * 15,
                duration: 1,
                ease: "power2.out",
            });
        });
    });

    // Handle window resize
    window.addEventListener("resize", () => {
        if (titleElement) {
            Draggable.get(".main-title")?.update();
        }
    });
}