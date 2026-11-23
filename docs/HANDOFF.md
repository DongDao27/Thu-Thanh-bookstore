# Bàn giao repository

SECURITY-NEWSLETTER (D121) đang chờ operator root activation. PR #28 đã merge; source candidate625a042 được chuẩn bị trên VPS, current vẫn c9bd205. Chưa đánh dấu các sửa quyền/defaultsite/worker hoặc email bản tin là đã hoạt động trên website. Issue #27 và Project item đang In progress.

Lệnh tiếp theo, trong SSH deploy@thu-thanh-staging:

```bash
sudo bash /srv/thu-thanh/private-transfer/security-newsletter/activate.sh
```

Script kiểm SHA/shared paths, backup service, quyền source (chỉ cache/storage cho PHP ghi), boot aswww-data, cài worker hạn quyền và default-deny Nginx, kiểm cấu hình, chuyển release và reload, kiểm HTTP và job probe không email/provider. Có rollback release/config khi lỗi. SSH không có sudo không mật khẩu; operator cần tự nhập riêng. Giữ cửa sổ SSH mở.

Kiểm chứng source: hosted185 backend tests/2.221 assertions,coverage91,43%,HTTPauth22,ops fixture,frontend và30browser tests đạt. Mail tests kiểm notification encrypted/afterCommit/rollback/duplicate, worker thật với mailarray, Resend SDK HTTPmock và lỗi provider. Đăng ký mới mới gửi email; email đã có không gửi lại. Chưa tự gửi testmail hoặc backfill danh sách cũ.

Security audit: PostgreSQL loopback, public22/80/443;9cổng nội bộ không kết nối được trong targetedprobe;10sensitiveHTTPchecks đạt,corskhôngreflectoriginlạ,CSRF419,directTLSnocert400. Dependency0advisories. Gitleaks lịch sử3finding làvăn bản;commitsmới0. SEC-01 medium quyền ghi source/worker vàSEC-02lowdefaultNginx fingerprinting được xác nhận, chờ áp dụng. Nginx candidate chỉ dùng testports để kiểm nonroot; rootcheck thật nằm trong activation. Báo cáo docs/SECURITY-PERFORMANCE.md, bằng chứng private.runtime/security-newsletter.

GRADING-FIX trước đó đã hoàn tất; frontend mobile92/94,CLS0,LCP3,4/3,0s. Đợt này không sửafrontend hoặc đo lại hiệu năng. Không phải chứng nhận pentest/tải lớn/điểm10.

Sau operator chạy: kiểm current625a042, services/workerUserwww-data/ProtectSystemstrict, sourcekhôngghiđược, rawIP404 vàapi200/401; xác nhận email bằng một đăng ký mới do chủ dự án thực hiện; cập nhật checkpoints vàđóngissue27 khi acceptance đạt. Giữenv,APP_KEY,database,uploads,backup vàoldrelease. Không seed/migrate/queryVNPAY/bậtfinancialscheduler.
