# -*- coding: utf-8 -*-
# Kişiselleştirilmiş özellikleri (a/b/c) birleştirir; her projeye 6 kişisel + 2 genel (tekrarsız) = 8 özellik.
import json, os, sys, re
T = os.path.dirname(os.path.abspath(__file__)); sys.path.insert(0, T)
from features_personal_a import P as A
from features_personal_b import P as B
from features_personal_c import P as C
ROOT = os.path.dirname(T)
core = open(os.path.join(ROOT, 'includes/core.php'), encoding='utf-8').read()
s = core.index('PROJECT_FEATURE_ICONS'); ALLOWED = set(re.findall(r"'(fa-[a-z0-9-]+)'", core[s:s+6000]))
GEN = json.load(open('/home/ubuntu/features_plan_generic.json', encoding='utf-8'))
K = ['icon','title_tr','sub_tr','title_en','sub_en','title_ar','sub_ar']
P = {}; P.update(A); P.update(B); P.update(C)
out = {}
for pid in sorted(P):
    rows = [dict(zip(K, t)) for t in P[pid]]
    assert len(rows) == 6, pid
    icons = {r['icon'] for r in rows}; titles = {r['title_tr'].lower() for r in rows}
    for g in GEN.get(str(pid), []):
        if len(rows) >= 8: break
        if g['icon'] in icons or g['title_tr'].lower() in titles: continue
        rows.append({k: g[k] for k in K}); icons.add(g['icon'])
    for r in rows: assert r['icon'] in ALLOWED, (pid, r['icon'])
    out[str(pid)] = rows
assert len(out) == 44, len(out)
j = json.dumps(out, ensure_ascii=False, indent=1)
for p in ['/home/ubuntu/features_plan.json', '/home/ubuntu/deploy_tmp/diag/features_plan.json']:
    open(p, 'w', encoding='utf-8').write(j)
with open(os.path.join(ROOT, 'sql/018_project_features_personalized.sql'), 'w', encoding='utf-8') as f:
    f.write('-- Proje özellikleri: her projenin sloganına göre kişiselleştirilmiş (TR/EN/AR)\nSET NAMES utf8mb4;\n')
    for pid, rows in out.items():
        v = json.dumps(rows, ensure_ascii=False).replace('\\', '\\\\').replace("'", "\\'")
        f.write(f"UPDATE projects SET features = '{v}' WHERE id = {pid};\n")
print('ok', len(out), 'projects;', {k: len(v) for k, v in out.items()})
