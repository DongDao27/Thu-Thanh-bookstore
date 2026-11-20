# Bắt đầu / tiếp tục — D114, 20/11/2026

Đọc AGENTS → STATUS → HANDOFF → PROGRESS mới nhất → DECISIONS → PRD/task và đặc tả liên quan; kiểm git status/branch/diff/log/source trước sửa. Không reset/clean/ghi đè dirty work hay bootstrap lại ứng dụng.

D114: chủ dự án cho phép commit/PR phần Đông300file trong clone riêng; [guide chỉ cho Thiện388file](docs/PHAN-CONG-VA-COMMIT.md), [manifest](docs/COMMIT-FILES.csv). Chủ repo đã chấp thuận README nền theo Daily Vocabulary; README-only commit đã lên main, source còn qua draftPR. Không push workspace gốc có originPayment. Publication in_progress, không merge/deploy; theo STATUS/HANDOFF mới nhất.

- Stack hiện hành: Next.js 16 JavaScript/JSX trên Vercel, Laravel 12/PHP 8.4 trên VPS, PostgreSQL 17, Cloudflare, Resend, Pusher (D98/D105). Main và Sandbox đã chuyển PostgreSQL; cụm MySQL cùng backup đã xóa theo D107, kể cả EM3 trong cụm được chủ dự án cho phép. Source EM3 ở repo khác không thuộc phạm vi.
- Main local: PostgreSQL `127.0.0.1:55432/thu_thanh`, API `localhost:8000`, web `localhost:3000`. Tests chỉ `thu_thanh_test`, không chạy PHPUnit và E2E đồng thời. Không suy ra dịch vụ đang chạy từ tài liệu: kiểm port/HTTP thực tế.
- Backend gate `externally_blocked`, release `not_started`. D34 cho phép frontend trong phạm vi API đã kiểm; không tự nâng gate từ tests local. B07 và B00-PG-LIVE cần nghiệm thu provider/hạ tầng thật.
- [README](README.md) là tổng quan; [chạy local](docs/CHAY-WEB-VA-LARAVEL.md), [deploy từng bước](docs/DEPLOYMENT.md), [bàn giao](docs/HANDOFF.md) là hướng dẫn hiện hành. PROGRESS/DECISIONS giữ sự kiện lịch sử, không phải runbook hiện tại.
- Figma offline V40: `output/figma/THUTHANH-FIGMA-V40.zip`, source `thu-thanh-figma-clicon/`; xem [hướng dẫn import](thu-thanh-figma-clicon/HUONG-DAN.md). Cần Run sau import; canvas native chưa nghiệm thu. Mobile là thiết kế app theo quyết định đã chốt.
- Giữ APP_KEY/env/uploads/workbook/ảnh gốc/PostgreSQL data và dump private. `.runtime` và `output` không vào Git. D102/D107 cho phép xóa các bản cũ đã ghi trong CLEANUP; không tái tạo rollback MySQL đã xóa. Ba thư mục metadata catalog cũ có kết quả chặn riêng trong STATUS/CLEANUP, không coi là MySQL.

Mỗi task ghi acceptance trước sửa, kiểm thực, cập nhật TASKS → PROGRESS → HANDOFF → STATUS; done chỉ khi acceptance đạt. Giữ `last_verified_commit=null` nếu chưa có commit và `git_dirty` đúng thực tế. Không tự commit/push/deploy/gửi mail hay payment thật từ yêu cầu sửa tài liệu.

D111: chủ dự án có [file Word hướng dẫn hoàn thiện 16 trang](docs/HUONG-DAN-HOAN-THIEN-DU-AN-THU-THANH.docx); đây là checklist/operator guide, không phải biên bản đã deploy.
