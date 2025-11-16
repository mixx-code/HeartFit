import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Import yang diperlukan
import Echo from "laravel-echo";
import Pusher from "pusher-js"; // Import langsung, tidak usah dynamic

// Set Pusher ke window
window.Pusher = Pusher;

// Initialize Echo langsung
function initializeEcho() {
    const scheme = import.meta.env.VITE_REVERB_SCHEME || "http";
    const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;

    if (!reverbKey) {
        console.error("VITE_REVERB_APP_KEY is not defined in .env file");
        return false;
    }

    console.log("[bootstrap] Initializing Echo with Reverb...");

    try {
        window.Echo = new Echo({
            broadcaster: "reverb",
            key: reverbKey,
            wsHost:
                import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
            wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
            wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
            forceTLS: scheme === "https",
            enabledTransports: ["ws", "wss"],
        });

        console.log("[bootstrap] Echo initialized successfully");
        console.log("[echo] Echo instance:", window.Echo);

        return true;
    } catch (error) {
        console.error("[bootstrap] Failed to initialize Echo:", error);
        return false;
    }
}

// Initialize langsung tanpa promise kompleks
const echoInitialized = initializeEcho();
window.echoReady = Promise.resolve(echoInitialized);
