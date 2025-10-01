@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Trash</h2>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Sender</th>
                <th>Message</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trashedMessages as $message)
            <tr>
                <td>{{ $message->sender->name }}</td>
                <td>{{ $message->body }}</td>
                <td>
                    <form action="{{ route('messages.restore', $message->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success btn-sm">Restore</button>
                    </form>
                    <form action="{{ route('messages.delete', $message->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Permanently Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
