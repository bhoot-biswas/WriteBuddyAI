export const fetchMessages = async ({ pageParam = 1 }) => {
	try {
		const params = new URLSearchParams({
			action: "writebuddy_ai_get_thread_messages",
			page: pageParam,
			end_date: writebuddy.current_time,
		});

		const response = await fetch(`${writebuddy.admin_url}?${params}`);

		if (!response.ok) {
			throw new Error("Network response was not ok");
		}
		const data = await response.json();
		return data;
	} catch (error) {
		throw new Error("Failed to fetch messages");
	}
};

export const saveMessage = async (newMessages) => {
	try {
		const response = await fetch(
			writebuddy.admin_url + "?action=writebuddy_ai_save_message",
			{
				method: "POST",
				headers: {
					"Content-Type": "application/json",
				},
				body: JSON.stringify({
					writebuddy_security: writebuddy.security,
					messages: newMessages,
				}),
			}
		);

		if (!response.ok) {
			throw new Error("Network response was not ok");
		}
		const data = await response.json();
		return data;
	} catch (error) {
		throw new Error("Failed to save message");
	}
};
