<div class="app__aside is-opened" id="aside-slide">
  <div class="aside">
    <div class="aside__logo"><img src="{{ asset('images/logo-text.png') }}" alt=""></div>
    <div class="aside__menu">
      <ul class="menu">
        <li class="menu__item"><a href="" class="menu__link {{!request()->is('/') ?: 'is-active'}}">Панель
            управления</a></li>
        <li class="menu__title">Разделы управления</li>
        <li class="menu__item"><a href="{{ route('budget.index') }}"
                                  class="menu__link {{!request()->is('budget') ?: 'is-active'}}">Бюджетные параметры</a>
        </li>
        <li class="menu__item"><a href="{{ route('involvement.index') }}"
                                  class="menu__link {{!request()->is('involvement') ?: 'is-active'}}">Вовлечение</a>
        </li>
        <li class="menu__item"><a href="{{ route('shipment.index') }}"
                                  class="menu__link {{!request()->is('shipment') ?: 'is-active'}}">План поставок</a>
        </li>
        <li class="menu__item"><a href="{{ route('finance.index') }}"
                                  class="menu__link {{!request()->is('finance') ?: 'is-active'}}">Форма №22</a>
        </li>
        <li class="menu__item"><a href="{{ route('application.index') }}"
                                  class="menu__link {{!request()->is('application') ?: 'is-active'}}">Денежная
            заявка</a>
        </li>
        <li class="menu__item"><a href=""
                                  class="menu__link menu__link--dropdown {{!request()->is('warehouse') && !request()->is('application') ?: 'is-active'}}">Распределение</a>
          <ul class="menu__dropdown">
            <li class="menu__item"><a href="{{ route('warehouse.index') }}"
                                      class="menu__link menu__link--empty {{!request()->is('warehouse') ?: 'is-active'}}">Мой склад</a>
            </li>
            <li class="menu__item"><a href="{{ route('warehouse.index') }}"
                                      class="menu__link menu__link--empty {{!request()->is('application') ?: 'is-active'}}">Невостребованные</a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
