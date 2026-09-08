(function () {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('#primary-nav');
  toggle?.addEventListener('click', function () {
    const open = nav?.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
})();
