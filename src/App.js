import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { Provider, teamsTheme } from "@fluentui/react-northstar";
import Popup from "./Popup";

// Create a client
const queryClient = new QueryClient({
	defaultOptions: {
		queries: {
			refetchOnMount: false,
			refetchOnWindowFocus: false, // default: true
		},
	},
});

export default function App() {
	return (
		<Provider theme={teamsTheme}>
			<QueryClientProvider client={queryClient}>
				<Popup />
			</QueryClientProvider>
		</Provider>
	);
}
