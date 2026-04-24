(function () {
  var cfg = (typeof MP_HEADER_CFG === "object" && MP_HEADER_CFG) || {};

  function allShells() { return document.querySelectorAll(".mp-header__shell"); }
  function allPhones() { return document.querySelectorAll(".mp-header__phone"); }
  function allFloats() { return document.querySelectorAll(".mp-header.mp-header--float"); }
  function allHeaders() { return document.querySelectorAll(".mp-header"); }
  function anyMenuOpen() { return !!document.querySelector(".mp-header__shell.is-open"); }

  if (cfg.scrollFloat) {
    var threshold = Math.max(0, parseInt(cfg.scrollOffset || 0, 10));
    var ticking = false;

    function initFloat() {
      allFloats().forEach(function (el) {
        if (el.dataset.mpFloatInit === "1") return;
        var spacer = document.createElement("div");
        spacer.className = "mp-header__spacer";
        spacer.style.height = el.offsetHeight + "px";
        if (el.parentNode) el.parentNode.insertBefore(spacer, el);
        el.classList.add("mp-header--fixed");
        el.dataset.mpFloatInit = "1";
        el._mpSpacer = spacer;
      });
    }

    function syncSpacers() {
      allFloats().forEach(function (el) {
        if (el._mpSpacer) el._mpSpacer.style.height = el.offsetHeight + "px";
      });
    }

    function updateScrolled() {
      var scrolled = (window.scrollY || window.pageYOffset || 0) > threshold;
      allFloats().forEach(function (el) {
        el.classList.toggle("is-scrolled", scrolled);
      });
      ticking = false;
    }

    function run() {
      initFloat();
      syncSpacers();
      updateScrolled();
    }

    window.addEventListener("scroll", function () {
      if (!ticking) {
        window.requestAnimationFrame(updateScrolled);
        ticking = true;
      }
    }, { passive: true });
    window.addEventListener("resize", function () {
      window.requestAnimationFrame(syncSpacers);
    });
    window.addEventListener("load", syncSpacers);
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", run);
    } else {
      run();
    }
  }

  if (cfg.hideOnScroll) {
    var lastY = window.scrollY || window.pageYOffset || 0;
    var accDown = 0;
    var accUp = 0;
    var hideDelta = Math.max(0, parseInt(cfg.hideDelta || 8, 10));
    var hideMinTop = Math.max(0, parseInt(cfg.hideMinTop || 0, 10));
    var tickingHide = false;

    function setHidden(flag) {
      allHeaders().forEach(function (h) {
        var isFloat = h.classList.contains("is-sticky") || h.classList.contains("mp-header--fixed");
        if (!isFloat) return;
        h.classList.toggle("is-hidden", flag);
      });
    }

    function onHideScroll() {
      var y = window.scrollY || window.pageYOffset || 0;
      var diff = y - lastY;
      lastY = y;
      tickingHide = false;

      if (anyMenuOpen()) {
        setHidden(false);
        accDown = 0; accUp = 0;
        return;
      }

      if (y <= hideMinTop) {
        setHidden(false);
        accDown = 0; accUp = 0;
        return;
      }

      if (diff > 0) {
        accDown += diff; accUp = 0;
        if (accDown >= hideDelta) { setHidden(true); }
      } else if (diff < 0) {
        accUp += -diff; accDown = 0;
        if (accUp >= hideDelta) { setHidden(false); }
      }
    }

    window.addEventListener("scroll", function () {
      if (!tickingHide) {
        window.requestAnimationFrame(onHideScroll);
        tickingHide = true;
      }
    }, { passive: true });
  }

  function closeAllMenus(exceptShell) {
    allShells().forEach(function (s) {
      if (s !== exceptShell) s.classList.remove("is-open");
    });
    document.querySelectorAll(".mp-header__menu-btn[aria-expanded=\"true\"]").forEach(function (b) {
      if (!exceptShell || !exceptShell.contains(b)) b.setAttribute("aria-expanded", "false");
    });
  }
  function closeAllPhones(exceptPhone) {
    allPhones().forEach(function (p) {
      if (p !== exceptPhone) p.classList.remove("is-open");
    });
  }

  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".mp-header__menu-btn");
    if (btn) {
      e.preventDefault();
      var shell = btn.closest(".mp-header__shell");
      if (!shell) return;
      var willOpen = !shell.classList.contains("is-open");
      closeAllMenus(willOpen ? shell : null);
      closeAllPhones(null);
      shell.classList.toggle("is-open", willOpen);
      btn.setAttribute("aria-expanded", willOpen ? "true" : "false");
      return;
    }

    var phone = e.target.closest(".mp-header__phone");
    if (phone) {
      var insidePop = e.target.closest(".mp-header__phone-pop");
      if (!insidePop) {
        e.preventDefault();
        var open = !phone.classList.contains("is-open");
        closeAllPhones(open ? phone : null);
        phone.classList.toggle("is-open", open);
      }
      return;
    }

    if (cfg.closeOnClick) {
      var link = e.target.closest(".mp-header__menu a");
      if (link) {
        closeAllMenus(null);
        return;
      }
    }

    if (cfg.closeOnOutside) {
      if (!e.target.closest(".mp-header__shell")) {
        closeAllMenus(null);
        closeAllPhones(null);
      }
    }
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeAllMenus(null);
      closeAllPhones(null);
    }
  });
})();
