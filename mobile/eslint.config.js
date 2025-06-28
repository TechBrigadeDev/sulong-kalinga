// https://docs.expo.dev/guides/using-eslint/
const { defineConfig } = require("eslint/config");
const expoConfig = require("eslint-config-expo/flat");
const unusedImports = require("eslint-plugin-unused-imports");
const simpleSort = require("eslint-plugin-simple-import-sort");
const eslintPluginPrettierRecommended = require("eslint-plugin-prettier/recommended");

module.exports = defineConfig([
    expoConfig,
    eslintPluginPrettierRecommended,
    {
        files: ["**/*.{ts,tsx}"],
        ignores: ["dist/*"],
        settings: {
            "import/resolver": {
                node: {
                    paths: ["."],
                    extensions: [".ts", ".tsx"],
                },
                "babel-module": {},
            },
        },
        plugins: {
            "no-relative-import-paths": require("eslint-plugin-no-relative-import-paths"),
            "unused-imports": unusedImports,
            "simple-import-sort": simpleSort,
        },
        rules: {
            "import/no-unresolved": "error",
            "no-relative-import-paths/no-relative-import-paths":
                [
                    "error",
                    { allowSameFolder: true },
                ],

            "no-unused-vars": "off", // or "@typescript-eslint/no-unused-vars": "off",
            "unused-imports/no-unused-imports":
                "error",
            "unused-imports/no-unused-vars": [
                "warn",
                {
                    vars: "all",
                    varsIgnorePattern: "^_",
                    args: "after-used",
                    argsIgnorePattern: "^_",
                },
            ],

            "simple-import-sort/imports": "error",
            "simple-import-sort/exports": "error",

            "import/namespace": [
                "error",
                {
                    allowComputed: true,
                },
            ],
        },
    },
]);
