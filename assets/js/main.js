(() => {
  const prefersReducedMotion = window.matchMedia(
    '(prefers-reduced-motion: reduce)'
  ).matches;

  const focusTarget = (target) => {
    if (!target.hasAttribute('tabindex')) {
      target.setAttribute('tabindex', '-1');
    }
    target.focus({ preventScroll: true });
  };

  const scrollToTarget = (target, hash) => {
    target.scrollIntoView({
      behavior: prefersReducedMotion ? 'auto' : 'smooth',
      block: 'start',
    });
    focusTarget(target);
    if (hash) {
      history.pushState(null, '', hash);
    }
  };

  document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href^="#"]');
    if (!link) return;

    const hash = link.getAttribute('href');
    const targetId = hash.slice(1);

    if (!targetId) return;

    const target = document.getElementById(targetId);
    if (!target) return;

    event.preventDefault();
    scrollToTarget(target, hash);
  });

  const filterInput = document.querySelector('[data-filter-posts]');
  const clearButtons = document.querySelectorAll('[data-clear-filter]');

  if (filterInput) {
    const cards = Array.from(document.querySelectorAll('[data-post-card]'));
    const emptyState = document.querySelector('[data-no-results]');
    const resultsCount = document.querySelector('[data-results-count]');
    const totalCount = document.querySelector('[data-post-count]');
    const total = cards.length;

    if (totalCount) {
      totalCount.textContent = String(total);
    }

    const applyFilter = () => {
      const query = filterInput.value.trim().toLowerCase();
      let matches = 0;

      cards.forEach((card) => {
        const searchText = card.dataset.searchText || card.textContent;
        const isMatch = !query || searchText.toLowerCase().includes(query);
        card.hidden = !isMatch;
        if (isMatch) matches += 1;
      });

      if (resultsCount) {
        resultsCount.textContent = String(matches);
      }

      if (emptyState) {
        emptyState.hidden = matches !== 0;
      }
    };

    filterInput.addEventListener('input', applyFilter);
    filterInput.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        filterInput.value = '';
        applyFilter();
      }
    });

    clearButtons.forEach((button) => {
      button.addEventListener('click', () => {
        filterInput.value = '';
        applyFilter();
        filterInput.focus({ preventScroll: true });
      });
    });

    applyFilter();
  }

  const copyButton = document.querySelector('[data-copy-link]');
  const status = document.querySelector('[data-copy-status]');

  if (copyButton) {
    const defaultStatus = status?.textContent || '';

    copyButton.addEventListener('click', async () => {
      if (!navigator.clipboard) {
        copyButton.dataset.state = 'error';
        if (status) status.textContent = 'Copy not supported in this browser.';
        return;
      }

      try {
        await navigator.clipboard.writeText(window.location.href);
        copyButton.dataset.state = 'success';
        if (status) status.textContent = 'Link copied—send it to someone who needs it.';
        setTimeout(() => {
          copyButton.dataset.state = '';
          if (status) status.textContent = defaultStatus;
        }, 2400);
      } catch (error) {
        copyButton.dataset.state = 'error';
        if (status) status.textContent = 'Copy unavailable—use your browser share menu.';
      }
    });
  }
})();
