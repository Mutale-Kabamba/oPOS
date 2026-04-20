import './bootstrap';

// Alpine.js is provided by Livewire/Filament — do not import it here.

const eyeOpenPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
const eyeClosedPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.128-2.267A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.968 9.968 0 01-4.104 5.136M15 12a3 3 0 00-4.243-2.829M9.88 9.88A3 3 0 0014.12 14.12M3 3l18 18" />';

window.togglePassword = function (id, trigger) {
	const input = document.getElementById(id);
	if (!input) return;

	const svg = trigger.querySelector('svg');
	const isHidden = input.type === 'password';

	input.type = isHidden ? 'text' : 'password';
	trigger.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');

	if (svg) {
		svg.innerHTML = isHidden ? eyeClosedPath : eyeOpenPath;
	}
};

if ('serviceWorker' in navigator) {
	window.addEventListener('load', () => {
		navigator.serviceWorker.register('/sw.js').catch(() => {
			// Swallow registration errors to avoid breaking core app behavior.
		});
	});
}
