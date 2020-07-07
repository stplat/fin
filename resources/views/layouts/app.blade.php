@include('layouts._head')
<body>
@include('layouts._sidebar')
@include('layouts._header')
<div class="body">
  <div id="app" @auth v-cloak @endauth>
    @yield('content')
  </div>
</div>
@include('layouts._footer')
