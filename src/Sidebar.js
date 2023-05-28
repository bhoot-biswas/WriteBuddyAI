import { useState, useEffect, useRef } from "@wordpress/element";
import { Spinner } from "@wordpress/components";
import { useInView } from "react-intersection-observer";
import { Chat as ReactChat } from "@fluentui/react-northstar";
import { useMutation } from "@tanstack/react-query";
import { css } from "@emotion/react";
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

export default function Sidebar() {
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
		onSuccess: (data) => {
			const newMessage = {
				id: Date.now(),
				sender_id: "-1",
				message: data.data.data,
				created_at: new Date(),
			};

			setMessages([...messages, newMessage]);
		},
		onError: (error) => console.error(error),
	});

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
	}, []);

	useEffect(() => {
		if (inView) {
			if (scrollRef.current) {
				setScrollPosition(scrollRef.current.scrollHeight);
			}

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
					setMessages([...messages, message]);
					saveChatMessage(message);
				},
			}}
		>
			<div
				css={css`
					position: relative;
					height: 400px;
				`}
			>
				<ChatBoxBody ref={scrollRef}>
					{hasPreviousPage ? (
						<div ref={ref}>
							<Spinner />
						</div>
					) : (
						"Nothing more to load"
					)}

					<ReactChat density="compact">
						<ChatMessages />
					</ReactChat>
				</ChatBoxBody>

				{hasScrolledUp && (
					<button
						css={css`
							position: absolute;
							bottom: 0;
						`}
						onClick={() => scrollToBottom("smooth")}
					>
						{writebuddy.i18n.scroll_to_bottom}
					</button>
				)}
			</div>

			<SendBox />
		</ChatBoxContext.Provider>
	);
}
