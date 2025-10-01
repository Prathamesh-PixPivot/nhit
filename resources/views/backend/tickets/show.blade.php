@extends('backend.layouts.app')
@section('content')

    <section class="section">
        <div class="row">
            <div class="col-lg-9">
                <h2 class="card-title">Ticket Details</h2>

                <table class="table table-bordered">
                    <tr class="p-2">
                        <td colspan="1">Name</td>
                        <td colspan="2">{{ $ticket->name }}</td>
                    </tr>
                    <tr class="p-2">
                        <td>Contact Number</td>
                        <td>{{ $ticket->number }}</td>
                    </tr>
                    <tr class="p-2">
                        <td>Error</td>
                        <td>{{ $ticket->error }}</td>
                    </tr>
                    <tr class="p-2">
                        <td>Description</td>
                        <td>{{ $ticket->description }}</td>
                    </tr>
                    <tr class="p-2">
                        <td>Entity Name</td>
                        <td>{{ $ticket->entity_name }}</td>
                    </tr>
                    <tr class="p-2">
                        <td>Created At</td>
                        <td>{{ $ticket->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y, h:i A') }}</td>
                    </tr>
                    <tr class="p-2">
                        <td>Priority</td>
                        <td>
                            @php
                                $priorities = ['L' => 'Low', 'M' => 'Medium', 'H' => 'High'];
                            @endphp
                            {{ $priorities[$ticket->priority] ?? $ticket->priority }}
                        </td>
                    </tr>
                    <tr class="p-2">
                        <td>Status</td>
                        <td>
                            @php
                                $statuses = ['O' => 'Open', 'IP' => 'In Progress', 'R' => 'Resolved', 'C' => 'Closed'];
                            @endphp
                            {{ $statuses[$ticket->status] ?? $ticket->status }}
                        </td>
                    </tr>
                    <tr class="p-2">
                        <th>Attachment</th>
                        <td>
                            @php
                                // $attachments = $ticket->attachments ?? [];
                                $attachments = json_decode($ticket->attachments, true) ?? [];

                                $first = $attachments[0] ?? null;
                                $remainingCount = count($attachments) - 1;
                            @endphp

                            @if ($first)
                                @if (Str::endsWith($first, ['.jpg', '.jpeg', '.png', '.gif']))
                                    <img src="{{ asset('storage/bugs/' . $first) }}" width="60" height="60"
                                        class="img-thumbnail" data-bs-toggle="modal"
                                        data-bs-target="#attachmentModal{{ $ticket->id }}" style="cursor:pointer;">
                                @elseif(Str::endsWith($first, ['.mp4', '.mov']))
                                    <video width="60" height="60" class="img-thumbnail" data-bs-toggle="modal"
                                        data-bs-target="#attachmentModal{{ $ticket->id }}" style="cursor:pointer;">
                                        <source src="{{ asset('storage/bugs/' . $first) }}">
                                    </video>
                                @endif

                                @if ($remainingCount > 0)
                                    <span class="badge bg-dark ms-1" style="cursor:pointer;" data-bs-toggle="modal"
                                        data-bs-target="#attachmentModal{{ $ticket->id }}">
                                        +{{ $remainingCount }} more
                                    </span>
                                @endif
                            @else
                                <span class="text-muted">No files</span>
                            @endif

                            <div class="modal fade" id="attachmentModal{{ $ticket->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Ticket Attachments</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div id="carousel{{ $ticket->id }}" class="carousel slide"
                                                data-bs-ride="carousel">
                                                <div class="carousel-inner">
                                                    @foreach ($attachments as $index => $file)
                                                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                            @if (Str::endsWith($file, ['.jpg', '.jpeg', '.png', '.gif']))
                                                                <img src="{{ asset('storage/bugs/' . $file) }}"
                                                                    class="d-block w-100"
                                                                    style="max-height:500px; object-fit:contain;">
                                                            @elseif(Str::endsWith($file, ['.mp4', '.mov']))
                                                                <video controls class="d-block w-100"
                                                                    style="max-height:500px;">
                                                                    <source src="{{ asset('storage/bugs/' . $file) }}">
                                                                    Your browser does not support the
                                                                    video tag.
                                                                </video>
                                                            @else
                                                                <p class="text-center">Unsupported file:
                                                                    <a href="{{ asset('storage/bugs/' . $file) }}"
                                                                        target="_blank">{{ $file }}</a>
                                                                </p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @if (count($attachments) > 1)
                                                    <button class="carousel-control-prev" type="button"
                                                        data-bs-target="#carousel{{ $ticket->id }}" data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon"></span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button"
                                                        data-bs-target="#carousel{{ $ticket->id }}" data-bs-slide="next">
                                                        <span class="carousel-control-next-icon"></span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                </table>


            </div>
            <div class="col-3 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Status Flow</h5>
                        <div class="position-relative">
                            <!-- Vertical Line -->
                            <div class="border-start border-3 border-primary position-absolute"
                                style="height: 100%; left: 10px; top: 0;"></div>

                            <!-- Steps -->
                            @foreach ($ticket->statusLogs()->orderBy('created_at')->get() as $index => $log)
                                <div class="d-flex align-items-start position-relative mb-4" style="padding-left: 30px;">
                                    <!-- Step Dot -->
                                    <div class="position-absolute bg-primary rounded-circle"
                                        style="width: 12px; height: 12px; left: 5px;"></div>

                                    <!-- Step Info -->
                                    <div>
                                        <p class="fw-bold mb-1">Step {{ $index + 1 }}</p>

                                        <p class="text-muted small mb-1">
                                            <strong>By:</strong> {{ $log->changedBy->name ?? 'System' }}
                                        </p>

                                        <p class="text-muted small mb-1">
                                            <strong>At:</strong>
                                            {{ $log->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}
                                        </p>

                                        <p class="text-muted small">
                                            <strong>Status:</strong>
                                            @switch($log->status)
                                                @case('O')
                                                    <span class="badge bg-dark">Open</span>
                                                @break

                                                @case('IP')
                                                    <span class="badge bg-warning text-dark">In Progress</span>
                                                @break

                                                @case('R')
                                                    <span class="badge bg-danger">Resolved</span>
                                                @break

                                                @case('C')
                                                    <span class="badge bg-success">Closed</span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary">{{ $log->status }}</span>
                                            @endswitch
                                        </p>

                                        @if (!$loop->last)
                                            <div class="position-absolute start-0 top-100 translate-middle-y border-start border-2"
                                                style="height: 30px; left: 6px;"></div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Comments ({{ $ticket->comments->count() }})</h5>
                        <!-- Existing Comments -->
                        @foreach ($ticket->comments as $comment)
                            @if (is_null($comment->parent_id))
                                <div class="card">
                                    <div class="card-header">
                                        <h6>({{ $comment->created_at->diffForHumans() }}) |
                                            ({{ $comment->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }})
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $comment->user->name }}</h5>
                                        <p class="card-text">{{ $comment->content }}</p>
                                        @foreach ($comment->replies as $reply)
                                            <div class="ml-5 border mb-2 mt-2 rounded"
                                                style="margin-left: 20px; padding: 10px;">
                                                <div class="">
                                                    <h6>({{ $reply->created_at->diffForHumans() }}) |
                                                        ({{ $reply->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }})
                                                    </h6>
                                                </div>
                                                <div class="">
                                                    <h5 class="card-title">{{ $reply->user->name }}</h5>
                                                    <p class="card-text">{{ $reply->content }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="">
                                            @if ($comment->user_id == Auth::id())
                                                {{-- <a href="{{ route('backend.comments.edit', $comment->id) }}"
                                                    class="btn-sm">Edit</a> | --}}
                                                <a href="#" class="btn-sm" data-toggle="collapse"
                                                    data-target="#editForm-{{ $comment->id }}">Edit</a> |
                                            @endif
                                            <a href="#" class="btn-sm" data-toggle="collapse"
                                                data-target="#replyForm-{{ $comment->id }}">Reply</a> |
                                            <span>{{ $comment->replies->count() }}
                                                {{ Str::plural('Reply', $comment->replies->count()) }}</span>
                                        </div>
                                        <div id="replyForm-{{ $comment->id }}" class="collapse mt-3">
                                            <form action="{{ route('backend.ticketComments.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <div class="mb-3">
                                                    <textarea name="content" class="form-control" required rows="3" placeholder="Write a reply..."></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary  button-with-spinner">
                                                    <span>Post Reply</span>
                                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                                </button>
                                            </form>
                                        </div>
                                        <div id="editForm-{{ $comment->id }}" class="collapse mt-3">
                                            <form action="{{ route('backend.ticketComments.update', $comment->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <textarea name="content" class="form-control" required rows="3">{{ $comment->content }}</textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary  button-with-spinner">
                                                    <span>Update</span>
                                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                                </button>
                                                <a href="#" class="btn btn-sm btn-secondary" data-toggle="collapse"
                                                    data-target="#editForm-{{ $comment->id }}">Cancel</a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        <!-- Add Comment Form -->
                        <form action="{{ route('backend.ticketComments.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                            <div class="mb-3">
                                <textarea name="content" class="form-control" required rows="3" placeholder="Write a comment..."></textarea>
                            </div>
                            {{-- <button type="submit" class="btn btn-primary">Add Comment</button> --}}
                            <button type="submit" class="btn btn-primary button-with-spinner">
                                <span>Add Comment</span>
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
    </section>
@endsection
@push('script')
    <script>
        $('.select2').select2();
        const fileInput = document.getElementById('file');
        const previewBox = document.getElementById('preview-box');
        const previewImage = document.getElementById('file-preview');
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modal-image');

        // Handle file input change
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewBox.style.display = 'block';
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                previewBox.style.display = 'none';
                previewImage.src = '';
                alert('Please select a valid image file.');
            }
        });

        // Show modal on preview click
        previewBox.addEventListener('click', function() {
            if (previewImage.src) {
                modalImage.src = previewImage.src;
                imageModal.style.display = 'flex';
            }
        });

        // Close modal on click anywhere
        imageModal.addEventListener('click', function() {
            imageModal.style.display = 'none';
        });
    </script>
@endpush
