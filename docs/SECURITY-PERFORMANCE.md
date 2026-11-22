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

## Cải thiện theo rubric — 23/11/2026

API guest luôn trả JSON401 kể cả thiếu Accept hoặc gửi text/html; có regression HTTP thực. DemoCatalogSeeder chỉ cho local/testing và database thu_thanh_demo/thu_thanh_test; không tạo tài khoản, không thay giá/tồn khi chạy lại, không được dùng để reset dữ liệu thật.

Banner public được render phía server, không chờ auth/catalog. Các request độc lập chạy song song; query bổ sung giới hạn40 sản phẩm. Ảnh crop SVG giữ bố cục cũ nhưng dùng image optimizer và chỉ tải khi vào gần viewport; main giữ chiều cao tối thiểu để footer không nhảy trong lúc streaming. Không cache dữ liệu tài khoản/đơn vào public snapshot.

Kiểm local source cải thiện:181 backend/2.193assertions,coverage91,37%;22 kiểm HTTPcookie/CSRF/token/guest401;16 frontend tests và build/check đạt. Bộ29E2E hiện có đạt; regression banner khi API chậm và responsive ranking cũng đạt riêng. Hosted CI và số đo website sau triển khai đang được kiểm tiếp.

Hai lần baseline Lighthouse mobile trên website:53/55,LCP7,6/7,0s,CLS0,49,~5.599KiB. Bản production local tối ưu:90/100 performance,100/100 best practices,CLS0,~1.395KiB,LCP3,7s. Đây là lab mobile trên máy kiểm; không thay số đo website triển khai, không phải kiểm tải lớn hoặc chứng nhận bảo mật tuyệt đối.

Sau mergePR26: hostedCI backend/frontend/30E2E đã đạt trên candidatec9bd205. Frontendwebsite đã triển khai: hai lần Lighthousemobile92/94,bestpractices100,CLS0,LCP3,4/3,0s,payload1470/1386KiB. LCP vẫn cần theo dõi trên thiết bị/mạng thực; điểmlab không bảo đảm điểm môn học. Backendfix401 đang chờ operatorkíchhoạt/reloadPHP-FPM; không gọi nó là đã sửa trênwebsite trước khi kiểm live.
