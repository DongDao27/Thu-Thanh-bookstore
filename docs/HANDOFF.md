# Bàn giao repository

SECURITY-NEWSLETTER (D121): bản625a042 đã kích hoạt trên VPS. **Không chạy lại activate.sh.** PR #28 đã merge; chủ dự án đã xác nhận nhận email thử, issue #27/Project item được đóng sau nghiệm thu.

Đã kiểm live: current625a042; Nginx/PHP-FPM/worker active; workerUserwww-data,ProtectSystemstrict,ProtectHomeyes,NoNewPrivilegesyes. FilePHPapp group-write0, source index0640. RawIPHTTP404 không lộversion; rawIPHTTPS bị từ chối.6 HTTPchecks vàdeploymentconfig đạt.

Scriptprobe ban đầu dùng fileproof660 ownerwww-data màdeploy không đọc được; PHP handler trả0 gây thông báo passed sai. Đã sửa privatequeue-probe.php sangdatabasecache/catchThrowable, activation yêu cầuJSONPASS. Kiểm lại thực tế PASS workerPID55457/UID33 ở tiến trình riêng; invalidinput trảexit1. Không nới quyền file hoặc thêmdeploy vào groupPHP.

Kiểm source/hosted:185backend/2.221assertions,coverage91,43%,HTTPauth22,ops/frontend và30E2E đạt; SupabasePreview skipped. Dependency0advisories; Gitleaks commitsmới0,3finding lịch sử là văn bản. PostgreSQL loopback; chỉ public22/80/443 trong12 cổng đã kiểm. Đây không phải pentest đầy đủ hoặc cam kết điểm10.

Email xác nhận bản tin: đăng ký mới queue encrypted saucommit; trùngemail không gửi lại; rollback không gửi. Tests worker/database,CSRF/throttle vànativeResend mock đã đạt. Đã submit qua website với địa chỉ do chủ dự án cung cấp:HTTP202,job1→0,failed0. Chủ dự án đã xác nhận nhận được thư. Không tự gửi lại hoặc gửi hàng loạt cho danh sách cũ.

Không còn bước bắt buộc trong phạm vi task này. Issue27/Project Closed theo bằng chứng acceptance. Giữenv,APP_KEY,database,uploads,backup vàreleasec9bd205. Không seed/migrate/queryVNPAY/bậtfinancialscheduler. Frontend đợt này không thay đổi; Lighthouse92/94,CLS0 là số đo đợt trước.
