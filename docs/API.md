# API Nhà sách Thu Thanh

## D110 — Đăng ký bản tin bằng email

`POST /api/v1/newsletter/subscriptions` nhận duy nhất `{ "email": "reader@example.com" }`. Không yêu cầu checkbox hoặc payload consent. Hành động gửi form đăng ký ghi `consented_at` hiện có ở lần đầu; email normalize, unique/idempotent và response202 giống nhau khi đã đăng ký, không lộ email/id. CSRF và throttle10/phút giữ nguyên; field lạ/email sai trả422. Đăng ký mới queue email xác nhận qua mailer hiện hành (Resend trên VPS) sau commit; job được mã hóa và retry tối đa3 lần. Email trùng không gửi lại; không gửi campaign hoặc backfill danh sách cũ. D110 thay checkbox của D43, không đổi schema/lịch sử.

## D104 — Mua ngay riêng

`POST /api/v1/checkout/quotes` nhận thêm `buy_now: {product_id, quantity}` tùy chọn. Laravel kiểm product/quantity1–99, availability/giá/phí; không nhận giá từ client. Quote có thể tạo khi giỏ trống; chỉ chọn đúng món và số lượng đó. `POST /orders` vẫn dùng quote_id và Idempotency-Key; đơn trực tiếp không xóa/thay món trong giỏ đã lưu. Giỏ thay đổi không làm mất hiệu lực direct quote, nhưng giá/tồn/hạn/ownership vẫn kiểm khi tạo đơn. Không đổi schema. Không gửi buy_now giữ hợp đồng checkout toàn giỏ cũ. Hủy đơn trực tiếp unpaid vẫn gộp món đơn vào giỏ một lần theo D68; giao diện xác nhận giải thích việc gộp. Return VNPAY không tự ghi paid.

## D96 — Ảnh gallery

Không đổi hợp đồng API. `images` dùng public storage hiện có, sắp theo sort_order/id; ảnh phụ nhập sau ảnh cũ nên giữ ảnh chính. Trang chi tiết chọn thumbnail, có aria-pressed/alt và dùng kích thước ảnh responsive; lệnh nhập và provenance ở [ngu?n v? d? li?u asset](ASSET-SOURCES.md).

## D92 — Trang chi tiết và mô tả sách

Frontend `/products/{id}` dùng `GET /api/v1/products/{product}` hiện có (ID số). ProductResource đã có `description`, `images`, category, SKU và giá/tồn; không thêm endpoint hay đổi hợp đồng API. Mô tả hiển thị bằng text React được escape, không render HTML từ catalog. Best seller/quickview dùng cùng nội dung. Giá và tồn khi mua vẫn do Laravel kiểm lại. Nội dung 82 sách và lệnh cập nhật an toàn được ghi tại [ngu?n v? d? li?u asset](ASSET-SOURCES.md).

## D91 — Token API, tài liệu tự sinh và vận hành

Website giữ session/CSRF. Nhóm session từ chối Authorization Bearer; external clients dùng riêng /api/v1/integrations, yêu cầu PAT thật và abilities, cookie không thay thế PAT. Mọi response cá nhân no-store. API token có rate limit60/phút, cấp token5/phút.

| Method/path (sau /api/v1) | Hợp đồng |
| --- | --- |
| GET /auth/tokens | Session: metadata50token mới nhất của mình; không trả hash/plaintext |
| POST /auth/tokens | Session+CSRF, body name/abilities/days/password; current password, days1–30, tối đa10token còn hiệu lực;201trả token plain một lần |
| DELETE /auth/tokens/{token} | Session+CSRF, chỉ token mình,204; token người khác404 |
| GET /integrations/account | PAT catalog:read; chỉ id/name/role của chủ token |
| GET /integrations/products | PAT catalog:read; cùng query/filter/resource catalog public |
| POST /integrations/products | PAT catalog:write **và** admin; dùng ProductRequest/CatalogService/tồn như API admin hiện có |
| GET /integrations/orders/{order} | PAT orders:read **và** customer; chỉ đơn mình, không thuộc mình404 |
| GET /admin/operations | Session admin, enabled/host snapshot và50alert đã che dữ liệu |
| GET /integrations/operations | PAT operations:read **và** admin, cùng snapshot |

Customer chỉ được cấp catalog:read/orders:read; admin catalog:read/catalog:write/operations:read. Không cho wildcard; scope không thay role/ownership. Cấp token khóa user và kiểm lại password để không vượt qua recovery đồng thời. Password recovery/admin password recovery thu hồi PAT của đúng tài khoản; logout website chỉ kết thúc session, external token cần revoke riêng. Expired/revoked401, thiếu scope/role hoặc dùng sai nhóm API403.

