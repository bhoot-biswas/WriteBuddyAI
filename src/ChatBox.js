import {
	__experimentalElevation as Elevation,
	__experimentalView as View,
} from "@wordpress/components";
import { css } from "@emotion/react";

export default function ChatBox({ children, ...otherProps }) {
	return (
		<View
			css={css`
				li {
					margin-bottom: 0;
				}
			`}
			{...otherProps}
			className="chatbox"
		>
			{children}
			<Elevation value={5} />
		</View>
	);
}
