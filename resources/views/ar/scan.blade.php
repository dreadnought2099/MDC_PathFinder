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
        const routeBase = @json(route('ar.select-room', ['markerIdentifier' => 'MARKER_ID']));
    </script>
    
    <script>
        // REGISTER BEFORE scene renders
        AFRAME.registerComponent('redirect-on-scan', {
            init: function() {
                this.el.addEventListener('markerFound', () => {
                    const markerId = this.el.getAttribute('id');
                    const finalUrl = routeBase.replace('MARKER_ID', markerId);
                    window.location.href = finalUrl;
                });
            }
        });

        AFRAME.registerComponent('scan-animation', {
            tick: function(time, timeDelta) {
                const y = Math.sin(time / 500) * 0.4;
                this.el.setAttribute('position', `0 0.101 ${y}`);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('#scan-line').setAttribute('scan-animation', '');
        });
    </script>

    <style>
        html,
        body {
            margin: 0;
            overflow: hidden;
            height: 100%;
            width: 100%;
        }
    </style>
</head>

<body>
    <a-scene embedded arjs="sourceType: webcam; detectionMode: mono;">
        <a-marker id="marker-1" type="pattern" url="/marker-patterns/pattern-1.patt" redirect-on-scan>
            <a-plane id="scan-line" position="0 0.101 0" width="1" height="0.02" rotation="-90 0 0" color="green">
            </a-plane>
        </a-marker>
        <a-marker id="marker-2" type="pattern" url="/marker-patterns/pattern-2.patt" redirect-on-scan>
            <a-plane id="scan-line" position="0 0.101 0" width="1" height="0.02" rotation="-90 0 0"
                color="green">
            </a-plane>
        </a-marker>
        <a-entity camera></a-entity>
    </a-scene>
</body>

</html>