Scramble viewer `/docs/api`, JSON `/docs/api.json`: chỉ admin session, kể cả local. Sinh từ routes/controllers/validation với security scheme phân biệt cookie+CSRF và Bearer; export `php artisan scramble:export --env=testing --path=storage/logs/openapi.generated.json`. Bản OpenAPI trong docs vẫn là contract kiểm khớp source; bản generated là artifact từ Scramble, không chứa dữ liệu người dùng. Website Next proxy /docs và /telescope về Laravel; quyền vẫn được Laravel kiểm.

Telescope `/telescope` chỉ mở sau khi bật TELESCOPE_ENABLED và migrate bảng mới, luôn chỉ admin session. Không lưu request/response body/header/cookies, SQL/bindings, exception message/trace arguments hoặc danh tính người dùng. Chỉ giữ method/status/duration/memory, mẫu URI, timing query và tên lỗi. Job watcher tắt vì failed-job updates của package có thể vượt entry filter; queue vẫn được kiểm bằng worker smoke/failed_jobs. Chi tiết bật local và nghiệm thu trong [vận hành và bảo mật](SECURITY-PERFORMANCE.md).

Prefix **/api/v1**, đặc tả máy đọc ở [openapi.json](openapi.json). ApiRouteContractTest đối chiếu route/method thực tế; các test resource/validation/quyền kiểm hành vi. Kết quả này không thay nghiệm thu merchant VNPAY thật. Giao diện khách/admin do Next.js, nghiệp vụ và dữ liệu do Laravel.

## Quy ước chung

- Thành công: `data`, `meta` gồm server_time UTC, request_id; danh sách có meta.pagination(page,per_page,total,last_page).
- Lỗi: `error` gồm code/message/fields và meta; không lộ stack, SQL hoặc secret. 401 chưa login,403 sai quyền,404 không tồn tại/không được xem,409 xung đột nghiệp vụ,419 CSRF,422 validation,429 throttle,503 thiếu cấu hình/provider.
- Phân trang page≥1, per_page theo giới hạn từng endpoint; filter/sort whitelist, thứ tự ổn định. Lịch sử admin tối đa50/mặc định15; frontend lấy10 để đọc gọn trên mobile.
- Session cookie và CSRF cho first-party; không bearer token trong localStorage. Mutation không tự retry khi lỗi mạng mơ hồ. Idempotency-Key bắt buộc cho tạo đơn/payment attempt; payload khác cùng key trả409.
- VND là số nguyên trong API; chỉ adapter VNPAY đổi sang đơn vịx100. API riêng private/no-store; IPN dùng ACK của VNPAY, không envelope thường.

## Auth

GET /sanctum/csrf-cookie nằm ngoài prefix; browser nhận XSRF-TOKEN, gửi X-XSRF-TOKEN và cookie. Giữ cùng hostname và port allowlist.

| Method | Endpoint              | Ghi chú                                                                                                                                                                                          |
| ------ | --------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| POST   | /auth/register        | name,email hoặc phone,password,password_confirmation; cấm role/admin fields                                                                                                                      |
| POST   | /auth/login           | identifier/password; regenerate session                                                                                                                                                          |
| POST   | /auth/logout          | Hủy session và tái tạo CSRF                                                                                                                                                                      |
| GET    | /auth/me              | Chỉ trường user an toàn                                                                                                                                                                          |
| PATCH  | /auth/profile         | Chính mình; name,email,phone (ít nhất một email/phone); current_password khi đổi định danh; chuẩn hóa/unique, cấm role/user_id/password; đổi email xóa token reset cũ và chưa coi là đã xác minh |
| POST   | /auth/forgot-password | Generic202; email khách, CSRF/throttle, không lộ tài khoản tồn tại                                                                                                                               |
| POST   | /auth/reset-password  | Token hash/hết hạn/một lần; đổi mật khẩu và thu hồi phiên cũ                                                                                                                                     |

Mật khẩu12–128ký tự, không trim/log; email/phone normalize server, phoneVN vềE.164. Recovery email đã có local, cần gửi mail Resend thật/worker khi mở dùng thật. Phone-only chưa có SMS; admin không dùng public reset.

## Catalog, giỏ và đơn

