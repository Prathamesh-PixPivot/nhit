@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Folders</h2>

    <div class="form-group">
        <form action="{{ route('folders.create') }}" method="POST">
            @csrf
            <input type="text" name="name" class="form-control" placeholder="Create New Folder">
            <button type="submit" class="btn btn-primary mt-2">Create Folder</button>
        </form>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Folder</th>
                <th>Messages</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($folders as $folder)
            <tr>
                <td>{{ $folder->name }}</td>
                <td>{{ $folder->messages->count() }}</td>
                <td>
                    <a href="{{ route('folders.view', $folder->id) }}" class="btn btn-primary btn-sm">View Messages</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
