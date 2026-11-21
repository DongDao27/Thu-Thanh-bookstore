# Thu Thanh Bookstore

Nền tảng bán sách và đồ dùng học tập cho Nhà sách Thu Thanh, thực hiện trong môn **Advanced Web Development**. Website hỗ trợ tìm sản phẩm, đặt hàng, thanh toán và theo dõi đơn; trang quản trị phục vụ catalog, tồn kho, xử lý đơn và báo cáo doanh thu của hệ thống.

**Next.js 16 · React 19 · Laravel 12 · PHP 8.4 · PostgreSQL 17**

> Trạng thái xuất bản ngày 20/11/2026: README được dùng làm commit nền theo chấp thuận của chủ repo. Source Đông và Thiện được đưa vào qua các Pull Request riêng, có review trước merge. Các đường dẫn source/tài liệu bên dưới có đầy đủ khi các PR tương ứng được merge. Nghiệm thu production và provider thật còn chờ; đây chưa phải bản công bố đã deploy.

---

## Thành viên

| Thành viên | Phần việc chịu trách nhiệm | Review |
| --- | --- | --- |
| **Đào Ngọc Đông** — [DongDao27](https://github.com/DongDao27) | Frontend Next.js, giao diện, plugin Figma web/mobile, tài liệu và bộ nộp | Backend của Thiện |
| **Thiện** | Laravel API, PostgreSQL, thanh toán, bảo mật backend, worker và hạ tầng | Frontend/Figma của Đông |

Phân công trách nhiệm dự kiến Đông60%/Thiện40%; không dùng số file, số commit hoặc tác giả lịch sử để suy ra tỷ lệ đóng góp. [Danh sách file](docs/COMMIT-FILES.csv) và [lệnh commit/PR dành cho Thiện](docs/PHAN-CONG-VA-COMMIT.md).

## Mục lục

1. [Tính năng](#tính-năng)
2. [Công nghệ](#công-nghệ)
3. [Kiến trúc và thiết kế](#kiến-trúc-và-thiết-kế)
4. [Khởi động nhanh](#khởi-động-nhanh)
5. [Cài đặt trên máy mới](#cài-đặt-trên-máy-mới)
6. [Lệnh thường dùng](#lệnh-thường-dùng)
7. [Kiểm thử và bằng chứng](#kiểm-thử-và-bằng-chứng)
8. [Bảo mật và hiệu năng](#bảo-mật-và-hiệu-năng)
9. [Cấu hình dịch vụ](#cấu-hình-dịch-vụ)
10. [Quy trình phát triển](#quy-trình-phát-triển)
11. [Triển khai và phần còn lại](#triển-khai-và-phần-còn-lại)
12. [Tài liệu và nguồn tài nguyên](#tài-liệu-và-nguồn-tài-nguyên)

---

## Tính năng

| Vai trò | Chức năng |
| --- | --- |
| Khách xem hàng | Trang chủ, danh mục, tìm kiếm/lọc/phân trang, bestseller và chi tiết/ảnh sản phẩm |
| Khách hàng | Đăng ký/đăng nhập, khôi phục mật khẩu, hồ sơ, giỏ hàng, Mua ngay, checkout giao hàng hoặc nhận tại cửa hàng |
| Theo dõi đơn | Chi tiết đơn, trạng thái xử lý/thanh toán, hủy khi đủ điều kiện, cập nhật qua realtime hoặc polling |
| Quản trị | Sản phẩm/ảnh, nhập và điều chỉnh tồn, xử lý đơn, đối soát thanh toán, báo cáo doanh thu |
| Tích hợp | VNPAY Sandbox, email Resend, polling trạng thái đơn (Pusher tùy chọn), đăng ký bản tin chỉ nhập email và nhấn Đăng ký |

Desktop dùng5cột, catalog25sản phẩm/trang; trang chủ tối đa10sản phẩm mới và15gợi ý không trùng các feed ưu tiên. Giá, tồn, quyền và trạng thái do Laravel xác nhận; giao diện không tự quyết định kết quả thanh toán.

## Công nghệ

| Thành phần | Công nghệ | Đích triển khai |
| --- | --- | --- |
| Website | Next.js16, React19, JavaScript/JSX, Tailwind4 | Vercel |
| Backend API | Laravel12, PHP8.4, Eloquent, Sanctum | VPS Linux |
| Database | PostgreSQL17 | VPS; main/test tách riêng |
| DNS và proxy | Cloudflare, HTTPS và giới hạn truy cập origin | Domain của chủ dự án |
| Email | Resend và queue Laravel | Sender/domain phải xác minh |
| Trạng thái đơn | Polling hiện có; Pusher tùy chọn | Release D116 không cần credentials Pusher |
| Thanh toán | VNPAY adapter, callback/IPN và đối soát | Sandbox trước nghiệm thu thật |
| Kiểm tra | PHPUnit, Node test runner, Playwright, GitHub Actions | Database test cô lập |
| Thiết kế | Plugin Figma offline V41, web/mobile và User Flow | Owner import/Run/Present |

## Kiến trúc và thiết kế

Trình duyệt → Next.js → Laravel API → PostgreSQL. API xử lý quyền truy cập, transaction, giá/tồn và trạng thái; queue/scheduler đảm nhiệm tác vụ nền. Resend và VNPAY cần cấu hình và nghiệm thu riêng. D116 dùng polling; chỉ nghiệm thu Pusher nếu chủ động bật lại.

| Thư mục | Nội dung |
| --- | --- |
| `frontend/src/app/` | Storefront, auth, giỏ/checkout/đơn và admin |
| `frontend/src/components/`, `frontend/src/lib/` | Thành phần dùng chung, API/CSRF, realtime và logic hiển thị |
| `backend/app/` | Controllers, FormRequests, Policies, Services, Models, Jobs và Notifications |
| `backend/database/`, `backend/tests/` | Migrations, dữ liệu catalog công khai và tests |
| `deploy/`, `scripts/` | Mẫu triển khai, local helpers, kiểm thử/monitoring |
| `thu-thanh-figma-clicon/` | Source/build/manifest của plugin Figma V41 |
| `docs/` | Đặc tả, runbook, báo cáo và checkpoint |

### Thiết kế và minh họa

[Review Figma](docs/FIGMA-REVIEW.md) và [hướng dẫn import plugin](thu-thanh-figma-clicon/HUONG-DAN.md) mô tả các màn web/mobile, luồng mua hàng và8UserFlow editable ở Landing page. Mobile là thiết kế app402×874 với search/app bar, thẻ2cột, danh sách cuộn, điều hướng và CTA theo ngữ cảnh.

Chạy plugin/build theo hướng dẫn để tạo ZIP và preview; `output/` không đưa vào Git. Kiểm mock/preview chưa thay bước chủ dự án duyệt canvas Figma native. Nguồn ảnh/font và quyền sử dụng được ghi trong [ASSET-SOURCES](docs/ASSET-SOURCES.md).

## Khởi động nhanh

Áp dụng cho **máy Windows hiện tại đã có dependencies, PostgreSQL runtime và dữ liệu**. Không bootstrap lại, seed demo hoặc tạo lại APP_KEY.

Terminal1 — PostgreSQL và API:

```powershell
Set-Location D:\ThuThanh-WebDesign
python scripts/postgres-test-local.py
powershell -ExecutionPolicy Bypass -File scripts/start-backend.ps1
```

Terminal2 — website:

```powershell
Set-Location D:\ThuThanh-WebDesign
powershell -ExecutionPolicy Bypass -File scripts/start-frontend.ps1
```

Mở **http://localhost:3000**, API **http://localhost:8000**, quản trị `/admin`. Database local ở `127.0.0.1:55432`; không công khai database ra Internet. Helper nhận ra web đã chạy và không tự tắt process khác. Xem [local và xử lý EADDRINUSE](docs/CHAY-WEB-VA-LARAVEL.md).

## Cài đặt trên máy mới

Yêu cầu: Git, PHP8.4 với `pdo_pgsql`, Composer2, Node22/npm, Python và PostgreSQL17. Chỉ làm sau khi đã có đầy đủ source từ hai PR; README nền một mình chưa chạy được ứng dụng.

```powershell
git clone https://github.com/DongDao27/Thu-Thanh-bookstore.git
Set-Location Thu-Thanh-bookstore
Set-Location backend
composer install
Set-Location ..\frontend
npm ci
Set-Location ..
```

Chuẩn bị `.env` private từ mẫu, PostgreSQL main/test riêng, APP_KEY và dữ liệu theo [hướng dẫn local](docs/CHAY-WEB-VA-LARAVEL.md) và [DEPLOYMENT](docs/DEPLOYMENT.md). Máy mới cần cài PostgreSQL hoặc cung cấp binaries trước khi dùng helper portable Windows. Clone không chứa runtime/database/uploads/secret.

Nếu chuyển cửa hàng hiện có, chuyển dump PostgreSQL, uploads và APP_KEY qua kênh riêng, restore vào DB trống rồi đối chiếu; không tạo APP_KEY mới hoặc dùng migrate:fresh/reset để xử lý lỗi. MySQL và backup của cụm cũ đã xóa theo D107, không dùng hướng dẫn lịch sử MySQL để khởi động hiện tại.

## Lệnh thường dùng

Tại gốc repo đầy đủ:

```powershell
python scripts/check_pack.py
python scripts/verify-store-assets.py
node scripts/test-figma-clicon.cjs
```

Trong `frontend/`:

```powershell
npm run check
npm run test:api
npm run build
```

Trong `backend/`: `php artisan app:environment-check`, `php artisan queue:failed`, `php artisan schedule:list`. Chỉ bật worker/scheduler khi đang nghiệm thu đúng môi trường: chúng có thể gửi mail, hết hạn và đối soát đơn thật.

## Kiểm thử và bằng chứng

| Phạm vi | Kết quả local đã ghi nhận | Mốc |
| --- | --- | --- |
| Frontend syntax/import | 71file đạt | D114 |
| Frontend unit/API client | 16tests đạt | D114 |
| Production build | 22static +2dynamic routes | D114 |
| Plugin Figma V41 | 214screens,8UserFlow,3287routes; mock đạt, native còn chờ | D115 |
| Backend | 177tests,2170assertions, không lỗi/skipped | D107 |
| Coverage backend | 91,35% | D106 |
| Chromium E2E đầy đủ | 29/29 | D106 |

Đây là kết quả theo từng mốc, không phải khẳng định đã chạy lại backend/E2E tại mỗi commit. GitHub Actions phải chạy và review kết quả cho HEAD PR mới; PR frontend thiếu backend giữ draft, không coi E2E chưa đủ source là passed. Kiểm tải staging và canvas Figma native chưa nghiệm thu.

Tests backend/E2E chỉ dùng `thu_thanh_test`, chạy tuần tự. Với test env local đã chuẩn bị, từ gốc:

```powershell
python scripts/run-postgresql-check.py php backend/artisan test
# Chi sau khi PHPUnit da ket thuc:
python scripts/run-postgresql-check.py node scripts/e2e.mjs
```

Không trỏ tests vào `thu_thanh` hoặc Sandbox. Chi tiết dependencies, Chromium và test environment trong [runbook local](docs/CHAY-WEB-VA-LARAVEL.md).

## Bảo mật và hiệu năng

- **Xác thực/quyền:** session cookie + CSRF cho first-party app; Laravel kiểm vai trò, ownership và private channel. Secret không đặt trong Git hoặc biến `NEXT_PUBLIC_*`.
- **Tiền và tồn:** validation, transaction/row lock, idempotency và đối soát tại backend; không xác nhận paid chỉ từ Return URL, không gọi mạng khi giữ khóa DB.
- **Đọc dữ liệu:** catalog có lọc/phân trang và feed giới hạn; frontend giới hạn thời gian GET15giây, kể cả đọc body, có lỗi/thử lại; không tự retry mutation gây ghi trùng.
- **Cache và riêng tư:** phân biệt catalog công khai với auth/cart/checkout/orders/admin. Cloudflare không cache response riêng tư hoặc có cookie nhạy cảm; cấu hình/invalidation phải kiểm theo môi trường, không tự đặt TTL dài cho tiền/tồn.
- **Tác vụ và vận hành:** mail qua queue, realtime chỉ báo cần đọc lại API; giám sát worker/scheduler/failed jobs và kiểm backup/restore. Chưa có bằng chứng staging10.000người đồng thời, không cam kết công suất chưa đo.

Xem [bảo mật/hiệu năng](docs/SECURITY-PERFORMANCE.md), [API](docs/API.md) và [quy tắc nghiệp vụ](docs/DOMAIN-RULES.md) để kiểm chi tiết.

## Cấu hình dịch vụ

| Dịch vụ | Cần chuẩn bị | Cần kiểm sau có domain/hạ tầng |
| --- | --- | --- |
| PostgreSQL | DB/role main và test, env private, backup | Quyền mạng, restore và đối chiếu dữ liệu |
| Vercel/VPS | Project/root directory, env API, PHP/dependencies | HTTPS, cookie/CSRF/CORS, worker/scheduler và health |
| Cloudflare | DNS, TLS, bảo vệ origin | Firewall/AOP/client-IP và bypass cache riêng tư |
| Resend | API key server-only, sender/domain và DNS | Email khôi phục/bản tin nhận thật, retry/failed jobs |
| Trạng thái đơn | D116 dùng polling; không cần app Pusher | Kiểm đọc lại trạng thái đơn và quyền owner/admin |
| VNPAY | Sandbox merchant/hash secret, Return/IPN/QueryDr config | Success/fail/cancel/replay/late/expiry/reconcile, không chỉ Return |

Không có domain/VPS/provider credentials không đồng nghĩa integration đã nghiệm thu. Không gửi keys trong README, Issue hoặc PR.

## Quy trình phát triển

Feature branch → commit đúng file phụ trách → push branch → Pull Request → người còn lại review → CI đủ/xanh → merge. README nền là ngoại lệ khởi tạo được chủ repo chấp thuận; source ứng dụng không push thẳng main. Không force push hoặc dùng admin bypass để vượt check/review.

Đông đưa phần frontend/Figma/docs lên draft PR trước; Thiện tự commit388file backend bằng tài khoản mình và PR riêng để Đông review. Sau backend merge, đồng bộ main vào branch Đông, chạy đủ CI rồi Thiện review. [Hướng dẫn Thiện và lệnh đầy đủ](docs/PHAN-CONG-VA-COMMIT.md).

Đọc [AGENTS](AGENTS.md), [START-HERE](START-HERE.md), [STATUS](docs/STATUS.json), [HANDOFF](docs/HANDOFF.md) trước khi sửa. Một người cập nhật checkpoint; dữ liệu/private và dirty work phải được giữ.

## Triển khai và phần còn lại

Backend gate **externally_blocked**, release **not_started**. Còn nghiệm thu VPS/Vercel/domain/TLS, email Resend và polling thực tế, VNPAY QueryDr/expiry/reconciliation và hai đơn lịch sử chưa rõ kết quả, backup offsite/restore, remote CI/load và canvas Figma native.

Triển khai thực tế cần yêu cầu riêng và môi trường đích; source vào main chưa tự cho phép deploy. [Hướng dẫn triển khai](docs/DEPLOYMENT.md) và [file Word hoàn thiện dự án](docs/HUONG-DAN-HOAN-THIEN-DU-AN-THU-THANH.docx) ghi thứ tự và checklist.

## Tài liệu và nguồn tài nguyên

- [PRD](docs/PRD.md), [database](docs/DATABASE.md), [API](docs/API.md), [OpenAPI](docs/openapi.json).
- [Yêu cầu môn](docs/COURSE-CHECKLIST.md), [review Figma](docs/FIGMA-REVIEW.md), [nguồn asset](docs/ASSET-SOURCES.md).
- [Lịch sử quyết định](docs/DECISIONS.md), [tiến độ](docs/PROGRESS.md), [nhật ký cleanup](docs/CLEANUP.json). Đây là lịch sử có mốc ngày, không thay runbook hiện hành.
- [Issues](https://github.com/DongDao27/Thu-Thanh-bookstore/issues): ghi bước tái hiện, kết quả mong đợi/thực tế, logs đã che dữ liệu; không gửi secrets hoặc thông tin khách hàng.

Cảm ơn các dự án Laravel, Next.js, React, PostgreSQL, Tailwind và các tác giả asset/font có nguồn trong repo. Giữ license/attribution đi kèm dependencies và tài nguyên; không tự suy ra license toàn dự án từ license của framework.

README tham khảo cách tổ chức mục lục, thành viên, setup, testing và workflow từ [Daily Vocabulary](https://github.com/AkioCkist/daily_vocabulary); nội dung, lệnh và kết quả được viết cho Thu Thanh theo source đã kiểm.
