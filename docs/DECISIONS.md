# Decision register
Ngày tạo 21/10/2026. Nhãn: CONFIRMED từ trao đổi; ENGINEERING_DEFAULT đề xuất để code; OPEN cần chính sách/nguồn thật.
Không được tự chuyển OPEN/ENGINEERING_DEFAULT thành CONFIRMED bằng việc code xong.

| ID | Trạng thái | Quyết định/ảnh hưởng |
|---|---|---|
| D01 | CONFIRMED | Backend Laravel đầy đủ trước, frontend sau gate |
| D02 | CONFIRMED | Next.js frontend, Laravel backend/migration, MySQL database |
| D03 | CONFIRMED | Giao diện theo Figma người dùng, không infographic |
| D04 | CONFIRMED | Một admin cấp sẵn, login chung, chỉ customer register |
| D05 | CONFIRMED | Guest browse, login để add cart/checkout; hai cách nhận |
| D06 | CONFIRMED | Delivery giữ300s, pickup giữ86400s tính từ tạo đơn |
| D07 | CONFIRMED | VNPAY Sandbox trước, Production khi có quyền |
| D08 | ENGINEERING_DEFAULT | PHP8.4/Laravel12 bám lab nếu repo chưa có version khác; pin runtime được hỗ trợ |
| D09 | ENGINEERING_DEFAULT | TypeScript/Tailwind frontend nếu repo chưa chốt tương đương |
| D10 | ENGINEERING_DEFAULT | Sanctum cookie SPA/CSRF, không personal token cho browser chính |
| D11 | ENGINEERING_DEFAULT | Card→popup, không phát sinh product detail UI; xác nhận lại mapping khi có Figma |
| D12 | ENGINEERING_DEFAULT | Quote5m không reserve; qty1..99; order cap1 tỷVND; image5MiB jpeg/png/webp |
| D13 | ENGINEERING_DEFAULT | Curated hot/bestseller flags; mới theo created_at, chưa quảng cáo ranking doanh số thật |
| D14 | CONFIRMED 2026-10-21 | Phí giao mặc định 25.000đ/đơn delivery theo yêu cầu mới nhất; pickup 0đ. Địa chỉ free text MVP; chưa có giới hạn vùng giao được chỉ định. |
| D15 | CONFIRMED qua D44, 23/10/2026 | Doanh thu tiền hàng sau giảm giá của đơn paid có receipt accepted; phí giao hàng thống kê riêng. Không hỗ trợ đổi trả/hoàn tiền. |
| D16 | OPEN | Provider QR/capabilities/merchant creds; không fake integration success |
| D17 | OPEN | Recovery phone-only, verify email/phone policy; chưa có OTP service |
| D18 | OPEN (local source found) | Có plugin Figma thu-thanh-store-v12-free V15.1 trong workspace; chưa có file key/node/export hoặc nghiệm thu canvas thực tế. Source local được dùng để audit; không còn báo thiếu toàn bộ thiết kế. |
| D19 | OPEN | Hosting/domain/budget, retention, người phụ trách đối soát/hoàn tiền |
| D20 | ENGINEERING_DEFAULT | Không POS khách vãng lai; revenue chỉ giao dịch hệ thống, nếu muốn toàn cửa hàng phải mở scope |
| D21 | SUPERSEDED BY D30 | Đề xuất refund ledger trước đây được giữ làm lịch sử. Chính sách hiện tại không đổi trả/hoàn tiền; không bật route ghi refund. Giữ dữ liệu tài chính cũ để đối soát. |
| D22 | ENGINEERING_DEFAULT | Không tự copy profile/2FA/token manager/TALL frontend từ lab vào scope |

## Xử lý thiếu thông tin
- Không chặn B00–B06 chỉ vì D14/15/16/18 mở; có thể triển khai config và failure path với fixtures.
- Không giả lập để đánh dấu integration pass. B10 nghiêm ngặt ghi blocker nếu thiếu sandbox/QR.
- Không dùng tên user/email thật trong seed công khai. Giá trị demo có nhãn demo.
- UI yêu cầu mới đổi scope phải ghi ngày/lý do và sửa PRD/API/tests liên quan.

## Lịch sử mới
Chưa có quyết định mới sau khi phát hành gói. AI thêm bản ghi có nguồn và ngày thực tế.

| D23 | ENGINEERING_DEFAULT 2026-10-21 | Workspace root is application repository; root docs are canonical checkpoints, original build pack preserved as reference. PHP8.4.25/Laravel12.70.0/MySQL8.4.13 selected after audit. MySQL loopback3307 separates lab. |
| D24 | ENGINEERING_DEFAULT 2026-10-21 | First-party API explicitly uses web middleware for cookie+CSRF even without Origin; Sanctum session guard, no token issuance. CSRF419 normalized to CSRF_TOKEN_MISMATCH envelope; HTTP verified. |

| D25 | ENGINEERING_DEFAULT 2026-10-21 | Product read schema includes is_hot/is_bestseller so admin can read curated settings. Textbook slug sach-giao-khoa reserved against renaming; category edits increment affected product versions. Images max8192px each dimension; deletion uses retryable after-commit cleanup job and requires worker. |

| D26 | ENGINEERING_DEFAULT 2026-10-21 | Continue independent B08/B09 code while B07 merchant/QR verification remains external-blocked, as AGENTS permits; dependency-complete/done and backend gate remain unverified. PAY redirect adapter does not fabricate pickup QR; QR stays503 until merchant contract and dedicated adapter are available. |

| D27 | ENGINEERING_DEFAULT 2026-10-21 | Proposed report clarification: only refunds of accepted receipts reduce merchandise/shipping sales; refunds of duplicate/late/mismatch receipts are reported separately as unallocated_refunds. unallocated_receipts is gross received in period. Revenue stays503 until STORE_REVENUE_POLICY=approved_v1 reflects actual owner approval; refund write stays disabled by default. Private evidence references point to operator-supplied files under private evidence/. D15 đã được chủ dự án chốt tại D44; approved_v1 được bật, refund writes vẫn tắt. |

| D28 | ENGINEERING_DEFAULT 2026-10-21 | Argon2id default hashing preserves proposed12?128 character passwords without bcrypt72-byte truncation. Private local disk generic URL serving disabled; evidence download uses admin route. Return endpoint responds JSON validation-only200, not draft302. Exact provider ACK schema and private evidence route documented. |

## Yêu cầu mới của chủ dự án — 21/10/2026

| ID | Trạng thái | Quyết định và phạm vi |
|---|---|---|
| D29 | CONFIRMED | Delivery mặc định 25.000đ; pickup vẫn miễn phí giao. Server tính phí, client không được ghi đè. Cấu hình phí trống dùng mặc định 25.000đ; cấu hình sai không tự thành 0đ. |
| D30 | CONFIRMED | Không đổi trả/hoàn tiền sau bán. Chặn ghi refund ở backend kể cả khi biến env cũ bật; giữ ledger/evidence lịch sử và xử lý tiền bất thường bằng đối soát. Hủy đơn chưa thanh toán và giải phóng tồn theo luật cũ vẫn được phép. |
| D31 | CONFIRMED | Tích hợp VNPAY thật để sau. Chặn tạo yêu cầu thanh toán mới ở chế độ deferred; vẫn giữ adapter/tests và callback/đối soát cho bằng chứng đã có. Không yêu cầu merchant credentials ở lượt audit này và không đánh dấu Sandbox passed. |
| D32 | CONFIRMED source location | Thiết kế local ở thu-thanh-store-v12-free, manifest ghi V15.1 dù tên thư mục V12. Audit source/prototype trước; chưa có yêu cầu build Next.js trong lượt kiểm tra này. Canvas/visual chưa được xác minh. |

Tiêu chí trước thay đổi B10 audit: phí delivery mặc định25000/pickup0 và không nhận tổng từ client; refund403 theo chính sách dù env cũ true; deferred payment503 không sinh attempt; source Figma được kiểm syntax/manifest và ghi rõ lệch PRD; Composer/PHP extensions được kiểm thực tế; chạy regression trên MySQL riêng. Gate BACKEND_VERIFIED không được tự nâng thành passed do dời VNPAY.


## D33 — Đối chiếu rubric CSB35050 (OPEN, 21/10/2026)

Nguồn: hai DOCX Prep/Project Brief chủ dự án cung cấp; yêu cầu hiện tại chỉ kiểm tra đáp ứng và patterns. Rubric yêu cầu Blade/Livewire/Tailwind, Sanctum token/abilities, Scramble, Telescope, coverage >70%, slow-query/server alerts và GitHub topic branches/PR/issues. Kế hoạch Next.js, cookie-only API và payment thực deferred chưa tự đáp ứng các tiêu chí tương ứng. Chưa có bằng chứng giảng viên chấp thuận stack khác.

Ghi conflict để giải quyết trước code phần liên quan: chủ dự án chốt hướng UI theo rubric hoặc cung cấp chấp thuận Next.js; phạm vi API client token có thể bổ sung độc lập với cookie browser; external API thật còn cần nghiệm thu theo scope đã chọn. Chưa thay đổi PRD/ARCHITECTURE quyết định stack, chưa cài package hoặc tự bỏ deferral VNPAY. Báo cáo đầy đủ: COURSE-RUBRIC-AUDIT.md. Đây là phát hiện audit, không phải quyết định đổi nghiệp vụ/kiến trúc đã được chủ dự án chấp thuận.


## D34 — CONFIRMED 22/10/2026: Next.js + Laravel, bắt đầu frontend có phạm vi

Chủ dự án xác nhận giảng viên yêu cầu frontend Next.js/backend Laravel và yêu cầu bắt đầu build theo Figma + workbook Nha_sach_Thu_Thanh_San_pham_2026-10-21.xlsx. Stack UI conflict D33 được giải quyết theo thông tin mới này; không chuyển sang Blade/Livewire. Các yêu cầu môn khác chưa tự được miễn.

Yêu cầu bắt đầu frontend là ngoại lệ có phạm vi cho dependency B10: cho phép F00/F01/F02/F03 triển khai tăng dần trên backend local đã kiểm chứng B00–B06, dù gate payment/revenue thật chưa đạt. BACKEND_VERIFIED không chuyển passed; release/thanh toán thật vẫn khóa. VNPAY tiếp tục deferred, không fake paid/COD; report vẫn theo policy gate. Một admin/login chung, server quyết định quyền/tiền/tồn, session cookie/CSRF, không token localStorage.

Workbook đã có trong workspace không đồng nghĩa đã nhập MySQL:136 sản phẩm/ảnh,6 danh mục chính/18 con; giá bán và tồn cửa hàng chưa có. Chỉ audit/stage dữ liệu chưa biết; chờ câu trả lời về giá/tồn trước ghi dữ liệu giao dịch hoặc seed demo. Chưa tự mở rộng database danh mục con hoặc thay giá tham khảo thành giá bán. Cần link/node hoặc thiết kế được xác nhận để nghiệm thu Figma; source local không thay chứng cứ pixel match.

Acceptance trước triển khai: Next App Router/TypeScript/Tailwind build/typecheck đạt; API client cùng origin có cookie/CSRF và xử lý lỗi; dữ liệu Excel audit có kiểm duplicate/ảnh/giá/tồn; ảnh/source được bảo toàn; responsive, keyboard/focus/error/loading và hành vi auth/cart đúng API; chỉ gọi phần đạt đã kiểm thực tế. Không deploy hoặc sửa prototype gốc.


## D35 — CONFIRMED 22/10/2026: workbook cập nhật và thiết kế local
Chủ dự án trả lời đã thay workbook có giá/tồn và chỉ có plugin Figma, chưa có link. Đọc lại source sha256 ae2ca77c6543732131739c830b5595c207c28f5398a9eb9a8131a30ee5fb0deb:136/136 có giá/tồn/ảnh. Tiếp tục dựng theo source plugin local người dùng chọn (header và editorial mapping cuối file), không chặn vì thiếu cloud link và không tuyên bố visual canvas đã verified.

Nhập dữ liệu cửa hàng từ cột O/P, không dùng giá tham khảo M; cover price N chỉ dùng khi hợp lệ, không tự tạo khuyến mãi. Map tên 6 danh mục về slug đã có; 18 nhóm con giữ trong metadata báo cáo/mô tả, không tự đổi FK/schema hoặc tạo thêm category ngoài PRD. Trusted CLI import có dry-run, atomic database transaction, skip existing SKU (không reset giá/tồn sau bán), ảnh nội bộ đã kiểm MIME/hash, opening ledger ghi nguồn workbook và actor hệ thống null được schema cho phép; không tự tạo admin/mật khẩu. Acceptance: invalid/missing price/stock reject trước ghi, idempotent rerun, ledger đúng, rollback toàn bộ khi lỗi, API read hiển thị ảnh/giá thật.


## D36 — CONFIRMED 22/10/2026: thiết kế thuthanh theo Clicon

Nguồn: chủ dự án cung cấp file e5iYNlmq8NbdCyNlhUXFer, trang 279:19819. Chỉ sửa bộ plugin local để import và duyệt; logo duy nhất `thuthanh`, nội dung tiếng Việt, giữ nghiệp vụ. Tạo edition thu-thanh-figma-clicon và trang riêng, giữ nguyên plugin V15.1 và canvas cũ. Tham khảo bố cục/màu Clicon, thay điện tử bằng sách; không thêm wishlist, coupon, so sánh, đổi tiền/ngôn ngữ hoặc đổi trả. Shipping25000/pickup0, không đổi trả/hoàn tiền; VNPAY vẫn deferred. Không sửa Laravel/Next.js, không ghi vào file Clicon tham khảo.

Acceptance trước thay đổi: JavaScript/manifest hợp lệ; kiểm tra mock Figma xây cả máy tính/điện thoại, route tồn tại, nhãn/logo/màu đúng, giá/ảnh mẫu từ workbook; rebuild/cancel không mất bản trước. Kiểm tra preview bố cục, đóng gói ZIP tự chứa và hướng dẫn import. Mock/preview không được gọi là chạy thành công trong Figma thật; cần chủ dự án import duyệt canvas.


## D37 — CONFIRMED 22/10/2026: thay toàn bộ layout và chia flow theo Clicon
Chủ dự án yêu cầu bỏ giao diện cũ, làm lại khung sản phẩm/trang chủ/các trang theo Clicon, nội dung nhà sách, chia từng flow và có UX đủ. Dòng nhỏ header chỉ đúng “Chào mừng đến với nhà sách Thu Thanh”; không ghép phí giao, cảnh báo hay trạng thái vào dòng này. Logo vẫn thuthanh, toàn bộ UI tiếng Việt. D37 thay phạm vi chỉnh màu/giữ layout D36 bằng viết lại layout plugin; Next.js/Laravel chưa đổi trong lượt duyệt Figma. Màn thông tin sách/bản mẫu chi tiết Clicon được phép thêm để duyệt, không đổi nghiệp vụ/API.

Giữ phí giao25000/pickup0, no returns/refunds, VNPAY deferred; trạng thái payment tương lai ghi nhãn trong nội dung màn. Không thêm rating/wishlist/coupon/COD/đổi tiền/ngôn ngữ/Google login/newsletter. Reuse ảnh thật136 sản phẩm; các nội dung marketing không bịa khuyến mãi. Tách section theo flow khám phá/tài khoản/giỏ-đặt hàng/đơn/thanh toán/quản trị/hỗ trợ trên mỗi nền tảng, liên kết prototype giữa các flow. Bản source V16 được backup trước khi thay; plugin chỉ thay các vùng có marker do plugin tạo, không xóa lớp thủ công ngoài vùng đó.

