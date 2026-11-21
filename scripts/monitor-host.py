"""Read real host CPU/RAM/disk/network counters; print no credentials or process arguments."""
import json
import sys
import time
from pathlib import Path

root = Path(__file__).resolve().parents[1]
sys.path.insert(0, str(root / '.runtime/monitor-tools'))
import psutil

before = psutil.net_io_counters()
started = time.monotonic()
cpu = psutil.cpu_percent(interval=1)
after = psutil.net_io_counters()
elapsed = time.monotonic() - started
print(json.dumps({
    'cpu_percent': cpu,
    'memory_percent': psutil.virtual_memory().percent,
    'disk_percent': psutil.disk_usage(str(root)).percent,
    'network_bytes_per_second': ((after.bytes_sent + after.bytes_recv) - (before.bytes_sent + before.bytes_recv)) / elapsed,
    'network_bytes_sent': after.bytes_sent,
    'network_bytes_received': after.bytes_recv,
    'sample_seconds': elapsed,
}))
