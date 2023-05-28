import { useDispatch } from "@wordpress/data";
import { store as blockEditorStore } from "@wordpress/block-editor";
import { createBlock } from "@wordpress/blocks";
import { Avatar, Chat } from "@fluentui/react-northstar";
import {
	AddIcon,
	ClipboardCopiedToIcon,
} from "@fluentui/react-icons-northstar";
import { BsRobot } from "react-icons/bs";
import useMessage from "./hooks/useMessage";

export default function Message() {
	const { sender_id, message } = useMessage();
	const { insertBlock } = useDispatch(blockEditorStore);
	const isSystem = sender_id === "-1";

	const handleInsertBlock = () => {
		const block = createBlock("core/paragraph", {
			content: message,
		});
		insertBlock(block);
	};

	const copyToClipboard = () => {
		navigator.clipboard.writeText(message);
	};

	const menu = {
		iconOnly: true,
		items: [
			{
				key: "add",
				icon: <AddIcon />,
				title: "Add",
				onClick: handleInsertBlock,
			},
			{
				key: "copy",
				icon: <ClipboardCopiedToIcon />,
				title: "Copy",
				onClick: copyToClipboard,
			},
		],
	};

	return (
		<Chat.Item
			gutter={
				isSystem ? (
					<Avatar {...{ size: "smallest", icon: <BsRobot /> }} />
				) : (
					<Avatar
						{...{
							size: "smallest",
							image: writebuddy.current_user.avatar_url,
						}}
					/>
				)
			}
			message={
				<Chat.Message
					actionMenu={menu}
					content={{
						content: (
							<div
								dangerouslySetInnerHTML={{
									__html: message,
								}}
							/>
						),
					}}
					author={
						isSystem
							? "WriteBuddy AI"
							: writebuddy.current_user.display_name
					}
					timestamp="10:15 PM"
					mine={!isSystem}
				/>
			}
		/>
	);
}
