<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Store</title>
</head>
<body>
    <h1>Pet Store Management</h1>

    <h2>Available Pets</h2>
    <ul>
        @foreach ($pets as $pet)
            <li>
                {{ $pet['id'] }} - {{ $pet['name'] ?? 'Unknown' }} ({{ $pet['status'] ?? 'Unknown' }})
                <form action="{{ route('pets.destroy', $pet['id']) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>

    <h2>Add a New Pet</h2>
    <form action="{{ route('pets.store') }}" method="POST">
        @csrf
        <label for="id">ID:</label>
        <input type="text" name="id" id="id" required>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>
        <label for="status">Status:</label>
        <input type="text" name="status" id="status" required>
        <button type="submit">Add Pet</button>
    </form>

    <h2>Edit Pet</h2>
    <form action="{{ route('pets.update', 1) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="id">ID:</label>
        <input type="text" name="id" id="id" value="1" readonly>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="Updated Name">
        <label for="status">Status:</label>
        <input type="text" name="status" id="status" value="available">
        <button type="submit">Update Pet</button>
    </form>
</body>
</html>
