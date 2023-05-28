import { Spinner } from "@wordpress/components";
import { MessageContext } from "./context";
import useChatMessages from "./hooks/useChatMessages";
import useChat from "./hooks/useChat";
import Message from "./Message";

export default function ChatMessages() {
	const { data: previousMessages, error, status } = useChatMessages();
	const { messages } = useChat();

	if (status === "loading") {
		return <Spinner />;
	}

	if (status === "error") {
		return <p>Error: {error.message}</p>;
	}

	return (
		<>
			{previousMessages.pages.map((page) => (
				<React.Fragment key={page.nextId}>
					{page.data.messages.map((message) => (
						<MessageContext.Provider value={message}>
							<Message />
						</MessageContext.Provider>
					))}
				</React.Fragment>
			))}

			{messages.map((message) => (
				<MessageContext.Provider value={message}>
					<Message />
				</MessageContext.Provider>
			))}
		</>
	);
}
