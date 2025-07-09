@extends('layouts.guest')

@section('head')
<script>
    const paths = @json($paths);

    AFRAME.registerComponent('show-path', {
        init: function () {
            paths.forEach(path => {
                const from = document.querySelector(`#${path.from_marker.marker_id}`);
                const to = document.querySelector(`#${path.to_marker.marker_id}`);
                const arrow = document.querySelector(`#arrow-${path.from_marker.marker_id}-to-${path.to_marker.marker_id}`);

                if (from && to && arrow) {
                    from.addEventListener('markerFound', () => {
                        const fromPos = from.object3D.position;
                        const toPos = to.object3D.position;

                        const dx = toPos.x - fromPos.x;
                        const dz = toPos.z - fromPos.z;
                        const angle = Math.atan2(dz, dx) * (180 / Math.PI);

                        arrow.setAttribute('position', {
                            x: fromPos.x + dx / 2,
                            y: 0.2,
                            z: fromPos.z + dz / 2
                        });

                        arrow.setAttribute('rotation', `0 ${-angle} 0`);
                        arrow.setAttribute('visible', true);
                    });

                    from.addEventListener('markerLost', () => {
                        arrow.setAttribute('visible', false);
                    });
                }
            });
        }
    });
</script>
@endsection

@section('content')
<a-scene embedded arjs="sourceType: webcam;" show-path>
    @foreach ($markers as $marker)
        <a-marker id="{{ $marker->marker_id }}" type="pattern" url="/{{ $marker->pattern_url }}">
            <a-entity geometry="primitive: cone; radiusBottom: 0.05; radiusTop: 0; height: 0.2"
                      material="color: red"
                      position="0 0.1 0">
            </a-entity>
        </a-marker>
    @endforeach

    @foreach ($paths as $path)
        <a-entity id="arrow-{{ $path->fromMarker->marker_id }}-to-{{ $path->toMarker->marker_id }}"
                  visible="false"
                  geometry="primitive: cone; radiusBottom: 0.05; radiusTop: 0; height: 0.2"
                  material="color: yellow"
                  position="0 0.2 0">
        </a-entity>
    @endforeach

    <a-entity camera></a-entity>
</a-scene>
@endsection
