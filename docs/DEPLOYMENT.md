# Staging Linux — cập nhật 22/11/2026

D116 thay yêu cầu Pusher bắt buộc bằng polling hiện có cho release này. Dùng `BROADCAST_CONNECTION=null` trên VPS, không đặt NEXT_PUBLIC_PUSHER_APP_KEY/CLUSTER trên Vercel. Không cần tạo app Pusher. Nếu chủ động dùng Pusher sau này, credentials phải đủ và kiểm private-channel thực tế; preflight không chấp nhận cấu hình Pusher nửa vời. Các kiểm bắt buộc Resend/VNPAY/HTTPS/DB/queue vẫn giữ.

AHV ECO1 Ubuntu24.04: API staging api-staging.nhasachthuthanh.id.vn; database thu_thanh_staging; migrate role thu_thanh_migrate và runtime role thu_thanh_app. Screenshots chủ dự án xác nhận PHP8.4.28/PG17.12/Composer2.11.2, UFW22/80/443, DBloopback, TLS OriginCA và GlobalAOP:proxy200/direct400. Chưa có app/data deploy; không xem healthz TLS là kiểm Laravel/provider.

Local Linux đã có hướng dẫn riêng trong workspace gốc; dùng Node24 cho local/CI/Vercel, python3 cho Linux. Hai mục Pusher/bản Windows trong runbook lịch sử bên dưới chỉ áp dụng nếu chủ động chọn lại, không phải yêu cầu cho release này.

# Hoàn thiện và triển khai từng bước — D108 / 20/11/2026

Stack: Next.js/Vercel, Laravel 12/PHP 8.4/VPS, PostgreSQL 17, Cloudflare, Resend, Pusher. Đây là runbook chuẩn bị và nghiệm thu; chưa phải biên bản đã deploy. Các bước 1–2 chạy trên Windows; bước VPS chạy qua SSH trên Linux. Thay domain ví dụ bằng domain bạn sở hữu.

## 1. Xác nhận bản local và dữ liệu cần giữ

Làm theo [chạy local](CHAY-WEB-VA-LARAVEL.md), mở localhost:3000. PostgreSQL main `127.0.0.1:55432/thu_thanh`; test `thu_thanh_test`, Sandbox riêng. Cụm/backup MySQL đã xóa theo D107; không còn bước chuyển từ MySQL. Main D106 đã đối chiếu 26 bảng, 137 sản phẩm, 219 ảnh, 8 đơn và thử restore riêng; đây là số liệu tại thời điểm kiểm, không phải số cố định của website.

Giữ private trong `.runtime/d106-cutover/`:

- `postgresql-verified.dump`: custom dump PostgreSQL đã thử restore.
- `uploads-public.zip`: ảnh/uploads tương ứng.
- `artifact-integrity.json`, `main-verified.json`, `restore-verified.json`: hash/size và kết quả đối chiếu.

Giữ APP_KEY từ `backend/.env` qua kênh riêng; không chạy key:generate với dữ liệu hiện có. Sandbox dump riêng ở `.runtime/d107-sandbox/postgresql-sandbox.dump`, không restore đè main. Nếu dữ liệu đã thay đổi sau D106, cần xuất **dump và uploads mới nhất cùng thời điểm** trước deploy; không coi dump cũ là bản mới nhất. Đóng ghi hoặc đặt maintenance khi chụp để DB/ảnh nhất quán. Backup PostgreSQL vẫn cần, không xóa cùng các backup MySQL đã bỏ.

## 2. Chọn source để đưa lên Git

Repo đang có nhiều thay đổi chưa commit; review trước khi stage. Từ gốc repo:

```powershell
git status --short
git branch --show-current
git diff --check
python scripts/check_pack.py
python scripts/verify-store-assets.py
python scripts/run-postgresql-check.py php backend/artisan test
```

Trong frontend chạy `npm run check`, `npm run test:api`, `npm run build`; E2E chạy riêng sau PHPUnit theo local guide. Review danh sách `git diff --cached --name-only` trước commit. Không đưa `.runtime`, `.env`, credentials, dump, uploads private vào Git. Commit/push revision đã review lên repo của dự án; chạy GitHub Actions và lưu commit/test logs. Các file chưa commit trên Windows không tự có trên VPS/Vercel. Không tự nhận CI từ xa passed từ kết quả local.

## 3. Chuẩn bị tài khoản, domain và VPS

Bạn cần domain cho phép đổi nameserver; tài khoản Cloudflare, Vercel, Resend, Pusher Channels; VPS Linux có SSH key. Không bắt buộc mua domain tại Cloudflare. Tạo zone Cloudflare, đổi nameserver tại nơi mua theo cặp nameserver dashboard cung cấp. Chờ zone Active.

