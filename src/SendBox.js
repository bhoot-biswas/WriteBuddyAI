import { useState } from "@wordpress/element";
import { Button, FlexBlock } from "@wordpress/components";
import { css } from "@emotion/react";
import { BsSend } from "react-icons/bs";
import useChat from "./hooks/useChat";

export default function SendBox() {
	const [message, setMessage] = useState("");
	const { addMessage } = useChat();

	const handleChange = (event) => {
		setMessage(event.target.value);
	};

	const handleSubmit = (event) => {
		event.preventDefault();
		if (message.trim() === "") {
			return;
		}

		addMessage({
			id: Date.now(),
			sender_id: "1",
			message: message,
			created_at: new Date(),
		});

		setMessage("");
	};

	return (
		<>
			<FlexBlock
				css={css`
					position: relative;

					input[type="text"] {
						width: 100%;
						padding-left: 16px;
						padding-right: 40px;
						min-height: 36px;
						border-radius: 100px;
						border: none;
						background-color: #f0f0f0;
					}
				`}
			>
				<form onSubmit={handleSubmit}>
					<input
						type="text"
						placeholder={writebuddy.i18n.write_a_reply}
						value={message}
						onChange={handleChange}
					/>

					<Button
						type="submit"
						css={css`
							position: absolute;
							top: 0;
							right: 0;
							bottom: 0;
							z-index: 1;
						`}
					>
						<BsSend />
					</Button>
				</form>
			</FlexBlock>
		</>
	);
}