| Method            | Endpoint                        | Quyền và dữ liệu                                                                                                                                 |
| ----------------- | ------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------ |
| GET               | /categories                     | Public, danh mục active                                                                                                                          |
| GET               | /products, /products/{product}  | Public; lọc/tìm/sort/page, dữ liệu xem nhanh; q tìm tên có/không dấu, `%`, `_`, `!` được hiểu là ký tự thật. Gợi ý dùng per_page=6 cùng endpoint |
| GET               | /storefront                     | Snapshot công khai, không session/Set-Cookie, cache20giây                                                                                        |
| GET               | /home                           | Feed có thể cá nhân hóa; không cache chung                                                                                                       |
| GET               | /cart                           | Customer, giỏ của mình                                                                                                                           |
| POST/PATCH/DELETE | /cart/items, /cart/items/{item} | Thêm/cập nhật/xóa; thêm giỏ chưa giữ tồn                                                                                                         |
| POST              | /checkout/quotes                | Customer; cách nhận,tên,phone,địa chỉ nếu giao                                                                                                   |
| POST              | /orders                         | quote_id và Idempotency-Key; lần đầu201,replay200/meta.replayed                                                                                  |
| GET               | /orders, /orders/{order}        | Customer owner; order là order_code                                                                                                              |
| POST              | /orders/{order}/cancel          | Chỉ đơn chưa thanh toán và đủ điều kiện; D68 phục hồi sản phẩm về giỏ một lần cùng transaction hủy/release; response Order giữ nguyên            |
| POST              | /orders/{order}/reorder         | Kiểm giá/tồn mới, cập nhật giỏ; không tự tạo đơn                                                                                                 |

Quote trả snapshot giá,cart_fingerprint,pricing_version,expires_at và totals. Checkout khóa/kiểm lại dữ liệu; giá/giỏ đổi hoặc hết hạn trả409 để khách xác nhận lại. Delivery25.000đ,pickup0đ; hàng/giá/giảm/ship do server quyết định. Tạo đơn và giữ tồn cùng transaction; clear giỏ sau tạo thành công. Không đổi trả/hoàn tiền.

## Thanh toán VNPAY

| Method | Endpoint                         | Hành vi                                                          |
| ------ | -------------------------------- | ---------------------------------------------------------------- |
| POST   | /orders/{order}/payment-attempts | Customer owner; delivery/pickup còn hạn; Idempotency-Key         |
| GET    | /orders/{order}/payment-status   | Owner; trạng thái server, can_retry và reconciliation_required   |
| GET    | /payments/vnpay/return           | Public nhưng kiểm chữ ký, throttle; trả kết quả xác minh an toàn |
| GET    | /payments/vnpay/ipn              | Callback có chữ ký/merchant/reference/amount, ACK nhà cung cấp   |

PaymentAttempt trả id,order_code,status,expires_at,payment_url,qr_content,qr_kind,provider. Customer qr_content=null; admin pickup có QR mở checkout URL theo D84 bên dưới. Native bank-app merchant QR chưa tích hợp; không gọi QR mở URL là mã ngân hàng đã xác minh.

Return frontend tại `/payment/vnpay-return` gửi vnp_* về API. Chữ ký sai422; signed success chỉ kích hoạt query server→VNPAY ngoài khóa DB. Chỉ query/IPN đã xác minh mới phân bổ receipt/paid và trừ tồn một lần. Query chưa rõ giữ pending; signed failure cho retry khi còn hạn. Reload/retry không gia hạn. Duplicate/late/mismatch không ghi nhận doanh số hoặc trừ tồn hai lần; các ca cần đối soát giữ luật backend.

D55 cho phép Sandbox trong env private với merchant của chủ dự án, code/env mẫu vẫn deferred mặc định. Luồng Sandbox/IPN đã được chủ dự án kiểm tra thành công. Production, QR tại quầy và IPN public vẫn cần cấu hình/nghiệm thu riêng; xem [cấu hình môi trường](../README.md).

## Admin

Mọi endpoint /admin yêu cầu auth + role admin; khách/guest không đọc được lịch sử.

| Method                | Endpoint                                                                     | Dữ liệu                                                                                                                                                        |
| --------------------- | ---------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| GET/POST/PATCH        | /admin/categories, /admin/categories/{category}                              | Danh mục                                                                                                                                                       |
| GET/POST/PATCH/DELETE | /admin/products, /admin/products/{product}                                   | GET list/detail (kể cả sản phẩm ẩn); DELETE archive, PATCH restore, giữ snapshot cũ. POST không bắt buộc slug, server tự sinh; PATCH bỏ qua slug sẽ giữ nguyên |
| POST/DELETE           | /admin/products/{product}/images, /admin/products/{product}/images/{image}   | Ảnh đúng product, validate upload                                                                                                                              |
| GET                   | /admin/inventory                                                             | Lọc category/q/page; giá bìa/giá bán, tồn vật lý/giữ/có thể bán                                                                                                |
| POST                  | /admin/inventory/{product}/adjustments                                       | delta,reason,request_key; ledger/idempotency                                                                                                                   |
| GET                   | /admin/inventory/{product}/movements                                         | Ledger đúng product, phân trang, actor/order eager-load                                                                                                        |
| GET                   | /admin/orders, /admin/orders/{order}                                         | Tìm/lọc/chi tiết đơn                                                                                                                                           |
| PATCH                 | /admin/orders/{order}/fulfillment                                            | target_status/version; chặn sai luồng/unpaid                                                                                                                   |
| GET                   | /admin/orders/{order}/history                                                | type=attempts/receipts/statuses; mỗi loại phân trang đầy đủ                                                                                                    |
| POST/GET              | /admin/orders/{order}/payment-attempts, /admin/orders/{order}/payment-status | Admin QR vẫn chặn khi chưa có adapter merchant                                                                                                                 |
| GET                   | /admin/dashboard, /admin/revenue                                             | Tổng quan/doanh thu sau giảm; ship riêng, from/to/granularity/CSV                                                                                              |
| GET/PATCH             | /admin/reconciliation-cases, /admin/reconciliation-cases/{case}              | Đối soát, version/evidence; không manual-paid                                                                                                                  |
| POST                  | /admin/orders/{order}/refund-records                                         | Cấm ghi mới theo chính sách không hoàn tiền, trả403                                                                                                            |

