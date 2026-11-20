# Chạy và bảo trì local — PostgreSQL / D108

Máy Windows hiện tại đã có dữ liệu cửa hàng và runtime PostgreSQL. PHP 8.4/pdo_pgsql, Composer 2, Node 22/npm và Python cần có trên PATH. Không chạy lại bootstrap hoặc sinh APP_KEY.

## 1. Khởi động database và API

Trong PowerShell từ gốc repo:

```powershell
cd D:\ThuThanh-WebDesign
python scripts/postgres-test-local.py
powershell -ExecutionPolicy Bypass -File scripts/start-backend.ps1
```

Tên script PostgreSQL giữ từ giai đoạn tạo môi trường test; hiện cluster này chứa main `thu_thanh`, test `thu_thanh_test`, restore-check và Sandbox ở các database riêng. Cổng database là **55432**, API **8000**. Script không reset dữ liệu hiện có. Giữ terminal API mở.

## 2. Khởi động website

Terminal thứ hai:

```powershell
cd D:\ThuThanh-WebDesign\frontend
powershell -ExecutionPolicy Bypass -File ..\scripts\start-frontend.ps1
```

Mở http://localhost:3000. Helper nhận ra Next của dự án đã giữ cổng và in URL để dùng lại; nếu cổng thuộc chương trình khác thì báo PID, không tự tắt. `npm run dev` vẫn dùng được khi cổng trống. Dùng `localhost` nhất quán để cookie/CSRF và Return URL hoạt động. `/admin` dành tài khoản quản trị đã có; không có mật khẩu mặc định mới. Sửa hồ sơ ở `/account`, Laravel kiểm mật khẩu hiện tại khi đổi email/số điện thoại.

Nếu gặp `EADDRINUSE ::1:3000`, kiểm cổng:

```powershell
Get-NetTCPConnection -State Listen -LocalPort 3000 | Select-Object LocalAddress,LocalPort,OwningProcess
```

Mở web đang chạy trước. Muốn restart, Ctrl+C tại terminal Next cũ rồi chạy helper lại. Nếu không còn terminal, dùng `Get-Process -Id <OwningProcess>` để xác định đúng tiến trình của dự án trước khi dừng; không tắt toàn bộ Node/PHP trên máy. Cổng 8000 cũng chỉ dành một backend tại một thời điểm.

D109: API client giới hạn GET trong 15 giây, kể cả lúc đọc response body. Nếu API bị treo, trang hiện lỗi và nút Thử lại thay vì loading vô hạn; auth read treo không giữ trang chủ mãi. Sau khi backend hoạt động lại, bấm Thử lại hoặc Ctrl+F5. Không tự retry thao tác đặt hàng/thanh toán.

## 3. Kiểm tra và tác vụ nền

Terminal mới tại `backend`:

```powershell
php artisan app:environment-check
php artisan queue:failed
php artisan schedule:list
```

Main phải báo PostgreSQL/database `thu_thanh`. Các lệnh kiểm cấu hình không chứng minh mail/provider thật đã hoạt động. Queue và scheduler chỉ bật ở môi trường bạn đang nghiệm thu, vì chúng thực sự xử lý email, hết hạn và đối soát đơn. Với main đang có đơn lịch sử chưa rõ provider, cần xử lý theo B07 trước khi tự chạy nền liên tục. Sandbox riêng có launcher ở mục 4; môi trường production dùng systemd trong [DEPLOYMENT](DEPLOYMENT.md).

## 4. Mở lại VNPAY Sandbox riêng

Workspace private `.runtime/sandbox-backend`, DB PostgreSQL `thu_thanh_sandbox`; key/merchant đã có trên máy, không nhập lại hoặc tạo lại. D107 đã đối chiếu 26 bảng, 137 sản phẩm, 9 đơn và 2 receipts lúc chuyển; số này không thay thế kiểm giao dịch mới.

```powershell
cd D:\ThuThanh-WebDesign
powershell -ExecutionPolicy Bypass -File scripts/sandbox.ps1 -Action status
```

Dừng backend chính bằng Ctrl+C trước khi dùng Sandbox cổng 8000. Lệnh sau khởi động API/worker/scheduler Sandbox và PostgreSQL, không tạo lại dữ liệu:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/sandbox.ps1 -Action start
```

Frontend vẫn ở localhost:3000, reload sau đổi backend. Khi xong:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/sandbox.ps1 -Action stop
```

