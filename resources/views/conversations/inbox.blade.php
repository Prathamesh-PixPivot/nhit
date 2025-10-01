@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Inbox</h2>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Sender</th>
                <th>Last Message</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($conversations as $conversation)
            <tr>
                <td>
                    {{ $conversation->userOne->id == auth()->id() ? $conversation->userTwo->name : $conversation->userOne->name }}
                </td>
                <td>
                    {{ $conversation->messages->first()->body }}
                </td>
                <td>
                    @if ($conversation->messages->first()->is_read)
                        <span class="badge bg-success">Read</span>
                    @else
                        <span class="badge bg-warning">Unread</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('conversation.show', $conversation->id) }}" class="btn btn-primary btn-sm">View</a>
                    <form action="{{ route('messages.trash', $conversation->messages->first()->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-danger btn-sm">Move to Trash</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
