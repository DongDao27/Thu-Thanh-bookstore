# Bàn giao repository

## Task hiện tại: GRADING-FEEDBACK — 26/11/2026

Yêu cầu: hoàn thiện theo bản chấm thử 8,25/10 rồi chấm lại. Theo D123, source sửa trên nhánh `fix/grading-feedback-20261126`, từ main8190455. Issue #29 và PR #30 dùng theo dõi đợt này. Source CI đã đạt; hoàn thiện checkpoint này rồi chuyển PR ready cho chủ dự án review. Chưa merge hoặc triển khai production.

Các thay đổi source:

- 24771a1: cache catalog40 sản phẩm trong snapshot, thu hẹp invalidation đơn/receipt, thêm cảnh báo IPN, đồng bộ API/OpenAPI và regression tests.
- dcf634e: Tailwind cho layout hiện có, intent prefetch, public static/ISR một giờ. Chặn utility container trùng tên để giữ chiều rộng/lề cũ.
- b62d315: React component tests, đo coverage toàn source trên Chromium production và workflow lưu report; 0056cd0: PR template/policy nối issue.
- 7aa6a3b: patch Symfony7.4.24 và dependency tương thích sau khi CI phát hiện5 advisory mới ngày25/11. Laravel12.73.0/PHP8.4 giữ nguyên. Composer validate/platform/audit sạch, backend188/2312 đạt sau patch; hosted CI của7aa6a3b đã đạt.

Kiểm local:188backend/2312assertions;15React/unit;16API client;build/check/Pint vànpm audit đạt. Full production E2E30 và1test responsive bổ sung đạt; kiểm375/768/1024/1440 không tràn và giữ lề, đã xem ảnh375/1440. Browser toàn65file:75,80%lines/73,12%statements. Không cộng lặp coverage unit và browser.

Hosted source7aa6a3b đã đạt Backend/Frontend/E2E/PRpolicy:188tests/2312assertions,coverage91,62%,HTTP22/ops smoke;31/31E2E,Nextcoverage76,27%lines/73,56%statements trên65file. SupabasePreview skipped, không tínhpassed. Backend CI đầu aeedd94 lỗi5advisory được giữ lịch sử riêng; đã sửa bằng patch7aa6a3b vàkiểm lại. Dev-run đầu29/30 doFastRefresh khôngtínhpassed; productionQRđãđạt.

Minh chứng: `docs/evidence/grading-feedback`; báo cáo `docs/GRADING-REASSESSMENT.md`; checklistvideo `docs/DEMO-EVIDENCE.md`. Log/report đầy đủ private ở`.runtime/grading-feedback` và`output/coverage`. Kết quả chấm ứng viên 9,75/10 chỉ là tự đánh giá; chưa xác nhận bài nộp main đã thay đổi.

Lệnh tiếp theo khi chủ dự án tiếp tục: `gh pr checks 30 --repo DongDao27/Thu-Thanh-bookstore`, đọc diff PR30 và xin chỉ định rõ về merge/production theoAGENTS trước activation. Source7aa6a3b đã có CI đầy đủ; commitcheckpoint cuối chỉ sửa tài liệu. Không tự tick review người khác. GitHub plugin trả403 với write issue/PR, nên đã dùnggh đăng nhập hiện hành; repo description đã cập nhật và đọc lại.

## Chờ chủ dự án và giới hạn vận hành

Ba video do chủ dự án cung cấp đã được kiểm và lưu dưới dạng clip MP4 công khai tại `docs/evidence/provider-demos`. Mục 3(f) đủ căn cứ cộng 0,5 điểm trong bản tự chấm. Giữ các tệp gốc trong thư mục Screencasts của chủ dự án. PR #30 chờ review và merge.


D119 giữ nguyên: không queryVNPAY/điều traQueryDr99, seed/migrate production hoặc tự bật scheduler tài chính. Production/deploy cần yêu cầu riêng theoAGENTS. Release live được nghiệm thu trước đây625a042; không chạy lạiactivation cũ. Giữenv/APP_KEY/database/uploads/backups/private runtime.

IP thật/rate-limit quaVercel/Cloudflare, scheduler, cachelock503, search trên dữ liệu lớn vàscale ngang cần phạm vi/kiểm riêng; đọcSECURITY-PERFORMANCE. Backendgate vẫnexternally_blocked vềvận hành24/7/tải lớn; không dùng điểm dự kiến thay cho gateproduction.

Project4 đã đóng theoD122 trước đợt này; không tự mở lại hoặc đổi metadata để thay bằng chứng. Task giữin_progress khi còn chờreview/phạm vi xuất bản.

Cluster test riêng fresh-pgdata đã được dừng sau kiểm tra; dữ liệu và fresh-settings/run.py vẫn ở .runtime, không ảnh hưởng PostgreSQL hệ thống. Khi cần tái kiểm local, khởi động lại cluster socket theo .runtime/grading-feedback/run.py và cấu hình fresh-settings; không dùng database app để chạytest.
