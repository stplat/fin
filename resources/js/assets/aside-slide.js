import raf from './raf';

document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelectorAll('#aside-slide-btn').length) {
    const btn = document.querySelector('#aside-slide-btn');
    const aside = document.querySelector('#aside-slide');
    const content = document.querySelector('#content-slide');

    btn.addEventListener('click', function(e) {
      const hamburger = this.querySelector('.hamburger');

      if (!hamburger.classList.contains('is-active')) {
        aside.classList.remove('is-opened');
        content.classList.add('is-full');

        content.addEventListener('transitionend', function handler() {
          hamburger.classList.add('is-active');
          content.removeEventListener('transitionend', handler);
        });
      } else {
        aside.classList.add('is-opened');
        content.classList.remove('is-full');

        content.addEventListener('transitionend', function handler() {
          hamburger.classList.remove('is-active');
          content.removeEventListener('transitionend', handler);
        });
      }

      e.preventDefault();
    });
  }
});
