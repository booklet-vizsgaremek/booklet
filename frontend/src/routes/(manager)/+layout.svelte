<script lang="ts">
	import SessionTimer from '$lib/components/SessionTimer.svelte';

	const { children } = $props();
</script>

<SessionTimer />

{@render children()}