History chỉ trả trường whitelist: hoạt động/trạng thái,số tiền,mốc giờ,mã giao dịch hoặc transition. Không trả payload/chữ ký/payment URL/provider_reference/session/business_key. Mỗi truy vấn giới hạn số bản ghi và sắp created_at/iddesc; API lịch sử không thay schema hoặc ghi dữ liệu.

D62: admin create/PATCH is_bestseller để trưng bày; section=bestseller gồm sản phẩm có cờ hoặc paid/accepted, xếp sold_count thực giảm dần, cờ admin rồi ID. Landing tối đa10. Cờ không đổi doanh thu/sold_count; bỏ tick không xóa doanh số thực.

D83: Bán chạy trên /home và /storefront trả tối đa10 sản phẩm hợp lệ, giữ thứ tự sold_count thực rồi ghim quản trị; không tạo lượt bán. CTA card Mua ngay/thêm giỏ dùng POST /cart/items hiện hành, quantity1; giá/tồn/quyền vẫn do backend xác nhận.


### D84 — Thu tiền tại quầy (05/11/2026)
POST /api/v1/admin/orders/{order}/cash-payment, body rỗng, admin xác nhận đã thu đủ payable_total. Khóa order/products, chặn pending/unknown/reconciliation/expired/closed/delivery, cash attempt succeeded + accepted receipt, audit actor, consume tồn và completed/fulfilled atomically. Retry cash trả đơn cũ. Không schema mới.
Admin payment-attempts: qr_content là /api/v1/payments/vnpay/checkout/{reference}, qr_kind=checkout_url. Public GET dùng reference ngẫu nhiên khó đoán, kiểm counter marker/pending/open/expiry rồi 302 no-store đến signed provider URL; không nhận amount từ client, không ghi paid. QR mở signed VNPAY checkout URL bằng camera; không phải native bank-app merchant QR. History counter_payment_requested gắn attempt_id; chỉ accepted IPN/query của attempt tại quầy tự hoàn tất pickup. Customer online không tự hoàn tất; Return không tự ghi paid. Không gia hạn, không thu cash khi VNPAY chưa rõ.
Khách chọn tại quầy giữ pickup unpaid/hạn hiện tại, về danh sách đơn. Đối tác chỉ landing. D84 thay quy định cũ chỉ VNPAY và QR503 cho QR mở trang checkout; native merchant QR chưa tích hợp.


## D105 — Private realtime / PostgreSQL
POST /api/v1/broadcasting/auth: cookie session+CSRF, throttle60/phút; socket_id dạng digits.digits, channel_name=private-orders.{order_code}; chỉ owner/admin có thể authorize, foreign/nonexistent channel403, guest401, sai input422, disabled503. Trả signature tại data.auth trong envelope data/meta, no-store. Không chấp nhận bearer thay session.
Pusher order.changed chỉ order_code/version để invalidate; client phải GET order API có ownership lại. Event queue sau commit, rollback không phát; không gửi địa chỉ/email/phone/amount. Reconnect/polling/manual GET đọc state thật, không tự POST thanh toán.
PostgreSQL17 thay MySQL; search không dấu qua unaccent/pg_trgm index. HTTP tiền/quyền/tồn và các response cũ giữ hợp đồng. Provider/live VPS acceptance chưa chạy.

26/11/2026: `/storefront` và `/home` bổ sung `data.catalog` (tối đa40 sản phẩm công khai mới nhất, cùng snapshot cache20giây). `data.new` vẫn tối đa12; recommendation của customer chỉ ở `/home` private. Frontend dùng `catalog` để chia feed, fallback `/products?sort=newest&per_page=40` khi backend cũ chưa cập nhật.
