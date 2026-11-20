# Thiện: đưa phần backend còn lại lên Pull Request

Cập nhật D114, 20/11/2026. Repo: [DongDao27/Thu-Thanh-bookstore](https://github.com/DongDao27/Thu-Thanh-bookstore). Chủ repo đã chấp thuận README nền theo cách tổ chức của Daily Vocabulary; chỉ README được đưa lên main lúc khởi tạo. Phần source Đông qua branch **`feat/dong-storefront-figma-docs`** và draft PR vào `main`; không merge source trong lần này. File này dành cho Thiện thực hiện phần còn lại bằng tài khoản của Thiện, sau đó review chéo.

**PR Đông đã mở: [#22](https://github.com/DongDao27/Thu-Thanh-bookstore/pull/22)**, đang draft. Frontend Actions trên source commit đạt; browser/E2E báo thiếu thư mục backend vì388file của Thiện chưa được đưa lên. Không merge PR Đông trước khi hoàn tất các bước backend và review bên dưới.

## 1. Thiện commit những file nào?

[COMMIT-FILES.csv](COMMIT-FILES.csv) là danh sách từng file. Lọc `owner=thien`, `commit=T1`: **388 file**. Đông giữ300 file (`D1=258`, `D2=12`, `D3=30`), trong đó README đã thuộc commit nền nên PR có299file, D3 thêm29file. Không commit backend thay Thiện. Tỷ lệ60/40 là trách nhiệm dự kiến, không phải số commit hoặc xác nhận tác giả toàn bộ code cũ.

| Phạm vi Thiện | Nội dung |
| --- | --- |
| `backend/` | Laravel API, auth/CSRF/quyền, sản phẩm, giỏ/đơn/tồn, VNPAY, Resend/Pusher, migrations/seeds/assets public và tests |
| `deploy/` | Mẫu VPS/Nginx/worker/scheduler/Cloudflare và env example |
| Root/CI | `.gitignore`, `.gitattributes`, `.dockerignore`, `.github/workflows/backend.yml` |
| `docs/` | `API.md`, `openapi.json`, `DATABASE.md`, `DOMAIN-RULES.md`, `SECURITY-PERFORMANCE.md`, `DEPLOYMENT.md` |
| `scripts/` | `auth-cookie-smoke.php`, `ops-smoke.php`, `check-coverage.php`, `monitor-host.py`, `monitoring-requirements.txt`, `postgres-test-local.py`, `run-postgresql-check.py`, `sandbox.ps1`, `start-backend.ps1`, `validate-deploy.php`, `load/public-storefront.k6.js` |

Không commit env thật, APP_KEY/password/keys, `.runtime/`, `output/`, DB/dump/backup, private uploads, logs, vendor/node_modules hoặc source Đông. Chỉ env example không secret. Các file TS/MySQL đã xóa không cần đưa lại vào repo mới. Git không mang database thật; data/media chuyển qua quy trình backup riêng, không đính dump vào PR.

## 2. Chuẩn bị và đăng nhập đúng tài khoản

Đông mời username GitHub thật của Thiện làm collaborator có quyền write, Thiện chấp nhận lời mời. Cài Git, GitHub CLI; kiểm backend cần PHP8.4/Composer/PostgreSQL17. Chạy PowerShell, không dán tiền tố `PS ...>` hoặc `>>`.

Source của Thiện ở `D:\ThuThanh-WebDesign` trên máy hiện tại. Nếu máy khác, nhận **`THIEN-BACKEND-SOURCE.zip`** từ Đông, giải nén ra thư mục riêng và sửa `$source` trong lệnh dưới. ZIP chỉ gồm388 file được giao + CSV + guide này, không có env/data/private uploads. ZIP và máy chứa source không thay tài khoản GitHub của Thiện.

```powershell
git --version
gh --version
gh auth login --hostname github.com --git-protocol https --web
gh auth setup-git
gh api user --jq .login
# Phai hien username cua Thien, khong phai DongDao27.
```

Đông phải hoàn tất tạo README nền main theo quyết định của chủ repo trước; source ứng dụng chỉ qua PR. Kiểm repo đã có main rồi mới clone. Không dùng workspace gốc có origin Payment để push.

## 3. Clone riêng và chép đúng 388 file

Thư mục `$work` phải chưa tồn tại. Không reset/clean hoặc ghi đè clone đang có việc. Không thay `main` bằng nhánh của Đông: PR backend cần độc lập để merge trước.

```powershell
$repo = 'DongDao27/Thu-Thanh-bookstore'
$source = 'D:\ThuThanh-WebDesign'
$work = 'D:\ThuThanh-review-thien'
if (Test-Path -LiteralPath $work) { throw 'Thu muc da ton tai; chon thu muc clone moi.' }
gh api "repos/$repo/git/ref/heads/main" --jq .object.sha
if ($LASTEXITCODE -ne 0) { throw 'Chua co main; lien he Dong, khong push truc tiep main.' }
git clone "https://github.com/$repo.git" "$work"
if ($LASTEXITCODE -ne 0) { throw 'Clone failed' }
Set-Location $work
git config user.name (Read-Host 'Ten that cua Thien')
git config user.email (Read-Host 'Email GitHub da xac minh hoac noreply cua Thien')
git switch -c feat/thien-backend-postgresql origin/main
if ($LASTEXITCODE -ne 0) { throw 'Khong tao duoc feature branch' }
$files = @(Import-Csv "$source\docs\COMMIT-FILES.csv" | Where-Object { $_.owner -eq 'thien' -and $_.commit -eq 'T1' } | Select-Object -ExpandProperty path)
if ($files.Count -ne 388) { throw 'Danh sach da thay doi; doi chieu lai voi Dong truoc khi copy.' }
foreach ($file in $files) {
    $src = Join-Path $source $file
    $dst = Join-Path $work $file
    if (-not (Test-Path -LiteralPath $src -PathType Leaf)) { throw "Thieu source: $file" }
    if (Test-Path -LiteralPath $dst) { throw "File da ton tai; review/merge thu cong: $file" }
}
foreach ($file in $files) {
    $src = Join-Path $source $file
    $dst = Join-Path $work $file
    New-Item -ItemType Directory -Force -Path (Split-Path -Parent $dst) | Out-Null
    Copy-Item -LiteralPath $src -Destination $dst
    if (-not $?) { throw "Copy failed: $file" }
}
git status --short
```

Các bước copy/stage kiểm từng file, không `git add .`. Nếu main đã có source mới hoặc copy lỗi, dừng và review; không xóa/reset cho hết lỗi. Không copy `.env` từ máy Đông vào clone để commit. Env chạy local tự tạo theo CHAY-WEB-VA-LARAVEL trong PR Đông; dùng DB test riêng.

## 4. Review, commit và mở PR của Thiện

Tiếp tục cùng PowerShell ở `$work`, giữ biến `$files/$repo`. Đọc source/backend tests trước khi nhận trách nhiệm. Điền kết quả kiểm thực, không chép số tests local cũ thành kết quả CI mới.

```powershell
foreach ($file in $files) {
    git --literal-pathspecs add -- "$file"
    if ($LASTEXITCODE -ne 0) { throw "Stage failed: $file" }
}
git diff --cached --stat
git diff --cached --check
if ($LASTEXITCODE -ne 0) { throw 'Sua whitespace truoc commit' }
git diff --cached --name-only
# Doc diff trong editor/Files changed; khong de secrets hoac file ngoai T1.
git diff --cached -- backend/app backend/config backend/routes backend/tests deploy
git commit -m 'feat(backend): import reviewed Laravel PostgreSQL API and operations'
if ($LASTEXITCODE -ne 0) { throw 'Commit failed' }
git remote -v
# Fetch/push origin phai la DongDao27/Thu-Thanh-bookstore.git.
git push -u origin feat/thien-backend-postgresql
if ($LASTEXITCODE -ne 0) { throw 'Push failed' }
$body = Join-Path $env:TEMP 'thu-thanh-pr-thien.md'
@'
Import reviewed Laravel API, PostgreSQL schema/tests, provider adapters and operations templates.
Scope: 388 T1 files from docs/COMMIT-FILES.csv in Dong storefront PR.
Validation: REPLACE with commands and actual results; Backend CI must pass.
Pending: production/staging, real mail/realtime and full VNPAY reconciliation acceptance.
No secrets, database dumps, deployment or automatic merge in this PR.
Reviewer: DongDao27. Merge this backend PR before finalizing Dong frontend PR.
'@ | Set-Content -Encoding UTF8 $body
notepad $body
# Sua Validation, luu/dong Notepad, roi:
gh pr create --repo $repo --base main --head feat/thien-backend-postgresql --title 'Backend: Laravel API, PostgreSQL and operations' --body-file $body --reviewer DongDao27
```

Thiện kiểm Backend workflow: Composer validate/platform/audit, Pint, migration/test/coverage>70%, OpenAPI/auth-cookie/ops smoke. CI dựng PostgreSQL test riêng; không chạy trên main DB, không chạy PHPUnit cùng E2E. Nếu chưa chạy local, ghi `not_run` và lý do thật. PR backend riêng chỉ có Backend workflow, không gọi là full project CI. Tài liệu liên kết/frontend nằm trong PR Đông và đầy đủ sau khi hai phần cùng vào main.

Nếu cần sửa, tiếp tục branch này → stage đúng file → commit → `git push`; PR tự cập nhật. Không `--force`, không giả `--author`, không tự approve/merge PR của mình.

## 5. Review chéo và hoàn tất PR Đông

1. Đông đọc diff/tests và Backend CI của Thiện, yêu cầu sửa nếu cần; chỉ sau approve mới merge PR backend. Lần xuất bản phần Đông chưa tự làm bước merge này.
2. Đông cập nhật `origin/main` vào branch `feat/dong-storefront-figma-docs`, giữ388 file do Thiện commit, chạy lại CI. PR Đông chuyển khỏi draft khi đủ backend, Frontend/Backend/E2E xanh; không coi E2E thiếu backend hoặc check skipped là passed.
3. Thiện review PR Đông bằng tài khoản mình. Main cần rule yêu cầu PR/approval/checks, resolve conversations và chặn force push. Dùng merge commit để giữ commit từng người, không bypass admin.

Lệnh cho **Thiện** đọc và approve PR Đông sau khi Đông báo đã đủ source/CI; nhập URL thật:

```powershell
$repo = 'DongDao27/Thu-Thanh-bookstore'
$pr = Read-Host 'URL PR storefront cua Dong'
gh api user --jq .login
gh pr view $pr --repo $repo --json author,baseRefName,headRefName,isDraft
$reviewedSha = gh pr view $pr --repo $repo --json headRefOid --jq .headRefOid
if ($LASTEXITCODE -ne 0 -or -not $reviewedSha) { throw 'Khong doc duoc head SHA' }
gh pr diff $pr --repo $repo
gh pr checks $pr --repo $repo --watch
if ($LASTEXITCODE -ne 0) { throw 'CI chua xanh; chua approve' }
$currentSha = gh pr view $pr --repo $repo --json headRefOid --jq .headRefOid
if ($LASTEXITCODE -ne 0 -or $currentSha -ne $reviewedSha) { throw 'Co commit moi; review lai.' }
# Chi approve khi da review code va du Backend/Frontend/E2E, PR khong con draft:
gh pr review $pr --repo $repo --approve --body 'Reviewed storefront, Figma and guides against the merged backend and actual CI evidence.'
```

Nếu chưa đạt: `gh pr review $pr --repo $repo --request-changes --body 'Ghi ro file, loi va dieu kien can sua'`. Có commit mới phải review lại. Chỉ sau review hợp lệ/CI xanh/conversation resolved mới merge qua GitHub hoặc `gh pr merge $pr --repo $repo --merge --match-head-commit $reviewedSha`; không dùng `--admin`.

Sau merge, trên clone sạch: `git fetch origin`, `git switch main`, `git pull --ff-only origin main`. Không chạy trên workspace gốc đang dirty.

Source vào main không đồng nghĩa hoàn thành deploy/provider: backend gate vẫn externally_blocked, release not_started. VPS/Vercel/domain, Resend/Pusher thật, VNPAY QueryDr/reconcile, backup offsite/restore và owner import native Figma còn nghiệm thu riêng.

Nguồn: [tạo PR](https://cli.github.com/manual/gh_pr_create), [review](https://cli.github.com/manual/gh_pr_review), [merge](https://cli.github.com/manual/gh_pr_merge), [branch protection](https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-protected-branches/about-protected-branches).
