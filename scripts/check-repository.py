#!/usr/bin/env python3
"""Check documentation links, checkpoint structure and OpenAPI references."""
import json
from pathlib import Path
import re
from urllib.parse import unquote

ROOT = Path(__file__).resolve().parents[1]


def load(name):
    return json.loads((ROOT / name).read_text(encoding='utf-8-sig'))


def main():
    for name in ['README.md', 'AGENTS.md', 'START-HERE.md', 'backend/artisan',
                 'frontend/package.json', 'docs/API.md', 'docs/DATABASE.md',
                 'docs/DOMAIN-RULES.md', 'design/figma/manifest.json']:
        assert (ROOT / name).is_file(), name
    tasks = load('docs/TASKS.json')
    by_id = {task['id']: task for task in tasks['tasks']}
    assert len(by_id) == len(tasks['tasks'])
    for task in by_id.values():
        assert task['status'] in tasks['status_values']
        assert task['acceptance'] and task['required_checks']
        assert all(dep in by_id for dep in task['depends_on'])
        if task['status'] == 'done':
            assert task['evidence']
    state = load('docs/STATUS.json')
    assert state['repository'] == 'DongDao27/Thu-Thanh-bookstore'
    for field in ['current_task_id', 'next_task_id']:
        assert state.get(field) is None or state[field] in by_id
    spec = load('docs/openapi.json')
    def references(node):
        if isinstance(node, dict):
            if '$ref' in node:
                ref = node['$ref']
                assert ref.startswith('#/'), ref
                target = spec
                for part in ref[2:].split('/'):
                    target = target[part.replace('~1', '/').replace('~0', '~')]
            for value in node.values():
                references(value)
        elif isinstance(node, list):
            for value in node:
                references(value)
    references(spec)
    markdown = [ROOT/'README.md', ROOT/'START-HERE.md', ROOT/'AGENTS.md',
                ROOT/'frontend/AGENTS.md', ROOT/'design/figma/README.md']
    markdown += list((ROOT/'docs').glob('*.md'))
    links = 0
    for file in markdown:
        content = file.read_text()
        assert content.count('```') % 2 == 0, file
        for target in re.findall(r'!?\[[^\]]*\]\(([^)]+)\)', content):
            if target.startswith(('https:', 'http:', 'mailto:', 'tel:', '#')):
                continue
            path = unquote(target.split('#', 1)[0].strip('<>'))
            assert (file.parent/path).exists(), (file, path)
            links += 1
    print(f'PASS: {len(markdown)} documents, {links} local links, checkpoints and OpenAPI references.')


if __name__ == '__main__':
    main()
