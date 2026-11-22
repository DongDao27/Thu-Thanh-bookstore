#!/usr/bin/python3
"""Pull a verified private backup over existing SSH. Never execute archived data."""
import fcntl
import hashlib
import json
import os
from pathlib import Path
import re
import subprocess
from datetime import datetime, timezone

BASE = Path.home()/'.local/share/thu-thanh-offsite'
SSH = ['ssh', '-i', str(Path.home()/'.ssh/thu_thanh_vps'), '-o', 'BatchMode=yes', '-o', 'StrictHostKeyChecking=yes', '-o', 'ConnectTimeout=15', 'deploy@103.253.21.169']
FILES = {'database.dump', 'uploads.tar.gz', 'backend.env', 'manifest.json'}

def run(args, **kwargs):
    return subprocess.run(args, check=True, timeout=180, **kwargs)

def digest(path):
    h = hashlib.sha256()
    with path.open('rb') as stream:
        for block in iter(lambda: stream.read(1024*1024), b''):
            h.update(block)
    return h.hexdigest()

def verify(folder):
    lines = (folder/'SHA256SUMS').read_text().splitlines()
    checked = set()
    for line in lines:
        checksum, name = line.split(maxsplit=1)
        name = name.strip().removeprefix('*')
        if name not in FILES or not re.fullmatch('[a-f0-9]{64}', checksum) or name in checked:
            raise RuntimeError('Unexpected checksum manifest')
        if (folder/name).is_symlink() or digest(folder/name) != checksum:
            raise RuntimeError('Backup checksum mismatch')
        checked.add(name)
    if checked != FILES:
        raise RuntimeError('Incomplete checksum manifest')

def main():
    os.umask(0o077)
    BASE.mkdir(parents=True, mode=0o700, exist_ok=True)
    with (BASE/'pull.lock').open('w') as lock:
        fcntl.flock(lock, fcntl.LOCK_EX | fcntl.LOCK_NB)
        health = run(SSH+['systemctl show thu-thanh-backup.service -p Result --value'], capture_output=True, text=True).stdout.strip()
        if health != 'success':
            raise RuntimeError('Remote backup service reports failure')
        names = run(SSH+['find /srv/thu-thanh/backups -mindepth 1 -maxdepth 1 -type d -printf "%f\\n"'], capture_output=True, text=True).stdout.splitlines()
        names = sorted(n for n in names if re.fullmatch(r'staging-\d{8}T\d{6}Z-[a-f0-9]{8}', n))
        if not names:
            raise RuntimeError('No complete remote backup')
        name = names[-1]
        target = BASE/name
        if not target.exists():
            pending = BASE/(name+'.partial')
            pending.mkdir(mode=0o700, exist_ok=True)
            for filename in sorted(FILES | {'SHA256SUMS'}):
                local = pending/filename
                with local.open('wb') as stream:
                    run(SSH+['cat /srv/thu-thanh/backups/'+name+'/'+filename], stdout=stream, stderr=subprocess.DEVNULL)
                local.chmod(0o600)
            verify(pending)
            pending.rename(target)
        else:
            verify(target)
        stamp = datetime.strptime(name[8:24], '%Y%m%dT%H%M%SZ').replace(tzinfo=timezone.utc)
        age = (datetime.now(timezone.utc)-stamp).total_seconds()/3600
        result = {'status': 'verified', 'snapshot': name, 'age_hours': round(age, 1), 'checked_at': datetime.now(timezone.utc).isoformat(), 'retention': 'preserve all; warn at 14 snapshots'}
        if age > 36:
            raise RuntimeError('Latest remote backup older than 36 hours')
        (BASE/'last-success.json').write_text(json.dumps(result, indent=2)+'\n')
        print(json.dumps(result))
        if len(list(BASE.glob('staging-????????T??????Z-????????'))) > 14:
            subprocess.run(['notify-send', 'Thu Thanh backup', 'More than 14 private snapshots; review retention and disk space.'], check=False)

if __name__ == '__main__':
    try:
        main()
    except Exception as error:
        print('OFFSITE_BACKUP_FAILED: '+type(error).__name__, flush=True)
        subprocess.run(['notify-send', '-u', 'critical', 'Thu Thanh backup failed', 'Check systemctl --user status thu-thanh-offsite.service and network/VPS backup.'], check=False)
        raise SystemExit(1)
