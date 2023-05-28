import { forwardRef } from "@wordpress/element";
import { __experimentalScrollable as Scrollable } from "@wordpress/components";

const ChatBoxBody = forwardRef(function ChatBoxBody(props, ref) {
	return <Scrollable style={{ height: "100%" }} {...props} ref={ref} />;
});

export default ChatBoxBody;
