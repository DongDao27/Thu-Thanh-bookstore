> D116 (22/11/2026): chủ dự án bỏ yêu cầu Pusher cho release này; dùng polling hiện có. Các mục Pusher bên dưới giữ làm lịch sử/tùy chọn, không chặn release polling-only. Resend/payment/CI và các kiểm bắt buộc khác vẫn giữ.

> Hiện hành D112 (20/11/2026): PostgreSQL 17 cho main/test/Sandbox; MySQL và backup đã xóa theo D107. Stack Vercel/VPS/Cloudflare/Resend/Pusher, plugin V40. Hướng dẫn vận hành: [DEPLOYMENT](DEPLOYMENT.md). Ghi chép D cũ bên dưới giữ đúng thời điểm.

# Rà soát bảo mật và hiệu năng D105 — 19/11/2026

Đã sửa source/config theo Vercel + VPS + PostgreSQL + Cloudflare + Resend + Pusher. Đây là rà soát phần hạ tầng/tích hợp và regression nghiệp vụ hiện có, không phải chứng nhận penetration test hoặc bảo đảm điểm10. Chưa triển khai account/provider thật; cấu hình mẫu không tự chứng minh VPS an toàn.

## Các phát hiện đã xử lý

1. **Mức trung bình — dependency frontend:** npm audit báo hai advisory cache disclosure GHSA-mfr8-8qww-v76h / GHSA-vc7v-qv4w-wrv8 ở Next16.4.2. Nâng patch16.4.6, giữ major16 và JavaScript. `frontend/package.json:15`, lockfile tương ứng; build/test đạt, npm audit0. Source hiện không dùng unstable_cache/use cache nên không khẳng định có đường khai thác trong ứng dụng này.
2. **Mức thấp — dependency backend:** composer audit báo GHSA-63hm-g96r-2m2q / CVE-2026-128083 ở Laravel12.70. Nâng12.73 và min constraint^12.73, giữ major12/PHP8.4. `backend/composer.json:12`; composer audit0. Không khẳng định đường khai thác Concurrency Process đang được ứng dụng sử dụng.
3. **Rủi ro quyền với realtime mới:** `backend/routes/api.php:41`, `backend/routes/channels.php:7` bắt session+CSRF+throttle, owner/admin; bearer/foreign/nonexistent channel bị chặn. Test HTTP thực xác nhận session có auth vẫn phải gửi CSRF. Không dùng route broadcast mặc định bỏ CSRF. Payload chỉ order_code/version, không PII/amount; frontend GET lại API có ownership. Không suy ra paid từ socket.
4. **Rủi ro phát dữ liệu trước commit/giữ kết nối khi provider lỗi:** `backend/app/Observers/OrderRealtimeObserver.php:20` enqueue sau commit, rollback không phát. Queue lỗi được report nhưng không biến payment đã commit thành HTTP failure; polling dự phòng đọc lại state. `backend/app/Providers/ResendServiceProvider.php:20` giới hạn connect3s/total10s, provider429 báo failure cho queue retry; không giả delivery thành công. Pusher HTTPS deadline và retry/backoff hữu hạn.
5. **Rủi ro mất constraint khi đổi database:** PostgreSQL không có unsigned integer như MySQL. Migration mới thêm CHECK nonnegative; CASE generated columns giữ tối đa một admin và một accepted receipt/đơn; khóa hàng/idempotency giữ nguyên. Test invalid price/stock/grade/quantity, duplicate receipt và race nhiều process đã đạt trên PostgreSQL17.11 test riêng. Các truy vấn MySQL IF/DATE_ADD/LOCATE/CHAR được đổi, giữ báo cáo ngày Việt Nam/UTC và tìm không dấu/literal wildcard.
6. **Rủi ro origin bypass/cache riêng tư:** `deploy/nginx/vps.conf:18` yêu cầu AOP, TLS strict, firewall Cloudflare theo hướng dẫn; không tin forwarded headers tùy ý (`:41`). Đây là mẫu chưa được kiểm nginx -t/TLS thật. `backend/app/Http/Middleware/RequestContext.php:20` giữ no-store private trừ snapshot public; Cloudflare/Vercel cache rules vẫn cần nghiệm thu. Production cookie host-only/Secure/HttpOnly/Lax, CORS chỉ origin được cấu hình; không localStorage bearer.
7. **Rủi ro kiểm tra sai thành công:** standalone smoke sau Laravel bootstrap có thể render exception rồi trả exit0. Hai smoke scripts đã đặt exception handler exit1, không in thông tin bí mật; lỗi assertion không còn được CI coi là pass.

## Hiệu năng đã chuẩn bị và giới hạn đo