Chọn tên frontend ví dụ `books.example.com`, API `api.books.example.com`. Tạo môi trường staging riêng trước release; preview Vercel không dùng chung cookie/keys/DB production. Trên VPS kiểm `/etc/os-release`, `free -h`, `df -h`, `nproc`. Cách thêm kho/cài PHP phụ thuộc bản Linux; không dán lệnh apt của bản khác. Ubuntu dual boot trên máy cá nhân chỉ chạy khi máy đang mở Ubuntu, không thay VPS chạy liên tục.

## 4. Cài nền tảng trên VPS

Cần PHP 8.4 CLI/FPM + pdo_pgsql/mbstring/bcmath/curl/xml/zip/fileinfo, Composer 2, Nginx và PostgreSQL 17 bản vá hiện hành. Giữ Laravel major đang dùng. Không cần chạy Next.js/Node production trên VPS khi frontend đã ở Vercel. Kiểm:

```bash
php8.4 -v
php8.4 -m
composer --version
psql --version
nginx -v
```

Tạo user `deploy` dùng SSH key và storage chia sẻ; mẫu đang dùng `/srv/thu-thanh`, group `www-data`, FPM socket PHP 8.4. Điều chỉnh khi distro/path khác. Chỉ cấp quyền ghi cho `backend/storage` và `backend/bootstrap/cache`, không chmod 777 hoặc chạy app bằng root. DB chỉ loopback/private, không mở cổng 5432 ra Internet. Runtime role không superuser/CREATEDB/CREATEROLE; migration dùng quyền DDL riêng khi triển khai.

## 5. Restore PostgreSQL và uploads

Chuyển dump/uploads/manifest bằng SSH vào thư mục private ngoài webroot. Đối chiếu SHA256 với manifest trước restore. Tạo DB **trống** `thu_thanh` UTF8, UTC và role riêng; provision extensions `unaccent`, `pg_trgm` bằng tài khoản có quyền. Từ SSH, mẫu restore:

```bash
pg_restore --no-owner --no-acl --exit-on-error --dbname=thu_thanh /private/path/postgresql-verified.dump
```

Đường dẫn trên là placeholder; dùng kết nối/role đã chuẩn bị và `.pgpass` quyền 0600 hoặc cơ chế quản lý secrets, không ghi mật khẩu vào command history. Restore/migration role phải có quyền trên schema/tables/extensions/functions; runtime role được cấp đúng quyền đọc/ghi và sequences sau đó. Không dùng `--clean` hay restore đè DB có dữ liệu. Giải nén uploads vào `/srv/thu-thanh/shared/backend/storage/app/public`, giữ cấu trúc thư mục; kiểm đủ ảnh được tham chiếu, IDs, sequences, totals và tồn sau restore. Không dùng `migrate:fresh`/seed demo.

## 6. Cấu hình Resend, Pusher và env Laravel

