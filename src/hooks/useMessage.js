import { useContext } from "@wordpress/element";
import { MessageContext } from "./../context";

export default function useMessage() {
	return useContext(MessageContext);
}
