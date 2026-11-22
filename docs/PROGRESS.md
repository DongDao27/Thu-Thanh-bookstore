# Tiến độ gần nhất

## 22/11/2026 — Rút gọn repository

Đã lưu snapshot private của lịch sử Git, index, config và 682 file làm việc trước khi dọn. Đồng bộ lại các bản sửa CI/ảnh/polling từ main23842a5; giữ nguyên logic thanh toán. Chuyển source thiết kế sang design/figma và workbook gốc sang data/catalog; cập nhật đường dẫn build/verifier. Bỏ checklist môn học, hướng dẫn hoàn thiện, chia commit và launcher PowerShell cũ. Viết lại README và checkpoint gọn.

Kiểm tra local đã đạt: 179 backend/2.180 assertions, 29 E2E, 16 frontend; build/check/import, 14 tài liệu/22 liên kết/OpenAPI, Figma V41 và nguồn ảnh đạt. Workbook di chuyển giữ nguyên byte. Không phát hiện secret/runtime trong các file chuẩn bị commit. Đã commit và push branch dựa trên main23842a5 của repo nhà sách. Source commit b2e04f2; cập nhật main bằng fast-forward sau khi hosted CI đạt. Bộ tài liệu kiểm chứng được giữ trong .runtime/repository-cleanup ngoài Git.

## Bằng chứng vận hành trước đợt dọn

Backend179tests/2180assertions, E2E29/29, frontend16/16/build đạt; main-domain72/72 và security7/7. SSH key-only và backup/restore mới đã kiểm. Chủ dự án chấp nhận Sandbox và yêu cầu không tiếp tục xử lý QueryDr99. Không có mutation thanh toán hoặc database trong đợt dọn repository.

## 22/11/2026 — Đối chiếu GitHub Project 4

Đã kiểm đủ 20 item thuộc repo nhà sách, đóng 13 issue kỹ thuật theo yêu cầu chủ dự án và giữ issue #2 đã đóng. Đồng bộ 14 mục vào Closed; 6 mục vào In progress. Các issue có ghi bằng chứng hiện hành, giữ riêng ghi chú lịch sử và không tự tick review của thành viên khác. Task tổng cập nhật checklist và giữ mở. Đọc lại API xác nhận cả 20 trạng thái khớp nhau.

Còn mở: #4 nghiệm thu canvas Figma; #12 QR tại quầy trên môi trường triển khai; #13 scheduler hết hạn đang tắt; #19 nghiên cứu người dùng; #20 slide/video và kiểm bản nộp; #21 task tổng. Vercel đã triển khai b287d3e thành công; curl tên miền chính/API trả 200. Không cần deploy lại chỉ vì đổi trạng thái Project. Không gọi QueryDr99.
