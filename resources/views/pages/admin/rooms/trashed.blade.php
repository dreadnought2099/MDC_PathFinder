<form action="{{ route('rooms.restore', $room->id) }}" method="POST">
    @csrf
    <button type="submit">Restore</button>
</form>

<form action="{{ route('rooms.forceDelete', $room->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit">Delete Permanently</button>
</form>
