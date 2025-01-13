<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Pets</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .photo {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>All Pets</h1>
    </header>

    <div class="container mt-4">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2>Add New Pet</h2>
        <form action="{{ route('pets.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="id">ID:</label>
                <input type="text" class="form-control" id="id" name="id" required>
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="available">Available</option>
                    <option value="pending">Pending</option>
                    <option value="sold">Sold</option>
                </select>
            </div>
            <div class="form-group">
                <label for="photoUrls">Photo URL:</label>
                <input type="text" class="form-control" id="photoUrls" name="photoUrls[0]" required>
            </div>
            <div class="form-group">
                <label for="category">Category Name:</label>
                <input type="text" class="form-control" id="category" name="category[name]">
            </div>
            <div class="form-group">
                <label for="tags">Tag Name:</label>
                <input type="text" class="form-control" id="tags" name="tags[0][name]">
            </div>
            <button type="submit" class="btn btn-success">Add Pet</button>
        </form>

        <h2>Pets</h2>
        <table class="table table-bordered table-striped">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Tags</th>
                    <th>Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($allPets as $pet)
                    <tr>
                        <td>{{ $pet['id'] }}</td>
                        <td>{{ $pet['name'] ?? 'Unknown' }}</td>
                        <td>{{ $pet['status'] ?? 'Unknown' }}</td>
                        <td>{{ $pet['category']['name'] ?? 'No Category' }}</td>
                        <td>
                            @if (isset($pet['tags']) && is_array($pet['tags']))
                                @foreach ($pet['tags'] as $tag)
                                    {{ $tag['name'] ?? 'No Tag' }}@if (!$loop->last), @endif
                                @endforeach
                            @else
                                No Tags
                            @endif
                        </td>
                        <td>
                            @if (isset($pet['photoUrls']) && count($pet['photoUrls']) > 0)
                                <img src="{{ $pet['photoUrls'][0] }}" alt="Photo" class="photo">
                            @else
                                No Photo
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal{{ $pet['id'] }}">Edit</button>
                            <form action="{{ route('pets.delete', $pet['id']) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>

                            <div class="modal fade" id="editModal{{ $pet['id'] }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $pet['id'] }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $pet['id'] }}">Edit Pet</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('pets.update', $pet['id']) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group">
                                                    <label for="name{{ $pet['id'] }}">Name:</label>
                                                    <input type="text" class="form-control" id="name{{ $pet['id'] }}" name="name" value="{{ $pet['name'] ?? '' }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status{{ $pet['id'] }}">Status:</label>
                                                    <select class="form-control" id="status{{ $pet['id'] }}" name="status" required>
                                                        <option value="available" {{ $pet['status'] == 'available' ? 'selected' : '' }}>Available</option>
                                                        <option value="pending" {{ $pet['status'] == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="sold" {{ $pet['status'] == 'sold' ? 'selected' : '' }}>Sold</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="photoUrls{{ $pet['id'] }}">Photo URL:</label>
                                                    <input type="text" class="form-control" id="photoUrls{{ $pet['id'] }}" name="photoUrls[0]" value="{{ $pet['photoUrls'][0] ?? '' }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="category{{ $pet['id'] }}">Category Name:</label>
                                                    <input type="text" class="form-control" id="category{{ $pet['id'] }}" name="category[name]" value="{{ $pet['category']['name'] ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="tags{{ $pet['id'] }}">Tag Name:</label>
                                                    <input type="text" class="form-control" id="tags{{ $pet['id'] }}" name="tags[0][name]" value="{{ $pet['tags'][0]['name'] ?? '' }}">
                                                </div>
                                                <button type="submit" class="btn btn-success">Save changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>