// Custom select (inc/select.php). Progressive enhancement over a real <select>:
// the native field stays the single source of truth, this only draws a styled
// proxy on top of it and keeps the two in sync.
//
// Backend integration:
//   • the <select> submits normally — nothing special to read server-side;
//   • picking an option sets select.value and fires native "input" + "change"
//     events, so external code can just listen on the <select>;
//   • external code may set select.value and dispatch "change" — the pill and
//     the list re-render themselves;
//   • [data-select-submit] submits the closest form after a user pick, which is
//     all a plain server-rendered filter (?sort=…) needs.
(function () {
  var OPEN_CLASS = "is-open";
  var UP_CLASS = "select--up";
  var instances = [];

  function CustomSelect(root) {
    this.root = root;
    this.native = root.querySelector(".js-select-native");
    this.toggle = root.querySelector(".js-select-toggle");
    this.list = root.querySelector(".js-select-list");
    this.valueEl = root.querySelector(".js-select-value");
    this.options = [];
    this.activeIndex = -1;
    this.isOpen = false;
    this.typed = "";
    this.typedTimer = null;
  }

  CustomSelect.prototype.init = function () {
    if (!this.native || !this.toggle || !this.list || !this.valueEl) return false;

    this.options = Array.prototype.slice.call(this.list.querySelectorAll(".select__option"));
    if (!this.options.length) return false;

    this.root.classList.add("select--ready");
    this.native.setAttribute("tabindex", "-1");
    this.native.setAttribute("aria-hidden", "true");

    var self = this;

    this.toggle.addEventListener("click", function () {
      self.isOpen ? self.close() : self.open();
    });

    // Focus never leaves the button (APG select-only combobox) — the current
    // option is announced through aria-activedescendant instead.
    this.toggle.addEventListener("keydown", function (e) {
      self.onKeydown(e);
    });

    this.list.addEventListener("click", function (e) {
      var option = e.target.closest ? e.target.closest(".select__option") : null;
      if (!option || !self.list.contains(option)) return;
      self.select(self.options.indexOf(option), true);
      self.close(true);
    });

    // Hovering an option makes it the keyboard target too — one visible cursor.
    this.list.addEventListener("mousemove", function (e) {
      var option = e.target.closest ? e.target.closest(".select__option") : null;
      if (!option || !self.list.contains(option)) return;
      self.setActive(self.options.indexOf(option), false);
    });

    // Someone else (backend JS, reset, autofill) changed the field.
    this.native.addEventListener("change", function () {
      self.syncFromNative();
    });

    this.syncFromNative();
    return true;
  };

  // ── State ──────────────────────────────────────────────────────────
  CustomSelect.prototype.indexOfValue = function (value) {
    for (var i = 0; i < this.options.length; i++) {
      if (this.options[i].getAttribute("data-value") === value) return i;
    }
    return -1;
  };

  // Redraw the pill + list from whatever the native <select> currently holds.
  CustomSelect.prototype.syncFromNative = function () {
    var index = this.indexOfValue(this.native.value);
    if (index < 0) index = this.native.selectedIndex;

    for (var i = 0; i < this.options.length; i++) {
      var selected = i === index;
      this.options[i].classList.toggle("is-selected", selected);
      this.options[i].setAttribute("aria-selected", selected ? "true" : "false");
    }

    if (index >= 0) {
      var text = this.options[index].querySelector(".select__option-text");
      this.valueEl.textContent = text ? text.textContent : this.options[index].textContent;
      this.root.classList.remove("select--empty");
    } else {
      this.root.classList.add("select--empty");
    }

    this.setActive(index >= 0 ? index : 0, false);
  };

  // Commit a choice back to the native field. `fromUser` distinguishes a real
  // click/keypress from a programmatic sync, so we only auto-submit for people.
  CustomSelect.prototype.select = function (index, fromUser) {
    if (index < 0 || index >= this.options.length) return;

    var value = this.options[index].getAttribute("data-value");
    if (this.native.value === value) return;

    this.native.value = value;
    this.syncFromNative();
    this.native.dispatchEvent(new Event("input", { bubbles: true }));
    this.native.dispatchEvent(new Event("change", { bubbles: true }));

    if (fromUser && this.root.hasAttribute("data-select-submit")) {
      var form = this.native.form;
      if (form) {
        if (typeof form.requestSubmit === "function") form.requestSubmit();
        else form.submit();
      }
    }
  };

  CustomSelect.prototype.setActive = function (index, scroll) {
    if (index < 0 || index >= this.options.length) return;

    for (var i = 0; i < this.options.length; i++) {
      this.options[i].classList.toggle("is-active", i === index);
    }
    this.activeIndex = index;
    if (this.isOpen) this.toggle.setAttribute("aria-activedescendant", this.options[index].id || "");

    if (scroll) {
      var option = this.options[index];
      var top = option.offsetTop;
      var bottom = top + option.offsetHeight;
      if (top < this.list.scrollTop) this.list.scrollTop = top;
      else if (bottom > this.list.scrollTop + this.list.clientHeight) {
        this.list.scrollTop = bottom - this.list.clientHeight;
      }
    }
  };

  // ── Open / close ───────────────────────────────────────────────────
  CustomSelect.prototype.open = function () {
    if (this.isOpen) return;

    closeAll(this);
    this.isOpen = true;
    this.root.classList.add(OPEN_CLASS);
    this.toggle.setAttribute("aria-expanded", "true");
    this.placeDropdown();

    this.setActive(this.activeIndex >= 0 ? this.activeIndex : 0, true);
  };

  CustomSelect.prototype.close = function (focusToggle) {
    if (!this.isOpen) return;

    this.isOpen = false;
    this.root.classList.remove(OPEN_CLASS, UP_CLASS);
    this.toggle.setAttribute("aria-expanded", "false");
    this.toggle.removeAttribute("aria-activedescendant");
    if (focusToggle) this.toggle.focus();
  };

  // Flip the panel above the field when there is no room underneath.
  CustomSelect.prototype.placeDropdown = function () {
    this.root.classList.remove(UP_CLASS);

    var rect = this.toggle.getBoundingClientRect();
    var needed = this.list.scrollHeight + 24;
    var below = window.innerHeight - rect.bottom;

    if (below < Math.min(needed, 240) && rect.top > below) {
      this.root.classList.add(UP_CLASS);
    }
  };

  // ── Keyboard (everything happens on the button) ────────────────────
  CustomSelect.prototype.onKeydown = function (e) {
    var last = this.options.length - 1;

    switch (e.key) {
      case "ArrowDown":
        e.preventDefault();
        if (!this.isOpen) this.open();
        else this.setActive(Math.min(this.activeIndex + 1, last), true);
        break;
      case "ArrowUp":
        e.preventDefault();
        if (!this.isOpen) this.open();
        else this.setActive(Math.max(this.activeIndex - 1, 0), true);
        break;
      case "Home":
        e.preventDefault();
        if (!this.isOpen) this.open();
        this.setActive(0, true);
        break;
      case "End":
        e.preventDefault();
        if (!this.isOpen) this.open();
        this.setActive(last, true);
        break;
      case "Enter":
      case " ":
      case "Spacebar":
        e.preventDefault();
        if (!this.isOpen) {
          this.open();
        } else {
          this.select(this.activeIndex, true);
          this.close(true);
        }
        break;
      case "Escape":
        if (!this.isOpen) return;
        e.preventDefault();
        this.close(true);
        break;
      case "Tab":
        this.close(false);
        break;
      default:
        // typing jumps to a matching option, like a native <select>
        if (e.key && e.key.length === 1 && !e.metaKey && !e.ctrlKey && !e.altKey) {
          e.preventDefault();
          if (!this.isOpen) this.open();
          this.typeahead(e.key);
        }
        break;
    }
  };

  CustomSelect.prototype.typeahead = function (char) {
    var self = this;
    if (this.typedTimer) window.clearTimeout(this.typedTimer);
    this.typedTimer = window.setTimeout(function () {
      self.typed = "";
    }, 600);

    this.typed += char.toLowerCase();

    for (var i = 0; i < this.options.length; i++) {
      var text = this.options[i].textContent.trim().toLowerCase();
      if (text.indexOf(this.typed) === 0) {
        this.setActive(i, true);
        return;
      }
    }
  };

  // ── Wiring ─────────────────────────────────────────────────────────
  function closeAll(except) {
    for (var i = 0; i < instances.length; i++) {
      if (instances[i] !== except) instances[i].close(false);
    }
  }

  function init() {
    var roots = document.querySelectorAll("[data-select]");

    for (var i = 0; i < roots.length; i++) {
      if (roots[i].dataset.selectReady) continue;
      var instance = new CustomSelect(roots[i]);
      if (instance.init()) {
        roots[i].dataset.selectReady = "1";
        instances.push(instance);
      }
    }

    if (instances.length && !init.bound) {
      init.bound = true;

      document.addEventListener("pointerdown", function (e) {
        for (var i = 0; i < instances.length; i++) {
          if (instances[i].isOpen && !instances[i].root.contains(e.target)) {
            instances[i].close(false);
          }
        }
      });

      document.addEventListener("keydown", function (e) {
        if (e.key !== "Escape") return;
        for (var i = 0; i < instances.length; i++) {
          if (instances[i].isOpen) instances[i].close(true);
        }
      });

      window.addEventListener("resize", function () {
        closeAll(null);
      });
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  // Re-scan after AJAX re-renders: window.initCustomSelects()
  window.initCustomSelects = init;
})();