Acceptance trước code: desktop1920/content1320/card234×320 theo source; mobile390 riêng; header exact tất cả màn; các màn chính có breadcrumb/sidebar/table/form theo mẫu thay vì layout cũ; đường đi bình thường và loading/empty/validation/error/out-of-stock/requote/permission/cancel/expiry/deferred; route đích tồn tại, overlay quay lại không đổi giỏ, total25000/pickup0; dataset136 ảnh/giá/tồn hash đúng; cancel/failure rollback; docs/ZIP/preview tự chứa. Figma thực và pixel comparison cần chủ dự án import, không nâng gate từ mock.


## D38 — CONFIRMED 23/10/2026: Figma gọn, code ứng dụng đầy đủ
Chủ dự án yêu cầu Figma chỉ4–6 sản phẩm mỗi danh mục, chỉ nối demo sản phẩm đầu, bỏ màn lặp, đủ chức năng demo. Chọn4 SKU/danh mục (24 tổng), Toán1 TT-SGK-002 là sản phẩm đầu và duy nhất có nhánh mua/giỏ/đặt hàng. Giữ layout Clicon, logo thuthanh, welcome exact và7 section/nền tảng; bỏ các bản đồ frame, dùng màn đầu từng section làm điểm mở. Giữ ví dụ quantity1/2 để chứng minh tổng tiền và delivery/pickup; gom lỗi/loading/disabled/focus vào boardUX.

Đây là giới hạn prototype, không giới hạn Laravel/Next.js: app vẫn phải đọc mọi SKU, tìm/lọc/phân trang, mua nhiều sản phẩm hợp lệ theo API. Không xóa/seed lại136 sản phẩm. Lượt này audit code thực và kiểm thử local để xác định việc còn thiếu; không suy audit là đã build xong toàn bộ UI/payment/deploy.

Acceptance trước sửa:24 SKU/hash/giá/tồn đúngnguồn, mỗi category4; tối đa40 màn/nền tảng (không tính vùng section), chỉSKU đầu có click mua/chỉnh sửa/tồn; giữ đủ7 flow và UXboard; không tạo chi tiết riêng/phân trang cho từngSKU; đích link hợp lệ, số lượng/tổng tiền đúng, rebuild/cancel giữ an toàn. Kiểm code nguồn, API/backend tests, Next build/typecheck/client tests khi môi trường cho phép; báo trung thực thiếu VNPAY/report/giao diện Clicon/admin chuyên biệt/deploy/course.

## D39 — CONFIRMED 23/10/2026: footer, phí tại checkout và prototype thanh toán đầy đủ

Theo yêu cầu mới: bỏ dải cam kết bốn ô; footer trắng chia cột Dịch vụ/Hỗ trợ/Tài khoản của tôi tham khảo ảnh Fahasa, giữ thương hiệu thuthanh và chính sách Thu Thanh, không thêm chứng nhận/liên hệ/đối tác giả. Phí giao giữ 25.000đ, pickup 0đ ở server; chỉ công bố số phí tại checkout và chứng từ đơn đã đặt. Trang duyệt, giỏ, footer và chính sách không niêm yết số phí. Đồng bộ phần chung này trên Next.js hiện hành; không triển khai provider thanh toán thật.

Figma hoàn thiện QR MẪU, chờ xác minh, thành công, thất bại, hết hạn, cần đối soát; QR chỉ chứa văn bản demo không phải thông tin chuyển tiền. Tách điều hướng mô phỏng khỏi thao tác khách xác nhận thanh toán. Cùng một luồng dùng cho giao hàng/nhận tại quầy, số lượng 1/2; thêm các trạng thái thanh toán có mục đích được phép vượt giới hạn40 màn D38, vẫn chỉ4 sản phẩm/danh mục và1 SKU demo. Không đổi đơn đã hết hạn thành paid, không tự tạo lần thử khi unknown, không thu tiền hoặc fake paid trên code thật. VNPAY tiếp tục deferred; backend/release gate không đổi.

Acceptance trước sửa: thay thế section V18 khi rebuild không nhân bản; kiểm tra containment và các cột footer/QR không chồng; QR mẫu hợp lệ offline; prototype có đủ nhánh và tổng tiền đúng số lượng; giữ rollback/manual layers. Chạy regression backend và Next build/typecheck/API tests; ghi rõ kiểm tra canvas thật/provider thật còn pending. Cập nhật gói import và hướng dẫn chạy không reset dữ liệu.

## D40 — CONFIRMED 23/10/2026: bản trình bày sạch, admin login và THUTHANH

Yêu cầu mới thay wordmark lowercase D36–D39 thành THUTHANH với serif đậm nghiêng đặc trưng; welcome không đổi. Figma thêm lựa chọn khách/quản trị khi đăng nhập và dashboard thống kê đơn/catalog/tồn (không tự chốt công thức doanh thu). Web thật vẫn login chung và server quyết định role; không tạo nút nâng quyền. Bỏ ghi chú kỹ thuật/demo/minh họa khỏi màn trình bày, giữ giải thích trong plugin/docs/metadata. QR vẫn payload vô hại; không kích hoạt provider thật.

Thanh toán prototype: chuyển chọn kết quả sang UI plugin, mặc định thành công; QR→chờ xác minh→kết quả thành công/thất bại/hết hạn/đối soát. Có receipt paid đúng số lượng/cách nhận; retry chỉ trạng thái thất bại còn hạn, unknown không retry; không hủy paid. Landing chỉ banner đầu có CTA Khám phá ngay, các banner khác thuần nội dung; breadcrumb chữ Trang chủ và logo đều về home. Dọn luồng deferred trong prototype, không thay deferral backend/app thật.

Acceptance trước sửa: label sạch, role login→dashboard clickable, chart/counts nhất quán fixtures, home duy nhất1 CTA khám phá, breadcrumb text link đúnghome, logoTHUTHANH/font fit; tiền/quantity/status/paid-actions đúng ở cả2platform; scenario selector không xuất hiện trong canvas. Rebuild thay V19 không chồng, preservation/rollback giữ; build/typecheck và mock/browser/package kiểm thực tế. Không schema/deploy/reset dữ liệu hoặc đánh dấu provider/gate passed.

## D41 — CONFIRMED 23/10/2026: cập nhật web thật theo V20 và giải thích vận hành

Chủ dự án yêu cầu cập nhật web và hướng dẫn local/Laravel/database/deploy. Cho phép áp V20 vào các route hiện có: header blue/nav white, CTA orange, card/lưới/home/category/footer, auth/cart/checkout/orders/admin cùng visual system; website giữ136SKU và multi-item, không mang giới hạn demo sang code. Thêm admin dashboard đọc dữ liệu thật: sản phẩm/danh mục đang bán, đơn hôm nay/chờ xử lý,7ngày và đơn gần nhất; không tính doanh thu hoặc bật VNPAY/QR mẫu trên web. Dùng múi giờ Asia/Ho_Chi_Minh cho ngày thống kê, stored timestamps UTC, cùng quy ước filter admin orders hiện tại.

Acceptance trước sửa: SSR/build/typecheck/client tests; role guest401/customer403/admin200 với dashboard; ngày VN tại ranh giới UTC, zero-fill7ngày, exclude inactive catalog and closed processing, eager-load recent orders, no money/revenue fields; contract docs đồng bộ. Browser storefront responsive/card/search/modal/home link/admin read-only QA khi có phiên test hợp lệ, không lấy mật khẩu owner hoặc tạo admin mới trên dev. Giữ tiền/tồn/quote/idempotency/session-CSRF. Hướng dẫn đúng MySQL portable3307/mysql-data, Laravel API/Eloquent và kiến trúc deploy có persistent storage; không deploy hoặc thay schema/env/dữ liệu. Gate provider/release không thay.


D41 dependency clarification: FUI01 là visual update + dashboard chỉ đọc trên storefront F01 đã done và design DUI05 đã done; dependency F02 ban đầu quá rộng vì chứa payment thật đang deferred. Sửa FUI01.depends_on thành DUI05/F01; không sửa F02/F03 thành done, không nâng backend/release gate. Đây không phải nghiệm thu chức năng checkout/payment còn thiếu.

## D42 — CONFIRMED 23/10/2026: thẻ Xem nhanh, logo Nhà sách Thu Thanh, ảnh gốc và banner Phương Nam

Yêu cầu mới thay wordmark THUTHANH serif D40 bằng logo riêng NHÀ SÁCH THU THANH tham khảo bố cục/màu chữ trong ảnh; không dùng tên Phương Nam làm thương hiệu cửa hàng. Thẻ sản phẩm hình lớn/tên/giá/giá gốc và giảm giá khi dữ liệu thật có/XEM NHANH. Không thêm rating/review/sales-counter giả. Tăng khoảng cách giữa các khối web và Figma.

D42 thay UX F05 login-before-preview: guest được mở Xem nhanh để xem ảnh/thông tin, chỉ yêu cầu auth khi thêm giỏ/Mua ngay; giữ quantity và sản phẩm khi quay lại auth, không chồng nhiều dialog. Mua ngay thêm giỏ bằng API thật rồi tới checkout lấy báo giá; server vẫn quyết định tiền/tồn/quyền, admin không mua nhưcustomer. Một SKU demo/4mẫu mỗi category chỉ áp Figma, app vẫn136SKU.

Ảnh gốc lấy theo source_image_url của chính136SKU workbook, kiểm MIME/kích thước/same-image similarity so thumbnail, không đoán bìa khác hoặc upscale giả. Bản HD được lưu riêng và map SKU trên frontend/Figma, giữ nguyên workbook, ảnhDB/import cũ, giá/tồn/ledger. Banner lấy từ homepage Phương Nam theo yêu cầu, chọn hình biên tập không mang mức giảm giá/quyền lợi sai với Thu Thanh; lưu nguồn/hash trong ASSET-SOURCES. Logo tự tạo bằng vector riêng. Prototype/cloud publication không tự suy từ task plugin local.

Acceptance trước sửa: nguyên dữ liệu136/6/1admin/tiền/tồn; asset manifest/hash/dimensions và bìa mẫu review; nguồn banner truy vết; gallery/web có spacing rõ; guest→quickview không auth trước; add→auth→resume quantity,1dialog,Escape/close/backdrop/keyboard; buyNow checkout đúng API; no fake discount. Figma V21 native card/quick overlays,4category samples/firstSKU,link targets và quantity/payment continuity/replaceV20/rollback; browser320/390/1440,build/typecheck/client tests; docs/gói plugin self-contained. Provider/revenue/release gates giữ nguyên, không mutation schema/deploy/dev database.


## D43 — CONFIRMED 23/10/2026: Landing page riêng, flow mở ngang, footer và bản tin

Yêu cầu mới bỏ CTA Khám phá ngay (thay D40–D42), nền xám làm ngăn giữa các panel trắng; logo riêng khác chữ/màu Phương Nam. Địa chỉ cửa hàng:295 Tôn Đức Thắng, Hòa Khánh, Đà Nẵng; cập nhật web/Figma/copy hiện hành, không sửa snapshot lịch sử đơn.

Figma V22: page tên Landing page để duyệt landing desktop/mobile riêng. Các luồng trải nghiệm trên hai page prototype được trải thành hàng từ trái sang phải, từng flow có nhãn/khoảng cách,frame top-level,không còn section/bảng lớn bao tất cả. Có một bản home entry trong mỗi prototype page để link Trang chủ hoạt động vì Figma prototype không điều hướng chéo page; page Landing page là bản duyệt tĩnh, chỉ2bản thêm có mục đích. Rebuild giữ node ngoài plugin và ảnh sửa tay,không chồng bản cũ;stage/rollback toàn bộ3page. Không có link canvas thực,scope vẫn localplugin.

Footer bổ sung dải nhãn hàng tham khảo ảnh chủ dự án,logo nguồn công khai truy vết; là dải thương hiệu,không tự tuyên bố đại lý/đối tác. Không thêm badge Bộ Công Thương hoặc logo phương thức thanh toán chưa tích hợp. Hai banner bổ sung theo bố cục2ô ảnh mẫu: ưu tiên thiết kế riêng tiếng Việt bằng sách đang bán nếu chưa có lựa chọn khác từ chủ dự án. Banner hiện hành và nguồn ghi ASSET-SOURCES.

Đăng ký nhận bản tin phải lưu email thật qua Laravel;publicPOST,CSRF,throttle,email normalize,explicit consent,duplicate-idempotent và không lộ trạng thái emailđãcó. Chỉ lưu đăng ký và xác nhận đúng việc đã ghi nhận;không giả gửi mail/campaign. Không tạo tài khoản/quyền/order/payment. Backend migration thêm bảng riêng trước,không sửa bảng nghiệp vụ/dữ liệu cũ;run targetedtest/APIcontract trước khi nối UI. Scope mới user cho phép frontend này khi endpoint riêng đã verified,không nâng backend/release gate toàn dự án. Production/emailcampaign/unsubscribe delivery cần giai đoạn riêng trước khi gửi thư thực.

Acceptance trước sửa:newsletters create/normalize/duplicate/consent/unknownfield/throttle/CSRF tested;schema/docsAPI đồng bộ;add-only migration bảo toàn136/6/1admin/giá/tồn. Web có separator xám/panel trắng,không còn Khám phá ngay,logo riêng,địa chỉ footer/dải nhãn hàng/form đăng ký/banners2,guest/customer/admin/checkout unchanged;browser320/390/1440/build/typecheck/tests. Figma page Landing page,flow top-levelhorizontal/non-overlap,route/quantity/payment continuity giữ;mock/pages/manualpreserve/rollback/gallery/ZIP verified. No fake certification/provider;no deploy/reset/devimports.

D43 banner clarification confirmed by owner: dùng nguyên hai banner Phương Nam như ảnh mẫu. Thay phương án tự dựng trước đó; giữ nguyên pixel/chữ/logo trong ảnh nguồn và chữ tiếng Anh vốn có của banner New Books theo yêu cầu cụ thể này. Logo header/footer vẫn là thiết kế riêng Thu Thanh. Không tự nhập sách/giá/tồn xuất hiện trong banner.


## D44 — CONFIRMED 23/10/2026: trang chủ theo bán hàng và quản trị/doanh thu

Chủ dự án yêu cầu bỏ2link Sách giáo khoa/Văn phòng phẩm trong nav và toàn bộ khối danh mục trên landing;không xóa category hoặc sản phẩm trong database/menuDanhmục. Home chỉ các khối Bán chạy nhất,Sản phẩm mới,Gợi ý cho bạn cùng banner/footer đã chốt. Ảnh lớn hơn,nút Xem nhanh typography/căn đều. Bestseller phải xếp theo tổng quantity của order_items thuộc đơn payment_status paid có accepted receipt,không từ cờ admin/đơn unpaid/duplicate/later money. Không tự tạo số bán hoặc paidorders trên development. New theo created_at/id;recommended deterministic diversity khi guest và theo categories mua/cart riêng khi customer,không dựa data người khác hoặc tracking guest.

Owner đã chọn rõ: DOANH THU TIỀN HÀNG SAU GIẢM GIÁ;PHÍ GIAO HÀNG THỐNG KÊ RIÊNG;chỉ đơn xác nhận paid/accepted. D15 được chốt,D27 receipt allocation separation giữ. Cho phép STORE_REVENUE_POLICY=approved_v1 trên local/config/template;không mở refund writes (no_returns_no_refunds vẫn giữ),không gộp duplicate/late/mismatch receipts thành sales và không bật provider thật. Báo cáo có khoảng ngày/nhóm ngày-tuần-tháng và CSV;zero periods không bịa sales. Thống kê là giao dịch online của hệ thống,không toàn bộ doanh thu cửa hàng/POS.

