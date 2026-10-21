(function () {
  "use strict";
  const lang = document.documentElement.lang === "ar" ? "ar" : "en";
  const t = (en, ar) => (lang === "en" ? en : ar);
  // Icons injected by motion helpers share the server-side icon styling.
  const iconPaths = {
    bolt: '<path d="m13 2-8 12h7l-1 8 8-12h-7z"/>',
    building:
      '<path d="M4 21V7l8-4v18M12 9h8v12M7 9h2M7 13h2M7 17h2M15 13h2M15 17h2"/>',
    arrow: '<path d="M5 19 19 5M9 5h10v10"/>',
    check: '<path d="m5 12 4 4L19 6"/>',
  };
  const icon = (name, cls = "") =>
    `<svg class="ui-icon ${cls}" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">${iconPaths[name] || iconPaths.arrow}</svg>`;

  // Marked sections become visible when they enter the viewport.
  function reveal() {
    const nodes = document.querySelectorAll(".reveal, .section-reveal");
    if (!("IntersectionObserver" in window)) {
      nodes.forEach((node) => node.classList.add("show"));
      return;
    }
    const observer = new IntersectionObserver(
      (entries) =>
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("show");
            observer.unobserve(entry.target);
          }
        }),
      { threshold: 0.08, rootMargin: "0px 0px -30px" },
    );
    nodes.forEach((node, index) => {
      node.style.transitionDelay = `${Math.min(index % 4, 3) * 70}ms`;
      observer.observe(node);
    });
  }

  // Page position drives progress, parallax and number animation.
  function interactions() {
    const progress = document.getElementById("scrollProgress");
    const onScroll = () => {
      const max = document.documentElement.scrollHeight - innerHeight;
      if (progress)
        progress.style.transform = `scaleX(${max > 0 ? scrollY / max : 0})`;
      document.body.classList.toggle("scrolled", scrollY > 40);
      document.querySelectorAll("[data-parallax]").forEach((img) => {
        img.style.transform = `translateY(${Math.min(scrollY * 0.06, 45)}px) scale(1.06)`;
      });
    };
    addEventListener("scroll", onScroll, { passive: true });
    onScroll();
    document.querySelectorAll(".interactive").forEach((card) => {
      card.addEventListener("pointermove", (e) => {
        if (innerWidth < 992) return;
        const r = card.getBoundingClientRect();
        card.style.transform = `perspective(900px) rotateX(${(e.clientY - r.top - r.height / 2) / -45}deg) rotateY(${(e.clientX - r.left - r.width / 2) / 45}deg)`;
      });
      card.addEventListener("pointerleave", () => {
        card.style.transform = "";
      });
    });
    const counters = document.querySelectorAll("[data-count]");
    if (!counters.length || !("IntersectionObserver" in window)) return;
    const counterObserver = new IntersectionObserver(
      (entries) =>
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          const el = entry.target,
            goal = Number(el.dataset.count),
            suffix = el.dataset.suffix || "";
          const duration = 1100,
            tick = performance.now();
          const frame = (now) => {
            const ratio = Math.min((now - tick) / duration, 1);
            el.textContent = `${Math.round(goal * (1 - Math.pow(1 - ratio, 3)))}${suffix}`;
            if (ratio < 1) requestAnimationFrame(frame);
          };
          requestAnimationFrame(frame);
          counterObserver.unobserve(el);
        }),
      { threshold: 0.5 },
    );
    counters.forEach((el) => counterObserver.observe(el));
  }

  // Shared icons and motion classes are attached to page elements.
  function decorate() {
    document
      .querySelectorAll("main > section:not(.hero):not(.page-hero)")
      .forEach((section) => {
        section.classList.add("section-reveal");
      });
    document.querySelectorAll(".btn-est i").forEach((mark) => {
      mark.innerHTML = icon("arrow");
    });
    document.querySelectorAll(".rule-list i").forEach((mark) => {
      if (!mark.querySelector(".ui-icon")) mark.innerHTML = icon("check");
    });
    document.querySelectorAll(".contact-card h3").forEach((heading, i) => {
      if (!heading.querySelector(".ui-icon"))
        heading.insertAdjacentHTML(
          "afterbegin",
          icon(i < 2 ? "bolt" : "building"),
        );
    });
    document
      .querySelectorAll(
        ".home-metrics article, .home-sector-card, .why-card, .value-card, .project-stack-item, .document-card, .scope-card, .service-deliverables article, .project-quote-banner",
      )
      .forEach((card) => card.classList.add("reveal", "motion-card"));
  }

  // The mobile menu closes after a navigation link is selected.
  function navigation() {
    document
      .querySelectorAll("#mainNav a:not([data-bs-toggle])")
      .forEach((link) =>
        link.addEventListener("click", () => {
          const nav = document.getElementById("mainNav");
          if (innerWidth < 1200 && nav?.classList.contains("show"))
            bootstrap.Collapse.getOrCreateInstance(nav).hide();
        }),
      );
  }

  // The custom pointer runs only with a fine pointer and motion enabled.
  function cursor() {
    if (
      !matchMedia("(pointer:fine)").matches ||
      matchMedia("(prefers-reduced-motion: reduce)").matches
    )
      return;
    document.body.insertAdjacentHTML(
      "beforeend",
      '<span class="cursor-dot"></span><span class="cursor-ring"></span>',
    );
    const dot = document.querySelector(".cursor-dot"),
      ring = document.querySelector(".cursor-ring");
    let rx = innerWidth / 2,
      ry = innerHeight / 2,
      mx = rx,
      my = ry;
    addEventListener("pointermove", (e) => {
      mx = e.clientX;
      my = e.clientY;
      dot.style.transform = `translate(${mx}px,${my}px)`;
    });
    const loop = () => {
      rx += (mx - rx) * 0.16;
      ry += (my - ry) * 0.16;
      ring.style.transform = `translate(${rx}px,${ry}px)`;
      requestAnimationFrame(loop);
    };
    loop();
    document.querySelectorAll("a,button,summary,input,textarea,select").forEach((el) => {
      el.addEventListener("mouseenter", () =>
        document.body.classList.add("cursor-active"),
      );
      el.addEventListener("mouseleave", () =>
        document.body.classList.remove("cursor-active"),
      );
    });
  }

  // The form validates the captcha locally and submits without a page reload.
  function form() {
    const form = document.getElementById("contactForm");
    if (!form) return;
    const question = document.getElementById("captchaQuestion");
    const answer = document.getElementById("captchaAnswer");
    const locationSelect = document.getElementById("projectLocation");
    const otherGroup = document.getElementById("otherLocationGroup");
    const otherInput = document.getElementById("otherLocation");
    const attachmentInput = form.elements.attachment;
    const selectedFileName = document.getElementById("selectedFileName");
    const refreshButton = document.getElementById("refreshCaptcha");
    const captchaError = document.getElementById("captchaError");
    const formMessage = document.getElementById("formMessage");
    const submitButton = form.querySelector('button[type="submit"]');
    const captchaUrl = document.documentElement.lang === "ar" ? "/ar/contact/captcha" : "/en/contact/captcha";
    const messages = {
      success: form.dataset.messageSuccess || "",
      error: form.dataset.messageError || "",
      captcha: form.dataset.messageCaptcha || "",
    };

    const showStatus = (element, text, variant) => {
      if (!element) return;
      element.textContent = text || "";
      element.classList.remove("alert-danger", "alert-success");
      element.classList.toggle("d-none", !text);
      if (text) element.classList.add(variant);
    };

    const updateLocation = () => {
      const custom = locationSelect?.value === "other";
      if (otherGroup) otherGroup.hidden = !custom;
      if (otherInput) {
        otherInput.required = custom;
        if (!custom) otherInput.value = "";
      }
    };

    const refreshCaptcha = async () => {
      if (answer) answer.value = "";
      try {
        const response = await fetch(captchaUrl, {
          headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
        });
        if (!response.ok) return;
        const data = await response.json();
        if (question && data.question) {
          question.textContent = data.question;
        }
      } catch (error) {
        // The server keeps validating the answer when the question cannot be loaded.
      }
    };

    attachmentInput?.addEventListener("change", () => {
      if (selectedFileName)
        selectedFileName.textContent =
          attachmentInput.files?.[0]?.name || t("No file selected", "لم يتم اختيار ملف");
    });

    refreshCaptcha();
    if (refreshButton) {
      refreshButton.setAttribute("aria-label", t("New question", "سؤال جديد"));
      refreshButton.addEventListener("click", (event) => {
        event.preventDefault();
        showStatus(captchaError, "", "alert-danger");
        refreshCaptcha();
      });
    }
    answer?.addEventListener("input", () => showStatus(captchaError, "", "alert-danger"));
    locationSelect?.addEventListener("change", updateLocation);
    updateLocation();

    const submitQuote = async () => {
      if (submitButton) submitButton.disabled = true;
      showStatus(formMessage, "", "alert-danger");
      try {
        const response = await fetch(form.action, {
          method: "POST",
          body: new FormData(form),
          headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
        });
        const payload = await response.json().catch(() => ({}));
        const firstError = payload.errors ? Object.values(payload.errors)[0]?.[0] : "";

        if (response.ok && payload.status === "ok") {
          showStatus(formMessage, payload.message || messages.success, "alert-success");
          form.reset();
          updateLocation();
          showStatus(captchaError, "", "alert-danger");
          refreshCaptcha();
          return;
        }

        showStatus(
          formMessage,
          payload.message || firstError || messages.error,
          "alert-danger",
        );
        if (payload.key === "invalid_captcha") {
          showStatus(captchaError, payload.message || messages.captcha, "alert-danger");
          refreshCaptcha();
        }
      } catch (error) {
        showStatus(formMessage, messages.error, "alert-danger");
      } finally {
        if (submitButton) submitButton.disabled = false;
      }
    };

    form.addEventListener("submit", (event) => {
      event.preventDefault();
      submitQuote();
    });
  }

  // Layout and interactions initialize after the document is ready.
  function init() {
    decorate();
    reveal();
    interactions();
    navigation();
    form();
    cursor();
    requestAnimationFrame(() => document.body.classList.add("site-ready"));
  }
  document.addEventListener("DOMContentLoaded", init);
})();
