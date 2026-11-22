# Nguồn tài nguyên

Nguồn URL, SHA-256, kích thước ảnh banner, logo nhãn hàng và font được lưu trong [ASSET-SOURCES.json](ASSET-SOURCES.json). Ảnh sản phẩm và đối chiếu SKU ở [PRODUCT-IMAGE-SOURCES.json](PRODUCT-IMAGE-SOURCES.json); gallery tại `backend/database/data/product-gallery.json`.

Ảnh đã được tải về và tự host, không tự thay bìa/SKU hoặc gán giá/khuyến mãi từ nội dung banner. Logo Nhà sách Thu Thanh là tài nguyên riêng; logo nhãn hàng không tuyên bố quan hệ đối tác hoặc chứng nhận.

Font Nunito Sans tự host; giữ [giấy phép OFL](../frontend/src/app/fonts/OFL.txt). Giữ giấy phép icon trong [source thiết kế](../design/figma/ICONS-LICENSE.txt).

Bảng sản phẩm gốc tại `data/catalog/`. Source Figma tại `design/figma/`; ảnh nhúng plugin được kiểm hash và pixel so với tài nguyên web. Chạy `python3 scripts/verify-store-assets.py` để kiểm nguồn ảnh; cần Pillow.