Hoàn thiện admin trong phạm vi hiện có:entry /admin và hướng dẫn login chung/serverrole;không resetpassword hoặc tạo admin mới trêndev. Create/update/images có kiểm tra và báo lỗi;thêm DELETE logical archive có version/lock/idempotence,giữ record/snapshot/stock history,không harddelete. Statusfilter/restore giúp quản lý hàng ngừng bán. Thêm quản lý category,revenue và đối soát trên API hiện có;không làm fakepaid/manualbankapproval hoặc newrefund. Backendranking/archive/report tests phải đạt trước frontend chức năng mới. No schema reset/import/deploy/owner credential dump.

Acceptance:paid ranking đúngquantity/ẩninactive/no doublecount;new/recommended đúngfilter/permission;archiveadmin-only/conflict/repeat/historypreserve;revenue ranges/zero-fill/dateVN/fees separate and legacy tests;roleauth/catalog CRUD/image/category/stock/order/revenue/reconcile browser QA isolatedDB. Finalbuild/typecheck/tests/docs synced;136prices/stocks/devadmin unchanged. Figmahome/nav/card/report source phù hợpscope,localpluginmock/ZIP verified if changed;provider/release gate còn chưa passed.

D44 triển khai tiện ích local `admin:account` chỉ đọc tên đăng nhập và `admin:password` phục hồi tương tác khi người dùng tự chạy, xác nhận và nhập mật khẩu hai lần. Không chạy phục hồi trên dev của chủ dự án trong phiên này; không có mật khẩu mặc định. Lệnh phục hồi từ chối production/noninteractive và thu hồi session admin khi người dùng thực hiện.


## D45 — CONFIRMED 23/10/2026: giá giảm, dữ liệu demo và chuẩn bị scale

Chủ dự án yêu cầu bỏ hai chữ/link Thương hiệu và Quản trị nhà sách tại footer, giữ logo nhãn hàng; thêm biểu tượng lửa cho Bán chạy nhất. New tiếp tục created_at/ID giảm dần; bestseller chủ dự án xác nhận xếp tổng số lượng đã bán, không theo tiền. Sau khi hỏi nguồn lịch sử thật, chủ dự án đổi yêu cầu sang tự tạo random: cho phép tạo khoảng1000đơn ở database demo local riêng, không sửa/trộn paid sales/stock/prices/customers trong thu_thanh thật và không đưa demo vào production. Không mở VNPAY hoặc fake paid endpoint trên web. Demo command phải có guard tên DB+môi trường, idempotent và có manifest nhận biết synthetic/provider demo; testFixtures tách DBtest.

Thêm phần trăm giảm giá khi tạo/sửa sản phẩm, tính sale_price server bằng số nguyên VND/làm tròn gần nhất; không tự giảm giá136sản phẩm hiện có. Không nhận đồng thời discount_percent và sale_price khác nguồn. API trả phần trăm được suy ra từ giá đang lưu; thẻ/popup thể hiện giá bán, giá gốc gạch ngang và badge giảm khi có giảm thật. Giá/tổng/quote/version/stock vẫn backend quyết định.

Mục tiêu10k người đồng thời là mục tiêu nghiệm thu hạ tầng, không mặc định10k checkout/RPS. Được tối ưu source, đọc public/cache/sharedRedis, truy vấn bounded, productionbuild/PHP-FPM/opcache/reverseproxy/CDN và thêm load harness/profiles. Không tự mua/provision/deploy hạ tầng hoặc tuyên bố10k đạt từ testlocalhost. Chỉ cache dữ liệu công khai; auth/cart/checkout/order/admin/personalized home/session/CSRF phải private/no-store và không cache Set-Cookie. Các mutation stock/price/order vẫn kiểm server; cache có TTL và invalidation sau commit. Test trước/sau đo query/response/latency trên môi trường cụ thể.

Dọn repo bằng inventory/reference audit; loại bản build/import/doc trùng đã thay thế khỏi vùng source hoạt động bằng archive có manifest/hash và hướng dẫn khôi phục. Không xóa env,DB,workbooknguồn,uploads,evidence,AGENTS/checkpoints/specs còn cần,dependencies hoặc userartwork. Không đổi tên hàng loạt source đang dùng. Acceptance: discount money edgecases/quote consistency/permissions; public cache isolation/invalidation/bounded recommendations; 1000demo receipts/order quantities totals/idempotence/guard; browser user/admin/responsive/errors; actual load smoke measured only at executed concurrency; finalbuild/typecheck/tests/docs/checkpoints. Backend related tests trước UI mới,provider/release gate giữ nguyên.


## D46 — CONFIRMED 23/10/2026: dọn tài liệu Markdown

Chủ dự án yêu cầu xóa các file Markdown không cần thiết. Loại review Figma cũ và prompt bootstrap/lab đã thay thế, gộp frontend verification vào VERIFICATION; giữ AGENTS/checkpoints/specs/guide hiện hành/audit môn học/nguồn asset. Archive ngoài repo, kiểm SHA/CRC trước xóa và sửa tham chiếu/check_pack. Không đổi source ứng dụng, database, env, workbook, upload hoặc package Figma. Acceptance: chỉ xóa danh sách đã audit, khôi phục được từng file, không còn link Markdown tới file đã xóa, kiểm tài liệu đạt. Chủ dự án xác nhận chưa có hạ tầng deploy; 10k vẫn chưa nghiệm thu.


## D47 — CONFIRMED 24/10/2026: bổ sung GitHub Project #4

Chủ dự án yêu cầu kiểm tra/bổ sung tất cả task theo master schedule đính kèm vào Project DongDao27/4. Repo đã xác minh DongDao27/Thu-Thanh-bookstore, có T01–T07 và năm milestone; thiếu T08–T18/MASTER. Được tạo issue thiếu, nối phụ thuộc, gắn milestone và thêm vào board; không tạo trùng hoặc đóng issue khi chưa nghiệm thu. Scope lịch cũ được đối chiếu D44–D46, không bật payment/deploy. Tiêu chí đạt: mỗi T01–T18 đúng một issue và có trong Project, MASTER đủ link, milestone đúng, trạng thái/checkbox cũ giữ nguyên; kiểm API sau mutation. Hiện gh token thiếu project, đang yêu cầu chủ tài khoản xác thực device flow.


## D48 — CONFIRMED 24/10/2026: commit/push toàn bộ source vào Payment

Chủ dự án chỉ định https://github.com/DongDao27/Payment làm nơi nhận source. API/ls-remote xác nhận repo đích trống và quyền ADMIN; local main chưa có commit/remote. Được tạo commit, origin và push main không force. Repo issue Thu-Thanh-bookstore/Project4 giữ nguyên. Chỉ commit source/assets/workbook/spec/tests/templates; không đưa env/keys/database runtime/vendor/node_modules/generated build vào Git. Acceptance: kiểm stagedfiles secrets/runtime/size, kiểm tra thích hợp, commit có hash thật, remote main trùng HEAD sau push, working tree sạch. Không deploy hoặc bật payment thật.


## D49 — CONFIRMED 24/10/2026: hiện trạng và hướng dẫn hai người đóng góp

Chủ dự án yêu cầu đề xuất cải thiện, liệt kê task đã làm và file hướng dẫn chia việc/commit trên hai máy; xác nhận tên Đông, Thiện. Phân công 9task/36điểm mỗi người là đề xuất trách nhiệm tương lai, chưa có username Thiện hoặc xác nhận công sức lịch sử. Không viết lại commit nền/author/ngày hoặc giả PR/đóng task. Source Payment, issue Thu-Thanh-bookstore/Project4, dùng fullURL để tham chiếu chéo repo. Acceptance: đủ18task hiện trạng có giới hạn rõ, bảng phân công cân bằng/reviewchéo, hướng dẫn identity/branch/stage/test/PR/merge/conflict/secrets và evidence thực; link/mapping/docs kiểm được. Không tự gán người/merge/push hoặc thay code ứng dụng.


## D50 — CONFIRMED 24/10/2026: đánh giá thiết kế trước mở bán

Chủ dự án yêu cầu đánh giá chuyên nghiệp và đề xuất sửa để dùng thật sau deploy. Scope đánh giá public browser/source/priorQA, không sửa UI/nghiệp vụ/Figma hoặc mua/deploy hạ tầng. Acceptance: chỉ rõ bằng chứng quan sát, ưu tiên/ảnh hưởng/tiêu chí đạt, phân biệt đề xuất với yêu cầu đã chốt; không nhận đạt provider/10k/WCAG từ local. Dùng banner riêng, giảm sốitem hoặc productURL là đề xuất cần duyệt, không tự thay D43/D44.


## D51 — CONFIRMED 24/10/2026: liên hệ/bản đồ, UX07/08 và admin chart

Chủ dự án cung cấp linkGoogleMaps địa chỉ295TônĐứcThắng,hotline0915466951,xác nhận07:30–21:30hằngngày. Yêu cầu mapdướifooter,UX07hitarea/focus/label,UX08recoveryvàđánhgiá/chỉnhadminicons/chart. Scope mới cho phép backend recovery trước UI: email broker Laravel, token hash/expire/oneuse/lock/sessionrevoke,customeronly,CSRF/rate/genericrequest;khôngSMSgiả/adminpublicreset. No schema mutation cần thiết nếu password_reset_tokens hiện hữu. EmailproductionphảiSMTPthật;localmailer chỉ development,khôngclaimdelivery. Backendtests/contracttrướcfrontend mới;UI maplazy/contact/passwordvisibility/inlineerrors,adminuniqueicons/chartaxis/filter/tooltipresponsive/dataempty. Acceptance bằng test/backend/APIcontract,frontendbuild/typecheck,browserisolatedDB/admin/guest/mobile/accessibility/map;khôngđổitổngdoanhthu,quyền,price/stock/provider/no-refund.

## D52 — CONFIRMED 24/10/2026: báo cáo Excel Đông/Thiện, 11–31/10/2026

Chủ dự án yêu cầu báo cáo phần trăm task GitHub và kế hoạch ba tuần; chốt ngày 11–17/10, 18–24/10, 25–31/10/2026. Scope tạo workbook và đọc GitHub; không thay trạng thái issue/Project, không commit/push hoặc deploy. Giữ phân công đề xuất D49: mỗi người 9 task/36 điểm, review chéo; không suy thành tác giả lịch sử. Tỷ lệ GitHub dựa trên issue đóng với lý do hoàn thành, tách rõ code local/chờ nghiệm thu. Hai tuần đã qua chỉ ghi bằng chứng có thật; lịch phân bổ là khung đối chiếu lập ngày24/10, không giả là kế hoạch đã chốt từ11/10. Tuần cuối là mục tiêu, provider/hosting/SMTP thật còn điều kiện ngoài. Acceptance: đủ18 task/link đúng, ba khoảng tuần đúng, tổng điểm cân bằng, công thức tỷ lệ có cached values, bảng tuần/in ấn dễ đọc, đối chiếu snapshot GitHub và kiểm cấu trúc XLSX bằng trình đọc độc lập. Không chạy lại application tests khi không đổi ứng dụng.

## D53 — CONFIRMED 24/10/2026: dọn repo, cập nhật README và commit toàn bộ thay đổi

Chủ dự án yêu cầu bỏ tài liệu đánh giá/đề xuất, hồ sơ test thừa và phần fetch dữ liệu không còn cần, cập nhật README rồi commit tất cả thay đổi. Dọn script tải ảnh/import workbook một lần, fixture browser thủ công cũ, snapshot/ảnh QA và audit trùng; giữ API fetch đang phục vụ web, bộ test tự động/CI, schema/domain/API, hướng dẫn chạy/deploy, nguồn ảnh, phân công và báo cáo Excel vừa yêu cầu. Lưu file bỏ trong ZIP ngoài repo, kiểm đường dẫn tuyệt đối/CRC/SHA trước khi xóa bản trong repo; manifest để truy lại chứng cứ lịch sử. Không xóa database, env, uploads, ảnh web/Figma, workbook, dữ liệu demo hoặc test nghiệp vụ.

Acceptance: danh sách dọn rõ, archive khôi phục đúng byte, không link hoạt động trỏ file đã bỏ, README đúng phiên bản/chức năng/contact/SMTP/giới hạn thật; kiểm tài liệu/API, backend tests/Pint/HTTP+ops smoke và frontend tests/build/typecheck trước commit. Không thêm tối ưu suy đoán hoặc đổi nghiệp vụ; giữ toàn bộ chức năng D51. Commit gồm source chưa commit D49–D52 và cleanup hiện tại, loại secret/runtime/dependencies; kiểm staged diff và hash thật, working tree sạch. Lượt này chỉ được yêu cầu commit, không tự deploy/mua hạ tầng hoặc bật provider thật.

## D54 — CONFIRMED 24/10/2026: tick checklist và đồng bộ trạng thái Project GitHub

Chủ dự án yêu cầu tick từng phần đã hoàn thành và chuyển task vào đúng trạng thái Project4. Được sửa body/checklist và Status các issue T01–T18/MASTER thuộc Thu-Thanh-bookstore; không tạo PR/review/tác giả giả hoặc tự gán username Thiện. Nguồn source Payment/main63d84ad, CI Backend/Frontend đều success đã đọc trực tiếp; local118backend/7client là kết quả D53, không chạy lại rồi gọi mới. Chỉ tick phần có source/tests/bằng chứng; ghi rõ adapter/fixture local khác merchant/provider thật. Dev done cho phần triển khai local đã đủ; In progress cho phần còn thiếu code hoặc nghiệm thu/canvas/SMTP/provider/staging/user testing. Không dùng In review/Merge Main để giả có PR/review; Closed chỉ khi acceptance và review thật đạt. MASTER phản ánh từng status/checklist và lịch ba tuần11–31/10 đã chốt D52, không hồi tố kết quả.

Acceptance: snapshot trước mutation, so sánh body/updatedAt ngay trước ghi để tránh đè sửa đồng thời; giữ checklist ngoài phạm vi, assignee/milestone/item cũ; thêm link source/test/CI và việc còn thiếu. Kiểm API sau ghi từngissue và tổng thể18task+MASTER, tất cả phần review/provider chưa đạt giữ unchecked. Inventory chưa có giá trong bảng và admin order chưa hiển thị lịch sử thanh toán phải ghi thiếu, không tick cả mục ghép. Không viết source ứng dụng, không đổi backend/release gate hoặc tạo thêm báo cáo QA rời trong repo vừa dọn.

## D55 — CONFIRMED 27/10/2026: mở VNPAY Sandbox cho khách, pickup trả online

Người dùng hiện tại của repo (git KienHungPG, máy macOS) yêu cầu "làm tính năng thanh toán bằng VNPAY" và chọn: khách được tự thanh toán online qua VNPAY cho cả đơn giao hàng và đơn nhận tại quầy; thời hạn giữ đơn giao hàng giữ 300 giây. Quyết định này thay phần "deferred" của D31 cho môi trường local/Sandbox: `VNPAY_DEFERRED` vẫn mặc định true trong code/env mẫu (an toàn), chỉ tắt trong `.env` private khi đã có TMN code/hash secret Sandbox của chính merchant. Production VNPAY, QR tại quầy cho admin, deploy và nhận tiền thật vẫn cần yêu cầu riêng; không dùng credentials công khai trên mạng.

Return URL trỏ trang Next `/payment/vnpay-return`. Return đã ký chỉ được: (a) kích hoạt query server→VNPAY ngoài khóa DB, nhận kết quả query đã ký mới tạo receipt/paid; (b) ghi attempt thất bại khi VNPAY ký `vnp_TransactionStatus=02` để khách thử lại. Return không bao giờ tự đánh dấu paid; query không xác định từ return không mở reconciliation case (scheduler/expiry giữ luật cũ). IPN giữ nguyên, cần URL public khi có hosting/tunnel.

