"""Windows: provision an isolated PostgreSQL test cluster; never touch app env/data.

Extract official PostgreSQL17 binaries into .runtime/postgresql17/pgsql first.
Run this once, then scripts/run-postgresql-check.py <command> [args...].
"""
from pathlib import Path
import os, secrets, subprocess, json

root = Path(__file__).resolve().parents[1]
runtime = root / '.runtime'
bin_dir = runtime / 'postgresql17/pgsql/bin'
data = runtime / 'd105-postgres-test-data'
secret = runtime / 'd105-postgres-test-password'
scan = runtime / 'd105-php-scan'
scan.mkdir(exist_ok=True)
modules = subprocess.check_output(['php', '-m'], text=True)
(scan/'pgsql.ini').write_text('' if 'pdo_pgsql' in modules.splitlines() else 'extension=pdo_pgsql\n', encoding='utf-8')
if not secret.exists():
    secret.write_text(secrets.token_hex(24),encoding='utf-8')
password=secret.read_text(encoding='utf-8')
env={**os.environ, 'PGPASSWORD':password}
def run(name,*args):
    return subprocess.run([str(bin_dir/(name+'.exe')),*args],env=env,check=True,creationflags=subprocess.CREATE_NO_WINDOW)
if not (data/'PG_VERSION').exists():
    run('initdb','-D',str(data),'-U','postgres','--pwfile='+str(secret),'--auth=scram-sha-256','--encoding=UTF8','--locale=C')
    with (data/'postgresql.conf').open('a') as f:
        f.write("\nlisten_addresses = '127.0.0.1'\nport = 55432\ntimezone = 'UTC'\n")
status=subprocess.run([str(bin_dir/'pg_ctl.exe'),'status','-D',str(data)],capture_output=True,creationflags=subprocess.CREATE_NO_WINDOW)
if status.returncode:
    run('pg_ctl','start','-D',str(data),'-l',str(runtime/'d105-postgres.log'),'-w')
# Bootstrap only this private, dedicated cluster.
check=subprocess.run([str(bin_dir/'psql.exe'),'-h','127.0.0.1','-p','55432','-U','postgres','-d','postgres','-Atc',"SELECT 1 FROM pg_roles WHERE rolname='thu_thanh_test'"],env=env,capture_output=True,check=True,creationflags=subprocess.CREATE_NO_WINDOW)
if check.stdout.strip()!=b'1':
    sql=runtime/'d105-test-role.sql'
    sql.write_text("CREATE ROLE thu_thanh_test LOGIN PASSWORD '"+password+"';\nCREATE DATABASE thu_thanh_test OWNER thu_thanh_test;\n",encoding='utf-8')
    run('psql','-h','127.0.0.1','-p','55432','-U','postgres','-d','postgres','-f',str(sql))
settings={'PHP_INI_SCAN_DIR':os.pathsep+str(scan),'APP_ENV':'testing','DB_CONNECTION':'pgsql','DB_PORT':'55432','DB_HOST':'127.0.0.1','DB_DATABASE':'thu_thanh_test','DB_USERNAME':'thu_thanh_test','DB_PASSWORD':password,'DB_URL':'','BROADCAST_CONNECTION':'null','MAIL_MAILER':'array'}
(runtime/'d105-test-environment.json').write_text(json.dumps(settings),encoding='utf-8')
print('Private PostgreSQL test runtime ready on 127.0.0.1:55432; main env/data untouched.')
