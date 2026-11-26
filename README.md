# Nhà sách Thu Thanh

Nền tảng bán sách và đồ dùng học tập, kết hợp cửa hàng trực tuyến với hệ thống quản trị sản phẩm, tồn kho, đơn hàng và doanh thu.

[![Backend CI](https://github.com/DongDao27/Thu-Thanh-bookstore/actions/workflows/backend.yml/badge.svg?branch=main)](https://github.com/DongDao27/Thu-Thanh-bookstore/actions/workflows/backend.yml)
[![Frontend CI](https://github.com/DongDao27/Thu-Thanh-bookstore/actions/workflows/frontend.yml/badge.svg?branch=main)](https://github.com/DongDao27/Thu-Thanh-bookstore/actions/workflows/frontend.yml)
[![E2E CI](https://github.com/DongDao27/Thu-Thanh-bookstore/actions/workflows/e2e.yml/badge.svg?branch=main)](https://github.com/DongDao27/Thu-Thanh-bookstore/actions/workflows/e2e.yml)

[Truy cập website](https://nhasachthuthanh.id.vn) · [Tài liệu API](docs/API.md) · [Thiết kế database](docs/DATABASE.md) · [Bảo mật và vận hành](docs/SECURITY-PERFORMANCE.md)

![Giao diện cửa hàng Thu Thanh với dữ liệu demo](docs/screenshots/storefront.jpg)

## Mục lục

- [Tính năng](#tính-năng)
- [Công nghệ và kiến trúc](#công-nghệ-và-kiến-trúc)
- [Cấu trúc repository](#cấu-trúc-repository)
- [Cài đặt local](#cài-đặt-local)
- [Kiểm thử](#kiểm-thử)
- [API và giám sát](#api-và-giám-sát)
- [Xử lý lỗi thường gặp](#xử-lý-lỗi-thường-gặp)
- [Tài liệu](#tài-liệu)
- [Thành viên](#thành-viên)

## Tính năng

### Cửa hàng trực tuyến

- Tìm kiếm và lọc sản phẩm theo danh mục, xem thông tin, giá và hình ảnh.
- Đăng ký, đăng nhập, cập nhật tài khoản và khôi phục mật khẩu qua email.
- Thêm giỏ hàng, mua ngay và tạo đơn với hai phương thức nhận hàng: tại cửa hàng hoặc giao tận nơi.
- Thanh toán qua VNPAY Sandbox, theo dõi trạng thái và lịch sử đơn hàng.
- Đăng ký nhận bản tin và nhận email xác nhận qua Resend.

### Quản trị

- Quản lý sản phẩm, danh mục và hình ảnh.
- Tra cứu tồn kho, điều chỉnh số lượng và theo dõi lịch sử biến động.
- Tra cứu, xử lý đơn hàng và theo dõi trạng thái thanh toán.
- Xem tổng quan hoạt động và báo cáo doanh thu.

<details>
<summary>Xem giao diện quản trị sản phẩm</summary>

![Giao diện quản trị sản phẩm với dữ liệu demo](docs/screenshots/admin-products.jpg)

</details>

## Công nghệ và kiến trúc

| Thành phần | Công nghệ |
|---|---|
| Giao diện | Next.js 16, React 19, JavaScript/JSX, Tailwind CSS |
| API và nghiệp vụ | Laravel 12, PHP 8.4 |
| Cơ sở dữ liệu | PostgreSQL 17 |
| Xác thực | Laravel Sanctum, session cookie và CSRF; PAT cho client tích hợp |
| Tác vụ nền | Laravel Queue với database driver |
| Email | Resend |
| Thanh toán | VNPAY Sandbox |
| Triển khai | Vercel, Ubuntu VPS, Nginx, PHP-FPM, Cloudflare |

Next.js cung cấp giao diện và chuyển tiếp các yêu cầu API đến Laravel. Laravel xử lý phân quyền, giá bán, tồn kho và trạng thái đơn hàng; PostgreSQL lưu trữ dữ liệu với các ràng buộc nhất quán.

- **Đơn hàng và tồn kho:** báo giá có thời hạn; transaction, row lock và idempotency bảo vệ các thao tác đồng thời.
- **Xác thực:** website dùng session cookie và CSRF; client tích hợp dùng token có phạm vi quyền, thời hạn và khả năng thu hồi.
- **Hiệu năng:** banner render phía server, ảnh tối ưu và tải theo viewport; phân trang, eager loading, indexes và cache cho dữ liệu cửa hàng công khai.
- **Tác vụ nền:** email được đưa vào queue sau commit; đăng ký bản tin trùng email không tạo thêm thư xác nhận.
- **Cập nhật đơn hàng:** polling mặc định; Pusher là tùy chọn.

## Cấu trúc repository

```text
backend/          Laravel API, nghiệp vụ, migrations, seeders và tests
frontend/         Next.js, giao diện, tài nguyên và browser tests
deploy/           Cấu hình triển khai, worker và công cụ vận hành
scripts/          Công cụ phát triển, kiểm thử và kiểm tra repository
data/catalog/     Dữ liệu sản phẩm gốc
design/figma/     Tài nguyên thiết kế và User Flow
docs/             Đặc tả API, database, nghiệp vụ và tài liệu kỹ thuật
```

## Cài đặt local

### 1. Chuẩn bị môi trường

Các ví dụ lệnh dùng Bash trên Linux, macOS hoặc Git Bash/WSL trên Windows.

| Công cụ | Phiên bản / yêu cầu |
|---|---|
| PHP | 8.4; PDO PostgreSQL, cURL, mbstring, XML, fileinfo và GD |
| Composer | 2.x |
| Node.js | 24 |
| PostgreSQL | 17 |
| Python | Python 3 với `venv`; dùng cho monitoring và kiểm tra repository |

### 2. Clone và cài dependencies

```bash
git clone https://github.com/DongDao27/Thu-Thanh-bookstore.git
cd Thu-Thanh-bookstore

composer install --working-dir=backend
npm ci --prefix frontend

python3 -m venv .venv
.venv/bin/python -m pip install -r scripts/monitoring-requirements.txt

cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env.local
```

Trên Windows, dùng `py -m venv .venv` và `.venv/Scripts/python.exe` thay cho `.venv/bin/python`. Các dịch vụ có thể chạy trực tiếp trên máy, không bắt buộc dùng Docker.

### 3. Cấu hình PostgreSQL và biến môi trường

Tạo role riêng và hai database dành cho demo và kiểm thử. Trên Ubuntu, chạy bằng tài khoản quản trị PostgreSQL:

```bash
sudo -u postgres createuser --pwprompt thu_thanh_local
sudo -u postgres createdb --owner=thu_thanh_local thu_thanh_demo
sudo -u postgres createdb --owner=thu_thanh_local thu_thanh_test
```

Trên Windows, có thể tạo role và database tương ứng bằng pgAdmin.

Cập nhật `backend/.env`:

```dotenv
APP_ENV=local
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=thu_thanh_demo
DB_USERNAME=thu_thanh_local
DB_PASSWORD="your-local-database-password"
```

Đặt `OPS_PYTHON_BINARY` thành đường dẫn tuyệt đối đến Python trong virtual environment, ví dụ `/path/Thu-Thanh-bookstore/.venv/bin/python`.

Trong `frontend/.env.local`, cấu hình:

```dotenv
LARAVEL_INTERNAL_URL=http://localhost:8000
```

### 4. Khởi tạo dữ liệu demo

```bash
cd backend
php artisan key:generate
php artisan migrate
php artisan db:seed --class=DemoCatalogSeeder
php artisan storage:link
php artisan admin:create
cd ..
```

`DemoCatalogSeeder` tạo **24 sản phẩm thuộc 6 danh mục**, có ảnh local và giá/tồn minh họa. Seeder chỉ chạy trong môi trường local/testing với database `thu_thanh_demo` hoặc `thu_thanh_test`; chạy lại giữ các chỉnh sửa giá và tồn đã có.

Lệnh `admin:create` tạo tài khoản quản trị tương tác bằng mật khẩu riêng. Seeder không tạo tài khoản mặc định hoặc đơn hàng; tài khoản khách hàng được đăng ký trên website.

Các lệnh khởi tạo ở trên dành cho database local mới. Với môi trường đã có dữ liệu, giữ nguyên `APP_KEY` và database hiện hành.

### 5. Chạy ứng dụng

Mở ba terminal từ thư mục gốc repository, mỗi terminal chạy một tiến trình:

```bash
# Terminal 1: Laravel API
php backend/artisan serve --host=localhost --port=8000

# Terminal 2: Next.js
npm run dev --prefix frontend

# Terminal 3: queue worker
php backend/artisan queue:work database --tries=5 --timeout=60
```

| Dịch vụ | Địa chỉ |
|---|---|
| Website | http://localhost:3000 |
| API | http://localhost:8000 |

Sử dụng hostname `localhost` nhất quán khi đăng nhập để session cookie và CSRF hoạt động đúng.

### 6. Email và thanh toán

Môi trường local mặc định dùng `MAIL_MAILER=log`; nội dung email được ghi vào `backend/storage/logs`. Để gửi qua Resend, cấu hình mailer, domain gửi đã xác minh và API key riêng theo các file môi trường mẫu, đồng thời giữ queue worker hoạt động.

Tích hợp VNPAY trên website hiện sử dụng **Sandbox**. Local có thể chạy giỏ hàng, tạo đơn và quản trị trước khi cấu hình merchant Sandbox. Khóa dịch vụ được đặt trong `.env` riêng của từng môi trường; không commit vào Git hoặc đưa vào biến `NEXT_PUBLIC_*`.

## Kiểm thử

### Cấu hình database test

```bash
cp backend/.env.testing.example backend/.env.testing
```

Cập nhật thông tin kết nối PostgreSQL trong `backend/.env.testing`, đặt `DB_DATABASE=thu_thanh_test` và `OPS_PYTHON_BINARY` thành đường dẫn tuyệt đối đến Python trong `.venv`.

```bash
(cd backend && php artisan key:generate --env=testing)
```

### Chạy các bộ kiểm tra

```bash
# Backend và yêu cầu môi trường PHP
(cd backend && composer check-platform-reqs && php artisan test)

# Frontend: API client, kiểm tra source và production build
npm run test:api --prefix frontend
npm run test:coverage --prefix frontend
npm run check --prefix frontend
npm run build --prefix frontend

# Browser E2E
npx --prefix frontend playwright install chromium
node scripts/e2e.mjs

# Tài liệu và cấu trúc repository
python3 scripts/check-repository.py
```

Chạy backend tests và E2E lần lượt vì chúng tạo lại các bảng trong database test. E2E sử dụng dữ liệu, tài khoản và provider fixture riêng; cần hai cổng `18082` và `3100` trống. Các bộ kiểm thử không yêu cầu khóa Resend hoặc VNPAY thật.

### Coverage frontend

Vitest/Testing Library kiểm component React; Playwright đo coverage trên build production được instrument riêng. Báo cáo trình duyệt tính toàn bộ `frontend/src/**/*.{js,jsx}`, kể cả file chưa chạy; CI bắt buộc **lines và statements đều >70%**. Coverage unit được lưu riêng, không cộng hai bản đồ statement khác nhau để tăng số đo.

```bash
npm run test:coverage --prefix frontend
FRONTEND_COVERAGE=1 node scripts/e2e.mjs
npm run coverage:report --prefix frontend
```

Mở `output/coverage/frontend/index.html` để xem từng file. Workflow Frontend lưu artifact `frontend-unit-coverage`; workflow E2E lưu báo cáo tổng trong `e2e-report`. Dữ liệu coverage local cần thuộc cùng một phiên source; trước lần đo lại, chuyển bản cũ ra khỏi `output/coverage/browser` để không trộn các phiên.

### Coverage backend và smoke tests

Cài Xdebug hoặc PCOV trước khi đo coverage:

```bash
(cd backend && XDEBUG_MODE=coverage php artisan test --coverage-clover=storage/logs/coverage.xml)
php scripts/check-coverage.php backend/storage/logs/coverage.xml

php scripts/auth-cookie-smoke.php
php scripts/ops-smoke.php
```

Hai smoke script đọc `.env.testing` và yêu cầu database `thu_thanh_test`. GitHub Actions chạy backend/coverage, frontend và browser E2E.

Kết quả kiểm chứng lịch sử ngày **23/11/2026**: **185 backend tests**, **2.221 assertions**, **91,43% statement coverage** và **30 browser E2E tests** đạt. Chi tiết được ghi tại [bảo mật và hiệu năng](docs/SECURITY-PERFORMANCE.md). Các kết quả mới và hướng dẫn quay minh chứng nằm trong [bằng chứng kiểm thử và demo](docs/DEMO-EVIDENCE.md).

Kiểm chứng local mới ngày **26/11/2026**: **188 backend tests / 2.312 assertions**, backend statement coverage local sau patch **91,72%** (CI **91,62%**); **15 React/unit tests**, **16 API-client tests**, **30 E2E và 1 kịch bản responsive bổ sung** đạt. Coverage Chromium tính toàn65file Next.js: **75,80% lines / 73,12% statements**; report unit được lưu riêng. Hosted CI của source7aa6a3b đạt **31/31 E2E**, **76,27% lines / 73,56% statements**; xem artifact trong workflow E2E.

## API và giám sát

- **REST API:** version `/api/v1`; tài liệu Scramble tại `/docs/api` và `/docs/api.json`, yêu cầu đăng nhập admin.
- **Telescope:** theo dõi request, query và lỗi; lọc dữ liệu nhạy cảm và giới hạn quyền truy cập.
- **Host monitoring:** thu thập CPU, RAM, disk và network qua Python; snapshot tại `/api/v1/admin/operations`, dành cho admin.
- **Cảnh báo:** theo ngưỡng và cooldown; có thể gửi qua queue khi cấu hình `OPS_ALERT_EMAIL`.
- **Logging:** log ứng dụng tại `backend/storage/logs`, kèm request ID để tra cứu.

Để sử dụng trên local, đặt `TELESCOPE_ENABLED=true`, `OPS_MONITORING_ENABLED=true` và cấu hình `OPS_PYTHON_BINARY`, sau đó chạy:

```bash
php backend/artisan config:clear
php backend/artisan ops:monitor
```

Đăng nhập admin để truy cập Telescope và các API giám sát. Có thể chạy `php backend/artisan schedule:work` trong một terminal riêng trên demo local để thực thi lịch expiry, monitoring và pruning. Cấu hình scheduler của môi trường triển khai được quản lý riêng theo [tài liệu vận hành](docs/SECURITY-PERFORMANCE.md).

## Xử lý lỗi thường gặp

| Hiện tượng | Cách kiểm tra |
|---|---|
| PHP báo thiếu extension hoặc `could not find driver` | Chạy `composer check-platform-reqs` trong `backend/`; bật PDO PostgreSQL và các extension PHP còn thiếu. |
| Không kết nối được PostgreSQL | Kiểm tra dịch vụ PostgreSQL, tên database, role và mật khẩu trong đúng file `.env` hoặc `.env.testing`. |
| Đăng nhập hoặc gửi form trả `419` | Truy cập `http://localhost:3000`, dùng hostname `localhost` nhất quán và kiểm tra `LARAVEL_INTERNAL_URL`; chạy `php backend/artisan config:clear` sau khi sửa cấu hình. |
| Không thấy sản phẩm demo hoặc ảnh | Kiểm tra database `thu_thanh_demo`, bước `DemoCatalogSeeder` và liên kết tạo bởi `php backend/artisan storage:link`. |
| Email chưa được xử lý | Kiểm tra queue worker; với `MAIL_MAILER=log`, xem nội dung trong `backend/storage/logs` thay vì hộp thư. |
| Monitoring báo thiếu `psutil` | Cài `scripts/monitoring-requirements.txt` vào `.venv` và đặt `OPS_PYTHON_BINARY` trỏ đúng interpreter đó. |

## Tài liệu

| Tài liệu | Nội dung |
|---|---|
| [API](docs/API.md) | Endpoints, xác thực và hợp đồng request/response |
| [OpenAPI](docs/openapi.json) | Đặc tả API |
| [Database và ERD](docs/DATABASE.md) | Quan hệ bảng, migrations và các ràng buộc dữ liệu |
| [Nghiệp vụ](docs/DOMAIN-RULES.md) | Quy tắc đơn hàng, tồn kho và thanh toán |
| [Bảo mật và vận hành](docs/SECURITY-PERFORMANCE.md) | Cấu hình triển khai, backup và kết quả kiểm chứng |
| [Nguồn tài nguyên](docs/ASSET-SOURCES.md) | Nguồn ảnh và tài nguyên giao diện |

## Thành viên

| Thành viên | GitHub | Phụ trách |
|---|---|---|
| Đào Ngọc Đông | [@DongDao27](https://github.com/DongDao27) | Frontend, thiết kế và tài liệu |
| Nguyễn Văn Thiện | [@thiennguyen230406](https://github.com/thiennguyen230406) | Backend, database, thanh toán và hạ tầng |
