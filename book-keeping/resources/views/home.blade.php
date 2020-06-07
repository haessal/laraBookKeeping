@extends('layouts.bookkeeping')

@section('pagetitle', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{{ __('Book List') }}}</div>
                <div class="card-body">
                    <ul class="list-group">
                        @isset($v2_create_book_page)
                        <li class="list-group-item" style="border: 0 none;">
                            <div class="text-right">
                                <button onclick="location.href='{{ route($v2_create_book_page) }}'" class="btn btn-success">
                                    <i class="fa fa-book"></i>　{{{ __('New') }}}
                                </button>
                            </div>
                        </li>
                        @endisset
                    </ul>
                    <br>
                    <div class="list-group">
                        @isset($book_list)
                        @foreach ($book_list as $book)
                        @isset($v2_book_page)
                        <a href="{{ route($v2_book_page, ['bookId' => $book['id']]) }}" class="list-group-item list-group-item-action"><i class="fa fa-book"></i>&nbsp {{{ $book['owner'] }}} / {{{ $book['name'] }}}</a>
                        @else
                        <span class="list-group-item list-group-item-action"><i class="fa fa-book"></i>&nbsp {{{ $book['owner'] }}} / {{{ $book['name'] }}}</span>
                        @endisset
                        @endforeach
                        @else
                        <span class="text-center">{{{ __("You don't have any books yet.") }}}</span>
                        <span class="text-center">{{{ __("Create your first book to start.") }}}</span>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
