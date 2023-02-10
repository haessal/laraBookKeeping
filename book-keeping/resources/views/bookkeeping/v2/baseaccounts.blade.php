@extends('bookkeeping.v2.base') @section('pagetitle', 'Accounts') @section('v2_page_content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 d-none d-md-block px-0">
            @isset($accountsnavilinks)
            <nav class="navbar rounded border">
                @foreach ($accountsnavilinks['list'] as $accountsnavi) @if($accountsnavi['name'] ==
                $accountsnavilinks['selected'])
                <span class="navbar-nav"><div style="color: #e36209">{{{ $accountsnavi['caption'] }}}</div></span>
                @else @empty($accountsnavi['link'])
                <span class="navbar-nav"><div class="text-dark">{{{ $accountsnavi['caption'] }}}</div></span>
                @else
                <a class="navbar-nav" href="{{ route($accountsnavi['link'], ['bookId' => $book['id']]) }}">
                    <div class="text-dark">{{{ $accountsnavi['caption'] }}}</div>
                </a>
                @endempty @endif @endforeach
            </nav>
            @endisset
        </div>
        <div class="col-12 d-block d-md-none px-0">
            @isset($accountsnavilinks)
            <nav class="navbar rounded border">
                @foreach ($accountsnavilinks['list'] as $accountsnavi) @if($accountsnavi['name'] ==
                $accountsnavilinks['selected'])
                <span class="navbar-nav"><div style="color: #e36209">{{{ $accountsnavi['caption'] }}}</div></span>
                @else @empty($accountsnavi['link']) @else
                <a class="navbar-nav" href="{{ route($accountsnavi['link'], ['bookId' => $book['id']]) }}">
                    <div class="text-dark">{{{ $accountsnavi['caption'] }}}</div>
                </a>
                @endempty @endif @endforeach
            </nav>
            @endisset
        </div>
    </div>
</div>
<div class="container-fluid mb-5 py-0">@yield('v2_page_accounts_content')</div>
@endsection
