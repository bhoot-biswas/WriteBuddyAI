import { useContext } from "@wordpress/element";
import { ChatBoxContext } from "./../context";

export default function useChat() {
	return useContext(ChatBoxContext);
}
