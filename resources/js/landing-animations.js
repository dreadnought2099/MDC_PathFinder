import gsap from 'gsap';

export function initLandingAnimations() {
    // SVG Circle Animations
    gsap.to('.svg-circle-1', {
        x: 50,
        y: 30,
        duration: 8,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut'
    });

    gsap.to('.svg-circle-2', {
        x: -40,
        y: -40,
        duration: 10,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut'
    });

    gsap.to('.svg-circle-3', {
        scale: 1.3,
        duration: 6,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut'
    });

    // SVG Path Animations
    gsap.to('.svg-path-1', {
        attr: { d: "M 0 100 Q 250 150 500 100" },
        duration: 4,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut'
    });

    gsap.to('.svg-path-2', {
        attr: { d: "M 500 200 Q 750 250 1000 200" },
        duration: 5,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut'
    });

    // Main Timeline with better spacing
    const tl = gsap.timeline({
        defaults: {
            ease: 'power3.out'
        }
    });

    // Welcome badge
    tl.to('.welcome-badge', {
        opacity: 1,
        y: 0,
        duration: 0.6,
        ease: 'back.out(1.4)'
    }, '-=0.2');

    // Main title - staggered by lines
    tl.to('.main-title', {
        opacity: 1,
        y: 0,
        duration: 0.8,
        ease: 'power2.out'
    }, '-=0.2');

    // Subtitle
    tl.to('.subtitle', {
        opacity: 1,
        y: 0,
        duration: 0.6
    }, '-=0.4');

    // CTA button
    tl.to('.cta-button', {
        opacity: 1,
        scale: 1,
        duration: 0.6,
        ease: 'back.out(1.4)'
    }, '-=0.3');

    // Feature pills
    tl.to('.features-pills', {
        opacity: 1,
        duration: 0.6
    }, '-=0.2');

    tl.from('.feature-pill', {
        scale: 0,
        opacity: 0,
        duration: 0.4,
        stagger: 0.1,
        ease: 'back.out(1.7)'
    }, '-=0.4');

    // Subtle parallax on mouse move (less aggressive)
    let mm = gsap.matchMedia();
    
    mm.add("(min-width: 768px)", () => {
        document.addEventListener('mousemove', function(e) {
            const moveX = (e.clientX - window.innerWidth / 2) * 0.005;
            const moveY = (e.clientY - window.innerHeight / 2) * 0.005;

            gsap.to('.main-title', {
                x: moveX * 2,
                y: moveY * 2,
                duration: 1,
                ease: 'power2.out'
            });

            gsap.to('.subtitle', {
                x: moveX,
                y: moveY,
                duration: 1,
                ease: 'power2.out'
            });

            gsap.to('.svg-circle-1', {
                x: moveX * 3,
                y: moveY * 3,
                duration: 1.5,
                ease: 'power2.out'
            });

            gsap.to('.svg-circle-2', {
                x: -moveX * 2,
                y: -moveY * 2,
                duration: 1.5,
                ease: 'power2.out'
            });
        });
    });
}