function raf(fn) {
  window.requestAnimationFrame(() => {
    window.requestAnimationFrame(() => {
      fn();
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const body = document.querySelector('body');  
  const btn = document.querySelector('.burger');
  const wrap = document.querySelector('.nav');
  const nav = document.querySelector('.nav__wrap');

  // Навигация (меню)
  btn.addEventListener('click', (e) => {
    if (!btn.classList.contains('is-active')) {
      btn.classList.add('is-active');
      body.classList.add('is-popup');
      wrap.style.display = 'block';
      raf(() => {
        nav.classList.add('is-active');
      });
    } else {
      nav.classList.remove('is-active');
      body.classList.remove('is-popup');
      nav.addEventListener('transitionend', () => {
        btn.classList.remove('is-active');
        wrap.style.display = 'none';
      }, {once: true});     
    }
    e.preventDefault();
  });  

  // Обработка Ресайзов документов
  window.addEventListener('resize', () => {
    if (btn.classList.contains('is-active')) {
      nav.classList.remove('is-active');
      body.classList.remove('is-popup');
      nav.addEventListener('transitionend', () => {
        btn.classList.remove('is-active');
        wrap.style.display = 'none';
      }, {once: true});
    }    
  });

  // Обработка кликов по документы
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.burger') && !e.target.closest('.nav__wrap')) {
      if (btn.classList.contains('is-active')) {
        nav.classList.remove('is-active');
        nav.addEventListener('transitionend', () => {
          btn.classList.remove('is-active');
          body.classList.remove('is-popup');
          wrap.style.display = 'none';
        }, {once: true});
      }      
    }
  });


});
