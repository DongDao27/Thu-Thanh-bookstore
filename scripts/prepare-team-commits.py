#!/usr/bin/env python3
"""Inventory/export reviewed public source. Never stages, commits, or pushes."""
import argparse
import csv
import json
import shutil
import subprocess
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
MANIFEST = ROOT / "docs/COMMIT-FILES.csv"
REMOTE = "https://github.com/DongDao27/Thu-Thanh-bookstore"
THIEN_DOCS = {"API.md", "DATABASE.md", "DOMAIN-RULES.md", "openapi.json", "SECURITY-PERFORMANCE.md", "DEPLOYMENT.md"}
THIEN_SCRIPTS = {"auth-cookie-smoke.php", "ops-smoke.php", "check-coverage.php", "monitor-host.py", "monitoring-requirements.txt", "postgres-test-local.py", "run-postgresql-check.py", "sandbox.ps1", "start-backend.ps1", "validate-deploy.php"}


def git(*args, cwd=ROOT):
    return subprocess.check_output(["git", *args], cwd=cwd).decode("utf-8").strip()


def safe(path):
    name = Path(path).name.lower()
    return not (
        any(p in {".runtime", "output", ".git", "node_modules", "vendor", ".next", "__pycache__"} for p in Path(path).parts)
        or (name.startswith(".env") and not name.endswith(".example"))
        or name in {"auth.json", "id_rsa", "id_ed25519"}
        or name.endswith((".log", ".sql", ".dump", ".pem", ".key", ".p12"))
        or (path.startswith("backend/storage/") and name != ".gitignore")
    )


def owner(path):
    if path.startswith(("backend/", "deploy/")) or path in {".gitignore", ".gitattributes", ".dockerignore", ".github/workflows/backend.yml"}:
        return "thien", "T1"
    if path.startswith("docs/") and Path(path).name in THIEN_DOCS:
        return "thien", "T1"
    if path.startswith("scripts/") and (Path(path).name in THIEN_SCRIPTS or path.startswith("scripts/load/")):
        return "thien", "T1"
    if path.startswith("frontend/") or path == ".github/workflows/frontend.yml":
        return "dong", "D1"
    if path.startswith("thu-thanh-figma-clicon/") or path == "scripts/test-figma-clicon.cjs":
        return "dong", "D2"
    return "dong", "D3"


def inventory():
    raw = subprocess.check_output(["git", "ls-files", "--cached", "--others", "--exclude-standard", "-z"], cwd=ROOT)
    rows = []
    for path in sorted({p.decode("utf-8") for p in raw.split(b"\0") if p}):
        source = ROOT / path
        if not source.is_file():
            continue  # Deleted TS/MySQL files do not exist in the new empty repository.
        if source.is_symlink() or not source.resolve().is_relative_to(ROOT.resolve()):
            raise ValueError(f"Refuse symlink/outside source: {path}")
        if not safe(path):
            raise ValueError(f"Private/generated candidate must be reviewed: {path}")
        if source.stat().st_size >= 100 * 1024 * 1024:
            raise ValueError(f"GitHub file size limit: {path}")
        who, group = owner(path)
        rows.append({"owner": who, "commit": group, "path": path})
    if not any(row["path"] == "docs/COMMIT-FILES.csv" for row in rows):
        rows.append({"owner": "dong", "commit": "D3", "path": "docs/COMMIT-FILES.csv"})
        rows.sort(key=lambda row: row["path"])
    return rows


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("action", choices=["plan", "verify", "export"])
    parser.add_argument("--owner", choices=["dong", "thien"])
    parser.add_argument("--target", type=Path)
    parser.add_argument("--apply", action="store_true", help="Copy only; default is dry run")
    args = parser.parse_args()
    rows = inventory()
    if args.action == "plan":
        with MANIFEST.open("w", encoding="utf-8-sig", newline="") as handle:
            writer = csv.DictWriter(handle, fieldnames=["owner", "commit", "path"])
            writer.writeheader()
            writer.writerows(rows)
    else:
        with MANIFEST.open(encoding="utf-8-sig", newline="") as handle:
            if list(csv.DictReader(handle)) != rows:
                raise ValueError("Manifest stale; review source and run plan again")
    if args.action == "export":
        if not args.owner or not args.target:
            parser.error("export requires --owner and --target")
        target = args.target.resolve()
        if target == ROOT.resolve() or target.is_relative_to(ROOT.resolve()) or ROOT.resolve().is_relative_to(target):
            raise ValueError("Use a separate sibling clone; never export over the working source")
        if Path(git("rev-parse", "--show-toplevel", cwd=target)).resolve() != target:
            raise ValueError("Target must be a Git clone root")
        urls = [git("remote", "get-url", "origin", cwd=target), git("remote", "get-url", "--push", "origin", cwd=target)]
        if any(url.rstrip("/").removesuffix(".git") != REMOTE for url in urls):
            raise ValueError("Target origin must be Thu-Thanh-bookstore, not Payment")
        branch = git("branch", "--show-current", cwd=target)
        if not branch.startswith(f"feat/{args.owner}-"):
            raise ValueError(f"Target must be on feat/{args.owner}-...; got {branch}")
        if git("status", "--porcelain", cwd=target):
            raise ValueError("Target has uncommitted files; review them before exporting")
        selected = [row for row in rows if row["owner"] == args.owner]
        for row in selected:  # Preflight all destinations before writing any file.
            dest = target / row["path"]
            if not dest.resolve().is_relative_to(target) or dest.is_symlink() or (dest.exists() and not dest.is_file()):
                raise ValueError(f"Unsafe destination: {row['path']}")
            if dest.is_file() and row["path"] != "README.md" and dest.read_bytes() != (ROOT / row["path"]).read_bytes():
                raise ValueError(f"Existing content differs; merge manually: {row['path']}")
        if args.apply:
            for row in selected:
                dest = target / row["path"]
                dest.parent.mkdir(parents=True, exist_ok=True)
                shutil.copyfile(ROOT / row["path"], dest)
        print(json.dumps({"action": "copied" if args.apply else "dry_run", "owner": args.owner, "files": len(selected), "target": str(target)}))
    print(json.dumps({"files": len(rows), "groups": {group: sum(r["commit"] == group for r in rows) for group in ["T1", "D1", "D2", "D3"]}}))


if __name__ == "__main__":
    main()
