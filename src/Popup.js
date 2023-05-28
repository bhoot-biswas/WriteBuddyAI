import { useState, useEffect, useRef } from "@wordpress/element";
import { Button, Spinner } from "@wordpress/components";
import { useInView } from "react-intersection-observer";
import { Chat as ReactChat } from "@fluentui/react-northstar";
import { useMutation } from "@tanstack/react-query";
import { css } from "@emotion/react";
import { BsChatLeftDots, BsX } from "react-icons/bs";
import { saveMessage } from "./api";
import { ChatBoxContext } from "./context";
import useChatMessages from "./hooks/useChatMessages";
import ChatMessages from "./ChatMessages";
import ChatBox from "./ChatBox";
import SendBox from "./SendBox";
import ChatBoxHeader from "./ChatBoxHeader";
import ChatBoxBody from "./ChatBoxBody";
import ChatBoxFooter from "./ChatBoxFooter";
import WriteBuddyAI from "./WriteBuddyAI";

export default function Popup() {
	const [isOpen, setIsOpen] = useState(false);
	const [scrollPosition, setScrollPosition] = useState(0);
	const [hasScrolledUp, setHasScrolledUp] = useState(false);
	const [hasScrolledDown, setHasScrolledDown] = useState(false);
	const [messages, setMessages] = useState([]);
	const { ref, inView } = useInView();
	const scrollRef = useRef(null);
	const {
		fetchPreviousPage,
		hasPreviousPage,
		isFetching,
		isFetchingPreviousPage,
		status,
	} = useChatMessages();
	const { mutate: saveChatMessage } = useMutation({
		mutationFn: saveMessage,
		onSuccess: (response) => {
			const message = {
				id: Date.now(),
				sender_id: "-1",
				message: response.data.content,
				created_at: new Date(),
			};

			setMessages([...messages, message]);
		},
		onError: (error) => console.error(error),
	});

	const handleToggleClick = () => {
		setIsOpen(!isOpen);
	};

	const scrollToBottom = (behavior = "auto") => {
		const scrollOptions = {
			top: scrollRef.current.scrollHeight,
			behavior,
		};

		scrollRef.current.scrollTo(scrollOptions);
		setHasScrolledUp(false);
	};

	useEffect(() => {
		if (isFetching || hasScrolledDown) {
			return;
		}

		if (scrollRef.current) {
			scrollToBottom();
			setHasScrolledDown(true);
		}
	}, [isFetching]);

	useEffect(() => {
		if (isFetchingPreviousPage) {
			return;
		}

		if (scrollRef.current) {
			scrollRef.current.scrollTop =
				scrollRef.current.scrollHeight - scrollPosition;
		}
	}, [isFetchingPreviousPage]);

	useEffect(() => {
		const handleScroll = () => {
			if (
				scrollRef.current.scrollHeight >= 2000 &&
				scrollRef.current.scrollHeight - scrollRef.current.scrollTop >=
					1500
			) {
				setHasScrolledUp(true);
			} else {
				setHasScrolledUp(false);
			}
		};

		if (scrollRef.current) {
			scrollRef.current.addEventListener("scroll", handleScroll);
			scrollToBottom();
		}

		return () => {
			if (scrollRef.current) {
				scrollRef.current.removeEventListener("scroll", handleScroll);
			}
		};
	}, [isOpen]);

	useEffect(() => {
		if (inView) {
			setScrollPosition(scrollRef.current.scrollHeight);
			fetchPreviousPage();
		}
	}, [inView]);

	useEffect(() => {
		if (scrollRef.current) {
			scrollToBottom("smooth");
		}
	}, [messages]);

	return (
		<ChatBoxContext.Provider
			value={{
				messages,
				addMessage: (message) => {
					const newMessages = [...messages, message];
					setMessages(newMessages);
					saveChatMessage(newMessages);
				},
			}}
		>
			{isOpen && (
				<ChatBox>
					<ChatBoxHeader>
						<WriteBuddyAI />
					</ChatBoxHeader>

					<div
						css={css`
							position: relative;
							flex-grow: 1;
							height: 1%;
							background-color: #f0f0f0;
						`}
					>
						<ChatBoxBody ref={scrollRef}>
							{hasPreviousPage && (
								<div
									ref={ref}
									css={css`
										display: flex;
										justify-content: center;
									`}
								>
									<Spinner />
								</div>
							)}

							<ReactChat
								css={css`
									padding: 0;
									border: none;
									background-color: inherit;
								`}
								density="compact"
							>
								<ChatMessages />
							</ReactChat>
						</ChatBoxBody>

						{hasScrolledUp && (
							<div
								css={css`
									position: absolute;
									bottom: 8px;
									width: 100%;
									display: flex;
									justify-content: center;
								`}
							>
								<Button
									css={css`
										padding-left: 16px;
										padding-right: 16px;
										border-radius: 1000px;
										background-color: #000000;
										color: #ffffff;

										&:hover,
										&:focus:not(:disabled),
										&:not([aria-disabled="true"]):active,
										&.is-pressed {
											box-shadow: none;
											color: #ffffff;
										}
									`}
									onClick={() => scrollToBottom("smooth")}
								>
									{writebuddy.i18n.scroll_to_bottom}
								</Button>
							</div>
						)}
					</div>

					<ChatBoxFooter>
						<SendBox />
					</ChatBoxFooter>
				</ChatBox>
			)}

			<button
				type="button"
				className="chatbox__opener"
				onClick={handleToggleClick}
			>
				{isOpen ? <BsX /> : <BsChatLeftDots />}
			</button>
		</ChatBoxContext.Provider>
	);
}
