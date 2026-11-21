> D116 (22/11/2026): chủ dự án bỏ yêu cầu Pusher cho release này; dùng polling hiện có. Các mục Pusher bên dưới giữ làm lịch sử/tùy chọn, không chặn release polling-only. Resend/payment/CI và các kiểm bắt buộc khác vẫn giữ.

> Hiện hành D112 (20/11/2026): PostgreSQL 17 cho main/test/Sandbox; MySQL và backup đã xóa theo D107. Stack Vercel/VPS/Cloudflare/Resend/Pusher, plugin V40. Hướng dẫn vận hành: [DEPLOYMENT](DEPLOYMENT.md). Ghi chép D cũ bên dưới giữ đúng thời điểm.

# Đối chiếu yêu cầu môn — D91, 12/11/2026

Cập nhật D98 (14/11/2026): frontend đã chuyển JavaScript/JSX; lệnh hiện hành `npm run check`, `npm run test:api`, `npm run build`. Đánh giá có điểm dự kiến theo yêu cầu mới ở [REVIEW-2026-11-14](COURSE-CHECKLIST.md); bảng D91 bên dưới là bằng chứng lịch sử. Repo chính Thu-Thanh-bookstore được kiểm read-only còn trống source, có Issues; cần publish/CI/review thật. Không lấy CI của Payment để cộng cho repo chính. Phân công mới [Đông60%/Thiện40%](PHAN-CONG-VA-COMMIT.md).

Căn cứ [Final Project Brief CSB35050](COURSE-BRIEF.txt), source và kết quả kiểm thực; không tự chấm điểm. D87 giữ Next.js theo xác nhận của chủ dự án, không chuyển Blade/Livewire. Khi nộp vẫn cần giữ xác nhận ngoại lệ từ giảng viên.

## Trạng thái sau bổ sung

| Tiêu chí | Source/bằng chứng local | Phần còn phải nghiệm thu |
| --- | --- | --- |
| GitHub/commit/Issues/branch/PR — 1đ | Có repo, lịch sử Git, workflows và hướng dẫn phân công | Thay đổi D91 chưa commit/push; cần PR/review/issues thật, không dựng lịch sử |
| DB/migrations/seed — 1đ | PostgreSQL17 (D105), factories/seed/migrations, schema và ERD trong [DATABASE](DATABASE.md); regression PostgreSQL175test/2164assertions | DB mới chỉ seed danh mục; catalog/admin cần chuyển dữ liệu hoặc cấp qua quy trình hiện có |
| UI/auth/forms/query — 1đ | Next/Tailwind, Laravel role/ownership/validation/CSRF; website giữ session | Ngoại lệ Next theo D87 cần bằng chứng xác nhận; không tuyên bố đã dùng Blade/Livewire |
| External API — 1đ | VNPAY adapter/callback/reconcile, failure/replay tests; D67 từng kiểm receipt Sandbox | IPN/query/expiry/reconcile merchant đầy đủ và gửi mail Resend thật chưa nghiệm thu |
| UI/business tests — 1đ | PHPUnit + browser E2E; giữ nguyên146test cũ và thêm18case mới | Kết quả hiện hành/giới hạn trong HANDOFF, không thay provider thật |
| REST CRUD — 0,5đ | Controllers/FormRequests/resources, versionv1, role và ownership | Demo trên môi trường nộp bài |
| Sanctum token — 0,25đ | PAT abilities theo role,1–30ngày, current-password/CSRF cấp token, revoke, chống bypass API session; test scope/expiry/ownership/HTTP | Không đưa bearer vào localStorage; token dùng cho external client |
| Scramble — 0,25đ | Package0.13.53, sinh docs từ source; viewer/JSON chỉ admin; security scheme cookie/CSRF/Bearer riêng | Generated artifact không thay manual business contract hoặc remote acceptance |
| API tests — 1đ | PHPUnit PostgreSQL, coverage91,35%(1754/1920statements), D106; gate CI fail khi <=70%, có kiểm boundary70/71 | Workflow mới chưa chạy trên GitHub vì chưa publish |
| Telescope — 0,25đ | Package5.28.0, bảng riêng, chỉ admin kể cả local; test ghi DB thật và redaction request/query/exception | Opt-in sau additive migrations của môi trường đích |
| Error handling/logging — 0,25đ | Exception mapping, request_id, sanitized operational alert; queue email sau commit nếu cấu hình | Resend/địa chỉ nhận thật chưa kiểm, không nói đã gửi |
| Slow-query alert — 0,25đ | DB listener/cooldown; SELECT SLEEP thực trên testDB kích hoạt alert, không lưu SQL/bindings | Chọn threshold theo máy chạy thật |
| Server monitoring — 0,25đ | psutil7.2.2 đo CPU/RAM/disk/network, ops:monitor, threshold/cooldown/cache snapshot/admin API | Windows local đã đo; cron/monitor trên Linux VPS chưa chạy |
| Async/resource loading/cache/antipattern — 2đ | Queue/afterCommit, storefront cache, ảnh/font local, Next build/splitting, bounded queries/locks/idempotency; worker smoke | Không coi local benchmark là tải production hoặc10k |

Bộ test full backend mới nhất:164test/2086assertions; Telescope final3test/23assertions; browser E2E cuối24/24(5,8phút), gồm docs admin-only qua Next proxy. Bằng chứng JUnit/coverage trong backend/storage/logs/phpunit-d91.xml và coverage-d91.xml; log E2E .runtime/d91-e2e-final.log. API client7/build22/typecheck, Pint, Composer audit và asset hashes đạt. Native Figma không kiểm lại trong D91 vì không đổi plugin/UI assets.

