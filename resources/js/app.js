import './bootstrap';

// Theme toggle with persistence and respect for prefers-color-scheme
(function () {
	const root = document.getElementById('app');
	if (!root) return;

	const storageKey = 'nhit.theme';
	const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
	const saved = localStorage.getItem(storageKey);
	const initial = saved || (prefersDark ? 'dark' : 'light');
	root.setAttribute('data-theme', initial === 'dark' ? 'dark' : '');

	function setTheme(next) {
		if (next === 'dark') {
			root.setAttribute('data-theme', 'dark');
			localStorage.setItem(storageKey, 'dark');
		} else {
			root.removeAttribute('data-theme');
			localStorage.setItem(storageKey, 'light');
		}
	}

	window.addEventListener('DOMContentLoaded', () => {
		// Move focus to error summary if present
		const errorSummary = document.getElementById('error-summary');
		if (errorSummary) {
			setTimeout(() => errorSummary.focus(), 0);
		}

		const btn = document.getElementById('themeToggleBtn');
		if (!btn) return;
		btn.addEventListener('click', () => {
			const isDark = root.getAttribute('data-theme') === 'dark';
			setTheme(isDark ? 'light' : 'dark');
		});
	});
})();
