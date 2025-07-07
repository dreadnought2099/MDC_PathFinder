<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>AR.js Marker Based</title>
    <script src="https://aframe.io/releases/1.2.0/aframe.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/AR-js-org/AR.js/aframe/build/aframe-ar.min.js"></script>
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
    <a-scene embedded arjs>
        <a-marker type="pattern" url="/markers/pattern1.patt">
            <a-box position="0 0.5 0" material="color: red;"></a-box>
        </a-marker>

        <a-entity camera></a-entity>
    </a-scene>
</body>

</html>
