import { defineConfig } from "vitest/config";
import { fileURLToPath } from "node:url";
export default defineConfig({
  resolve: { alias: { "@": fileURLToPath(new URL("./src", import.meta.url)) } },
  test: {
    environment: "jsdom",
    include: ["tests/unit/**/*.test.{js,jsx}"],
    setupFiles: ["tests/unit/setup.js"],
    coverage: {
      provider: "istanbul",
      include: ["src/**/*.{js,jsx}"],
      reporter: ["text", "json", "json-summary", "html", "lcov"],
      reportsDirectory: "../output/coverage/unit",
    },
  },
});
