#!/usr/bin/env bash
set -euo pipefail
test "$(id -u)" = 0 || { echo 'Run sudo bash on VPS'; exit 1; }
test -s /home/deploy/.ssh/authorized_keys
/usr/sbin/sshd -t
target=/etc/ssh/sshd_config.d/00-00-thu-thanh.conf
backup=/root/thu-thanh-ssh-hardening
test ! -e "$backup" || { echo 'Previous hardening state exists; inspect before rerunning'; exit 1; }
install -d -m 700 "$backup"
if test -f "$target"; then cp -p "$target" "$backup/original.conf"; fi
cat > "$backup/rollback.sh" <<'ROLLBACK'
#!/usr/bin/env bash
set -euo pipefail
if test -f /root/thu-thanh-ssh-hardening/original.conf; then
  cp -p /root/thu-thanh-ssh-hardening/original.conf /etc/ssh/sshd_config.d/00-00-thu-thanh.conf
else
  rm -f /etc/ssh/sshd_config.d/00-00-thu-thanh.conf
fi
/usr/sbin/sshd -t
systemctl reload ssh
ROLLBACK
chmod 700 "$backup/rollback.sh"
systemd-run --unit=thu-thanh-ssh-rollback --on-active=5m /bin/bash "$backup/rollback.sh"
cat > "$target" <<'CONFIG'
# Root SSH disabled; deployment uses its existing authorized key and sudo.
PermitRootLogin no
PasswordAuthentication no
KbdInteractiveAuthentication no
PubkeyAuthentication yes
CONFIG
chmod 644 "$target"
if ! /usr/sbin/sshd -t; then bash "$backup/rollback.sh"; exit 1; fi
systemctl reload ssh
/usr/sbin/sshd -T | grep -E '^(permitrootlogin|passwordauthentication|kbdinteractiveauthentication|pubkeyauthentication) '
echo 'Keep this SSH session open. Automatic rollback in 5 minutes.'
echo 'After a SECOND key-only deploy login succeeds, cancel rollback:'
echo 'sudo systemctl stop thu-thanh-ssh-rollback.timer'
