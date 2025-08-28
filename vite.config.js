import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import { viteStaticCopy } from "vite-plugin-static-copy";

export default defineConfig({
    // server: {
    //     host: "192.168.1.28",
    //     port: 5173,
    //     hmr: {
    //         host: "192.168.1.28",
    //     },
    // },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
        viteStaticCopy({
            targets: [
                {
                    src: "resources/fonts/*",
                    dest: "fonts", // copied to public/build/fonts/
                },
            ],
        }),
    ],
});
