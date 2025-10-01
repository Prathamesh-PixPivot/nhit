@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Compose Message</h2>

    <form action="{{ route('messages.send') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="recipient">Recipient:</label>
            <select name="recipient_id" class="form-control" required>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="body">Message:</label>
            <textarea name="body" class="form-control" placeholder="Type your message here"></textarea>
        </div>

        <div class="form-group">
            <label for="attachment">Attachment:</label>
            <input type="file" name="attachment" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>
@endsection
