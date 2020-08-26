<div class="header">
  <div class="header__top">
    <div class="header__btn" id="aside-slide-btn">
      <button class="hamburger hamburger--arrow-r" type="button">
            <span class="hamburger-box">
              <span class="hamburger-inner"></span>
            </span>
      </button>
    </div>
    <div class="header__login">
      <img src="{{ asset('images/admin_avatar.png') }}" alt="" id="login">
      <div class="login-dropdown" id="login-dropdown">
        <ul>
          <li class="login-dropdown__item">
            <a href="{{ route('logout') }}" class="login-dropdown__link"
               onclick="event.preventDefault();document.getElementById('logout-form').submit();">Выход</a>
          </li>
        </ul>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
        </form>
      </div>
    </div>
  </div>
  <div class="header__bottom">
    <div class="header__title"><h3>{!! $title ?? '' !!}</h3></div>
    <div class="header__breadcrumbs">
      <ul class="breadcrumbs">
        <li class="breadcrumbs-item"><a href="/">Главная</a></li>
        @if ($subtitle)
          <li class="breadcrumbs-item"><a href="{!! $subtitle_link ?? '' !!}">{!! $subtitle ?? '' !!}</a></li>
          <li class="breadcrumbs-item">{!! $title ?? '' !!}</li>
        @else
          <li class="breadcrumbs-item">{!! $title ?? '' !!}</li>
        @endif
      </ul>
    </div>
  </div>
</div>
