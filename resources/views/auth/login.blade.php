@extends('layouts.auth', ['title' => 'Вход в систему'])

@section('content')
  <div class="auth">
    <div class="auth__wrap">
      <div class="auth__logo">
        <img src="{{ asset('images/admin/logo.png') }}" alt="">
      </div>
      <div class="auth__entry">
        <div class="card c-shadow">
          <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
              <label for="login"><strong>Имя пользователя</strong></label>
              <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
            </div>
            <div class="form-group">
              <label for="password"><strong>Пароль</strong></label>
              <input id="password" type="password" class="form-control @error('name') is-invalid @enderror" name="password" required autocomplete="current-password">
              @error('name')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div class="form-group form-check m-0">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Запомнить</label>
              </div>
              <button type="submit" class="btn btn-primary">Войти</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
