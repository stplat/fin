<div class="app__aside is-opened" id="aside-slide">
  <div class="aside">
    <div class="aside__logo"><img src="{{ asset('images/logo-text.png') }}" alt=""></div>
    <div class="aside__menu">
      <ul class="menu">
        @can('view-page', 'index')
          <li class="menu__item"><a href="" class="menu__link {{!request()->is('/') ?: 'is-active'}}">Панель
              управления</a></li>
        @endcan
        <li class="menu__title">Разделы управления</li>
        @can('view-page', 'budget.index')
          <li class="menu__item"><a href="{{ route('budget.index') }}"
                                    class="menu__link {{!request()->is('budget') ?: 'is-active'}}">Бюджетные
              параметры</a>

          </li>
        @endcan
        @can('view-page', 'involvement')
          <li class="menu__item"><a href="{{ route('involvement.index') }}"
                                    class="menu__link {{!request()->is('involvement') ?: 'is-active'}}">Вовлечение</a>
          </li>
        @endcan
        @can('view-page', 'shipment')
          <li class="menu__item"><a href="{{ route('shipment.index') }}"
                                    class="menu__link {{!request()->is('shipment') ?: 'is-active'}}">План поставок</a>
          </li>
        @endcan
        @can('view-page', 'finance')
          <li class="menu__item"><a href="{{ route('finance.index') }}"
                                    class="menu__link {{!request()->is('finance') ?: 'is-active'}}">Форма №22</a>
          </li>
        @endcan
        @can('view-page', 'application')
          <li class="menu__item"><a href="{{ route('application.index') }}"
                                    class="menu__link {{!request()->is('application') ?: 'is-active'}}">Денежная
              заявка</a>
          </li>
        @endcan
        <li class="menu__item"><a href=""
                                  class="menu__link menu__link--dropdown {{!request()->is('warehouse') && !request()->is('unused') && !request()->is('orders') ?: 'is-active'}}">Распределение</a>
          <ul class="menu__dropdown">
            @can('view-page', 'material.warehouse')
              <li class="menu__item"><a href="{{ route('material.warehouse') }}"
                                        class="menu__link menu__link--empty {{!request()->is('warehouse') ?: 'is-active'}}">Мой
                  склад</a>
              </li>
            @endcan
            @can('view-page', 'material.unused')
              <li class="menu__item"><a href="{{ route('material.unused') }}"
                                        class="menu__link menu__link--empty {{!request()->is('unused') ?: 'is-active'}}">Невостребованные</a>
              </li>
            @endcan
            @can('view-page', 'material.orders')
              <li class="menu__item"><a href="{{ route('material.orders') }}"
                                        class="menu__link menu__link--empty {{!request()->is('orders') ?: 'is-active'}}">Заявки</a>
              </li>
            @endcan
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
