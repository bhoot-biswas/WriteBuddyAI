import { Flex } from "@wordpress/components";
import { css } from "@emotion/react";

export default function ChatBoxFooter(props) {
	return (
		<Flex
			css={css`
				padding: 1rem;
			`}
			{...props}
		/>
	);
}
