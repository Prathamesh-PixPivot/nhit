@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Labels</h2>

    <div class="form-group">
        <form action="{{ route('labels.create') }}" method="POST">
            @csrf
            <input type="text" name="name" class="form-control" placeholder="Create New Label">
            <button type="submit" class="btn btn-primary mt-2">Create Label</button>
        </form>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Label</th>
                <th>Messages</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($labels as $label)
            <tr>
                <td>{{ $label->name }}</td>
                <td>{{ $label->messages->count() }}</td>
                <td>
                    <a href="{{ route('labels.view', $label->id) }}" class="btn btn-primary btn-sm">View Messages</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