Acceptance trước sửa: customer tạo attempt cho delivery và pickup còn hạn; admin QR vẫn 503 QR_CAPABILITY_UNAVAILABLE; deferred mặc định vẫn 503 không sinh attempt. Return: chữ ký sai 422 không gọi provider; success đã ký + query thành công → paid đúng một receipt, trừ tồn một lần; success đã ký + query lỗi → vẫn pending, không receipt/case; failed đã ký → attempt failed, đơn unpaid, can_retry, attempt mới có reference mới; return lặp không double-apply; return không gọi query lần hai trong 5 phút. UI: nút thanh toán VNPAY ở chi tiết đơn khi còn hạn, chuyển hướng payment_url; trang return xác minh rồi về đơn; thông báo lỗi tiếng Việt cho cấu hình thiếu/hết hạn/chờ đối soát. Chạy PaymentTest/OwnerPolicyTest/full backend suite, Pint, frontend test:api/typecheck/build; cập nhật API/DOMAIN-RULES/openapi/TASKS/PROGRESS/HANDOFF/STATUS. Sandbox thật chỉ ghi passed khi có giao dịch Sandbox của merchant; nếu chưa có credentials ghi not_run.

## D56 — CONFIRMED 28/10/2026: hoàn thiện phần độc lập và tinh gọn bàn giao

Chủ dự án yêu cầu hoàn thiện dự án sau payment và dọn doc/example/Markdown không cần. Người dùng xác nhận payment mới hoàn tất code/cấu hình, chưa có giao dịch merchant Sandbox thực tế. Repo hiện main1247330 sạch; giữ payment D55, không nhận đã nghiệm thu merchant/SMTP/deploy/10k. Máy Windows hiện chưa có TMN/hash secret, VNPAY_DEFERRED=true; không tự sửa secret/env hoặc tạo giao dịch trên database thật.

Được hoàn thiện giá và lịch sử tồn kho, lịch sử lần thanh toán/receipt/trạng thái đơn cho admin, các lỗi thực tế tìm thấy qua kiểm thử. Backend read-only API có phân trang/validation/admin guard/resource whitelist trước Next UI theo phạm vi cho phép D34; không đổi giá/hold/receipt allocation và không cần mutation schema. Acceptance: khách/guest không đọc được history, chỉ lịch sử đúng product/order, phân trang đầy đủ/sắp xếp ổn định/bounded, không lộ payload/chữ ký/secret; số giá từ server. Frontend có loading/empty/error/pagination, dữ liệu API thật và thao tác keyboard/mobile; payment return phân biệt success/fail/pending, không tự quyết định paid. Chạy test tiền/tồn/quyền, API-source parity/Pint, frontend tests/build/typecheck và browser local tách DB trước kết luận.

Dọn tài liệu trùng bằng gộp hướng dẫn/kiến trúc/giới hạn/nguồn tham khảo, archive CRC/SHA ngoài repo; giữ AGENTS/checkpoint/PRD/domain/API/schema/OpenAPI và nguồn asset cần bảo trì. Env mẫu canonical trong backend/frontend/deploy giữ để clone/CI/Docker; bỏ template trùng sau sửa hết tham chiếu. Không xóa dependencies, test source/CI, env thật, workbook, ảnh/Figma hoặc database. Cập nhật README theo môi trường Windows hiện tại, các bước cấu hình Sandbox thật còn thiếu và runbook ngắn; không dựng thêm báo cáo QA rời hoặc tự đánh dấu task release done.


## D57 — Áp dụng UI/UX Pro Max theo yêu cầu chủ dự án (28/10/2026)

Chủ dự án yêu cầu dùng skill nextlevelbuilder/ui-ux-pro-max-skill để sửa giao diện. Cài bản upstream 28b3b8dee8c976df4fa3e94aed4e092ad43fcb03 vào Codex skills cá nhân; đọc SKILL.md và truy vấn design-system/Next.js. Gợi ý chỉ là tham khảo: giữ nhận diện xanh–trắng, logo riêng, ba banner gốc, ba feed sản phẩm, thông tin cửa hàng và nghiệp vụ đã chốt. Không thêm CTA khám phá, dữ liệu doanh số giả hoặc thay đổi thanh toán/quyền/tồn.

Phạm vi được phép: tinh chỉnh Next.js typography tiếng Việt, token màu/khoảng cách, card/nút xem nhanh, điều hướng, biểu mẫu và bố cục admin trên API hiện có. Không sửa Figma canvas hoặc triển khai production trong task này. Tiêu chí đạt trước thay đổi: font self-host có tiếng Việt; card và CTA thẳng hàng; focus/hover/reduced-motion; không tràn trang ở 375/768/1024/1440; xem nhanh/đăng nhập/admin và lỗi tải hoạt động; client tests/build/typecheck và check_pack đạt. Không gọi cải thiện hình thức là nghiệm thu WCAG, merchant payment hay tải 10k.

D57 đã kiểm tra local theo phạm vi trên: 7 client tests/build/typecheck; responsive/keyboard/quickview/admin/form/error-retry đạt. Không có mẫu bookstore tự động khớp hoàn toàn sau retry, chỉ dùng UX chung/Next.js guidance phù hợp. Asset/catalog bảo toàn; không thay backend/DB/env hoặc canvas, không nghiệm thu merchant/staging/fullWCAG. Chi tiết kết quả trong TASKS/PROGRESS/STATUS.


## D58 — Footer gọn, bản đồ nhỏ, lên đầu trang và banner Fahasa (2026-10-28T19:52:06+07:00)

Chủ dự án yêu cầu dùng UI/UX Pro Max: bỏ nội dung footer trùng; đưa bản đồ thành ô nhỏ dưới logo; thêm nút lên đầu trang bán trong suốt khi cuộn; chọn banner từ Fahasa không có logo Fahasa cho header và giữa các mục landing. Yêu cầu này thay giới hạn D57 về banner mới: giữ ba ảnh Phương Nam đã duyệt, bổ sung banner chủ đề Fahasa và thay dòng announcement của khu mua sắm bằng banner hẹp. Admin giữ header hiện có. Không đổi danh mục/feed, giá/giảm giá, ship, thanh toán hoặc API.

Tiêu chí trước sửa: footer chỉ một địa chỉ/điện thoại/giờ mở cửa, một map nhỏ ngay dưới logo, không lặp link chính sách cùng đích; newsletter vẫn có label/consent/feedback. Banner không có logo Fahasa, không đưa voucher/freeship/mức giảm cụ thể của bên khác vào giá Thu Thanh; link vào danh sách nội bộ phù hợp. Giữa Bán chạy/Sản phẩm mới/Gợi ý có banner; ảnh tự host có nguồn/hash/dimensions, responsive/lazy, không auto-rotate. Nút lên đầu chỉ xuất hiện khi cuộn, 48px, có nhãn/focus, tránh bottomnav và tôn trọng reduced motion. Kiểm tra 375/768/1024/1440, xem nhanh, liên kết banner, form/footer, nút cuộn, build/typecheck/client tests, assets/check_pack/diff.

Đầu phiên có thay đổi next-env.d.ts do dev (trỏ .next/dev), server của chủ dự án đang chạy 3000/8000. Giữ nguyên file/process đó; build/QA trong bản copy riêng dưới .runtime và port3100. Không reset dữ liệu/env hoặc sửa Figma canvas.

D58 đã kiểm tra local theo tiêu chí trên. Nút được dock vào hàng bản quyền khi footer xuất hiện để không che form. Đã bỏ39 selector footer cũ thay vì thêm lớp override. Skill không có match footer trực tiếp sau retry, dùng UX chung/Next.js guidance phù hợp. Newsletter response fixtures chỉ để kiểm UI, không ghi email thật. Giữ next-env.d.ts từ trước, không stage chung; chi tiết kết quả ở TASKS/PROGRESS/STATUS.


## D59 — Kiểm tra web và đồng bộ plugin Figma V25 (2026-10-28T21:20:19+07:00)

Chủ dự án yêu cầu kiểm tra bản web D58 rồi đồng bộ Figma. Dùng bộ plugin local đã chọn, giữ manifest ID, 3 page/7 flow mỗi nền tảng, 24 SKU/4 mỗi danh mục và chỉ SKU đầu mua demo. Đồng bộ font/tokens/header, banner Fahasa giữa feed, footer liên hệ/map nhỏ và nút lên đầu. Không tạo cloud file mới, không chỉnh template Community của người khác. Layer UI phải chỉnh sửa được; chỉ nội dung ảnh/banner/map là raster. Giữ ảnh chỉnh tay/node ngoài plugin, rebuild atomic/rollback, không tăng trang lặp.

Acceptance trước sửa: web client tests/build/typecheck/assets đạt; Figma có nguồn banner/map PNG offline đúng hash; footer 1 địa chỉ/phone/giờ, map300×164 dưới logo, admin không footer mua sắm; SCROLL_TO đúng màn, logo/Trang chủ về landing, không chồng layer/flow, kiểm đủ payment quantity và rollback. Gói V25/source/SVG preview/ZIP CRC khớp; không claim canvas thật hoặc provider thật đã nghiệm thu. Giữ next-env.d.ts có từ trước, DB/env và source web.

D59 local acceptance đạt: web recheck, mock126frame/2518reaction và native geometry/flow/rollback, asset pixel parity, package CRC. V25 giữ số page/frame, bổ sung Scroll to trong cùng màn và mapURL; sourceUI vẫn là layer editable. Dock-state trong Figma được mô tả rõ; native canvas cần owner Present, không claim live acceptance.

## D60 — Gợi ý tìm kiếm và form tạo sản phẩm (2026-10-28)

Chủ dự án yêu cầu gợi ý khi nhập, sửa thêm sản phẩm/ảnh, bỏ nhập slug và bỏ banner trên header. Yêu cầu mới thay phần header quảng cáo của D58; giữ banner trong nội dung trang chủ. Slug vẫn là khóa nội bộ, server tự tạo khi POST không gửi slug; giữ URL cũ khi đổi tên. Không đổi tiền/tồn/quyền hoặc schema.

Tiêu chí trước sửa: gợi ý debounce từ API thật, hủy phản hồi cũ, chọn bằng bàn phím/chuột mở xem nhanh, Enter mở kết quả có tiêu đề tìm kiếm; có loading/empty/error. Admin tạo được sản phẩm không nhập slug, chọn ảnh ngay khi tạo; nếu ảnh lỗi giữ sản phẩm đã tạo và cho tải lại, không POST tạo trùng. Lỗi validation hiển thị cụ thể; ảnh giữ tỉ lệ, không cảnh báo Next/Image khi kiểm trình duyệt. Không có header quảng cáo; giữ dữ liệu/env/next-env.d.ts có từ trước. Kiểm API trên MySQL test riêng trước UI, build/typecheck, browser desktop/mobile và dữ liệu thực chỉ đọc. Provider thật/canvas/staging vẫn chưa nghiệm thu.

Kiểm browser phát hiện LOCATE không khớp `sach` với `Sách` dù collation Unicode CI. Sửa bộ lọc tên dùng LIKE với escape literal `%`, `_`, `!` và binding, không đổi schema/sort/quyền; bổ sung kiểm tìm có/không dấu và ký tự wildcard trước nghiệm thu.

Kiểm PHP thực tế có upload_max_filesize=2M, không khớp hợp đồng ảnh 5MiB. Bổ sung cấu hình upload riêng của dự án, script chạy local truyền ini-scan cho tiến trình con và Docker COPY cùng file; không sửa php.ini toàn máy. Demo server trực tiếp dùng -d tương ứng. Kiểm tiến trình HTTP con nhận ảnh thật lớn hơn2MiB, giữ pdo_mysql/ini gốc; không suy ra Docker đã build hoặc deploy.

D60 local acceptance đạt (2026-10-29T00:38:36+07:00):129test/1812assertion, Pint,7client/build/typecheck, browsersearch/editor/recovery/cart và PNG3.147.861byte upload201. Cấu hìnhPHP riêng không đổi ini máy. DBthực136giá/tồn khớp. FigmaV25/Docker/provider thật không nghiệm thu trong task này.

## D61 — Hoàn thiện tài khoản, điều hướng/footer/admin và Figma (29/10/2026)

Theo yêu cầu chủ dự án: bỏ cả welcome trên header; đổi nav thành Tất cả danh mục/Trang chủ/Đơn hàng; footer carousel thương hiệu có sản phẩm với nút trước/sau; chỉnh sửa hồ sơ lưu DB; bỏ nhãn không gian quản trị/eyebrow và chữ giải thích lặp; chart doanh thu chỉ ở trang báo cáo. Bán chạy vẫn xếp số lượng paid/accepted, không gán cờ giả. Đang hỏi phạm vi giảm giá; không tạo doanh số giả trong database cửa hàng. Dữ liệu demo đã tách riêng.

Acceptance trước sửa: API hồ sơ chỉ sửa chính mình, whitelist name/email/phone, chuẩn hóa/unique/ít nhất1identity, mật khẩu hiện tại khi đổi email/phone, không đổi role/password hoặc snapshot đơn; response không lộ secret. Backend test trước UI. Form có label/error/success/busy, header và reload đồng bộ DB. Carousel không auto-play, nút44px+, responsive và link kết quả thật; thương hiệu chỉ hiện nếu catalog có hàng khớp. Tổng quan admin không có chart doanh thu theo ngày; báo cáo vẫn lọc/chart/CSV. Kiểm các route/flow desktop/mobile và ghi rõ external/not-run. Plugin local V26 đồng bộ D60–D61, landing riêng, flow từ trái sang phải tối đa12frame/hàng, không overlap, giữ node ngoài plugin/ảnh chỉnh tay/rollback; mock/package không thay nghiệm thu canvas thật. Giữ env/DB chính/next-env.d.ts; không deploy thật.

D61 bổ sung theo câu trả lời chủ dự án: giảm ở database chính và gỡ demo/doanh số giả. Đã giảm11–17% cho7SKU (audit private), không tăng doanh số. Sau backup đã xóa1.000đơn mãDEMO và các dòng phụ thuộc/receipt giả, giữ1đơn ngoài tập mãDEMO. Không DROP database chứa dòng ngoài tập synthetic. Gỡ seeder/command/router/script/demo flag, giữ test nghiệp vụ và fixture ở DBtest. Main137sản phẩm,0paid, không có mãDEMO.

D61 local acceptance đạt:130test/1777assertion;7client/build22route/typecheck; browser profileDB/carousel/admin/report/CSV và responsive đạt. FigmaV26 mock132frame/2754reaction/package đạt; canvas/provider/staging chưa nghiệm thu. Giá main: TT-VH-00111%,TT-VPP-00112%,TT-DC-00113%,TT-TT-00114%,TT-QLN-00115%,TT-VH-00216%,TT-VPP-00217%; không đổi tồn/snapshot.

## D62 — Admin chọn sản phẩm Bán chạy và chuẩn bị Sandbox (29/10/2026)

Chủ dự án yêu cầu thêm lựa chọn Bán chạy khi tạo/sửa sản phẩm và chọn vài sản phẩm đang có. Yêu cầu mới thay giới hạn chỉ paid/accepted của D61 ở khâu trưng bày: dùng cờ is_bestseller đã có để đưa sản phẩm vào mục; vẫn giữ sản phẩm có lượng bán thật, xếp lượng bán đã xác minh giảm dần, dùng lựa chọn admin khi chưa có doanh số. Không tạo receipt/order/sold_count giả, không đổi công thức báo cáo. Cờ bỏ chọn không xóa doanh số thật. Không schema mutation.

