<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import * as AlertDialog from '$lib/components/ui/alert-dialog';
	import * as m from '$lib/paraglide/messages.js';

	const TIMEOUT_MS = 15 * 60 * 1000;
	const WARN_MS = 60 * 1000;

	let timeLeft = $state(TIMEOUT_MS);
	let open = $derived(timeLeft <= WARN_MS);
	let interval: ReturnType<typeof setInterval>;
	let timeout: ReturnType<typeof setTimeout>;

	const minutes = $derived(Math.floor(timeLeft / 60000));
	const seconds = $derived(Math.floor((timeLeft % 60000) / 1000));

	const signout = async () => {
		clearInterval(interval);
		clearTimeout(timeout);
		await fetch('/signout', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: ''
		});
	};

	const startTimers = () => {
		clearInterval(interval);
		clearTimeout(timeout);
		timeLeft = TIMEOUT_MS;

		interval = setInterval(() => {
			timeLeft -= 1000;
		}, 1000);

		timeout = setTimeout(signout, TIMEOUT_MS);
	};

	onMount(() => {
		window.addEventListener('mousemove', startTimers);
		window.addEventListener('keydown', startTimers);
		window.addEventListener('click', startTimers);
		window.addEventListener('touchstart', startTimers);

		startTimers();

		return () => {
			clearInterval(interval);
			clearTimeout(timeout);
			window.removeEventListener('mousemove', startTimers);
			window.removeEventListener('keydown', startTimers);
			window.removeEventListener('click', startTimers);
			window.removeEventListener('touchstart', startTimers);
		};
	});

	const alertDescription = () => {
		let remaining = `${minutes}:${seconds.toString().padStart(2, '0')}`;
		if (window.matchMedia('(pointer: coarse)').matches)
			return m['admin.session_expiry_alert.description_mobile']({ remaining });
		else return m['admin.session_expiry_alert.description_desktop']({ remaining });
	};
</script>

<AlertDialog.Root {open}>
	<AlertDialog.Content>
		<AlertDialog.Header>
			<AlertDialog.Title>{m['admin.session_expiry_alert.title']()}</AlertDialog.Title>
			<AlertDialog.Description>
				{alertDescription()}
			</AlertDialog.Description>
		</AlertDialog.Header>
	</AlertDialog.Content>
</AlertDialog.Root>
