// Used only by the isolated E2E webpack process; production stays on SWC/Turbopack.
const {
  createInstrumenter,
} = require("../frontend/node_modules/istanbul-lib-instrument");
module.exports = function (source, map) {
  // Next must statically read font calls and route configuration in server modules.
  if (
    this.resourcePath.replaceAll("\\", "/").includes("/src/app/") &&
    !source.includes('"use client"')
  ) {
    this.callback(null, source, map);
    return;
  }
  const instrumenter = createInstrumenter({
    esModules: true,
    produceSourceMap: true,
    parserPlugins: ["jsx"],
  });
  const code = instrumenter.instrumentSync(
    source,
    this.resourcePath,
    map || undefined,
  );
  this.callback(null, code, instrumenter.lastSourceMap());
};
