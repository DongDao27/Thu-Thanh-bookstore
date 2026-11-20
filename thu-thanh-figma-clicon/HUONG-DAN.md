# Plugin Figma Thu Thanh V40 — email-only, mobile app và User Flow

Bản hiện hành theo D110. Đây là plugin offline sinh layer editable; không tự cập nhật canvas khi chỉ thay source/ZIP trên máy.

## Import và cập nhật

1. Giải nén `output/figma/THUTHANH-FIGMA-V40.zip` vào thư mục riêng.
2. Figma Design → Plugins → Development → Import plugin from manifest → chọn `manifest.json` vừa giải nén.
3. Mở plugin, kiểm panel ghi **V40**, chọn kịch bản thanh toán rồi bấm **Cập nhật thiết kế · Hai nền tảng**. Chờ hoàn tất; chỉ import không thay canvas.
4. Bấm **Landing page · Bản duyệt riêng** để xem desktop/app; bấm **User Flow · 8 luồng hoạt động** để đến section sơ đồ bên dưới.
5. Bấm Máy tính hoặc Điện thoại, chọn luồng rồi Present. Có thể sửa text/ảnh/Auto Layout bằng layer Figma.

Plugin nhận diện vùng V39/V38 và các bản cũ để thay đúng phần managed; giữ node ngoài vùng plugin và ảnh chủ dự án thay tay. Hủy/lỗi trước commit giữ bản trước. Ghi chú ngoài vùng plugin có thể làm vị trí bộ mới dịch sang bên để tránh chồng lấp.

## Thay đổi V40

- Newsletter desktop chỉ email + Đăng ký; bỏ dòng tick. Website/API cũng đổi email-only, giữ validation/CSRF/throttle/chống trùng; không gửi consent:true ngầm.
- Desktop giữ header76/lề2%/5cột/catalog25trang/Mới10/Gợi ý15 không trùng; chi tiết đầy đủ, auth giữ số lượng, Mua ngay riêng giữ giỏ, quote → review → đặt/thanh toán và trạng thái lỗi/pending/expiry/manual refresh.
- Mobile **402×874 là thiết kế app**: status bar, app bar/back,4tab và home indicator; không breadcrumb/footer/map/partner web. Home có search,6shortcut danh mục, feed cuộn ngang; catalog2cột. Không bê hai banner biên tập desktop sang app. Chưa triển khai ứng dụng native.
- Landing page có section **User Flow · Luồng hoạt động** với8sơ đồ editable có bước/điều kiện/mũi tên: khám phá; tài khoản; giỏ/Mua ngay; giao hàng/VNPAY; nhận tại quầy; theo dõi/hủy; quản trị; bản tin website. Đây là sơ đồ giải thích, không gọi provider.

## Dữ liệu và kiểm tra

35SKU lấy mẫu, không giới hạn137SKU website. Giá/đơn/QR là dữ liệu minh họa; Return không tự quyết định paid. Cả35ảnh mẫu PNG giữ nguyên pixel nguồn đã kiểm SHA256; ảnh thay tay được ưu tiên giữ khi chạy lại.

Từ root: `python thu-thanh-figma-clicon/build.py`, `node scripts/test-figma-clicon.cjs`, `python scripts/verify-store-assets.py`. Test kiểm geometry/containment/labels/navigation/auth/money/quantity,upgradeV38/manual photos/atomic rollback/cancel và kết quả thanh toán. Preview SVG ở `output/figma/preview`, manifest luồng ở `output/figma/LUONG-THIET-KE.json`.

Mock/preview browser không thay nghiệm thu Figma native: chủ dự án cần import/Run/Present. Không có cloud file link trong tác vụ này; chưa sửa hoặc kiểm một canvas cloud cụ thể.


## V40 — scrollbar, footer và mobile commerce

- Desktop bestseller: bỏ hai nút lên/xuống, có rail/thumb scrollbar và viewport overflow dọc. Trong Present, cuộn nội dung bằng con lăn/trackpad/vuốt. Rail/thumb trong thiết kế minh họa trạng thái đầu; không phải DOM scrollbar của trình duyệt. Website dùng scrollbar native thật và hỗ trợ bàn phím chọn hạng.
- Footer desktop: khoảng heading/link và dòng liên kết được thu gọn; không còn các hàng48px cách quá xa. Mobile app không có footer website.
- Mobile: search cố định trên app bar;6shortcut danh mục; thẻ sản phẩm292px,2cột; bestseller và sản phẩm mới dạngrail; gợi ý dạnggrid. Tab đang chọn có nền/nhấn màu rõ. Chỉ tham khảo pattern app mua sắm, giữ nhận diện Thu Thanh; không chép thương hiệu/campaign Shopee.
- Chi tiết có thanh Thêm giỏ/Mua ngay cố định và số lượng rõ. Giỏ/tổng tiền gọn; checkout có bước Thông tin/Xác nhận, màn review chỉ đọc và cho Thay đổi; CTA cố định tách khỏi tabbar. Đơn hàng dùng filter trạng thái/card rõ; tài khoản có shortcut Đơn hàng/Giỏ.
- Giữ auth/quantity/directcart/quote-review và quy tắc paid/tồn; không thay API/backend/dữ liệu. Tám UserFlow và bảo toàn ảnh sửa tay/rollback vẫn được kiểm; cần import V40 và Run để canvas thật đổi.
