// https://docs.expo.dev/guides/using-eslint/
const { defineConfig } = require('eslint/config');
const expoConfig = require("eslint-config-expo/flat");

module.exports = defineConfig([
  expoConfig,
  {
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
    plugins: ["import", "no-relative-import-paths"],
    rules: {
      "import/no-unresolved": "error",
      "no-relative-import-paths/no-relative-import-paths": [
        "error",
        { allowSameFolder: true },
      ],
    },
  },
]);