Acceptance trước sửa: admin create/edit lưu cờ đúng DB, khách không được mutate, catalog/snapshot chỉ hiện sản phẩm/category active; sold_count chỉ là paid/accepted thực (0 với sản phẩm chưa bán), ranking ổn định, tối đa7 ở landing và filter/list cùng logic; cache invalidation sau đổi cờ. Chọn7SKU giảm giá đã duyệt D61 làm trưng bày, không đổi giá/tồn. Hướng dẫn VNPAY từ tài liệu chính thức, kiểm cấu hình chỉ in readiness không in secret, không giả giao dịch hoặc tự gọi merchant. CI thất bại ởcomposer audit/symfony-mime; bản vá dependency chỉ thực hiện sau chấp thuận theo skill gh-fix-ci. Giữ next-env.d.ts/env/key và dữ liệu đã có.

Chủ dự án đã chấp thuận cập nhật bản vá symfony/mime và xác nhận chưa đăng ký merchant. Chỉ cập nhật7.4.19→7.4.22, không bỏ composer audit. Chuẩn bị bản local riêng tại .runtime/sandbox-backend và DBthu_thanh_sandbox bằng schema/migration hiện có, không đổi schema ứng dụng/main. Chỉ copy catalog/ảnh/admin và số dư tồn ban đầu; không copy khách/đơn/receipt. Env/key/cookie/storage riêng và deferred=true, không gọi VNPAY trước khi có merchant. Đây là môi trường thử giao thức Sandbox, không tái tạo doanh số demo.


## D63 — Nhận merchant và cấu hình Sandbox local (29/10/2026)

Người dùng đã nhận email merchant Sandbox và yêu cầu hướng dẫn tiếp tục. Tiêu chí trước thay đổi: chỉ ghi credentials vào .runtime/sandbox-backend/.env private, mở VNPAY_DEFERRED=false trong bản thử, giữ database thu_thanh_sandbox/key/storage/cookie và main .env/dữ liệu; config:clear, environment-check và9readiness checks đạt mà không in secret. Chỉ kiểm HTTP/API read-only, không tạo đơn/receipt hoặc tự xác nhận paid. Đăng ký merchant không phải giao dịch đã thành công; B07 còn chờ success/cancel/query/IPN thật. Return local dùng localhost3000; IPN cần public HTTPS và gửi VNPAY, không tự gửi email. Cập nhật hướng dẫn/checkpoint theo bằng chứng; không commit secret.


## D64 — Điều tra giao dịch Sandbox đang chờ (29/10/2026)

Người dùng hỏi vì sao kết quả thanh toán chưa thành công. Kiểm read-only đúng đơn trong ảnh thấy pending/0receipt; gateway query thất bại cURL60/certificate. Tiêu chí trước sửa: dùng CA bundle từ HTTPS chính thức curl.se, kiểm SHA256 do nguồn công bố; chỉ cấu hình PHP của bản Sandbox, không tắt xác minh TLS/chữ ký; query chính giao dịch có thật, chỉ apply qua PaymentService nếu provider xác minh hợp lệ. Không sửa DB paid thủ công, không tạo giao dịch/đơn mới, không bỏ luật hết hạn/đối soát. Restart đúng ba dịch vụ có PID identity; giữ main env/data/key và owner frontend. Ghi riêng kết quả cấu hình, query/provider, receipt/order và IPN chưa kiểm. Source/UI không cần thay nếu lỗi chỉ ở PHP local; trang return hiện chỉ fetch một lần nên cần tải lại khi trạng thái thay đổi.


## D65 — Hoàn thiện khả năng chạy lại và kiểm thử Sandbox (29/10/2026)

User không tìm thấy giao dịch ở merchant, yêu cầu kiểm đầy đủ để thanh toán Sandbox thành công và giải thích sau tắt/mở máy. Tiêu chí trước sửa: kiểm request/redirect/return thật từ tài khoản khách Sandbox riêng qua browser; được tạo tối thiểu đơn/attempt để kiểm giao thức VNPAY Sandbox bằng thẻ thử đã cung cấp, không giao dịch Production, không dựng doanh số giả, không trả lại đơn cũ đang đối soát. Không fake signed response hoặc sửa paid thủ công. Chỉ source thay đổi có lỗi đã chứng minh; meaningful money/security tests và backend gate trước UI mới. Thêm launcher start/stop/status idempotent cho bản Sandbox private, kiểm DB/CA/config, dùng đúng thư mục và không sửa main/env/key/uploads; không bootstrap/reset hoặc ghi merchant vào Git. Kiểm restart lưu dữ liệu, role/CSRF/amount/signature/idempotency và HTTP thực. IPN public nếu chưa được đăng ký phải ghi pending, không gửi email thay user. Ghi rõ giao dịch nào verified hoặc not_run/blocker, giữ backend/release gate theo acceptance còn thiếu.


## D66 — UI, E2E và đồng bộ Figma (29/10/2026)

User yêu cầu Bán chạy dạng danh sách xếp hạng + vùng chi tiết, sửa stepper, bỏ nút cập nhật ở khách, test FE–BE có thể chạy lại và đồng bộ plugin. Acceptance trước sửa: giữ luật xếp sản phẩm/sold_count và quyền/giá/tồn; không đổi sang doanh số tuần hoặc bịa tác giả/rating. Bán chạy tối đa7 từ API hiện có, chọn hàng bằng chuột/bàn phím, responsive; stepper cùng chiều cao/căn giữa/giới hạn tồn; tài khoản khách không thấy thao tác cập nhật, admin giữ quyền server. E2E có MySQL/testing/cookie/CSRF thật và provider emulator chỉ ở test router có guard, storage/session/copy frontend riêng; không truy cập merchant/main env/DB, không coi emulator là nghiệm thu Sandbox. Test thực thi các luồng khách/admin/CRUD/upload/giá/checkout/payment success/fail/idempotency/quyền và responsive; không claim bao phủ mọi tình huống. Figma giữ IDmanifest, landing riêng, flow tách ngoài board, trái sang phải tối đa12màn/hàng, native layer/no-overlap và sample SKU flow; nâng V27 cùng test/package, không sửa canvas thật nếu không có file đích. Deploy staging Sandbox có thể chuẩn bị nhưng không tự deploy hoặc mở Production/tiền thật khi chưa hạ tầng/merchant.
## D67 — Kết quả thanh toán và logo thương hiệu (31/10/2026)

User báo đã thanh toán thành công; đây là bằng chứng người dùng cung cấp, chưa tự nghiệm thu receipt/IPN thật. Acceptance trước sửa: kết quả paid từ Laravel mới hiện tick xanh lớn; failed/cancel hiện X đỏ, pending/đối soát hiện đồng hồ và hướng dẫn không trả lại khi chưa rõ. Hiển thị mã đơn/phương thức/tổng tiền và chi tiết chỉ lấy đơn được API cấp quyền, không bịa ngày/tổng. Bỏ thao tác Đặt lại sản phẩm khỏi UI đơn; API reorder giữ tương thích. User yêu cầu toàn bộ logo ảnh carousel theo Phương Nam: dùng đúng ảnh10nhãn hàng tham chiếu, bỏ chữ thay logo/số sản phẩm và thanh cuộn lộ; không giả có tồn/sản phẩm khi nhãn hàng chưa có hàng. Không sao chép thông tin pháp nhân/chứng nhận/payment methods không có. Giữ luật tiền/tồn/quyền và Figma layout trước đó. Kiểm FE API/build/typecheck/E2E success/fail/pending/quantity/role và logo responsive; không reset main/Sandbox hay test thật thêm khi không cần.
## D68 — Hủy đơn đưa hàng về giỏ (31/10/2026)

Chủ dự án yêu cầu bỏ bảng chi tiết bên dưới kết quả thanh toán; thao tác đơn chỉ thanh toán/hủy. Hủy hợp lệ phải chuyển về giỏ và giữ sản phẩm để đặt đơn mới sau này. Acceptance: cancel kiểm ownership và trạng thái tiền như cũ; xác minh provider ngoài DB lock, hủy/release và phục hồi giỏ cùng transaction; user→order→product lock order, retry không cộng hai lần (marker lịch sử theo đơn). Gộp giỏ hiện có, không xóa món khác, giữ sản phẩm kể cả ngừng bán/hết tồn để cảnh báo và kiểm lại khi checkout; giới hạn99/món, giá hiện hành, giỏ không giữ tồn/giá. Không hủy paid/pending chưa xác minh/đối soát để phục hồi giỏ. Expiry tự động không phục hồi giỏ; khách chủ động hủy một đơn đã đóng unpaid có thể nhận lại hàng một lần. Không sửa schema/env/main/Sandbox data; meaningful tests cho rollback/idempotency/quyền/không đủ tồn và luồng browser hủy→giỏ→checkout mới. Gate backend trước UI thay đổi hành vi. Bỏ bảng payment-items và CSS/tests liên quan, giữ thông báo kết quả + thông tin thanh toán.


## D69 — Bản độc lập THUTHANH-EM3, Node.js/Express (31/10/2026)

Chủ dự án yêu cầu sao chép toàn bộ dự án sang folder THUTHANH-EM3 và chuyển backend sang Node.js/Express, bao gồm VNPAY. Yêu cầu mới này thay baseline Laravel CHỈ cho bản EM3; bản gốc giữ Laravel và dữ liệu hiện có. Express là backend, MySQL vẫn là database; EM3 dùng database/cookie/cổng riêng (thu_thanh_em3, 8001/3001), không kết nối ghi vào thu_thanh hoặc thu_thanh_sandbox. Giữ UI và hợp đồng API, giá VND nguyên, delivery25000/pickup0, no refund, quyền/session/CSRF, transaction/row locks/idempotency, quote snapshot, giữ/tiêu thụ/trả tồn, cart restore once. VNPAY port HMAC PAY/QueryDr/IPN/Return; chỉ IPN/query xác minh mới ghi paid. Local merchant chỉ sao chép env private, không vào Git/log; không tự tạo giao dịch provider/production hoặc đăng ký IPN.

Acceptance trước source/schema mới: bản riêng chạy Node không cần PHP; schema/seed/migration CLI, catalog/assets hiện hữu, auth/customer/admin/cart/order/inventory/revenue/newsletter/recovery/upload, scheduler và payment adapter; meaningful automated tests trên database EM3 test riêng, frontend API/typecheck/build và HTTP qua proxy. Sao chép dữ liệu nghiệp vụ local sang database mới, giữ khóa/FK/snapshot và upload; không sao chép session/reset token/job đang chạy. Nghiệm thu merchant/IPN/SMTP thật là external-blocked cho bản mới dù bản cũ từng có receipt Sandbox accepted. Không gọi done khi còn test/chức năng thiếu.


## D70 — Chuyển EM3 ra D:/THUTHANH-EM3 (2026-11-01T21:45:23+07:00)

User yêu cầu di chuyển folder ra ổ D và kiểm chứng dữ liệu/khả năng chạy. Acceptance trước di chuyển: đích chưa tồn tại; xác minh hai absolute paths; dừng đúng services của EM3; move nguyên folder, giữ env/SESSION_SECRET/uploads/dependencies; không di chuyển hoặc reset MySQL data của repo gốc. Kiểm hash toàn bộ file trừ generated node_modules/.next/.runtime, đếm catalog/orders/users/ảnh/schema và hash dữ liệu trước/sau; rebuild frontend tại đường dẫn mới, chạy backend tests43 trên DB test riêng, frontend API/build/typecheck, HTTP proxy và launcher. Ghi rõ local ready khác provider/SMTP/production acceptance; giữ gate external. Cập nhật link/tài liệu trỏ đích mới, không tạo junction giả bản sao còn trong repo.


## D83 — Nút căn giữa,10hạng cuộn và CTA mua trực tiếp (04/11/2026)

Yêu cầu chủ dự án: sửa component/padding Figma; Bán chạy tối đa10sản phẩm có cuộn dọc và chỉ báo/cuộn tới hạng sau; bỏ CTA Xem nhanh, thay Mua ngay và icon thêm giỏ. CTA card thao tác1sản phẩm, guest đăng nhập/đăng ký tiếp tục đúng ý định một lần, admin không mua; bấm ảnh/tên vẫn có thông tin/số lượng. Không tự thêm đơn/doanh số giả; xếp hạng theo dữ liệu thật/ghim quản trị hiện hành. Áp dụng FigmaV34, EM3 và Laravel theo yêu cầu rõ ràng, frontend local được phép theo D34. Không đổi tiền/tồn/đơn/schema/provider. Acceptance: component HUG/căn giữa với icon không stretch chữ;10hạng có viewport/scrollbar và thao tác hạng10; native API strict/geometry/route/rollback/budget; backend feed10 test, browser mua/add/auth/failure đúng một lần, build/typecheck/sourcechecks cả2bản. Bảo toàn dirtyfiles/env/mysql/storage, không commit/push.


## D84 — Thanh toán gọn, thu tiền tại quầy, đối tác ở landing (05/11/2026)

Chủ dự án cho phép pickup thanh toán ngay / tại quầy / hủy đơn; delivery thanh toán / hủy. Admin pickup thu tiền mặt hoàn tất ngay hoặc mở QR thanh toán VNPAY đúng tổng đơn. Cash tạo attempt/accepted receipt, audit actor, consume tồn và completed/fulfilled atomically, retry không ghi nhận hai lần. Chặn delivery/closed/expired/paid/pending/unknown/reconciliation. VNPAY hiện có chỉ hỗ trợ QR mở signed checkout URL bằng camera, không phải native bank-app merchant QR. Chỉ accepted IPN/query của attempt admin mở tại quầy tự hoàn tất; customer online giữ fulfillment cũ. Return đơn thuần không paid. Popup theo API. Không sửa schema, dữ liệu chính/env/provider; không gọi mạng dưới lock. Đối tác chỉ landing, tách shell/footer. Phạm vi Laravel hiện tại, không tự sửa EM3/Figma.

Acceptance trước source: tests quyền/amount/cash retry/rollback/expiry/pending/stock/revenue, QR URL signature/amount/replay/callback đúng phạm vi; API/build/typecheck/browser customer/admin/QR/cash/landing. Backend local trước UI nghiệp vụ; giữ external gates. Yêu cầu này thay quy định chỉ VNPAY cho pickup và admin QR503 trong phạm vi QR mở checkout URL; native merchant QR vẫn chưa tích hợp.

D84 QR readability: đường dẫn signed provider quá dài khi thu xuống mobile; thay bằng QR liên kết ngắn cùng origin chứa provider_reference ngẫu nhiên, endpoint công khai chỉ redirect checkout còn hiệu lực. Không ảnh QR dịch vụ ngoài. Giải mã độc lập screenshot mobile trước nghiệm thu local.


## D85 — Đồng bộ và kiểm tra cả Laravel, EM3, Figma (05/11/2026)
Chủ dự án yêu cầu kiểm tra/sửa đầy đủ cả hai ứng dụng và Figma theo D84. Phạm vi gồm pickup ba lựa chọn; admin cash receipt/stock/audit/atomic/idempotency và QR đúng tổng, only verified IPN/query auto complete; delivery pay/cancel; đối tác chỉ landing. Port semantics sang Express, giữ MySQL/session/CSRF/provider adapters/ports/env/data riêng. Rà soát lỗi source đã chứng minh ở cả hai bản; không hứa không còn mọi lỗi. Full backend và browser suites, API/build/typecheck/checks cả hai; tests DB riêng, mỗi app backend/E2E chạy tuần tự, QA ports không trùng. Figma V35 cập nhật plugin offline hiện có, đúng actions/payment states/partners layout, native API/geometry/routes/rollback/manual preservation và ZIP source hashes; canvas thật cần đúng file đích, không giả mock là canvas đã cập nhật. Không reset main DB/schema/env, không tạo đơn/doanh số/provider/SMTP thật, không deploy/commit/push. Ghi acceptance trước source và kết quả thực tế khi hoàn tất.


