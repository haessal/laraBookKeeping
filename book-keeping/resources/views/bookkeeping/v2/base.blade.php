@extends('layouts.bookkeeping')

@section('content')
<div class="container-fuid bg-light">
    <div class="container text-center py-2 mb-4">
        @isset($bookName)
        <p class="h4 mb-0"><i class="fa fa-book"></i>&nbsp {{{ $bookName['owner'] }}} / {{{ $bookName['name'] }}}</p>
        @endisset
    </div>
    <div class="container-fuid">
        <div class="row">
            <div class="col-6 col-md-1 border-bottom px-0"></div>
            <div class="col-md-10 px-0 d-none d-md-block">
                <nav class="navbar navbar-expand-md p-0 navbar-light bg-light">
                    <ul class="navbar-nav nav-justified w-100">
                        <li class="nav-item border-left border-right bg-white" style="border-top: 3px solid #e36209;">
                            <span class="nav-link text-dark"><i class="fa fa-home"></i>&nbsp ホーム</span>
                        </li>
                        <li class="nav-item border-bottom">
                            <a href="./2" class="nav-link"><i class="fa fa-search"></i>&nbsp 伝票検索</a>
                        </li>
                        <li class="nav-item border-bottom">
                            <a href="./3" class="nav-link"><i class="fa fa-pencil-alt"></i>&nbsp 振替伝票</a>
                        </li>
                        <li class="nav-item border-bottom">
                            <a href="./4" class="nav-link"><i class="fa fa-chart-pie"></i>&nbsp 財務諸表</a>
                        </li>
                        <li class="nav-item border-bottom">
                            <a href="./5" class="nav-link"><i class="fa fa-shopping-cart"></i>&nbsp 勘定科目</a>
                        </li>
                        <li class="nav-item border-bottom">
                            <a href="./6" class="nav-link"><i class="fa fa-cog"></i>&nbsp 設定</a>
                        </li>
                        <li class="nav-item border-bottom">
                            <span class="nav-link text-dark"></span>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="col-6 col-md-1 border-bottom px-0"></div>
        </div>
    </div>
</div>
<div class="container-fuid">
    @yield('v2_page_content')
</div>
@endsection