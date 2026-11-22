# Phạm vi sản phẩm

Nhà sách Thu Thanh cung cấp cửa hàng trực tuyến và công cụ quản lý sản phẩm, tồn kho, đơn đặt hàng và doanh thu của hệ thống.

## Vai trò

- Guest: xem/tìm sản phẩm và danh mục.
- Customer: tài khoản, giỏ, mua ngay, đặt nhận tại cửa hàng hoặc giao hàng, thanh toán và theo dõi đơn của mình.
- Admin: catalog/ảnh/danh mục, tồn kho, đơn, đối soát và báo cáo; không đăng ký admin công khai.

## Quy tắc chính

Laravel là nguồn quyết định quyền, tiền, tồn và trạng thái. Báo giá có hạn, snapshot giá/đơn bất biến, kiểm tồn trong transaction và idempotency bảo vệ thao tác lặp. Session cookie + CSRF cho website first-party. Không lưu bearer token trong localStorage.

Thanh toán được nghiệm thu theo luồng VNPAY Sandbox/IPN; chưa kích hoạt tiền thật. Không tự đổi đơn thành paid từ URL quay về. Không hoàn tiền/đổi trả trong phạm vi hiện tại. Báo cáo không đại diện toàn bộ doanh thu bán trực tiếp tại cửa hàng.

Frontend Next.js App Router dùng JavaScript/JSX. PostgreSQL 17 là database hiện hành. Giao diện giữ nhận diện xanh–trắng; source thiết kế ở `design/figma`.

Chi tiết hợp đồng tại API, DATABASE và DOMAIN-RULES. Không thêm chức năng ngoài phạm vi từ việc thay vị trí file hoặc rút gọn tài liệu.