## D86 — Rubric và giao diện Laravel Blade/Livewire; nút Figma Fill/padding16 (05/11/2026)
Chủ dự án yêu cầu hướng tới10/10 theo rubric ảnh và xác nhận chuyển UI Laravel từ Next.js sang Blade+Livewire; EM3 vẫn Next.js, không đổi source EM3 trong D86. Quyết định này thay D34 chỉ trong bản Laravel. UI/asset/business rules giữ theo thiết kế hiện hành, thêm Blade+Livewire/Tailwind thực sự cho storefront/auth/cart/checkout/orders/payment/admin/catalog/inventory/revenue/reconciliation/account/recovery/newsletter; Next source cũ giữ để không ghi đè dirty work, không làm UI canonical của Laravel sau migration. Laravel services vẫn quyết định tiền/tồn/quyền/receipt, cookie+CSRF first-party.

Rubric: đo coverage thật và gate >70% toàn app, bổ sung test meaningful; Sanctum personal-token/abilities/expiry/revoke dành external client, không thay browser bằng localStorage; Scramble auto-doc/export/protected viewer; Telescope admin-only và redact secrets, slow-query alert/server-health monitor thực chạy; benchmark local có số đo và giới hạn. Git/topic branch/PR/issues cần bằng chứng thật, không dựng lịch sử/commit/PR giả, không tự publish dirty changes có sẵn. Chưa có PDF rubric gốc trong workspace, đối chiếu ảnh + audit D33 khôi phục read-only từ archive và ghi giới hạn. Không tự xác nhận điểm chính thức10.

Figma pluginV36: vùng thao tác AutoLayout ngang gap rõ, padding16 bốn phía, nút mua FILL, nút giỏ vuông và centered, label HUG; giữ nativeAPI/geometry/routes/legacyV35/manual-preservation/rollback, ZIP + preview kiểm thực. Chỉ plugin root Laravel để user import; EM3giữV35.

Acceptance trước source: full backend/tests/coverage >70 actual, Blade full customer/admin browser flows + responsive375/768/1440 + CSRF/auth/totals/payment/stock/retry, API token security contracts/docs, admin observability và HTTP/monitor/benchmark evidence, Vitebuild, Figma strict/ZIP/preview. Test DB riêng, không reset dev data/env/key/uploads/merchant hoặc gửi giao dịch/email thật. Trước migration Telescope tables checkpoint; production/release/provider còn external. Checkpoints in_progress nếu còn việc.


## D87 — Giữ Next.js theo xác nhận mới của chủ dự án (05/11/2026)
Chủ dự án đã hỏi lại và xác nhận chỉ cần sử dụng framework; Next.js được chấp nhận, không phải chuyển Blade/Livewire. Quyết định này hủy phần migration UI của D86, khôi phục D34 cho Laravel; cả Laravel và EM3 giữ Next.js. Gỡ đúng source/dependency/route do migration vừa tạo, bảo toàn dirty work trước D86. Không áp dụng yêu cầu Blade/Livewire của rubric cũ để tự thay stack nữa. Các tiêu chí coverage/API/docs/monitor vẫn cần bằng chứng thực tế, không tự tuyên bố đạt10. D86 thử nghiệm chưa hoàn tất được thu hồi, không ghi passed.
Phạm vi tiếp tục: plugin root V36 hàng CTA Auto Layout/padding16/buy FILL/cart56, nhãn HUG căn giữa; UI Next của Laravel đồng bộ, EM3 source giữ nguyên. Acceptance: restore source đúng phạm vi, composer validation/backend regression; plugin strict geometry/native Fill/rollback/ZIP; Next build/typecheck và responsive browser 375/768/1440 không tràn, nút có khoảng cách/padding đúng. Không migration/schema/env/provider/production/commit/push.


## D88 — Kiểm tra trước commit/nộp bài (06/11/2026)
Chủ dự án yêu cầu xác minh MySQL EM3/Laravel có dùng chung, kiểm tra UI/E2E toàn bộ và file thừa/lỗi trước commit. Phạm vi cả hai repo, giữ Next.js và khác biệt UI D87 đã chốt; không tự đồng bộ EM3 V36. Acceptance trước sửa: đọc effective DB connection và SELECT DATABASE/server identity, không in secret; full backend/API/check/build/typecheck và full E2E từng app chạy tuần tự do QA ports dùng chung; UI desktop/tablet/mobile và ảnh/lỗi console; rà candidate Git cho secret/build/log/cache/temp/duplicate/dead assets và dependencies. Sửa lỗi tái hiện trong phạm vi, chỉ dọn file xác định là generated/không dùng và bảo toàn main DB/env/uploads/manual assets. Không reset/schema/deploy/real payment/email/commit/push. Ghi file không nên commit và giới hạn provider thật; không gọi external/skipped là passed.


## D90 — Dọn repo theo phạm vi môn và chuẩn bị hướng dẫn Deployer (12/11/2026)
Chủ dự án yêu cầu đọc lại dự án, bỏ file không dùng và hướng dẫn deploy dựa Laravel deployment10.x/Deployer7.x. Giữ PHP8.4/Laravel12/Next theo D87, không downgrade theo tài liệu mẫu. Không coi dọn repo là đã đáp ứng toàn bộ rubric. Giữ bản trích brief trước khi archive; chỉ chuyển artifacts/build/QA/công cụ một lần đã xác minh ra kho ngoài repo có khôi phục. Giữ business data, env/key/uploads, source dirty, tests, assets hiện hành và plugin offline; không sửa EM3.
Acceptance trước mutation: manifest path/hash/khôi phục, kiểm reference/process, source/module/asset checks và hướng dẫn Deployer7 cho monorepo Laravel+Next, shared env/storage, HTTPS/CSRF, worker/cron, backup/rollback. Đọc hai URL gốc và đối chiếu Laravel12. Chỉ chuẩn bị/review local, không tự push/deploy. Ghi rõ thiếu sót rubric; D90 không thêm tính năng lớn. D91 sửa lại dấu tiếng Việt của đoạn D90 bị lỗi encoding, không đổi quyết định lịch sử.

## D91 — Hoàn thiện yêu cầu môn trước deploy (12/11/2026)

<!-- D92 is recorded at the end of this document; D91 remains historical. -->

Chủ dự án yêu cầu kiểm tra và hoàn thiện trước, deploy để sau. Mở rộng phạm vi D90 sang bổ sung các thiếu sót kỹ thuật rubric: Sanctum PAT có abilities/expiry/revoke cho external client; Scramble sinh tài liệu từ code; Telescope chỉ admin và che dữ liệu; slow-query/error alerts; giám sát CPU/RAM/disk/network có threshold; coverage thực và CI gate >70%, ERD/tài liệu demo và regression. Giữ Next.js D87 và session+CSRF trên website. Token API là phần mở rộng riêng, không đổi trình duyệt sang bearer/localStorage; không thêm POS/refund hoặc đổi luật tiền/tồn.

Acceptance trước source/schema: test token không vượt scopes/role/ownership, expired/revoked không dùng được, Bearer không bypass API session; docs/Telescope guest/customer bị chặn kể cả local, tài liệu tự sinh có endpoints và dữ liệu nhạy cảm không được Telescope lưu; slow-query/error/host thresholds sinh cảnh báo có chống lặp và ngữ cảnh không chứa SQL/bindings/token/PII; monitor đo thực trên máy hiện tại và alert-path kiểm fixture hoặc ngưỡng demo ghi rõ. Package install theo major hiện có, migration mới chỉ bổ sung bảng và kiểm trên thu_thanh_test; không migrate/reset dữ liệu chính. Coverage đo thật và fail CI khi không vượt70%; full backend/API/build/typecheck/E2E/HTTP/worker checks phù hợp. Không deploy/push/commit, không gửi giao dịch/email thật; phần nghiệm thu cần VPS/provider/người dùng phải ghi riêng, không gọi là passed.


## D92 — Vintage, danh mục gọn và chi tiết sách (12/11/2026)
Chủ dự án yêu cầu bỏ Trang chủ/Đơn hàng chỉ trong menu danh mục; Đối tác nằm ngoài nền trắng; giảm khoảng trắng, sửa căn lề/chữ/nút và đổi màu vintage. Bấm ảnh/tên mở trang chi tiết ảnh/thông tin/mô tả theo ảnh tham khảo; giữ quickview và hành vi mua/giỏ hiện có qua nút Xem nhanh. Quyết định này thay giới hạn không có route chi tiết độc lập của PRD cũ trong phạm vi storefront. Best seller có mô tả dễ đọc và link xem đủ. Giữ giá/tồn/phí/quyền, không sửa Figma hoặc EM3 khi chưa yêu cầu.
Acceptance trước source/data: dùng token kem giấy/nâu/đỏ gạch đủ contrast, desktop/mobile375/768/1440 không tràn; danh mục không còn hai link trên, tiêu đề đối tác trên canvas; khoảng cách section/footer thu gọn; ảnh/tên mở detail, gallery/quantity/buy/auth tiếp tục đúng; mô tả sách dựa metadata/nguồn và không bịa thông số/cốt truyện. Dữ liệu mô tả cập nhật có backup, chỉ SKU/tên khớp và mô tả nền cũ; không ghi đè mô tả thủ công, không thay giá/tồn/khuyến mãi/tài khoản/đơn, không schema/reset. Kiểm backend nội dung trước UI, typecheck/build/API/browser thực và regression phù hợp. Không deploy/commit/push/giao dịch/email thật; strict gates giữ ngoại vi.

## D93 — Khôi phục màu xanh, lề web gọn và Figma mobile app

Yêu cầu mới thay màu vintage D92 bằng palette xanh cũ; header thấp hơn, footer gần nhau, bỏ Xem nhanh trên card và giữ ảnh/tên mở chi tiết. Desktop web dùng lề khoảng2% mỗi bên như ảnh thứ hai (không cap1320px gây lề lớn trên màn rộng). Mobile app chỉ thiết kế trong bộ plugin Figma hiện hành, không viết ứng dụng mobile hoặc đổi website thành app. Theo workflow offline đã chốt, nâng V37 có status bar/app bar, back navigation, tab bar/safe area, không breadcrumb/footer website trên điện thoại. Giữ web prototype và nghiệp vụ hiện có, EM3/dữ liệu không đổi.

Acceptance trước sửa: màu root trở lại xanh/trắng/xám; header desktop khoảng76px, footer title/link cùng cụm có khoảng cách gọn và click targets hợp lý; desktop1440/1920/2560 có lề gần2%, không tràn. Card không còn nút Xem nhanh, ảnh mở detail và mua/auth/quantity còn đúng. Figma mobile402x874 có lớp editable, app chrome và safe area, không siteFooter/breadcrumb/browser address bar, các luồng bảy nhóm/prototype/ảnh thủ công vẫn giữ. Kiểm typecheck/build/API/browser regression và plugin mock/geometry/rebuild/rollback/package; canvas thật chỉ gọi verified nếu đã đọc/chỉnh native file. Không deploy/commit/push/schema/DB/env/payment/email.

## D94 — Sửa bố cục quản trị và rà soát font trước deploy (13/11/2026)

Chủ dự án cung cấp ảnh lỗi thao tác tồn kho dính chữ, checkbox Bán chạy căn giữa và phần quản lý ảnh nằm sau nút lưu. Sửa theo source thực tế: nhóm thao tác có khoảng cách/vùng bấm riêng, ba checkbox cùng lề; quản lý ảnh nằm trước nút Lưu sản phẩm cuối modal, giữ form ảnh độc lập để không submit nhầm sản phẩm. Rà tiếng Việt/font/control và các màn public/admin bằng browser; không thay palette/lề D93, Figma/EM3, backend/API/DB hoặc luật tiền/tồn.

Acceptance trước sửa: Điều chỉnh/Lịch sử không dính/wrap từng nhãn, vẫn mở đúng modal; checkbox đang bán/hot/bán chạy cùng trục trái; tạo/sửa sản phẩm đều có Lưu ở cuối sau chọn/quản lý ảnh; upload/xóa ảnh không submit form sản phẩm, nút Lưu vẫn chạy validation và khóa khi busy. Kiểm source encoding/font tải thực/các glyph tiếng Việt và ảnh browser public/admin; typecheck/build/API/E2E hiện có phù hợp, không gọi kiểm local là đã sẵn sàng production khi còn gate ngoại vi. Chưa deploy/commit/push/migrate/giao dịch/email thật.

## D95 — Phân biệt repo chính và repo mẫu (13/11/2026)

Chủ dự án xác nhận DongDao27/Thu-Thanh-bookstore là repo chính; DongDao27/Payment là repo mẫu dùng để sửa. Đánh giá nộp môn, Issues/PR/workflow phải đối chiếu repo chính; CI/lịch sử của Payment không tự tính cho repo chính. Kết quả source/tests local chỉ chứng minh bản local đã kiểm, không tự chứng minh mã trên repo chính đã đồng bộ. Không suy ra vai trò repo từ origin của workspace, không tự đổi remote/push/chuyển dirty work từ lời xác nhận này.

## D96 — Lưới sản phẩm cân đối và ảnh bổ sung đúng sản phẩm (13/11/2026)

Chủ dự án chọn đề xuất lưới5–6thẻ/hàng trên desktop rộng, ảnh chung khung/tên tối đa3dòng/giá+nút thẳng hàng; yêu cầu tìm và thêm ảnh của sản phẩm dưới ảnh chính ở trang chi tiết. Dùng nguồn catalog đã nhập để xác minh đúng sản phẩm/ấn bản/biến thể, không dùng ảnh AI hoặc ảnh sản phẩm khác làm ảnh phụ. Khảo sát toàn bộ catalog có nguồn, chỉ nhập ảnh gallery xác minh được; ghi số lượng và trường hợp nguồn không có ảnh phụ. Không vượt challenge/đăng nhập của nguồn; dùng nội dung web công khai đọc được. Giữ ảnh chính và ảnh quản trị đã thêm.

Acceptance trước source/data: home5cột từ1280px, catalog5cột từ1440px,6cột từ1800px; các cỡ nhỏ giữ responsive hiện có. Ảnh/category/title/price/action có hàng nhất quán; title clamp3dòng, click ảnh/detail/giỏ/auth không đổi. Gallery ảnh local được lưu trong ProductImage/public storage hiện có, thumbnail phía dưới chọn được ảnh chính bằng chuột/phím và có alt/pressed state. Nhập có manifest nguồn/hash, dry-run mặc định, backup trước apply, append/idempotent, khớp SKU+tên, không xóa/ghi đè ảnh cũ; giữ giá/tồn/số đơn/users và kiểm invariants riêng cho main/Sandbox đang dùng. Asset download xong trước DB transaction, không giữ lock khi gọi mạng. Test import-safety, API/typecheck/build/browser/grid/gallery/regression phù hợp. Không Figma/EM3/mobile-app/deploy/commit/push/schema/env/payment/email thật.

## D97 — Thu gọn thẻ và lấp đầy lưới sản phẩm (13/11/2026)

Yêu cầu mới ưu tiên thẻ nhỏ, gap ngắn, hiển thị thêm sản phẩm. Thay mốc D96: home 6 cột từ1280 và8 từ1800; catalog6 từ1440 và8 từ2200. Ảnh desktop180px thay270px, gap lưới12px, padding thẻ10px, nút44px; tên vẫn tối đa3dòng và giá/nút cùng hàng. Catalog24 sản phẩm/trang; home lấy24 sản phẩm mới qua API hiện có, giữ gợi ý hiện có trước rồi bổ sung sản phẩm khác không trùng để đủ24 khi có dữ liệu. Không tạo sản phẩm giả hoặc thêm vào bảng xếp hạng để lấp chỗ. Nếu tổng kết quả ít hơn một hàng/không chia hết thì giữ dữ liệu thật.