Resend: Add Domain/subdomain bạn sở hữu, thêm chính xác các DNS SPF/DKIM dashboard cấp, chờ Verified; cấu hình DMARC theo chính sách domain. Bản ghi mail để DNS-only. Tạo sending API key, chọn `MAIL_FROM_ADDRESS` thuộc domain đã verify. Resend gửi email, không tự cung cấp hộp thư người dùng. [Tài liệu domain Resend](https://resend.com/docs/dashboard/domains/introduction).

Pusher: tạo **Channels app**, ghi app ID/key/secret/cluster đúng app. ID/secret chỉ VPS; frontend dùng public key/cluster. Private channel cần Laravel cho phép owner/admin qua session/CSRF; không mở public để né lỗi auth. [Pusher private authorization](https://pusher.com/docs/channels/server_api/authorizing-users/).

Tạo private `/srv/thu-thanh/shared/backend/.env` từ [mẫu backend](../deploy/.env.backend.example). Điền APP_KEY đã giữ, DB role/host/port/password, domain và provider keys. Những mục chính:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.books.example.com
FRONTEND_URL=https://books.example.com
DB_CONNECTION=pgsql
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SANCTUM_STATEFUL_DOMAINS=books.example.com
QUEUE_CONNECTION=database
CACHE_STORE=database
MAIL_MAILER=resend
BROADCAST_CONNECTION=pusher
```

Điền `RESEND_API_KEY`, sender, `PUSHER_APP_*`, cluster và VNPAY thật trong file private; không dán keys vào chat/Git hoặc NEXT_PUBLIC. VNPAY ở **Sandbox** cho nghiệm thu môn; không tự bật thanh toán production. Chỉ đặt `VNPAY_DEFERRED=false` khi đã có merchant và cấu hình Sandbox đúng. IPN/Return URL phải khớp đăng ký và domain. `app:deployment-check` yêu cầu payment cấu hình đầy đủ, không bỏ guard để vượt kiểm.

## 7. TLS, Cloudflare và Nginx API

Tạo bản ghi `api` A/AAAA về VPS, bật Proxied. Cài certificate server hợp lệ/Cloudflare Origin CA cho đúng hostname và bật **Full (strict)**. Thiết lập Authenticated Origin Pulls (AOP) ở cả Cloudflare và Nginx; certificate AOP và certificate server là hai mục khác nhau. Mẫu [vps.conf](../deploy/nginx/vps.conf) có placeholder domain/cert và yêu cầu mTLS, cần thay trước dùng. Global AOP xác thực Cloudflare network; muốn giới hạn zone/account cần cấu hình phù hợp. [Cloudflare AOP](https://developers.cloudflare.com/ssl/origin-configuration/authenticated-origin-pull/).

Firewall cho Cloudflare IPv4/IPv6 hiện hành vào API HTTPS; SSH chỉ IP quản trị/VPN, giữ phiên SSH và kiểm đường quản trị trước áp dụng. Không mở dev ports/DB/FPM ra Internet. Không để bản ghi DNS/mail khác lộ cùng origin. Proxy DNS cần kết hợp firewall/TLS/AOP để bảo vệ origin; không hứa IP không bao giờ bị tìm ra.

Nginx chỉ phục vụ `/srv/thu-thanh/current/backend/public`. Trong `http {}` khai báo log format theo mẫu, không ghi query chứa token/chữ ký:

```nginx
log_format thu_thanh_safe '$remote_addr $request_method $uri $status $body_bytes_sent $request_time';
```

Mẫu bỏ forwarded headers không kiểm chứng, chặn PHP trong uploads/dotfiles. Cài [PHP upload config](../deploy/php/production.ini) đúng FPM trước kiểm. Chạy `sudo nginx -t` rồi reload khi pass. Bypass cache/challenge cho API riêng tư, auth, `/sanctum/*`, admin/account/cart/checkout/orders/reset-password; không Cache Everything. IPN không bị interactive challenge, Laravel vẫn kiểm chữ ký/amount/reference/idempotency. Kiểm truy cập origin trực tiếp bị từ chối.

## 8. Publish Laravel và bật worker/scheduler

[Deployer recipe](../deploy/deploy.php) dùng Deployer **7**, mặc định branch `main`, host alias `staging`, đường dẫn `/srv/thu-thanh`. Revision đã review phải ở branch đó hoặc đổi branch trong cấu hình riêng trước chạy. Trên máy điều phối đã cài Deployer 7:

```bash
export DEPLOY_HOST=your-ssh-host
export DEPLOY_REPOSITORY=git@github.com:your-account/your-repository.git
dep -f deploy/deploy.php deploy staging
```

Thay hostname/repo thật; user deploy cần read-access repo, Composer và quyền sudo giới hạn reload PHP-FPM. Recipe cài vendors trong backend, link shared storage/env, cache config/routes/events, migrate additive, chạy preflight trước symlink và restart queue. Đọc lỗi trước retry; migration có thể đã chạy dù symlink chưa đổi. Rollback source không tự rollback dữ liệu. Staging cần DB/đường dẫn riêng nếu chạy cùng production.

Sau publish, từ `/srv/thu-thanh/current/backend`:

```bash
php8.4 artisan app:environment-check
php8.4 artisan app:deployment-check
php8.4 artisan queue:failed
php8.4 artisan schedule:list
```

Hai check đầu phải pass với env triển khai; local thiếu providers/HTTPS thì deployment-check fail là đúng. Chúng kiểm cấu hình, không thay nghiệm thu mạng/provider. Cài ba unit [worker](../deploy/php/thu-thanh-worker.service), [scheduler service](../deploy/php/thu-thanh-scheduler.service), [scheduler timer](../deploy/php/thu-thanh-scheduler.timer) vào `/etc/systemd/system`, kiểm user/path rồi:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now thu-thanh-worker.service thu-thanh-scheduler.timer
sudo systemctl status thu-thanh-worker.service thu-thanh-scheduler.timer
sudo journalctl -u thu-thanh-worker.service -n 50 --no-pager
```

Chỉ một scheduler: không thêm cron hoặc schedule:work song song timer. Worker timeout 60s < retry_after 90s; theo dõi failed_jobs/queue delay. Unit frontend cũ không bật. Docker compose là phương án API-only riêng, không trộn volume/path với bản VPS này; không `down -v` trên dữ liệu cần giữ.

## 9. Deploy frontend lên Vercel

Import Git repo, chọn Root Directory **frontend**, framework Next.js, Node **24.x**, build `npm run build`. Theo [env frontend](../frontend/.env.production.example):

```dotenv
LARAVEL_INTERNAL_URL=https://api.books.example.com
NEXT_PUBLIC_PUSHER_APP_KEY=public-key-from-your-app
NEXT_PUBLIC_PUSHER_APP_CLUSTER=ap1
```

API origin là server-only, HTTPS, không localhost; key/cluster phải trùng app Pusher. Secrets DB/Resend/Pusher secret/VNPAY chỉ trên VPS. Redeploy sau đổi biến build. Đặt biến theo môi trường Vercel; Preview dùng staging riêng.

Trong Settings → Domains, thêm domain frontend và tạo A/CNAME ở Cloudflare **theo dashboard Vercel hiện tại**, không chép IP tutorial. Chờ domain/certificate hợp lệ. Khuyến nghị DNS-only cho bản ghi Vercel; `api` vẫn Proxied. Nếu giảng viên yêu cầu proxy cả frontend, cấu hình theo [hướng dẫn reverse proxy Vercel](https://vercel.com/docs/security/reverse-proxy) và kiểm thêm certificate/challenge/cache/IP. [Thêm domain Vercel](https://vercel.com/docs/domains/working-with-domains/add-a-domain).

Browser gọi cùng origin frontend qua rewrites có sẵn; giữ host-only cookie (`SESSION_DOMAIN` trống), Secure/HttpOnly/Lax và CSRF. Không chuyển token sang localStorage. Khi Vercel gọi API, origin có thể nhận IP egress Vercel: kiểm rate-limit với hai client thật; không trust-all hoặc tin header IP tùy ý để sửa. Nếu IP bị gộp, phải thiết kế proxy-header có xác thực hoặc luồng API khác đã kiểm cookie/CORS trước release.

## 10. Nghiệm thu thật trước khi tuyên bố hoàn thiện

| Kiểm tra | Điều kiện đạt / bằng chứng |
| --- | --- |
| HTTPS/origin | Frontend/API certificate hợp lệ; origin trực tiếp bị chặn; private response không bị cache |
| Auth/quyền | Hai browser/user riêng; login/logout, CSRF sai bị chặn, khách không vào admin, user khác không xem đơn/private channel |
| Resend | Quên mật khẩu → nhận thư thật → URL HTTPS đúng → reset được; worker retry/error có log đã che secrets |
| Pusher | Browser 1 xem đơn, thao tác hợp lệ ở browser 2 → cập nhật qua event; kiểm reconnect, logout và polling fallback |
| VNPAY Sandbox | Thành công/hủy/lỗi/expiry/retry/replay; QueryDr/IPN xác nhận; tiền/tồn đúng, không ghi nhận lần hai; xử lý hai đơn lịch sử chưa rõ bằng đối soát |
| Dữ liệu | Đủ ảnh/counters/IDs/sequences; dump mới + uploads + APP_KEY lưu private ngoài VPS; restore DB riêng và đối chiếu |
| CI/chất lượng | Backend tests/coverage, frontend build/E2E và dependency audits trên commit deploy; lưu logs |
| Hiệu năng | EXPLAIN truy vấn nóng; k6 staging theo tải thống nhất, ghi p95/error-rate/CPU/RAM/connections/queue delay; điều chỉnh pool/cache theo số đo |
| Vận hành | Reboot thử có API/worker/timer tự lên; monitoring, cảnh báo, backup định kỳ và quy trình rollback |

[Security/performance](SECURITY-PERFORMANCE.md) ghi bằng chứng local và giới hạn; [course checklist](COURSE-CHECKLIST.md) đối chiếu rubric. Chưa có kết quả tải thì không nhận đã hỗ trợ 10k hay chắc 10 điểm. Không dùng fixture làm bằng chứng email/socket/payment thật. Chỉ cập nhật backend/release gate khi các điều kiện bắt buộc đạt.

## 11. Hoàn thiện Figma và bộ nộp

Giải nén `output/figma/THUTHANH-FIGMA-V40.zip`, import manifest rồi **Run** plugin, mở Present và đi bảy luồng mỗi platform theo [HUONG-DAN](../thu-thanh-figma-clicon/HUONG-DAN.md). Plugin offline đã kiểm tự động; canvas native còn cần bạn review. Prototype lấy mẫu 35 SKU, không phải toàn bộ 137 sản phẩm; mobile là thiết kế app theo yêu cầu đã chốt.

Lưu URL frontend/API, revision deploy, CI/test/coverage/load/restore logs, ảnh nghiệm thu, README/runbook và phân công để trình bày. Không nộp `.env`/keys/dumps chứa dữ liệu cá nhân công khai. Xong từng mục mới ghi done vào checklist; không lấy việc deploy thành công thay tất cả nghiệm thu.
