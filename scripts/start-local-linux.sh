#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$root"
if [[ -s "$HOME/.nvm/nvm.sh" ]]; then
  source "$HOME/.nvm/nvm.sh"
  nvm use 24 >/dev/null
fi
export PATH="$HOME/.local/bin:$PATH"
data="$root/.runtime/linux-setup/pgdata"
case "${1:-}" in
  database)
    [[ -f "$data/PG_VERSION" ]] || { echo 'Linux database has not been restored.'; exit 1; }
    if ! /usr/lib/postgresql/17/bin/pg_ctl status -D "$data" >/dev/null; then
      /usr/lib/postgresql/17/bin/pg_ctl start -D "$data" -l "$root/.runtime/linux-setup/postgres.log" -w
    fi
    ;;
  backend)
    cd backend
    exec php artisan serve --host=localhost --port=8000
    ;;
  frontend)
    cd frontend
    exec npm run dev
    ;;
  *) echo "Usage: bash scripts/start-local-linux.sh database|backend|frontend"; exit 1 ;;
esac
