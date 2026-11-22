# Thiết kế Figma Thu Thanh

Source plugin thiết kế desktop/mobile và User Flow. Không phải ứng dụng production.

Build từ thư mục gốc: `python3 design/figma/build.py`. ZIP xuất tại `output/figma/THUTHANH-FIGMA-V41.zip`. Import `manifest.json` vào Figma Development, chạy plugin và chọn phạm vi cần dựng.

Kiểm tra offline: `node scripts/test-figma-clicon.cjs`. Dữ liệu catalog đối chiếu riêng có thể chỉ định bằng `THUTHANH_CATALOG_SOURCE`. Giấy phép icon: [ICONS-LICENSE.txt](ICONS-LICENSE.txt).

`code.js` và ZIP là file sinh tự động, không theo dõi trong Git; luôn build trước khi import plugin.
