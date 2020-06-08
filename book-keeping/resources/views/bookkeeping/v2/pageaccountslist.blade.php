@extends('bookkeeping.v2.base')

@section('pagetitle', 'Accounts')

@section('v2_page_content')

<div class="container py-4 mb-5">
    <div class="py-3">
        <div class="h3">資産</div>
        <hr>
        <div class="card mb-2">
            <div class="card-header">
                <div>科目グループ1</div>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item bg-secondary">
                        <div>科目11</div>
                    </li>
                    <li class="list-group-item bg-secondary">
                        <div>科目12</div>
                    </li>
                    <li class="list-group-item">
                        <div>科目13</div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-header">
                <div>科目グループ2</div>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item bg-secondary">
                        <div>科目21</div>
                        <div>簡単な説明</div>
                    </li>
                    <li class="list-group-item">
                        <div>科目22</div>
                        <div class="text-secondary">簡単な説明</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="py-3">
        <div class="h3 float-left">負債</div>
        <div class="text-right">新規作成</div>
        <hr>
        <div class="card mb-2">
            <div class="card-header">
                <div class="float-left">科目グループ3</div>
                <div class="text-right">変更</div>
            </div>
            <div class="card-body">
                <div class="text-right p-1">新規作成</div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="float-left">科目31</div>
                        <div class="text-right">変更</div>
                    </li>
                    <li class="list-group-item">
                        <div class="float-left">科目32</div>
                        <div class="text-right">変更</div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-header">
                <div class="float-left">科目グループ4</div>
                <div class="text-right">変更</div>
            </div>
            <div class="card-body">
            <div class="text-right p-1">新規作成</div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="float-left">科目41</div>
                        <div class="text-right">変更</div>
                        <div class="text-left text-secondary">簡単な説明</div>
                    </li>
                    <li class="list-group-item">
                        <div class="float-left">科目42</div>
                        <div class="text-right">変更</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="py-3">
        <div class="h3 float-left">費用</div>
        <div class="text-right">新規作成</div>
        <hr>
        <div class="card mb-2">
            <div class="card-header">
                <div class="float-left">科目グループ5</div>
                <div class="text-right">変更</div>
            </div>
            <div class="card-body">
                <div class="text-right p-1">新規作成</div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="float-left">科目51</div>
                        <div class="text-right">変更</div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-header">
                <div class="float-left">科目グループ6</div>
                <div class="text-right">変更</div>
            </div>
            <div class="card-body">
            <div class="text-right p-1">新規作成</div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="float-left">科目61</div>
                        <div class="text-right">変更</div>
                        <div class="text-left text-secondary">簡単な説明</div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-header">
                <div class="float-left">科目グループ7</div>
                <div class="text-right">変更</div>
            </div>
            <div class="card-body">
            <div class="text-right p-1">新規作成</div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="float-left">科目71</div>
                        <div class="text-right">変更</div>
                        <div class="text-left text-secondary">簡単な説明</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="py-3">
        <div class="h3 float-left">収益</div>
        <div class="text-right">新規作成</div>
        <hr>
        <div class="card mb-2">
            <div class="card-header">
                <div class="float-left">科目グループ8</div>
                <div class="text-right">変更</div>
            </div>
            <div class="card-body">
                <div class="text-right p-1">新規作成</div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="float-left">科目81</div>
                        <div class="text-right">変更</div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-header">
                <div class="float-left">科目グループ9</div>
                <div class="text-right">変更</div>
            </div>
            <div class="card-body">
            <div class="text-right p-1">新規作成</div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="float-left">科目91</div>
                        <div class="text-right">変更</div>
                        <div class="text-left text-secondary">簡単な説明</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
