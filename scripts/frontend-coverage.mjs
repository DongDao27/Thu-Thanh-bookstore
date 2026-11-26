import fs from "node:fs";
import path from "node:path";
import { createRequire } from "node:module";
import { fileURLToPath } from "node:url";
const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");
const require = createRequire(path.join(root, "frontend/package.json"));
const { createInstrumenter } = require("istanbul-lib-instrument");
const {
  createCoverageMap,
  createFileCoverage,
} = require("istanbul-lib-coverage");
const { createContext } = require("istanbul-lib-report");
const reports = require("istanbul-reports");
const map = createCoverageMap({});
const unit = path.join(root, "output/coverage/unit/coverage-final.json");
if (!fs.existsSync(unit))
  throw new Error(
    "Run npm run test:coverage first; all src files must be in the denominator.",
  );
// Use original-source Istanbul maps for every file, including unvisited routes.
// Vitest's transformed JSX source maps have different end columns; merging those
// with browser maps would count the same statements twice. Keep unit coverage
// as its own report; the >70% gate below is the full-source browser measurement.
function walk(directory) {
  return fs.readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    const file = path.join(directory, entry.name);
    return entry.isDirectory()
      ? walk(file)
      : /\.(js|jsx)$/.test(file)
        ? [file]
        : [];
  });
}
for (const file of walk(path.join(root, "frontend/src"))) {
  const instrumenter = createInstrumenter({
    esModules: true,
    parserPlugins: ["jsx"],
  });
  instrumenter.instrumentSync(fs.readFileSync(file, "utf8"), file);
  map.addFileCoverage(instrumenter.lastFileCoverage());
}
const browser = path.join(root, "output/coverage/browser");
if (!fs.existsSync(browser))
  throw new Error("Run FRONTEND_COVERAGE=1 npm run test:e2e first.");
for (const file of fs
  .readdirSync(browser)
  .filter((name) => name.endsWith(".json"))) {
  for (const snapshot of JSON.parse(
    fs.readFileSync(path.join(browser, file)),
  )) {
    for (const [name, data] of Object.entries(snapshot)) {
      const relative = name.replaceAll("\\", "/").split("/src/")[1];
      if (!relative) continue;
      const target = path.join(root, "frontend/src", relative);
      if (!fs.existsSync(target))
        throw new Error(`Unknown covered source ${target}`);
      const expected = map.fileCoverageFor(target).toJSON();
      for (const field of ["statementMap", "fnMap", "branchMap"]) {
        if (JSON.stringify(expected[field]) !== JSON.stringify(data[field]))
          throw new Error(
            `Coverage map differs from current source: ${relative}. Archive old snapshots and rerun tests.`,
          );
      }
      map.merge({ [target]: createFileCoverage({ ...data, path: target }) });
    }
  }
}
const context = createContext({
  dir: path.join(root, "output/coverage/frontend"),
  coverageMap: map,
});
for (const name of ["text", "html", "json", "json-summary", "lcovonly"])
  reports.create(name).execute(context);
const summary = map.getCoverageSummary().toJSON();
console.log(
  JSON.stringify({ sourceFiles: map.files().length, ...summary }, null, 2),
);
for (const metric of ["lines", "statements"]) {
  if (summary[metric].pct <= 70) {
    console.error(
      `Frontend ${metric} coverage must be >70%; received ${summary[metric].pct}%`,
    );
    process.exitCode = 1;
  }
}
