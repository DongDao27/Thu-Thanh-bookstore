"""Run a command with the private, isolated PostgreSQL test environment."""
from pathlib import Path
import json
import os
import subprocess
import sys

root = Path(__file__).resolve().parents[1]
settings = root / '.runtime/d105-test-environment.json'
if not settings.is_file():
    raise SystemExit('Prepare private test settings with postgres-test-local.py first (Windows); CI uses service env.')
environment = json.loads(settings.read_text(encoding='utf-8'))
if environment.get('APP_ENV') != 'testing' or environment.get('DB_CONNECTION') != 'pgsql' or environment.get('DB_DATABASE') != 'thu_thanh_test':
    raise SystemExit('Refusing any environment except isolated PostgreSQL thu_thanh_test.')
if len(sys.argv) < 2:
    raise SystemExit('Usage: python scripts/run-postgresql-check.py <command> [args...]')
command = sys.argv[1:]
working_directory = root
if len(command) >= 2 and Path(command[0]).stem.lower() == 'php' and command[1].replace('\\', '/') == 'backend/artisan':
    command[1] = 'artisan'
    working_directory = root / 'backend'
raise SystemExit(subprocess.call(command, cwd=working_directory, env={**os.environ, **environment}))
