import { loadHeader } from "./header.js";
import { loadCarousel } from "./carousel.js";
import { loadFooter } from "./footer.js";

document.addEventListener("DOMContentLoaded", () => {
    loadHeader();
    loadCarousel();
    loadFooter();
});