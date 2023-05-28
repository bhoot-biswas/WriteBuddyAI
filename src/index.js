import { createRoot, render } from "@wordpress/element";
import App from "./App";
import "./scss/index.scss";

const domElement = document.createElement("div");
document.body.appendChild(domElement);

if (createRoot) {
	createRoot(domElement).render(<App />);
} else {
	render(<App />, domElement);
}