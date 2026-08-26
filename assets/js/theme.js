/*
Vanilla-JS progressive enhancement for the design system's interactive
patterns (mobile nav, accordion, tabs). No React/Babel in production —
markup is server-rendered by PHP templates; this just wires up toggling.
*/
(function () {
  'use strict';

  document.addEventListener('click', function (event) {
    var hamburger = event.target.closest('.cs-hamburger');
    if (hamburger) {
      var nav = document.querySelector('.cs-nav-mobile');
      if (nav) {
        var isOpen = nav.classList.toggle('is-open');
        hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      }
      return;
    }

    var accordionTrigger = event.target.closest('.cs-accordion__trigger');
    if (accordionTrigger) {
      var item = accordionTrigger.closest('.cs-accordion__item');
      var wasOpen = item.classList.contains('is-open');
      item.parentElement.querySelectorAll('.cs-accordion__item.is-open').forEach(function (openItem) {
        if (openItem !== item) openItem.classList.remove('is-open');
      });
      item.classList.toggle('is-open', !wasOpen);
      return;
    }

    var tab = event.target.closest('.cs-tabs__tab');
    if (tab) {
      var tabsRoot = tab.closest('.cs-tabs');
      var index = Array.prototype.indexOf.call(tab.parentElement.children, tab);
      tabsRoot.querySelectorAll('.cs-tabs__tab').forEach(function (t, i) {
        t.classList.toggle('is-active', i === index);
      });
      tabsRoot.querySelectorAll('.cs-tabs__panel').forEach(function (p, i) {
        p.classList.toggle('is-active', i === index);
      });
    }
  });
})();
