import raf from './raf';

document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelectorAll('#login').length) {
    const login = document.querySelector('#login');
    const dropdown = document.querySelector('#login-dropdown');

    login.addEventListener('click', function(e) {
      if (!login.classList.contains('is-active')) {
        dropdown.style.display = 'block';

        raf(() => {
          dropdown.classList.add('is-dropped');
          login.classList.add('is-active');
        });
      } else {
        dropdown.classList.remove('is-dropped');
        dropdown.addEventListener('transitionend', function handler() {
          login.classList.remove('is-active');
          dropdown.style.display = 'none';
          dropdown.removeEventListener('transitionend', handler)
        });
      }

      e.preventDefault();
    });

    document.addEventListener('click', function(e) {
      if (!e.target.closest('.login-dropdown') && e.target !== login && login.classList.contains('is-active')) {
        dropdown.classList.remove('is-dropped');
        dropdown.addEventListener('transitionend', function handler() {
          login.classList.remove('is-active');
          dropdown.style.display = 'none';
          dropdown.removeEventListener('transitionend', handler)
        });
      }
    });
  }
});
