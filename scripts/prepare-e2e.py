"""Copy JavaScript frontend for E2E without touching the owner's source/cache."""
from pathlib import Path
import shutil

root = Path(__file__).resolve().parents[1]
source = root / 'frontend'
target = root / '.runtime/e2e-frontend'
target.mkdir(parents=True, exist_ok=True)
for name in ['src', 'public', 'e2e', 'tests']:
    shutil.copytree(source / name, target / name, dirs_exist_ok=True)
# A previous isolated copy may still contain the old TypeScript pages. Remove
# only obsolete mirrors, never files from the owner's frontend directory.
for pattern in ('*.ts', '*.tsx'):
    mirrors = [file for directory in ('src', 'e2e', 'tests')
               for file in (target / directory).rglob(pattern)]
    mirrors.extend(target.glob(pattern))
    for stale in mirrors:
        relative = stale.relative_to(target)
        if any(part in ('node_modules', '.next') for part in relative.parts):
            continue
        if stale.is_file() and not (source / relative).exists():
            stale.unlink()
for name in ['tsconfig.json']:
    stale = target / name
    if stale.exists():
        stale.unlink()
for name in ['package.json', 'package-lock.json', 'jsconfig.json', 'postcss.config.mjs', 'playwright.config.js']:
    shutil.copy2(source / name, target / name)
config = (source / 'next.config.js').read_text(encoding='utf-8')
config = config.replace('output: "standalone",', 'output: undefined,\n  turbopack: { root: '+repr(root.as_posix())+' },')
(target / 'next.config.js').write_text(config, encoding='utf-8')
print('Isolated E2E frontend prepared; owner build/cache unchanged.')
