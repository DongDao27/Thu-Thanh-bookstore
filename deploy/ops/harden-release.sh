#!/usr/bin/env bash
set -euo pipefail

# Run as root after Composer/Artisan preparation, before switching current.
# PHP-FPM may write only generated cache/storage, never application source.
[[ $EUID -eq 0 ]] || { echo 'Run with sudo.' >&2; exit 1; }
release=$(realpath -e -- "${1:?Provide the prepared release directory}")
[[ $release =~ ^/srv/thu-thanh/releases/[A-Za-z0-9_-]{1,64}$ ]] || {
  echo 'Expected a release under /srv/thu-thanh/releases.' >&2; exit 1;
}
[[ -f $release/backend/public/index.php && -L $release/backend/storage && -L $release/backend/.env ]]
[[ $(readlink -f "$release/backend/storage") == /srv/thu-thanh/shared/backend/storage ]]
[[ $(readlink -f "$release/backend/.env") == /srv/thu-thanh/shared/backend/.env ]]

# Do not follow .env/storage symlinks into shared data or secret files.
chown -hR deploy:www-data "$release"
find "$release" -type d -exec chmod 2750 {} +
find "$release" -type f ! -perm /111 -exec chmod 0640 {} +
find "$release" -type f -perm /111 -exec chmod 0750 {} +
chmod 2770 "$release/backend/bootstrap/cache"
find "$release/backend/bootstrap/cache" -maxdepth 1 -type f -exec chmod 0660 {} +
if [[ -d $release/.git ]]; then
  find "$release/.git" -type d -exec chmod 0700 {} +
  find "$release/.git" -type f -exec chmod 0600 {} +
fi

runuser -u www-data -- test -r "$release/backend/public/index.php"
for path in "$release" "$release/backend/app" "$release/backend/public" "$release/backend/public/index.php" "$release/backend/.env"; do
  if runuser -u www-data -- test -w "$path"; then
    printf 'Unsafe PHP-FPM write access: %s\n' "$path" >&2; exit 1;
  fi
done
runuser -u www-data -- test -w "$release/backend/bootstrap/cache"
echo 'Release source read-only to PHP-FPM; generated cache remains writable. Shared data untouched.'
