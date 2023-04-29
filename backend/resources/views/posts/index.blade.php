@extends('layouts.app')

@section('title')

@section('content')
    @forelse($posts as $post)
        <div class="mt-2 border border-2 rounded py-3 px-4">
            <a href="{{ route('post.show', $post->id) }}"><h2 class="h4">{{ $post->title }}</h2></a>
            <h3 class="h6 text-muted">{{ $post->user->name }}</h3>
            <p class="fw-light mb-0">{{ $post->body }}</p>
            {{-- if the owner of the post is the Auth user, show Edit and Delete buttons. --}}
            @if($post->user->id === Auth::user()->id)
                <div class="text-end mt-2">
                    <a href="{{ route('post.edit', $post->id) }}" class="btn btn-outline-primary btn-sm border border-0">Edit</a>
                    <form action="{{ route('post.destroy', $post->id) }}" method="post" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger border border-0 btn-sm">Remove</button>
                    </form>
                </div>
            @endif
        </div>

    @empty
        <div style="margin-top: 100px">
            <h2 class="text-muted text-center">No posts yet.</h2>
            <p class="text-center">
                <a href="{{ route('post.create') }}" class="text-decoration-none text-info fw-bold">Create a new Post</a>
            </p>
        </div>
    @endforelse
@endsection