- `backend/database/migrations/2026_11_19_000002_add_postgresql_catalog_search.php:16` cố định dictionary unaccent trong hàm immutable + GIN pg_trgm, query binding và escape literal (`backend/app/Services/CatalogService.php:30`). Nếu dictionary đổi phải REINDEX; extension/DDL cần provision bởi migration role. Chưa đo tải sản phẩm thật ở VPS, không cam kết latency cho từ khóa ngắn hoặc dữ liệu lớn.
- Migration000003 thêm index cho truy vấn order-items, images, reservation và reconciliation; PostgreSQL không tự tạo index trên cột FK. Giữ pagination giới hạn, eager loading và public snapshot cache hiện có; test warm snapshot không phát query DB và rollback không publish giá tạm.
- Realtime chỉ lazy-load khi có public key; gộp burst250ms, bỏ event cũ/trùng/khác đơn, cleanup socket khi rời trang, không auto-retry mutation. Polling10s/tối đa30lượt vẫn là fallback. Có thể đo để giảm polling khi live socket ổn định sau nghiệm thu, chưa tuyên bố đã giảm request rate ở production.
- Worker/scheduler trên VPS, OPcache/PHP-FPM config có sẵn; database không public, không giữ khóa khi gọi provider. VPS size/pool/connection limit/Redis tùy số đo, không mặc định cấu hình 10k đồng thời.

## Bằng chứng local

- PostgreSQL DBtest riêng `127.0.0.1:55432/thu_thanh_test`, PHP8.4.25, Laravel12.73; main và Sandbox đã chuyển PostgreSQL theo D106/D107, APP_KEY/uploads giữ nguyên; MySQL đã xóa.
- Backend175test/2164assertions,0error/failure/skipped; coverage **91,34% (1729/1893 statements)**. `backend/storage/logs/phpunit-d105-coverage.xml`, `coverage-d105.xml`; test provider là giả lập, concurrency dùng process/DB thật.
- HTTP19 kiểm tra cookie/CSRF/PAT/logout; queue thật consume fixture, expiry/reconcile/schedule smoke qua PostgreSQL. Resend429 không success; Pusher rollback không publish và owner/admin/foreign/bearer được kiểm.
- JavaScript70files/check,11tests, production build22static+2dynamic routes, composer validate/platform/audit và npm audit đạt. Backend Pint đạt. `check_pack.py` chỉ kiểm tài liệu, không thay app test.
- Chromium E2E **29/29passed trong một lượt5,2phút** trên PostgreSQL; report output/e2e/report/index.html. EXPLAIN actual CatalogService với20000synthetic products trong transaction rollback dùng GIN index,20matches/execution2,940ms cho một mẫu local; không là p95/load test. Evidence .runtime/d105-search-plan.txt. Coverage không chứng minh không có lỗ hổng.

## Việc còn phải nghiệm thu trước release

**P1:** domain/Vercel/VPS thực, firewallIPv4+IPv6/AOP/TLS, chặn truy cập origin trực tiếp; hai client thật qua Vercel kiểm IP/rate-limit/CSRF/cookie/cache. Không trust-all để sửa IP egress bị gộp. Local main/Sandbox đã chuyển và thử restore PostgreSQL theo D106/D107; còn restore/đối chiếu trên VPS và backup ngoài máy khi deploy.

**P1:** Resend DNS verification/SPF/DKIM/DMARC, reset email thật/queue failure; Pusher app credentials/live reconnect/auth thu hồi sau logout; VNPAY QueryDr/expiry/reconciliation provider acceptance cũ; CI GitHub remote. Không gọi mail/socket thật đã passed từ fake tests.

**P2:** k6 staging theo tải được giảng viên chấp thuận, p95/error-rate/CPU/RAM/DB connections/queue latency; EXPLAIN ANALYZE trên dữ liệu đại diện, restore drill và kiểm cross-region latency Vercel↔VPS. Điểm10 phụ thuộc rubric và demo thật, không thể bảo đảm bằng đổi tool.

Hướng dẫn thao tác và nguồn chính thức ở [DEPLOYMENT.md](DEPLOYMENT.md).

## Bằng chứng mới hơn D106/D107

Backend D107 sau xóa toàn bộ MySQL: 177 tests/2170 assertions, không lỗi/skipped (`backend/storage/logs/phpunit-d107-final.xml`). Coverage D106: 91,35% (1754/1920 statements), `backend/storage/logs/coverage-d106.xml`; E2E D106 29/29 trong một lượt. Các con số D105 bên trên là lần đo lịch sử, không thay chứng cứ mới. VPS/Resend/Pusher thật, CI remote và tải staging vẫn chưa nghiệm thu.
