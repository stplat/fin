import raf from './raf';

document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelectorAll('.menu__link--dropdown').length) {
    const buttons = document.querySelectorAll('.menu__link--dropdown');

    [ ...buttons ].forEach(button => {
      button.addEventListener('click', function (e) {

        if (!button.classList.contains('is-active')) {
          button.classList.add('is-active');
        } else {
          button.classList.remove('is-active');
        }

        e.preventDefault();
      });
    });

    // login.addEventListener('click', function(e) {
    //   if (!login.classList.contains('is-active')) {
    //     dropdown.style.display = 'block';
    //
    //     raf(() => {
    //       dropdown.classList.add('is-dropped');
    //       login.classList.add('is-active');
    //     });
    //   } else {
    //     dropdown.classList.remove('is-dropped');
    //     dropdown.addEventListener('transitionend', function handler() {
    //       login.classList.remove('is-active');
    //       dropdown.style.display = 'none';
    //       dropdown.removeEventListener('transitionend', handler)
    //     });
    //   }
    //
    //   e.preventDefault();
    // });
    //
    // document.addEventListener('click', function(e) {
    //   if (!e.target.closest('.login-dropdown') && e.target !== login && login.classList.contains('is-active')) {
    //     dropdown.classList.remove('is-dropped');
    //     dropdown.addEventListener('transitionend', function handler() {
    //       login.classList.remove('is-active');
    //       dropdown.style.display = 'none';
    //       dropdown.removeEventListener('transitionend', handler)
    //     });
    //   }
    // });
  }
});
