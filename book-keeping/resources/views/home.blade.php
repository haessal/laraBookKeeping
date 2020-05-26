@extends('layouts.bookkeeping')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Book List</div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item" style="border: 0 none;">
                            <div class="text-right">
                                <button onclick="location.href='{{ route($v2_create_book_page) }}'" class="btn btn-success">
                                    <i class="fa fa-book"></i>　新規作成
                                </button>
                            </div>
                        </li>
                    </ul>
                    <br>
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action"><i class="fa fa-book"></i>&nbsp m-rky / m-rkyのブック</a>
                        <a href="#" class="list-group-item list-group-item-action"><i class="fa fa-book"></i>&nbsp kakei / kakeiのブック</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
