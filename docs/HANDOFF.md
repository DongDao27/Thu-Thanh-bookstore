# Bàn giao repository

Đã hoàn tất REPO-CLEANUP: source ứng dụng đồng bộ bản main đã kiểm, README gọn, tài liệu chia commit/hướng dẫn hoàn thiện cũ đã bỏ; thiết kế ở design/figma và workbook ở data/catalog. code.js/ZIP Figma là output build, không theo dõi trong Git. Source công cụ vận hành ở deploy/ops.

Kiểm local: backend179tests/2180assertions, E2E29/29, frontend16/16/check/build; repository14docs/22links/OpenAPI, FigmaV41offline214screens/3287routes và nguồn ảnh136originals/35PNG đều đạt. Workbook giữ nguyên byte. Source commit b2e04f23290ea6480864a762428ead061118466b đã được xuất bản vào repo nhà sách; theo dõi hosted CI trước khi fast-forward main.

Origin: git@github.com:DongDao27/Thu-Thanh-bookstore.git. Local main dựa trên origin/main của repo này; lịch sử Payment trước đó giữ ở archive/payment-main và snapshot private. Không force push, không sửa lịch sử repo đích. Bản sao source/index/config trước dọn ở .runtime/repository-cleanup; không xuất bản.

Tiếp theo chỉ xử lý yêu cầu mới. Không điều tra QueryDr99, không sửa thanh toán/đơn/scheduler theo D119. Website hiện dùng Sandbox; không gọi nó là thanh toán tiền thật hoặc chứng nhận tải lớn.

Giữ .env, APP_KEY, uploads, runtime và backup private. Offsite laptop cần phiên hoạt động và mạng; không phải monitoring/cloud24/7. Phần kiểm tải đại diện/24x7 nằm ngoài đợt dọn này, không tự đánh dấu đạt.
