@extends('layouts.app')

@section('content')

<div class="container">
    @if ($conversation)
    <h2>Conversation with {{ $conversation->userOne->id == auth()->id() ? $conversation->userTwo->name : $conversation->userOne->name }}</h2>

    <div class="card">
        <div class="card-body">
            @foreach ($conversation->messages as $message)
                <div class="{{ $message->sender_id == auth()->id() ? 'text-right' : 'text-left' }}">
                    <strong>{{ $message->sender->name }}:</strong>
                    <p>{{ $message->body }}</p>

                    @if ($message->attachment)
                        <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank">View Attachment</a>
                    @endif

                    <small>{{ $message->created_at->diffForHumans() }}</small>
                </div>
                <hr>
            @endforeach
        </div>
    </div>

    <!-- Reply Form -->
    <form action="{{ route('conversation.reply', $conversation->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <textarea name="body" class="form-control" placeholder="Type your reply here"></textarea>
        </div>
        <div class="form-group">
            <input type="file" name="attachment" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Send Reply</button>
    </form>
    @else
    <h2>Conversation with empty</h2>
    @endif
</div>
@endsection
