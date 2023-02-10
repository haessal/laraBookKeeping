@extends('layouts.bookkeeping') @section('content') @isset($book)
<div class="bg-light">
    <div class="container mb-4 py-2 text-center">
        <p class="h4 mb-0">
            <i class="fa fa-book"></i>
            &nbsp {{{ $book['owner'] }}} / {{{ $book['name'] }}}
        </p>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-1 border-bottom px-0"></div>
            <div class="col-10 d-none d-md-block px-0">
                <nav class="navbar navbar-expand-md navbar-light bg-light p-0">
                    <ul class="navbar-nav nav-justified w-100">
                        @isset($navilinks) @foreach ($navilinks as $navi) @empty($navi['link'])
                        <li class="nav-item border-bottom">
                            <span class="nav-link text-dark"></span>
                        </li>
                        @else @if($navi['link'] == $selflinkname)
                        <li class="nav-item border-left border-right bg-white" style="border-top: 3px solid #e36209">
                            <span class="nav-link text-dark">
                                <i class="{{ $navi['icon'] }}"></i>
                                &nbsp {{{ $navi['caption'] }}}
                            </span>
                        </li>
                        @else
                        <li class="nav-item border-bottom">
                            <a href="{{ route($navi['link'], $book['id']) }}" class="nav-link">
                                <i class="{{ $navi['icon'] }}"></i>
                                &nbsp {{{ $navi['caption'] }}}
                            </a>
                        </li>
                        @endif @endempty @endforeach @else
                        <li class="nav-item border-bottom">
                            <span class="nav-link text-dark"></span>
                        </li>
                        @endisset
                    </ul>
                </nav>
            </div>
            <div class="col-10 d-block d-md-none border-bottom px-0">
                <nav class="navbar navbar-expand-md navbar-light p-0">
                    <ul class="navbar-nav flex-column border-top border-left border-right w-100 mb-2 bg-white">
                        @isset($navilinks) @foreach ($navilinks as $navi) @isset($navi['link'])
                        <li class="nav-item border-bottom">
                            @if ($navi['link'] == $selflinkname)
                            <span class="nav-link text-dark px-3" style="border-left: 2px solid #e36209">
                                <i class="{{ $navi['icon'] }}"></i>
                                &nbsp {{{ $navi['caption'] }}}
                            </span>
                            @else
                            <a
                                href="{{ route($navi['link'], $book['id']) }}"
                                class="nav-link px-3"
                                style="border-left: 2px solid #ffffff">
                                <i class="{{ $navi['icon'] }}"></i>
                                &nbsp {{{ $navi['caption'] }}}
                            </a>
                            @endif
                        </li>
                        @endisset @endforeach @endisset
                    </ul>
                </nav>
            </div>
            <div class="col-1 border-bottom px-0"></div>
        </div>
    </div>
</div>
<div class="container-fluid">@yield('v2_page_content')</div>
@endisset @endsection
