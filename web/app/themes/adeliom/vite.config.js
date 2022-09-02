import { defineConfig } from 'vite';
import symfonyPlugin from "vite-plugin-symfony";
import glob from "glob";
import path from "path";

let entries = {
    "tailwind": './assets/tailwind/index.ts'
};

const getEntries = (entries) => {
    let globEntries = {};
    glob.sync(`./assets/components/**/index.ts`).forEach((jsFile) => {
        let destFile = jsFile.replace("./assets/", "")
            .replace(path.extname(jsFile), "")
            .replace('index', "")
            .replace(/\/$/, '');
        Object.assign(globEntries, {[destFile]: jsFile})
    })
    return Object.assign({}, entries, globEntries);
}

export default defineConfig(({ command, mode }) => {
    return {
        root: path.resolve(__dirname, ''),
        build: {
            outDir: 'build',
            manifest: true,
            rollupOptions: {
                input: getEntries(entries)
            }
        },
        resolve: {
            alias: {
                "@tailwind": path.resolve(__dirname, 'assets/tailwind'),
                "@components": path.resolve(__dirname, 'assets/components/')
            }
        },
        plugins: [
            symfonyPlugin({
                servePublic: false,
                buildDirectory: 'build',
                refresh: [
                    "**/*.php",
                    "views/**/*.twig"
                ]
            })
        ],
        server: {
            host: "0.0.0.0",
            watch: {
                usePolling: true
            }
        }
    }
});
