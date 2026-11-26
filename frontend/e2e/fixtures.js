import { test as base, expect } from "@playwright/test";
import { mkdirSync, writeFileSync } from "node:fs";
import { resolve } from "node:path";
import { randomUUID } from "node:crypto";
export { expect };
const sessions = new WeakMap();
export async function trackCoverage(page) {
  if (process.env.FRONTEND_COVERAGE !== "1" || sessions.has(page)) return;
  const snapshots = [];
  sessions.set(page, snapshots);
  page.on("console", (message) => {
    if (
      message.type() === "debug" &&
      message.text().startsWith("__coverage__:")
    )
      snapshots.push(JSON.parse(message.text().slice("__coverage__:".length)));
  });
  await page.addInitScript(() => {
    window.addEventListener("beforeunload", () => {
      if (window.__coverage__)
        console.debug("__coverage__:" + JSON.stringify(window.__coverage__));
    });
  });
}
export async function saveCoverage(page) {
  const snapshots = sessions.get(page);
  if (!snapshots) return;
  if (!page.isClosed()) {
    const coverage = await page.evaluate(() => window.__coverage__);
    if (coverage) snapshots.push(coverage);
  }
  const directory = resolve("../output/coverage/browser");
  mkdirSync(directory, { recursive: true });
  writeFileSync(
    resolve(directory, `${randomUUID()}.json`),
    JSON.stringify(snapshots),
  );
  sessions.delete(page);
}
export const test = base.extend({
  page: async ({ page }, use) => {
    await trackCoverage(page);
    await use(page);
    await saveCoverage(page);
  },
});
