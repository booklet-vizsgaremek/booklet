import * as m from '$lib/paraglide/messages.js';

const getGreeting = (firstName: string | null) => {
	if (!firstName) return null;
	const hour = new Date().getHours();
	if (hour >= 5 && hour < 12) return m['greetings.morning']({ firstName });
	if (hour >= 12 && hour < 18) return m['greetings.afternoon']({ firstName });
	return m['greetings.evening']({ firstName });
};

export default getGreeting;
