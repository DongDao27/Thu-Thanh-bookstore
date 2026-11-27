# Bằng chứng kiểm thử và demo

## Video minh chứng dịch vụ ngoài

Ba bản quay đầy đủ có cùng thời lượng với các tệp gốc. Các bản công khai che thanh địa chỉ, thông tin giao hàng, dữ liệu thẻ và OTP Sandbox, token khôi phục, thông tin đăng nhập và nội dung riêng trong hộp thư. Tệp xuất bỏ track âm thanh vì các bản gốc không có tiếng nghe được.

| Luồng | Video đầy đủ | Xem nhanh | Nội dung kiểm chứng |
|---|---|---|---|
| Thanh toán VNPAY Sandbox | [MP4 đầy đủ](evidence/provider-demos/vnpay-sandbox-full.mp4) | [Clip ngắn](evidence/provider-demos/vnpay-sandbox.mp4) | Cổng VNPAY ghi rõ chế độ Test, xác thực giao dịch và trở về Thu Thanh với trạng thái “Thanh toán thành công”. |
| Quên mật khẩu | [MP4 đầy đủ](evidence/provider-demos/resend-forgot-password-full.mp4) | [Clip ngắn](evidence/provider-demos/resend-forgot-password.mp4) | Thư “Đặt lại mật khẩu — Nhà sách Thu Thanh” xuất hiện trong Gmail. Thu Thanh xác nhận mật khẩu đã được cập nhật. |
| Đăng ký bản tin | [MP4 đầy đủ](evidence/provider-demos/resend-subscribe-full.mp4) | [Clip ngắn](evidence/provider-demos/resend-subscribe.mp4) | Website ghi nhận đăng ký và Gmail nhận thư “Đăng ký nhận bản tin thành công — Nhà sách Thu Thanh”. |

Hai clip email ghi nhận thư đến hộp thư người nhận. Cấu hình Resend của dự án được ghi trong [tài liệu vận hành](SECURITY-PERFORMANCE.md). Video không quay dashboard Resend hoặc message ID. [Mã SHA-256 của các MP4 công khai](evidence/provider-demos/manifest.json).

## Tái hiện kiểm thử

Làm theo phần Kiểm thử trong [README](../README.md). Chỉ dùng database PostgreSQL `thu_thanh_test` và storage E2E riêng. Backend và browser chạy lần lượt. Không chạy seed/migrate/test trên database thật.

- Component: `npm run test:unit --prefix frontend`; test ẩn/hiện mật khẩu, validation/hint, giá/discount, mô tả plain text, newsletter thành công/lỗi/chống gửi lặp, intent prefetch và Save-Data/2G.
- Coverage unit: `npm run test:coverage --prefix frontend` (Vitest/Istanbul, tính toàn source, report riêng).
- Browser production: `FRONTEND_COVERAGE=1 node scripts/e2e.mjs`, sau đó `npm run coverage:report --prefix frontend`. Istanbul đo toàn bộ65 file JS/JSX; server-only modules chưa được browser chạy vẫn ở mẫu số với0hit. File/chức năng chưa chạy không bị loại khỏi báo cáo.
- Coverage browser không cộng vào unit để tránh đếm lặp statement sau transform JSX. Ngưỡng >70% lines/statements áp dụng cho phép đo browser toàn source, độc lập coverage Laravel. Branch/function được báo cáo nhưng không gọi là đạt70% nếu chưa đạt.
- Coverage local cũ cần chuyển ra thư mục lưu riêng trước khi chạy lại, tránh trộn source. CI dùng checkout mới.
- Backend: `cd backend && php artisan test`; kiểm OpenAPI, quyền/tiền/tồn, cache40 sản phẩm, invalidation và cảnh báo lỗi IPN. Resend/VNPAY trong automated tests là fixture/mock.

Workflow Frontend lưu coverage unit; E2E build instrument chỉ trong môi trường test, chạy Chromium, chặn provider thật và lưu HTML/LCOV/JSON trong artifact `e2e-report`. Build production bình thường giữ SWC/Turbopack, không đưa instrumentation lên website.

Kết quả và giới hạn của đợt26/11 được ghi trong [đánh giá sau sửa](GRADING-REASSESSMENT.md); artifact CI mới là nơi xem report đầy đủ. Không lấy số đo Lighthouse ngày23/11 làm phép đo mới.

## Kết quả CI được lưu

Source7aa6a3b đã đạt Backend/Frontend/E2E/PRpolicy. Backend188tests/2312assertions,statementcoverage91,62%,HTTP22 vàops smoke; frontend15unit/16APIclient,build/check; browser31/31 vàcoverage76,27%lines/73,56%statements trên65file. Xem [snapshot hosted](evidence/grading-feedback/hosted-browser-summary.json) và [liên kết CI](evidence/grading-feedback/verification.json). Supabase Preview skipped; chưa merge/deploy production.
