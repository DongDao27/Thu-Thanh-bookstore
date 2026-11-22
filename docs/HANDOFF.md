# Bàn giao repository

GRADING-FIX đã hoàn tất. PR #26 đã merge; source candidate c9bd205 đã được kiểm trên clone mới từ GitHub, hosted CI và môi trường triển khai. VPS current trỏ `/srv/thu-thanh/releases/c9bd205`; Nginx, PHP-FPM và worker active. Release cũ 23842a5 được giữ để rollback.

Kiểm chứng: 181 backend tests / 2.193 assertions, coverage 91,37%; 22 HTTP auth checks; 16 frontend tests và build; 30 browser tests trên hosted CI. Demo seed local/testing tạo 24 sản phẩm/ảnh, không tài khoản mặc định. Sau activation, 11 kiểm tra HTTP live đạt; API guest trả 401 với mọi Accept đã kiểm qua cả API và frontend proxy.

Lighthouse mobile live hai lần: performance 92/94, CLS 0, LCP 3,4/3,0s, best practices 100. Baseline trước sửa là 53/55 và CLS 0,49. Báo cáo private: `output/grading-performance-2026-11-23.report.html`; bằng chứng khác ở `.runtime/grading-fix`. Đây là đo lab, không phải chứng nhận tải lớn hoặc bảo mật tuyệt đối.

Lỗi activation đầu tiên đã rollback an toàn. Cache Artisan thuộc deploy:deploy 0660 làm www-data không đọc được; script private đã sửa nhóm/quyền, kiểm boot bằng www-data trước switch, chờ readiness và ghi bước lỗi. Lần kích hoạt sau đạt.

Không cần chạy lại activation. Không có công việc tiếp theo bắt buộc trong phạm vi GRADING-FIX. Issue #25 được đóng sau kiểm live; các mục ngoài phạm vi hoặc chưa nghiệm thu khác không tự coi là hoàn tất. Điểm chính thức do giảng viên quyết định; Next.js được chấp nhận theo xác nhận chủ dự án. Figma/video không thuộc bản nộp môn web (D120).

Giữ APP_KEY, .env, database, uploads, private transfer và backup ngoài Git. VNPAY vẫn Sandbox; không gọi QueryDr 99, không bật scheduler tài chính theo D119. Backup/offsite laptop cần mạng và phiên hoạt động; giám sát 24/7 và tải lớn chưa được chứng minh. Origin là repo nhà sách; không dùng lại repo Payment cũ hoặc force push.
