# Bằng chứng kiểm thử và demo

## Video của chủ dự án

Chủ dự án xác nhận ngày26/11/2026 sẽ tự quay/gửi hai video. **Chưa có đường dẫn video**. Không dùng test fixture, HTTP mock hoặc câu xác nhận trong tài liệu làm bằng chứng đã gọi provider thật.

### Video VNPAY Sandbox

1. Cho thấy domain và tài khoản customer; chọn sản phẩm, số lượng, tổng tiền và tạo đơn.
2. Mở VNPAY Sandbox, thực hiện giao dịch test. Không dùng tiền thật.
3. Quay kết quả quay về, mã đơn và mã giao dịch provider; mở lại lịch sử đơn để cho thấy server đã xác nhận thanh toán.
4. Nếu quay trang admin, cho thấy receipt đã accepted và trạng thái đơn phù hợp. Không đổi trạng thái đơn thủ công để tạo bằng chứng.
5. Che cookie, mật khẩu, OTP, merchant secret/chữ ký hoặc dữ liệu cá nhân không cần thiết. Không gọi QueryDr để xử lý lỗi99 theo D119.

Đường dẫn: **chờ chủ dự án bổ sung**.

### Video email Resend

1. Cho thấy đăng ký email mới và response/thông báo đã nhận yêu cầu.
2. Mở hộp thư nhận để thấy nội dung và thời gian email xác nhận.
3. Mở dashboard Resend để thấy message id, timestamp và delivery event tương ứng; che API key, người nhận và thông tin riêng nếu cần.
4. Có thể cho thấy queue/failed jobs sau gửi; job hết không tự chứng minh thư đến inbox.
5. Không gửi hàng loạt hoặc gửi lại thư chỉ để lấy video nếu không cần thiết.

Đường dẫn: **chờ chủ dự án bổ sung**.

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
