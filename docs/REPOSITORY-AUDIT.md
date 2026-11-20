# Rà soát repository — D108, 20/11/2026

Đã quét danh mục **681 file first-party**, đọc/kiểm UTF-8 của 413 file văn bản, parse 20 JSON và 7 Python. Không đưa dependencies (`vendor`, `node_modules`), Git internals, runtime private hoặc build output vào phạm vi source. Đây là kiểm tính nhất quán và regression có mục tiêu; không phải tuyên bố đã kiểm mọi hành vi hoặc audit bảo mật toàn bộ ứng dụng từng dòng.

## Đã sửa

- Viết lại README, START-HERE, local guide và deployment guide; bỏ các lớp hướng dẫn cũ mâu thuẫn, lệnh MySQL và đường dẫn backup đã xóa.
- Đồng bộ PRD/DATABASE/API/checklist/security với PostgreSQL, Resend, Pusher và plugin V38; sửa F05 từ popup cũ thành chi tiết sản phẩm/mua riêng hiện hành.
- Sửa tham chiếu README catalog không tồn tại về tài liệu asset đang dùng; sửa hướng dẫn CSS và JavaScript/JSX trong AGENTS.
- Cập nhật bàn giao và flags hiện hành của STATUS; giữ PROGRESS/DECISIONS và các snapshot kiểm chứng cũ đúng thời điểm.
- Sửa bộ kiểm ảnh còn báo cố định 24 mẫu. Thay 11 thumbnail JPEG 145px trong plugin bằng PNG giữ nguyên điểm ảnh nguồn, bổ sung source SHA256/format; cả 35 mẫu đã kiểm đối chiếu điểm ảnh. Không đổi catalog/database/uploads của website.
- Đóng gói lại `output/figma/THUTHANH-FIGMA-V38.zip`; SHA256 **82f2642da07f1ca0aeae55deac7e218b286572d277e03cfc0459890f6e35e506**. Gói này thay gói V38 D106; checksum cũ trong lịch sử vẫn là bằng chứng của bản cũ.

## Kiểm thực trong D108

| Lệnh / phạm vi | Kết quả |
| --- | --- |
| `python scripts/check_pack.py` | Đạt: required files, task dependencies, OpenAPI references và link Markdown local |
| Quét first-party UTF-8/JSON/Python | Đạt; không thấy script MySQL đã xóa được gọi trong scripts/deploy/workflows/backend config |
| `python scripts/verify-store-assets.py` | Đạt: 136 ảnh nguồn, 35 ảnh PNG plugin lossless, banner/brand/map hashes |
| `npm run check` trong frontend | Đạt 70 file JavaScript/JSX và import |
| `npm run test:api` trong frontend | 11/11 đạt |
| `php artisan app:environment-check` trong backend | PostgreSQL main `thu_thanh`, UTC, PHP 8.4.25/Laravel 12.73.0 |
| `python scripts/run-postgresql-check.py php backend/artisan test --filter=EnvironmentTest` | 2 tests / 7 assertions đạt trên DBtest riêng |
| `python scripts/run-postgresql-check.py php scripts/auth-cookie-smoke.php` | 19 kiểm HTTP cookie/CSRF/PAT/logout đạt |
| `php scripts/validate-deploy.php` | Parse hai Compose YAML đạt; không chạy Docker/Nginx/systemd |
| `node scripts/test-figma-clicon.cjs` | Đạt: 35 mẫu, 214 frames, 3 pages, 3279 routes; geometry/flow/auth/quantity/money/upgrade/rollback/cancel |
| ZIP Figma | CRC đạt; checksum mới ở trên |
| HTTP main | Web/API 200, catalog 137 sản phẩm tại lúc kiểm |
| `git diff --check` | Đạt |

Log chi tiết private: `.runtime/d108-repository-audit.json`, `.runtime/d108-figma.log`. Các lỗi được cố ý inject trong test rollback/cancel plugin là kịch bản kiểm thử; kết quả cuối `passed` và exit 0.

Full backend **177 tests/2170 assertions** là kết quả D107; coverage **91,35%** và E2E **29/29** là D106. D108 không chạy lại full suite/build/E2E vì thay đổi tập trung ở tài liệu và ảnh plugin; không gắn nhãn các lần đo trước thành chạy mới. Source app Laravel/Next không thay đổi hành vi trong D108.

## Chưa hoàn tất trên dịch vụ thật

VPS/Vercel/domain/TLS/firewall/AOP; Resend delivery; Pusher private socket/reconnect/logout; VNPAY QueryDr/expiry/reconciliation đầy đủ; restore/backup ngoài máy; CI remote; tải staging và review canvas native. Backend gate vẫn `externally_blocked`, release `not_started`.

Đi lần lượt theo [DEPLOYMENT](DEPLOYMENT.md); chạy local theo [CHAY-WEB-VA-LARAVEL](CHAY-WEB-VA-LARAVEL.md); import rồi Run plugin theo [HUONG-DAN](../thu-thanh-figma-clicon/HUONG-DAN.md). Chưa commit/push/deploy hoặc gọi email/payment provider thật trong phiên này.