Acceptance trước sửa: thẻ thấp hơn D96, hàng desktop6/8 theo mốc mới, gap12, tên/giá/nút thẳng hàng, không tràn ngang375/768/1280/1440/1920/2560. Catalog tải24 và phân trang/lọc vẫn đúng; home mới/gợi ý tối đa24 ID không trùng, click ảnh mở detail/nút mua giữ hành vi. Kiểm typecheck/API/build và browser geometry/navigation. Không đổi backend/database/env/Figma/EM3/deploy/commit/push.

## D98 — Rà soát cấu trúc, chấm rubric, JavaScript và phân công60/40 (14/11/2026)

Chủ dự án yêu cầu rà cấu trúc/tính dễ bảo trì, chấm điểm và việc cần làm để tối đa, chia lại công việc/commit Thiện40% Đông60%, chuyển frontend TypeScript sang JavaScript. Đây là thay đổi lựa chọn kỹ thuật D87: vẫn Next.js/React/Tailwind nhưng component.jsx, helpers/tests/config.js, jsconfig.json; không còn source .ts/.tsx hay phụ thuộc TypeScript trực tiếp. JSX là cú pháp giao diện trong JavaScript. Không đổi nghiệp vụ, palette/layout, backend/schema/env/data/Figma/EM3. Giữ hợp đồng API trong docs, thêm kiểm cú pháp/import cho JS và chạy API/build/fullE2E. Sao lưu toàn bộ source chuyển đổi trước khi đổi đuôi, kiểm output runtime tương đương; cập nhật CI/scripts/docs tham chiếu đường dẫn mới.

Acceptance trước sửa: frontend source/config/tests đều JS/JSX, alias hoạt động, không TS-only syntax/import hỏng; API7/build/fullE2E đạt. Báo cáo có căn cứ brief/source/test và GitHub chính, phân biệt local/provider/staging chưa kiểm; điểm dự kiến không phải điểm giảng viên. Phân công100điểm khối lượng (Đông60/Thiện40), gói commit theo việc thực tế/tiêu chí nghiệm thu; không giả tác giả/ngày hoặc sửa lịch sử, không tự commit/push/assign GitHub/deploy. GitHub read-only ngày14/11: repository metadata size0, fetch README main trả404 This repository is empty; giữ bằng chứng. Không tự chuyển source lên repo từ yêu cầu phân công.

## D99 — Lưới5cột, feed không trùng và chuẩn hóa CSS (14/11/2026)

Acceptance: desktop>=1280 home/catalog5cột,25sản phẩm/trang; bestseller ưu tiên trước,new loại bestseller,recommended loại hai feed trước và giữ thứ tự gợi ý. Bổ sung ứng viên catalog tối đa100 qua API có sẵn, không giả dữ liệu. CSS một entrypoint, gom legacy vào styles, loại khai báo bị ghi đè chắc chắn trong cùng selector/context (giữ thứ tự cascade và important), backup/hash mọi file thay/xóa. Chỉ bỏ file xác minh không dùng; không xóa env/data/runtime/uploads/tests/licenses. Kiểm JS/API/build/fullE2E và báo cáo cleanup. Không backend/schema/Figma/deploy/commit/push.

## D100 — Mới10, Gợi ý15 (14/11/2026)

Theo yêu cầu mới, chỉ giới hạn hai mục trang chủ: Mới tối đa10 và Gợi ý tối đa15 sản phẩm thật. Giữ5cột desktop, không trùng giữa các mục, Bán chạy và catalog25/trang không đổi. Acceptance: helper trả10/15 khi đủ dữ liệu, ít thì giữ số thực, thứ tự/không trùng giữ; kiểm9unit/API, JS và build. Không backend/data/Figma/deploy/commit.

## D101 — Dọn tài liệu và runtime (14/11/2026)

Theo yêu cầu chủ dự án: một README chính; gom hướng dẫn chuyên biệt, archive tài liệu/manifest và script/log/QA cũ ngoài workspace. Acceptance: không mất nội dung, snapshot/hash/index khôi phục; giữ MySQL/datadir/env/APP_KEY/Sandbox/uploads/tools còn dùng; dịch vụ3000/8000/3307 giữ; cập nhật mọi link/harness kiểm docs; không đổi code nghiệp vụ/schema/data, không deploy/commit. Hướng dẫn cũ/backup giữ ngoài source, snapshot manifest cũ đóng gói trong CLEANUP.json để kiểm docs hoạt động trên máy mới.

## D102 — Xóa vĩnh viễn bản cũ (16/11/2026)

Chủ dự án xác nhận không cần khôi phục và yêu cầu xóa backup/bản Figma cũ, phần đã thay không dùng lại. Quyết định này thay chính sách archive D101 cho các target trong CLEANUP.json: hai archive cũ, V36 ZIP, preview/báo cáo/ZIP source cũ và ảnh QA đã thay thế. Giữ V37 ZIP/plugin hiện hành, Git, source/test/rubric docs và database/env/uploads/Sandbox/tools đang dùng. Acceptance: xác minh absolute target thuộc hai archive chỉ định hoặc output; gỡ junction mà không duyệt đích, xóa thật không tạo backup mới, current-file hash giữ, link/docs/JS/API checks đạt. Không tự xóa tính năng ngoài rubric còn đang dùng, không schema/reset/deploy/commit.

## D103 — Windows hosting và audit giỏ→thanh toán (16/11/2026)

Chủ dự án hỏi Windows hosting và kiểm luồng mua hàng để đề xuất trải nghiệm tốt hơn. Phạm vi audit source và chạy test trên DBtest riêng; không tự đổi guest auth/cart, nghĩa Mua ngay, hold time, provider hoặc giá. Acceptance: phân biệt Windows local/WindowsServer/WSL/LinuxVPS; kiểm add/auth/quantity/cart/quote/order/payment pending/failure/cancel/idempotency; báo phát hiện có source và kiểm thực, tách lựa chọn nghiệp vụ cần chốt khỏi lỗi, fixture khỏi provider thật. Không production/deploy/mainDB/payment/email thật, không commit/push; tiếp tục độc lập trong khi UX48 có thao tác xóa bị policy chặn.

Nhận định source D103 (đề xuất, chưa đổi nghiệp vụ):

- P1 `store-provider.jsx purchaseProduct` thêm món vào giỏ và chuyển checkout; QuoteService snapshot toàn bộ giỏ. Mua ngay khi đã có món khác sẽ mua toàn giỏ, chưa có selected-items/direct-checkout độc lập. Cần chốt nghĩa Mua ngay; nếu mua riêng thì phải có hợp đồng checkout subset và giữ các món còn lại, không xóa giỏ cũ để giả lập.
- P1 checkout hiện lấy báo giá → xác nhận đơn → chi tiết đơn → thanh toán. Có thể gộp UX thành kiểm thông tin/tổng tiền và CTA đặt hàng-thanh toán, giữ server quote/stock/idempotency. Không tự bỏ kiểm hoặc thêm COD giao hàng.
- P1 cart checkout Link chỉ dùng aria-disabled/class pointer-events:none; không có handler chặn Enter hoặc bỏ href khi chưa sẵn sàng. Đây là khoảng trống accessibility ở client, backend vẫn chặn hàng thiếu tồn. Nên dùng disabled button/guard điều hướng thực sự.
- P2 vào thẳng checkout giỏ rỗng vẫn thấy form, lỗi EMPTY_CART chỉ sau submitquote. Nên chặn sớm với đường quay về danh mục; server guard vẫn giữ.
- P2 `orders/[code] cancel` refreshCart lỗi sau khi server đã hủy rơi vào catch chung nên có thể báo mạng như hủy thất bại. Nên tách thông báo mutation thành công khỏi lỗi refresh, như purchaseProduct hiện đã làm.
- P2 use-order-polling tự đọc10giây/lần và tối đa30lần rồi dừng im lặng; nên có trạng thái lần cập nhật cuối/CTA đọc lại đơn sau giới hạn hoặc lỗi, vẫn không retry thanh toán khi pending/đối soát chưa rõ.
- P2 checkout form không lưu địa chỉ qua reload/chuyển trang, tên/SĐT đã điền theo account. Có thể bổ sung địa chỉ tài khoản hoặc draft thích hợp với dữ liệu cá nhân; tránh tự lưu secret hoặc token vào localStorage.
- Guest auth bắt buộc trước add là nghiệp vụ hiện hành, không phải bug. Guest cart và đăng nhập muộn có thể giảm ma sát nhưng cần quyết định domain/API/security riêng; chưa triển khai.

Chốt audit không phải chấp thuận thay các nghiệp vụ trên. Money/stock/order/pending guards hiện có cần giữ; các test provider là fixture, không thay nghiệm thu merchant thật.

## D104 — Cải thiện mua hàng và Ubuntu dual boot (16/11/2026)

Chủ dự án yêu cầu triển khai cải thiện UX D103, kiểm font/luồng khác; xác nhận Ubuntu cài riêng/dualboot100GB. MuaNgay chuyển checkout riêng product_id/quantity, không thêm/xóa món giỏ; quote hỗ trợ optional buy_now object, Laravel vẫn tínhgiá/khóa tồn/idempotency, không schema. Checkout giỏ thông thường giữ toàn giỏ. Auth bắt buộc hiện hành giữ, không tự mở guestcheckout/COD/đổi phí/hạn. Rút bớt chuyển trang khi thanh toán giao hàng bằng CTA đặt hàng+thanh toán sau bước review quote; nếu payment-init lỗi thì về đơn đã tạo, không tạo đơn khác. Pickup giữ lựa chọn trả tại quầy. Chặn checkout rỗng/không sẵn sàng cả bàn phím, lưu draft nhận hàng sessionStorage theo user/TTL, không token; lỗi refresh sau hủy không phủ nhận mutation, polling có trạng thái và nút đọc lại. Font/CSS tokens giữ, chỉ thêm nội dung/controls dùng style hiện có.

Acceptance: backend verify buy_now quote/direct order/tồn/price/replay/giỏ cũ giữ nguyên và validation; backend trước frontend mới. Kiểm API/JS/build/E2E/regression, typography actualfont/glyph/375–1440, không đổimain/Sandbox/env/schema/Figma/EM3/deploy/commit. Hướng dẫn Ubuntu theo bản cài riêng:100GB là dunglượngkhởiđầu, kiểmdf/free/nproc/os, build+services+TLS/tunnel+nguồnđiện; không coi dualboot là VPS24/7, không tự cài Linux từWindows hoặc mua hosting.


## D105 — Hạ tầng theo yêu cầu giảng viên (19/11/2026)

Yêu cầu mới của chủ dự án thay quyết định MySQL/cùng VPS trước đây: Next.js trên Vercel; Laravel API trên VPS; PostgreSQL là database đích; DNS tại Cloudflare, proxy bảo vệ API origin; gửi mail bằng Resend; realtime bằng Pusher. Giữ PHP8.4/Laravel12 và Next.js JavaScript theo D98. Domain có thể mua ở registrar bất kỳ hỗ trợ đổi nameserver, không tự mua/đăng ký dịch vụ. Realtime trạng thái đơn là extension được chấp thuận, thay mục ngoài scope realtime socket của PRD; chỉ báo thay đổi và đọc lại API, không quyết định paid tại client.

Conflict đã giải quyết: MySQL trong AGENTS/PRD/DATABASE và single-VPS deploy bị thay bởi D105. Các bằng chứng MySQL lịch sử giữ nguyên, không đổi nhãn thành PostgreSQL. Không chuyển/xóa database chính hoặc Sandbox; chuẩn bị source/migration/CI và kiểm PostgreSQL trên DBtest mới riêng. Chuyển dữ liệu thật phải backup, đối chiếu tiền/tồn/ID/sequence và thử restore trước cutover.

Acceptance trước sửa (B00-PG): PostgreSQL migrations và meaningful tests quyền/tiền/tồn/concurrency đạt trên DBtest riêng; CI/smoke/E2E không còn ép MySQL; Resend SDK + cấu hình private; Pusher private order channel có owner/admin/session/CSRF/throttle, event sau commit và payload không PII; frontend nghe tín hiệu đọc lại API, giữ polling khi mất kết nối; deploy chỉ backend trên VPS và Vercel root frontend, HTTPS/Cloudflare Full(strict), origin firewall/AOP, cache bypass private. Kiểm dependency audit/format/build thích hợp. Không cam kết điểm10 hay throughput khi chưa đo. Provider delivery/live socket, CI remote, VPS/TLS/load và data cutover chưa chạy phải ghi chưa verified; backend/release gate không nâng.


## D106 — Hoàn thiện chạy local PostgreSQL và đồng bộ plugin Figma (19/11/2026)

Chủ dự án yêu cầu hoàn thiện để tự deploy và xác nhận sửa plugin Figma local hiện có. Cho phép backup nhất quán MySQL cũ, tạo database PostgreSQL development riêng, chuyển toàn bộ dữ liệu nghiệp vụ với IDs/UTC/JSON/boolean/constraints/sequences, đối chiếu counts+hash+tiền+tồn, thử pg_dump/restore trên DB riêng rồi chuyển cấu hình local (giữ APP_KEY/uploads và giữ bản gốc để quay lại). D106 mở bước cutover local vốn chưa được D105 cho phép; không cần đợi VPS để làm. Không fake payment/đổi trạng thái đơn để vượt constraint, không gửi provider/mail thật, không deploy/mua dịch vụ. Không xóa backups trong lúc còn làm hoặc chưa đủ kiểm chứng. Figma chỉ plugin offline, không tuyên bố canvas cloud đã cập nhật.

Acceptance trước mutation: source MySQL được đọc nhất quán + dump private; migration PostgreSQL main riêng đạt, đối chiếu mọi bảng nghiệp vụ bằng count/hash đã canonicalize với generated fields loại riêng, kiểm tổng tiền/tồn và sequence, kiểm file uploads tồn tại; dump/restore PostgreSQL trên DB khác đối chiếu lại. Main .env giữ key/provider settings, bật pdo_pgsql và cấu hình app local chạy được; test không chạm main, suite backend/concurrency/HTTP/queue và E2E/build/audit phù hợp; vận hành worker/scheduler/production preflight phát hiện thiếu config thay vì báo ready giả. Plugin mới khớp D100 home10/15, desktop5cột/catalog25, D104 mua riêng/giỏ/delivery CTA/failure/manual refresh, giữ mobile app Figma và business rules; mock/prototype geometry/navigation/build ZIP verified, không khẳng định canvas thật hay production load đã kiểm. TASKS→PROGRESS→HANDOFF→STATUS cuối việc, không commit/push/deploy.


## D107 — Xóa MySQL và backup MySQL theo yêu cầu trực tiếp (20/11/2026)

Chủ dự án yêu cầu sau khi PostgreSQL chạy đúng thì xóa các phần MySQL kể cả backup. Chỉ dẫn này thay yêu cầu giữ rollback MySQL của D105/D106 trong phạm vi được xác nhận. Acceptance trước mutation: PostgreSQL main/restore/counts/hash/APP_KEY/media còn đúng; inventory các file/schema phụ thuộc; di chuyển dữ liệu Sandbox quan trọng sang PostgreSQL và đối chiếu trước loại MySQL để không mất receipt ngoài ý muốn; dừng đúng tiến trình cũ trước xóa; xác minh absolute paths trong workspace trước mọi recursive delete/move. Không xóa PostgreSQL/dump/uploads/APP_KEY hoặc rollback catalog không liên quan. Framework vendor drivers/historical audit không coi là dịch vụ MySQL của ứng dụng.

