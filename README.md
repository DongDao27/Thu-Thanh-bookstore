# Nhà sách Thu Thanh

Website bán sách và đồ dùng học tập, gồm cửa hàng trực tuyến và trang quản trị catalog, tồn kho, đơn hàng, thanh toán và doanh thu.

**Website:** [nhasachthuthanh.id.vn](https://nhasachthuthanh.id.vn) · **Repository:** [DongDao27/Thu-Thanh-bookstore](https://github.com/DongDao27/Thu-Thanh-bookstore)

## Chức năng

- Tìm kiếm, lọc danh mục, xem giá và ảnh sản phẩm.
- Đăng ký, đăng nhập, quản lý tài khoản và khôi phục mật khẩu qua email.
- Giỏ hàng, mua ngay, báo giá theo giá/tồn hiện tại; nhận tại cửa hàng hoặc giao hàng.
- Đặt hàng, thanh toán VNPAY Sandbox, xem trạng thái và lịch sử đơn.
- Quản trị sản phẩm, ảnh, danh mục, điều chỉnh tồn, xử lý đơn và báo cáo doanh thu.
- Cập nhật trạng thái bằng polling; Pusher là tùy chọn.

**Thanh toán hiện dùng Sandbox, chưa nhận tiền thật.** Laravel quyết định quyền, giá, tồn và trạng thái thanh toán; trình duyệt không tự xác nhận đã trả tiền.

## Công nghệ

| Thành phần | Công nghệ |
|---|---|
| Frontend | Next.js 16, React 19, JavaScript/JSX, Tailwind CSS |
| Backend | Laravel 12, PHP 8.4, session cookie và CSRF |
| Database | PostgreSQL 17 |
| Hạ tầng | Vercel, Ubuntu VPS, Nginx/PHP-FPM, Cloudflare |
| Email | Resend; queue database |

## Cấu trúc

```text
backend/          Laravel API, migration, dữ liệu chuẩn hóa và tests
frontend/         Next.js, tài nguyên giao diện, unit tests và E2E
```

- `deploy/`: cấu hình container/VPS, worker, scheduler và công cụ vận hành.
- `scripts/`: kiểm thử, kiểm tra dữ liệu và lệnh chạy local.
- `design/figma/`: source plugin thiết kế desktop/mobile và User Flow.
- `data/catalog/`: bảng sản phẩm gốc.
- `docs/`: đặc tả API, database, nghiệp vụ, nguồn ảnh và trạng thái dự án.

## Chạy local

Yêu cầu PHP 8.4 + Composer, Node.js 24 và PostgreSQL 17. Cài dependency và tạo file cấu hình **cho lần cài mới**:

```bash
composer install --working-dir=backend
npm ci --prefix frontend
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env.local
```

Điền kết nối PostgreSQL trong `backend/.env`. Trên database mới, chạy:

```bash
cd backend
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan admin:create
```

Giữ nguyên APP_KEY và dữ liệu khi tiếp tục một môi trường đã tồn tại. File `.env` chứa cấu hình riêng, không commit.

Mở terminal riêng cho từng tiến trình từ thư mục gốc:

```bash
php backend/artisan serve --host=localhost --port=8000
npm run dev --prefix frontend
php backend/artisan queue:work database
```

Web: `http://localhost:3000`; API: `http://localhost:8000`. Frontend chuyển tiếp API qua `LARAVEL_INTERNAL_URL`, không chứa khóa nhà cung cấp.

## Kiểm thử

Tests chỉ dùng database riêng `thu_thanh_test`, cấu hình bằng `backend/.env.testing` hoặc môi trường CI. Chạy backend và E2E lần lượt:

```bash
(cd backend && php artisan test)
npm run test:api --prefix frontend
npm run check --prefix frontend
npm run build --prefix frontend
node scripts/e2e.mjs
python3 scripts/check-repository.py
```

Các workflow trong `.github/workflows` kiểm backend, frontend và E2E trên GitHub Actions. Kết quả local gần nhất: **179 backend tests, 29 E2E, 16 frontend tests** đạt; kết quả của mỗi commit xem tại tab Actions.

## Tài liệu kỹ thuật

[API](docs/API.md) · [OpenAPI](docs/openapi.json) · [Database](docs/DATABASE.md) · [Nghiệp vụ](docs/DOMAIN-RULES.md) · [Vận hành và bảo mật](docs/SECURITY-PERFORMANCE.md) · [Nguồn tài nguyên](docs/ASSET-SOURCES.md)

## Thành viên

- **Đào Ngọc Đông — [DongDao27](https://github.com/DongDao27):** frontend, thiết kế và tài liệu.
- **Thiện:** backend, database, thanh toán và hạ tầng.
