# Import và cập nhật Figma — Thu Thanh V41

V41 sửa mobile và User Flow theo phản hồi ngày21/11/2026. Manifest vẫn dùng ID plugin cũ để nhận diện các bản đã dựng. Không cần thay cấu hình mạng hoặc nhập API key.

## Cập nhật file Figma hiện tại

1. Giải nén `THUTHANH-FIGMA-V41.zip` vào thư mục riêng, giữ đủ5file.
2. Figma → Plugins → Development → Import plugin from manifest → chọn `manifest.json` trong bản V41. Nếu đã import bản cũ, chọn lại manifest bản mới để tránh chạy nhầm code.
3. Mở file Thu Thanh cần sửa; chạy plugin. Trong **Phạm vi cập nhật**, giữ lựa chọn **Mobile + User Flow (giữ nguyên trang desktop)**.
4. Nhấn **Cập nhật thiết kế**, chờ thông báo thành công. Chế độ này giữ nguyên các node trên trang desktop, cập nhật trang mobile và Landing page có8UserFlow. Với file mới chưa có desktop, plugin sẽ tạo đủ hai nền tảng.
5. Dùng nút **Điện thoại** để mở màn đầu, hoặc **User Flow** để tới sơ đồ trên Landing. Present và kiểm các thao tác trong checklist dưới.

Plugin thay các node do plugin quản lý khi dựng xong; nên nhân bản file trước nếu đã sửa tay nhiều. Lỗi hoặc nhấn Dừng giữ bản trước; ảnh sản phẩm sửa tay và node ngoài vùng plugin được bảo toàn. Không chạy đồng thời hai bản plugin.

## Mobile V41

- Bốn tab Trang chủ/Danh mục/Giỏ hàng/Tài khoản luôn hiện ở màn thường. Hộp thoại mở trên màn nền và giữ đóng/quay lại theo ngữ cảnh.
- Header112px, điều hướng80px ở cuối khung874px. Trang có hành động mua dùng thêm72px phía trên tab; nội dung cuộn có viewport riêng, không nằm phía sau CTA.
- Chi tiết: nút icon **Thêm vào giỏ hàng** và chữ **Mua ngay**; giữ sản phẩm/số lượng qua đăng nhập, Mua ngay không xóa giỏ đã lưu.
- Giỏ hàng gọn: hàng/số lượng/xóa, tạm tính và chú thích phí một lần; nút **Thanh toán**. Chọn giao/nhận tại cửa hàng ở bước thông tin.
- Form checkout: **Tiếp tục**; bước xem lại chỉ đọc có **Thay đổi** và **Đặt hàng/Đặt trước**. Phí và tổng tiền vẫn đúng theo loại đơn.
- Không dùng ghi chú kỹ thuật hoặc marketing thừa thay thông tin sản phẩm/trạng thái cần thiết.

## User Flow trên Landing

8sơ đồ editable: khám phá, tài khoản, giỏ/Mua ngay, giao hàng/thanh toán, nhận tại cửa hàng, theo dõi/hủy, quản trị và bản tin. Hình tròn vàng bắt đầu; hình chữ nhật xanh nhạt là thao tác; hình thoi là quyết định; hình tròn xanh lá kết thúc. Mũi tên có nhãn và đường quay lại, không dùng chữ BƯỚC/ĐIỀU KIỆN trên mỗi node.

Luồng mô tả điều người dùng thấy/làm. Mua ngay giữ giỏ cũ; Return không tự quyết định đã trả; chưa rõ tiền thì chờ xác nhận/đối soát; không hủy hoặc trả tồn khi chưa xác nhận chưa thanh toán. Bản tin chỉ email + Đăng ký, không checkbox.

## Present: kiểm nhanh

1. Trang chủ → danh mục/tìm kiếm → chi tiết; tab luôn nhìn thấy khi cuộn.
2. Thêm giỏ/Mua ngay ở chi tiết → đăng nhập hoặc đăng ký → tiếp tục đúng lựa chọn, số lượng1–2; không ghi thêm hàng khi quay lại.
3. Giỏ → đổi số lượng/xóa → Thanh toán → giao/nhận tại quầy → Tiếp tục → xem lại/Thay đổi → Đặt hàng.
4. Kiểm thành công/thất bại/chờ/hết hạn/đối soát, đơn hàng và hủy; không biến trạng thái chưa rõ thành paid.
5. Tab/CTA không che trường cuối, giá hoặc hàng cuối; trên mỗi màn chỉ có một home indicator, dưới tab bar.

Đây là prototype35SKU mẫu; luồng mua tương tác minh họa bằng cuốn Toán1 đầu tiên, số lượng1–2. Các SKU khác không được gán nhầm giao dịch của cuốn đầu. Website thật dùng Laravel API và catalog hiện hành. QR là mẫu vô hại, không chuyển tiền; plugin không gọi email/provider/DB.

## Build và kiểm tra source

`python thu-thanh-figma-clicon/build.py` tạo ZIP V41 trong output/figma. `node scripts/test-figma-clicon.cjs` kiểm geometry, route, tiền/số lượng,8UserFlow, cập nhật riêng mobile giữ desktop, nâng bản V40 và rollback/cancel. Nếu source catalog không nằm ở backend/database/catalog.json trên máy kiểm, đặt biến test-only THUTHANH_CATALOG_SOURCE trỏ tới snapshot catalog đã xác minh; không đưa snapshot private vào Git.

Native Figma đã được sửa một phần qua MCP ngày21/11; giới hạn lượt gọi Starter chặn phần kiểm cuối/UserFlow. **Phải chạy V41 trong file để áp dụng đầy đủ bản mới**; preview/mock không thay nghiệm thu canvas thật. Xem docs/FIGMA-REVIEW.md để phân biệt bằng chứng native và offline.
