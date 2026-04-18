<script lang="ts">
	import { Badge } from '$lib/components/ui/badge/index.js';
	import * as m from '$lib/paraglide/messages.js';

	let { status } = $props();

	const statusLabels: Record<string, () => string> = {
		pending: m['orders.status.pending'],
		ready: m['orders.status.ready'],
		cancelled: m['orders.status.cancelled'],
		completed: m['orders.status.completed']
	};

	const statusVariants: Record<string, 'progress' | 'warning' | 'destructive' | 'success'> = {
		pending: 'progress',
		ready: 'warning',
		cancelled: 'destructive',
		completed: 'success'
	};
</script>

<Badge variant={statusVariants[status]}>
	{statusLabels[status]()}
</Badge>
