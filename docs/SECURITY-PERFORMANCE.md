# Vận hành và bảo mật

## Hiện hành

Web: https://nhasachthuthanh.id.vn . API: https://api.nhasachthuthanh.id.vn . Frontend trên Vercel, Laravel/PHP-FPM và PostgreSQL trên VPS; Cloudflare bảo vệ API bằng TLS và Authenticated Origin Pulls. Alias API staging được giữ để tiếp nhận IPN đã đăng ký.

API kiểm quyền/ownership, CSRF, validation và idempotency. Session cookie host-only, Secure, HttpOnly, SameSite=Lax. Không đặt provider secret trong biến NEXT_PUBLIC hoặc Git. Backend kiểm chữ ký, mã giao dịch và số tiền trước khi xác nhận paid. Không thực thi PHP từ uploads.

SSH root, password và keyboard-interactive đã tắt; deploy dùng key. Thử phiên key thứ hai thành công trước khi hủy timer rollback. PostgreSQL chỉ lắng nghe loopback. Worker database hoạt động; scheduler tài chính chưa bật. VNPAY vẫn Sandbox, QueryDr giữ nguyên theo D119.

## Backup

Backup VPS lúc 03:30 giờ Việt Nam, có khoảng ngừng API ngắn để đảm bảo database/uploads nhất quán. Backup chứa dump, uploads, manifest và env private, luôn nằm ngoài webroot.

Công cụ `deploy/ops/offsite-pull.py` và user timer kéo bản mới về laptop, kiểm SHA-256 trước khi công bố snapshot. File 0600, thư mục 0700. Giữ các bản cũ, cảnh báo khi vượt 14 snapshot; cảnh báo desktop khi job lỗi hoặc backup quá 36 giờ. Laptop cần mở phiên và có mạng; chưa phải giám sát/offsite cloud 24/7.

Đã phục hồi thử snapshot sau đổi tên miền vào cluster PostgreSQL 17 riêng, không TCP: khớp toàn bộ 33 bảng, 137 sản phẩm, 14 đơn và 221 file uploads. Không restore đè database thật.

## Kiểm chứng

Backend 179 test/2.180 assertions; E2E 29/29; frontend 16/16 và production build đạt. Website chính 72/72 kiểm tra, bảo vệ API 7/7. Tải nhẹ 20 yêu cầu/concurrency2 đều HTTP200; chưa chứng minh tải 10.000 người dùng đồng thời.

Cấu hình deploy tham khảo ở `deploy/`; không chạy lại script hardening trên VPS đã áp dụng. Các file env thật, private snapshot, báo cáo runtime và logs không xuất bản lên repository.
