import { defineConfig, includeIgnoreFile } from "eslint/config";
import { fileURLToPath } from "node:url";
import tseslint from "typescript-eslint";

const gitignorePath = fileURLToPath(new URL(".gitignore", import.meta.url));

export default defineConfig([
    includeIgnoreFile(gitignorePath, {
        gitignoreResolution: true,
    }),

    {
        files: [
            "resources/js/**/*.js",
            "resources/js/**/*.jsx",
            "resources/js/**/*.ts",
            "resources/js/**/*.tsx",
        ],
    },

    ...tseslint.configs.recommended,
]);
