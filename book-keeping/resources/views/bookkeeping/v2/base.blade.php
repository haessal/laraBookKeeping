@extends('layouts.bookkeeping')

@section('content')
@isset($book)
<div class="container-fuid bg-light">
    <div class="container text-center py-2 mb-4">
        <p class="h4 mb-0"><i class="fa fa-book"></i>&nbsp {{{ $book['owner'] }}} / {{{ $book['name'] }}}</p>
    </div>
    <div class="container-fuid">
        <div class="row">
            <div class="col-6 col-md-1 border-bottom px-0"></div>
            <div class="col-md-10 px-0 d-none d-md-block">
                <nav class="navbar navbar-expand-md p-0 navbar-light bg-light">
                    <ul class="navbar-nav nav-justified w-100">
                        @isset($navilinks)
                        @foreach ($navilinks as $navi)
                        @empty($navi['link'])
                        <li class="nav-item border-bottom">
                            <span class="nav-link text-dark"></span>
                        </li>
                        @else
                        @if($navi['link'] == $selflinkname)
                        <li class="nav-item border-left border-right bg-white" style="border-top: 3px solid #e36209;">
                            <span class="nav-link text-dark"><i class="{{ $navi['icon'] }}"></i>&nbsp {{{ $navi['caption'] }}}</span>
                        </li>
                        @else
                        <li class="nav-item border-bottom">
                            <a href="{{ route($navi['link'], $book['id']) }}" class="nav-link"><i class="{{ $navi['icon'] }}"></i>&nbsp {{{ $navi['caption'] }}}</a>
                        </li>
                        @endif
                        @endempty
                        @endforeach
                        @else
                        <li class="nav-item border-bottom">
                            <span class="nav-link text-dark"></span>
                        </li>
                        @endisset
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
@endisset
@endsection
