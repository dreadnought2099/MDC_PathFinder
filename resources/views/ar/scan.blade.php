<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>MDC Pathfinder Scanner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- A-Frame + AR.js -->
    <script src="https://aframe.io/releases/1.2.0/aframe.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/AR-js-org/AR.js/aframe/build/aframe-ar.min.js"></script>

    <script>
        // REGISTER BEFORE scene renders
        AFRAME.registerComponent('redirect-on-scan', {
            init: function () {
                this.el.addEventListener('markerFound', () => {
                    const markerId = this.el.getAttribute('id');
                    window.location.href = `/navigate/from/${markerId}`;
                });
            }
        });
    </script>

    <style>
        html, body {
            margin: 0;
            overflow: hidden;
            height: 100%;
            width: 100%;
        }
    </style>
</head>

<body>
    <a-scene embedded arjs="sourceType: webcam; detectionMode: mono;">
        <a-marker id="marker1" type="pattern" url="/marker-patterns/pattern-1.patt" redirect-on-scan>
            <a-box position="0 0.5 0" material="color: blue;"></a-box>
        </a-marker>

        <a-entity camera></a-entity>
    </a-scene>
</body>

</html>
