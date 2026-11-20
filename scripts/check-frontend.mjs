import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { createRequire } from 'node:module';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../frontend');
const require = createRequire(path.join(root, 'package.json'));
const { parse } = require('@babel/parser');
const walk = dir => fs.readdirSync(dir, { withFileTypes: true }).flatMap(entry =>
  entry.isDirectory() ? walk(path.join(dir, entry.name)) : [path.join(dir, entry.name)]);
const files = ['src', 'tests', 'e2e'].flatMap(dir => walk(path.join(root, dir)));
files.push(...fs.readdirSync(root).filter(name => /\.(?:js|jsx|mjs|ts|tsx)$/.test(name)).map(name => path.join(root, name)));
let checked = 0;
for (const file of files) {
  if (/\.tsx?$/.test(file)) throw new Error(`Unexpected TypeScript source: ${file}`);
  if (!/\.(?:js|jsx|mjs)$/.test(file)) continue;
  const ast = parse(fs.readFileSync(file, 'utf8'), { sourceType: 'module', plugins: ['jsx'] });
  for (const node of ast.program.body) {
    if (!['ImportDeclaration', 'ExportNamedDeclaration', 'ExportAllDeclaration'].includes(node.type) || !node.source) continue;
    const specifier = node.source.value;
    if (!specifier.startsWith('.') && !specifier.startsWith('@/')) continue;
    const target = specifier.startsWith('@/') ? path.join(root, 'src', specifier.slice(2)) : path.resolve(path.dirname(file), specifier);
    if (![target, ...['.js', '.jsx', '.json', '/index.js', '/index.jsx'].map(ext => target + ext)].some(name => fs.existsSync(name)))
      throw new Error(`Unresolved import ${specifier} in ${file}`);
  }
  checked++;
}
if (fs.existsSync(path.join(root, 'tsconfig.json'))) throw new Error('Use jsconfig.json for this JavaScript frontend');
console.log(`JavaScript syntax and local imports passed: ${checked} files. Build and tests verify runtime behavior.`);
