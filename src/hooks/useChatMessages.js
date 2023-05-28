import { useInfiniteQuery } from "@tanstack/react-query";
import { fetchMessages } from "./../api";

export default function useChatMessages() {
	return useInfiniteQuery(["chatMessages"], fetchMessages, {
		getPreviousPageParam: (firstPage) => firstPage.data.previous_page,
	});
}
