<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The policy mappings for the application.
   *
   * @var array
   */
  protected $policies = [
    // 'App\Model' => 'App\Policies\ModelPolicy',
  ];

  /**
   * Register any authentication / authorization services.
   *
   * @return void
   */
  public function boot()
  {
    $this->registerPolicies();

    /**
     * Проверяем может ли текущий пользователь находится на текущей
     * странице (либо на странице заданной параметром @page).
     *
     * @return bool
     */

    Gate::before(function ($user) {
      if ($user->role->slug == 'admin') return true;
    });

    Gate::define('view-page', function ($user, $page = false) {
      $page = $page ? $page : request()->route()->getName();

      return $user->role->permissions->filter(function ($permission) use ($page) {

        if (Str::contains($page, '|')) {
          $page = explode('|', $page);
          return in_array(str_replace('view-', '', $permission->slug), $page);
        }

        return $permission->slug == str_replace('/', '', 'view-' . $page);
      })->isNotEmpty();
    });
  }
}
