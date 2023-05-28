import { FlexBlock, FlexItem } from "@wordpress/components";
import { css } from "@emotion/react";
import { BsRobot } from "react-icons/bs";

export default function WriteBuddyAI() {
	return (
		<>
			<FlexItem>
				<div
					css={css`
						display: flex;
						align-items: center;
						justify-content: center;
						width: 48px;
						height: 48px;
						border-radius: 50%;
						background-color: #f7f7f7;

						svg {
							width: 28px;
							height: 28px;
						}
					`}
				>
					<BsRobot />
				</div>
			</FlexItem>
			<FlexBlock>
				<div
					className="name"
					css={css`
						font-size: 16px;
						font-weight: bold;
					`}
				>
					{writebuddy.i18n.ai}
				</div>
				<div className="seen">{writebuddy.i18n.chat_now}</div>
			</FlexBlock>
		</>
	);
}