Brief không đặt mục tiêu10.000người đồng thời. Không thêm cluster/Redis/load10k để thay các tiêu chí môn thực tế. Không có tuyên bố đủ10đ hoặc đã nghiệm thu production.

## Bật diagnostics để demo local

D91 giữ nguyên database/env/uploads chính. Các bảng mới đã được migrate/test trên thu_thanh_test. Khi cần dùng trên dữ liệu cửa hàng, backup trước, review hai migration mới (Telescope và operational_alerts), rồi operator áp dụng additive migrations; không migrate:fresh, không seed lại catalog/admin. Bật flags **sau** khi bảng tồn tại.

Từ gốc repo, cài collector (máy Windows hiện tại đã có bản private trong .runtime/monitor-tools):

```bash
python -m pip install -r scripts/monitoring-requirements.txt
```

Trong env private của môi trường demo, sau migration:

```dotenv
TELESCOPE_ENABLED=true
OPS_MONITORING_ENABLED=true
OPS_SLOW_QUERY_MS=500
OPS_CPU_PERCENT=90
OPS_MEMORY_PERCENT=90
OPS_DISK_PERCENT=90
OPS_NETWORK_BYTES_PER_SECOND=125000000
OPS_ALERT_EMAIL=
```

Giữ email trống nếu không muốn gửi mail thật; không dùng thông tin mail/merchant giả làm bằng chứng giao dịch. Linux mặc định python3, Windows python; có thể đặt OPS_PYTHON_BINARY thành đường dẫn Python/venv đã cài psutil.

Sau khi config:clear ở môi trường được phép, chạy trong backend:

```bash
php artisan ops:monitor
php artisan schedule:list
php artisan schedule:work
php artisan queue:work database --tries=5 --timeout=60
```

Hai lệnh work chạy terminal riêng; chỉ một scheduler. ops:monitor trả JSON số đo thật, exit1 khi vượt ngưỡng hoặc collector lỗi; exit1 không mặc định là bug. Scheduler chạy mỗi phút khi bật; snapshot hết hạn sau5phút. Cảnh báo có code/context đã whitelist, tối đa một lần/code/5phút; admin đọc GET /api/v1/admin/operations. Telescope prune48giờ; đặt retention alert/log theo môi trường để tránh đầy đĩa.

Đăng nhập admin trên web rồi mở /docs/api hoặc /docs/api.json cùng origin; /telescope khi đã bật. Backend và Next đã có routing tương ứng. Guest/customer bị403; Telescope tắt mặc định thì404. Không log request/response bodies, headers/cookies, token, SQL/bindings, exception messages/arguments hoặc email/name vào Telescope. Đây là tradeoff giảm chi tiết để bảo vệ dữ liệu; vẫn giữ status/timing và tên class.

## Kiểm chứng và bộ nộp

- Backend: php artisan test; coverage cần Xdebug/PCOV và php scripts/check-coverage.php với Clover report.
- HTTP: php scripts/auth-cookie-smoke.php đạt18kiểm tra thực cookie/CSRF/PAT; php scripts/ops-smoke.php kiểm worker/database queue/scheduler. Chỉ testDB, không chạy đồng thời PHPUnit/E2E.
- Scramble: php artisan scramble:export --env=testing --path=storage/logs/openapi.generated.json trong backend sau khi schema test sẵn sàng; không export nhầm DB chưa migrate.
- Frontend: API7/build22/typecheck và npm run test:e2e; browser provider/mail là fixture.
- GitHub: review source thật rồi commit/push/PR gắn issue khi được yêu cầu; không tạo commit/tác giả/review giả.

Đề yêu cầu tài liệu overview/cách chạy/tools/features/screenshots/ERD và **video demo tối đa10phút, MP4 từ720p**, link người chấm truy cập được. ERD đã bổ sung trong DATABASE. Video/người dùng review và bằng chứng cộng tác thật vẫn cần thực hiện, không được thay bằng file README đánh dấu done.

Gợi ý video:1phút overview/stack/ERD;3phút customer search/auth/cart/checkout/order;2phút admin CRUD/inventory/report;1phút Sandbox kết quả đã verify và giới hạn;2phút token/docs/Telescope/slow-query/host/queue;1phút kết quả tests/coverage và links nộp. Không quay env/secret/token plain/private receipts hoặc dữ liệu cá nhân thật.

Deploy để sau theo yêu cầu mới. Hạ tầng/domain/HTTPS/backup-restore/Resend/provider và canvas native là các nghiệm thu riêng; không nâng backend strict/release gate từ local tests.

## Điểm dự kiến và ưu tiên D98–D101

Ước tính local8,5/10, tính Next.js đạt frontend theo yêu cầu chủ dự án, giảng viên quyết định điểm chính thức. GitHub0,25/1;DB0,9/1;UI/chức năng2,65/3;REST2/2;monitor0,9/1;hiệu năng1,8/2. Repo chính trống source khi kiểm14/11; cần source/CI/PR/review thật, dựng máy mới, nghiệm thu provider/mail/monitor/worker, đo coverage và video≤10phút≥720p. Full báo cáo D98 và checklist nộp cũ có bản archive trong CLEANUP.json. Không cần10k concurrent để thay tiêu chí môn.
