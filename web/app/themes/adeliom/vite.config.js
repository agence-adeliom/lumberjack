import { defineConfig } from 'vite';
import glob from "glob";

const path = require('path');
const theme = path.basename(__dirname);

let entries = {
    "tailwind": './assets/tailwind/index.ts'
};

glob.sync(`./assets/components/**/index.ts`).forEach((jsFile) => {
    let destFile = jsFile.replace("./assets/", "")
      .replace(path.extname(jsFile), "")
      .replace('index', "")
      .replace(/\/$/, '');
    Object.assign(entries, {[destFile]: jsFile})
})

export default defineConfig(({ command }) => {
    return {
        root: './',
        base: command === 'serve' ? '/' : '/vite/',
        publicDir: 'resources/static',
        build: {
            manifest: true,
            outDir: path.resolve(__dirname, ''),
            emptyOutDir: true,
            rollupOptions: {
                input: entries,
            }
        },
        resolve: {
            alias: {
                "@tailwind": path.resolve(__dirname, 'assets/tailwind'),
                "@components": path.resolve(__dirname, 'assets/components/')
            }
        },
        plugins: [
            {
                name: 'php',
                handleHotUpdate({file, server}) {
                    if (file.endsWith('.php')) {
                        server.ws.send({type: 'full-reload', path: '*'});
                    }
                },
            },
            {
                name: 'twig',
                handleHotUpdate({file, server}) {
                    if (file.endsWith('.twig')) {
                        server.ws.send({type: 'full-reload', path: '*'});
                    }
                },
            },
        ],
    }
});