Inventory phát hiện cụm dùng chung còn có thu_thanh_em3/thu_thanh_em3_test (EM3 ngoài phạm vi theo quy định cũ). Đã hỏi chủ dự án xác nhận phạm vi riêng; chưa được xóa hai schema này hoặc cả cluster chứa chúng nếu chưa có câu trả lời. Tiếp tục chuyển Sandbox/dọn source và backup riêng Thu Thanh trong khi chờ. Không mua/deploy/provider thật, không xóa các metadata backup từng bị policy-blocked vì không thuộc MySQL.


### D107 — Owner xác nhận phạm vi cuối — 2026-11-20T00:42:52+07:00

Chủ dự án trả lời rõ: “Xóa cả dữ liệu EM3 và toàn bộ cụm MySQL này”. Cho phép xóa vĩnh viễn thu_thanh_em3/thu_thanh_em3_test cùng toàn bộ engine/data/config/log/backup MySQL của cụm trong D:/ThuThanh-WebDesign/.runtime. Thay điều kiện chờ xác nhận ở trên; không cần hỏi lại. Không đụng source repo EM3 ở thư mục khác. PostgreSQL main/test/restore/Sandbox, dumps PostgreSQL, APP_KEY/media và audit JSON vẫn giữ. Trước xóa kiểm đường dẫn tuyệt đối/chặn reparse, dừng đúng MySQL trong workspace; sau xóa phải không còn các target/port3307 và API137SKU/web200 vẫn đạt.

## D108 ? R? so?t to?n repo v? th?ng nh?t h??ng d?n hi?n h?nh (20/11/2026)

Ch? d? ?n y?u c?u ki?m tra t?t c? file k? c? README r?i h??ng d?n t?ng b??c ?? ho?n thi?n. Ph?m vi: qu?t source/config/docs first-party; ??i chi?u ???ng d?n, stack, l?nh ch?y, env m?u, CI/deploy v? plugin V38; s?a c?c h??ng d?n hi?n h?nh c?n m?u thu?n D105?D107. L?ch s? c? ng?y trong PROGRESS/DECISIONS v? migration/upgrade t??ng th?ch ???c gi? ??ng s? ki?n, kh?ng ??i nh?n test MySQL c? th?nh PostgreSQL. Kh?ng s?a vendor/node_modules hay coi ch?ng l? runtime MySQL.

Acceptance tr??c s?a: README/START-HERE/HANDOFF/local guide/deploy guide kh?ng c?n ch? d?n ch?y script ?? x?a ho?c gi? backup MySQL kh?ng t?n t?i; PRD/DATABASE/API/security/course checklist ph?n ?nh PostgreSQL/Resend/Pusher/V38; link n?i b?, JSON/check_pack, frontend check/test v? API/environment smoke th?ch h?p ??t. H??ng d?n ph?n bi?t Windows v?i VPS, gi? APP_KEY/media/PG dumps, c? ti?u ch? nghi?m thu mail/realtime/TLS/backup/provider/load th?t. Kh?ng reset/schema mutation/commit/push/deploy/provider mail hay payment th?t; kh?ng n?ng release gate t? vi?c s?a docs.

D108 ph?t hi?n khi th?c thi b? ki?m: verify-store-assets.py c?n gi? ??nh 24 PNG trong khi V38 c? 35 s?n ph?m, 11 thumbnail m?i JPEG145px thi?u source hash. Ph?m vi s?a ???c m? ?? d?ng PNG gi? nguy?n ?i?m ?nh ngu?n cho 11 m?u n?y, b? sung provenance/format, t?nh s? l??ng ki?m th?c, build l?i V38 v? ch?y regression plugin. Kh?ng ??i ?nh/catalog/database website ho?c nghi?p v?; checksum ZIP m?i ph?i c?p nh?t STATUS, l?ch s? hash D106 gi? nguy?n.

## D109 — Sửa local kẹt tải sản phẩm và cổng 3000 (2026-11-20T03:47:09+07:00)

Chủ dự án báo EADDRINUSE và trang chủ kẹt Đang tải dữ liệu, yêu cầu sửa. Kiểm ban đầu: PostgreSQL55432/API8000/Next3000 đều chạy, listener3000 đúng repo; products và storefront qua proxy trả200, browser mới tải được catalog. Không kết luận mất dữ liệu hoặc bootstrap lại. Source API client thiếu deadline GET: một auth/me hoặc storefront bị treo có thể giữ trang ở loading mãi.

Acceptance trước sửa: giới hạn thời gian đọc GET, giữ caller abort và không retry mutation; test timeout trước/sau headers, caller cancellation và request kế tiếp không bị ảnh hưởng. Browser kiểm loading thoát khi auth/catalog treo và lỗi có nút thử lại, sau khôi phục tải sản phẩm được. Restart chỉ đúng Next trong workspace, kiểm web/API/products và browser thực; helper local xử lý cổng đang dùng mà không tự tắt process khác. Không đổi UI/giá/tồn/quyền/schema/APP_KEY/dữ liệu hoặc gọi provider/deploy. Cập nhật checkpoint đúng kết quả; gate production giữ nguyên.

## D110 — Email-only newsletter, mobile app và User Flow Landing (2026-11-20T04:03:25+07:00)

Chủ dự án yêu cầu bỏ dòng checkbox bản tin: chỉ nhập email và nhấn Đăng ký; đồng bộ website và plugin offline, kiểm web/mobile, thêm section User Flow trong Landing page. Thay yêu cầu checkbox/consent payload D43: thao tác gửi form Đăng ký nhận bản tin thể hiện ý định đăng ký, server ghi consented_at hiện có tại lần đăng ký đầu; không gửi consent:true ngầm, không thêm campaign hoặc gọi provider thật. API nhận email-only, vẫn normalize/validate/CSRF/throttle/unique và không lộ subscriber. Không schema mutation hoặc sửa dữ liệu lịch sử.

Acceptance trước sửa: backend NewsletterTest kiểm email-only/idempotency/invalid-extra/CSRF/throttle đạt trước UI; API/OpenAPI đồng bộ. Web không còn checkbox, submit email-only và busy/error/success giữ; E2E trên DBtest riêng. Plugin V39 bỏ checkbox, desktop giữ5cột/25trang/home10+15/checkout hợp đồng, mobile có app bar/back/bottomnav/safe area không footer/breadcrumb web, cải thiện home/category/product browsing theo app thay copy desktop. Landing có section User Flow editable, nodes/arrows/branches đúng auth/cart/direct/delivery/pickup/payment/order/admin/newsletter, không tuyên bố paid chỉ từ Return, không release khi uncertain. Kiểm containment/navigation/photo preservation/upgradeV38/rollback/cancel/build/ZIP và preview; canvas native vẫn owner import offline. Không commit/push/deploy/provider/email/payment thật, không sửa tiền/tồn/quyền ngoài thay đổi newsletter được yêu cầu.

## D111 — File Word hướng dẫn hoàn thiện dự án (2026-11-20T17:00:18+07:00)

Chủ dự án yêu cầu một file doc hướng dẫn từng bước các việc còn phải làm. Tạo DOCX tiếng Việt trong docs, dựa STATUS/HANDOFF/source và runbook hiện hành, có status snapshot, thứ tự prerequisite, provider settings trước/sau deploy, VNPAY Sandbox/local/domain/IPN/Return/QueryDr, PostgreSQL/data/APP_KEY, FigmaV39, Git/CI/VPS/Vercel, kiểm security/performance/backup và bộ nộp. Không giả định đã có domain/VPS hoặc credentials mới; không chứa secrets hay dữ liệu khách hàng.

Acceptance trước tạo file: DOCX mở bằng Word, văn bản/bảng/checklist đọc được và Việt ngữ đúng; lệnh tách Windows/VPS, template được đánh dấu rõ; phân biệt kiểm local cũ với việc chưa nghiệm thu. Kiểm DOCX XML/links/content, xuất PDF private để kiểm pagination và preview nếu Word có sẵn; lưu checksum. Chỉ tạo tài liệu và checkpoint, không deploy/commit/push/schema/provider/mail/payment hoặc chạy lại app tests không cần thiết.

## D112 — Scrollbar bestseller, footer gọn và mobile commerce app (2026-11-20T18:03:00+07:00)

Chủ dự án gửi hai screenshot plugin: bestseller có hai nút mũi tên thay scrollbar, footer line spacing quá lớn; yêu cầu mobile dựa cách tổ chức app bán hàng chuyên nghiệp như Shopee. Phạm vi được chấp thuận: sửa plugin offline lên V40 và footer website tương ứng; web bestseller hiện đã dùng native overflow/scrollbar, giữ thao tác chọn hạng/keyboard. Mobile app thiết kế lại home/search/category/product cards/detail/cart/orders/account và thanh điều hướng theo phân cấp commerce, giữ nhận diện xanh Thu Thanh. Tham khảo bố cục app mua sắm từ trang Shopee chính thức; không copy logo/asset/campaign hoặc tự thêm voucher/rating/doanh số/chat nghiệp vụ.

Acceptance trước sửa: desktop bestseller native overflow + rail/thumb nhìn đúng scrollbar, không nút rank-up/down; wheel/touch và nội dung10hạng có thể cuộn, không trải hết danh sách tĩnh. Footer khoảng cách heading/link/row gọn, không chồng text; responsiveweb giữ touch targets phù hợp. Mobile search dễ thấy, category shortcut rõ,2cột card gọn, bestseller dạngrail, thông tin sản phẩm/quantity/CTA dễ tìm và safearea; preserve authintent/quantity/directcart/quote-review/money/order transitions. Mock phải kiểm geometry/nativeAutoLayout/scrollbar/route/financial/upgradeV39/manualphotos/rollback/UserFlow8; xem preview 402x874 các màn chính. Website CSS/check/build/browser/scroll/keyboard thích hợp đạt. Giữ diagram Landing,newsletteremailonly,35ảnhnguồn; không backend/schema/data/APP_KEY/provider/deploy/commit/push hoặc copy thương hiệu Shopee.
## D113 — Phân công file và Pull Request trước merge (20/11/2026)

Yêu cầu mới: hướng dẫn rõ file Đông/Thiện chịu trách nhiệm và lệnh commit/push/PR/review/merge vào DongDao27/Thu-Thanh-bookstore. Giữ phân công D98: Đông frontend/Figma/tài liệu, Thiện backend/PostgreSQL/thanh toán/hạ tầng; 60/40 là trách nhiệm, không phải tác giả lịch sử hoặc số commit. GitHub đọc thực tế: repo public, branches=[], size=0; origin local vẫn Payment. Chưa có base để mở PR. Việc tạo commit nền README trên main là ngoại lệ khởi tạo cần chủ repo quyết định, chưa được thực hiện hoặc ngầm cho phép trong task tài liệu.

Acceptance trước sửa: guide PowerShell đầy đủ, danh mục từng file hiện có không thiếu/trùng và không đưa file đã xóa/secret/runtime vào repo mới; clone riêng giữ dirty workspace; Thiện PR backend vào integration trước, Đông review rồi Đông PR frontend/Figma/docs vào integration, Thiện review; PR cuối vào main chỉ sau CI đầy đủ xanh và review. Cung cấp export chọn file có kiểm origin/nhánh/trạng thái, kiểm trong repo tạm; không commit/push/PR/merge hoặc sửa Git identity/remote thật. Checkpoint một người cập nhật; gate backend/release giữ nguyên.

## D114 — Đưa phần Đông lên PR, để Thiện tự commit backend (20/11/2026)

Chủ dự án yêu cầu thực hiện commit/push/PR phần mình, còn file chỉ hướng dẫn Thiện commit phần còn lại. Tài khoản hiện hành DongDao27 và Git identity local khớp Đông; dùng đúng identity sẵn có, không giả tác giả lịch sử. D114 thay thứ tự xuất bản D113: Đông mở draft PR vào main trước, Thiện PR backend riêng vào main để Đông review; sau khi backend merge, đồng bộ main vào branch Đông, chạy đủ CI và Thiện review PR Đông. Không merge trong task hiện tại, không commit phần T1 dưới danh nghĩa Đông. Repo đích vẫn branches=[]; đã hỏi chủ repo ngoại lệ tạo duy nhất README nền main, chưa có câu trả lời thì không làm mutation GitHub phụ thuộc nền.

Acceptance trước sửa/thực hiện: rút gọn guide thành file/lệnh dành cho Thiện, giữ manifest đúng owner; stage/commit riêng D1/D2/D3 trong clone mới, xác minh danh tính, không secrets/private/runtime/DBdata hoặc đổi repo gốc. Kiểm frontend/plugin/docs phù hợp và exact-tree trong clone; PR ghi rõ thiếu backend nên draft/E2E chưa thể nghiệm thu. Push chỉ repo Thu-Thanh-bookstore đúng branch feature, không force/merge/deploy. Kiểm PR/files/commits thực sau tạo; cập nhật bằng chứng và checkpoint trung thực. Nếu chưa được phép tạo nền, hoàn tất chuẩn bị local và giữ phần GitHub chờ câu trả lời, không tự diễn giải sự im lặng là cho phép.

Chủ repo đã trả lời câu hỏi ngoại lệ: yêu cầu sửa README tương tự AkioCkist/daily_vocabulary để làm nền rồi dùng PR. Đây là chấp thuận cụ thể cho README nền duy nhất trên main; các source khác chỉ qua branch/PR, không merge task này. Đã đọc README repo mẫu bằng GitHub connector; chỉ tham khảo cấu trúc, không sao chép chức năng/stack/số đo chưa có. README nền là README dự án đã sửa, không README trống;300file Đông gồm1README nền +299file PR, D3 diff29 thay vì30 vì README đã có trong base. Thiện388file giữ nguyên. API/DB/UI/business không thay.

## D115 — Mobile gọn, điều hướng luôn hiện và User Flow chuẩn (21/11/2026)

Chủ dự án cung cấp file Figma H6ixSJmRWBtwI9B1CDhEBi, yêu cầu bỏ chữ thừa, chức năng dễ hiểu, chi tiết có icon thêm giỏ và Mua ngay, Trang chủ/Danh mục/Giỏ hàng/Tài khoản luôn hiện; User Flow theo hình Start/Step/Decision/End. D115 thay quyết định D112 ẩn bottomnav khi có CTA. Đã đọc canvas thực:106mobileframes; thanh dưới lệch y âm hoặc574 do rootAutoLayout/MAX constraints sau resize; không thể lấy mock cũ làm bằng chứng native.

Acceptance trước sửa: sửa trực tiếp file thật và source plugin V41; dùng viewport nội dung cuộn riêng giữa header/action/nav, không chồng UI, thanh dưới đúng y trong874px. Customer nav4đích luôn hiện, CTA chi tiết icon giỏ + Mua ngay, CTA giỏ/checkout ngắn rõ; giữ số lượng/giá/tồn/route/auth và paid chỉ sau xác nhận. Rà các đích điều hướng/trạng thái lỗi/hủy/hết hạn/đối soát, không gán sai tiền/SKU. UserFlow editable có circle start/end, rectangle thao tác, diamond điều kiện, Yes/No và arrow/loop tránh chữ/shape; mô tả việc người dùng thấy/làm, không dồn jargon server lên app. Native screenshot, containment và route audit; plugin mock/build/asset và các kiểm phù hợp đạt. Không sửa Laravel/schema/provider/data hoặc merge/deploy. Plugin trong workspace gốc đang bị xóa bởi thay đổi ngoài phiên; giữ nguyên deletion, làm trên source còn nguyên ở D:/ThuThanh-review-dong, không tự restore/ghi đè dirty work.
