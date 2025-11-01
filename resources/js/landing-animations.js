import gsap from "gsap";

export function initLandingAnimations() {
    // ✅ Helper: Animate only if element exists
    function animateIfExists(target, vars) {
        if (document.querySelector(target)) {
            gsap.to(target, vars);
        }
    }

    // ✅ SVG Circle Animations
    animateIfExists(".svg-circle-1", {
        x: 50,
        y: 30,
        duration: 8,
        repeat: -1,
        yoyo: true,
        ease: "sine.inOut",
    });

    animateIfExists(".svg-circle-2", {
        x: -40,
        y: -40,
        duration: 10,
        repeat: -1,
        yoyo: true,
        ease: "sine.inOut",
    });

    animateIfExists(".svg-circle-3", {
        scale: 1.3,
        duration: 6,
        repeat: -1,
        yoyo: true,
        ease: "sine.inOut",
    });

    // ✅ SVG Path Animations
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

    // ✅ Main Timeline (fade/slide elements in sequence)
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

    // ✅ Subtle parallax on mouse move
    const mm = gsap.matchMedia();

    mm.add("(min-width: 768px)", () => {
        document.addEventListener("mousemove", (e) => {
            const moveX = (e.clientX - window.innerWidth / 2) * 0.005;
            const moveY = (e.clientY - window.innerHeight / 2) * 0.005;

            animateIfExists(".main-title", {
                x: moveX * 2,
                y: moveY * 2,
                duration: 1,
                ease: "power2.out",
            });

            animateIfExists(".subtitle", {
                x: moveX,
                y: moveY,
                duration: 1,
                ease: "power2.out",
            });

            animateIfExists(".svg-circle-1", {
                x: moveX * 3,
                y: moveY * 3,
                duration: 1.5,
                ease: "power2.out",
            });

            animateIfExists(".svg-circle-2", {
                x: -moveX * 2,
                y: -moveY * 2,
                duration: 1.5,
                ease: "power2.out",
            });
        });
    });
}