Chỉ các dịch vụ Sandbox do launcher quản lý bị dừng; dữ liệu giữ nguyên. Sau đó chạy lại backend chính nếu cần. IPN phải dùng HTTPS public URL đã đăng ký đúng với VNPAY; Quick Tunnel đổi URL thì phải cập nhật phía merchant. Không đăng nhập/thanh toán qua một origin khác với Return URL đã cấu hình. Dùng thẻ Sandbox từ [VNPAY chính thức](https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html); không đánh dấu paid bằng tay. B07 còn QueryDr/expiry/reconciliation và hai đơn lịch sử chưa rõ kết quả. Signed Return đơn thuần chưa đủ chứng minh đã trả tiền.

## 5. Kiểm thử riêng, chạy tuần tự

Từ gốc repo:

```powershell
python scripts/check_pack.py
python scripts/verify-store-assets.py
python scripts/run-postgresql-check.py php backend/artisan test
python scripts/run-postgresql-check.py php scripts/auth-cookie-smoke.php
python scripts/run-postgresql-check.py php scripts/ops-smoke.php
```

Trong `frontend`:

```powershell
npm run check
npm run test:api
npm run build
```

E2E chạy sau PHPUnit, từ gốc repo: `python scripts/run-postgresql-check.py node scripts/e2e.mjs`. Lần đầu cài Chromium trong frontend bằng `npx playwright install chromium`. E2E dùng DBtest và server QA riêng; không trỏ test vào cửa hàng/Sandbox, không chạy đồng thời PHPUnit. `.env.testing` hiện tại đã dùng PostgreSQL riêng, nên cũng có thể chạy `php artisan test` trong backend. `check` là kiểm cú pháp/import JavaScript, không phải kiểm kiểu TypeScript.

## 6. Máy mới và dữ liệu

Clone source rồi `composer install` trong backend, `npm ci` trong frontend. Chuẩn bị PostgreSQL 17/PHP pdo_pgsql và `.env` private từ mẫu. Clone Git không có database/uploads/APP_KEY. Muốn giữ cửa hàng hiện có: chuyển dump PostgreSQL + uploads + APP_KEY qua kênh riêng, restore vào DB trống rồi kiểm, theo [DEPLOYMENT](DEPLOYMENT.md). Máy mới không có runtime portable sẽ phải cài hoặc cung cấp PostgreSQL binaries trước khi dùng helper Windows. Không copy `.env` local/debug lên production.

## Tìm đúng nơi sửa

Các đường dẫn sau tính từ `frontend/`:

| Muốn sửa | File/thư mục |
| --- | --- |
| Trang chủ, số sản phẩm | `src/components/home-storefront.jsx`, `src/lib/home-collections.js` |
| Catalog, lọc, tìm, phân trang | `src/app/products/page.jsx` |
| Thẻ và nút mua | `src/components/product-card.jsx`, `product-actions.jsx` |
| Chi tiết/ảnh/mô tả | `src/components/product-detail.jsx`, `product-picture.jsx`, `product-description.jsx` |
| Header/footer/menu | `src/components/store-shell.jsx` |
| Auth/trạng thái dùng chung | `src/components/store-provider.jsx` |
| Quản trị | `src/app/admin/` |
| Giỏ/checkout/đơn | `src/app/cart/`, `src/app/checkout/`, `src/app/orders/` |
| HTTP/CSRF | `src/lib/api-client.js` |
| Realtime/polling | `src/lib/order-realtime.js`, `src/lib/use-order-polling.js` |
| CSS/font | `src/app/styles/index.css`, `src/app/fonts/` |
| Test | `tests/api-client.test.js`, `e2e/store.spec.js` |

Tiền/tồn/quyền do Laravel quyết định. Giữ stylesheet hiện có, không thêm lớp override cuối file để che lỗi. Desktop 5 cột, catalog 25/trang, trang chủ 10 mới + 15 gợi ý tối đa. Hợp đồng ở [API](API.md), nguồn ảnh ở [ASSET-SOURCES](ASSET-SOURCES.md).

Figma hiện hành **V40**: [import manifest rồi Run/Present](../thu-thanh-figma-clicon/HUONG-DAN.md). Resend/Pusher cấu hình thật trên VPS theo [hướng dẫn triển khai](DEPLOYMENT.md); local/test dùng mail array/log và không tự gửi mail thật.
