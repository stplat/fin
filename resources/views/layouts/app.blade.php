@include('layouts._head')

<body>
@include('layouts._aside')
<div class="app__content" id="content-slide">
  @include('layouts._header')
  <main class="app__main" id="app" @auth v-cloak @endauth>
    @yield('content')
  </main>
</div>

@include('layouts._footer')
