# Rà soát Figma — V41 / D115, 21/11/2026

File chủ dự án: https://www.figma.com/design/H6ixSJmRWBtwI9B1CDhEBi/Untitled?node-id=7-25879 . Phiên MCP:7acb55df-2d5d-4f0d-a642-987524fdd59e.

## Đã sửa trực tiếp và phần chưa thể xác nhận

Đọc canvas106mobileframes phát hiện root Auto Layout/constraints làm bar ở y âm hoặc574, che nội dung như screenshot. Đã sửa trực tiếp101màn thường: viewport cuộn riêng, nav4đích ở y794, CTA ở y722 khi cần, chi tiết có icon giỏ và Mua ngay; giữ29binding hành động. 5khung hộp thoại giữ cơ chế trên màn nền. Sau bulk mutation thành công, Figma Starter báo hết lượt MCP nên không thể chụp/đọc kiểm cuối hoặc thay8UserFlow trên canvas. Không tuyên bố native đã hoàn tất. Hai screenshot native đầu chỉ là bước sửa thử, không phải chứng cứ cuối.

## Bản offline hoàn thiện để áp dụng

ZIP THUTHANH-FIGMA-V41.zip và source ở D:/ThuThanh-review-dong/thu-thanh-figma-clicon. Plugin trong workspace gốc đã bị xóa ngoài phiên; không tự restore hoặc ghi đè deletion đó. Bản ZIP mới được bàn giao trong output/figma tại workspace gốc.

V41 dùng viewport112..722 khi có CTA,112..794 khi không; tab bar794..874. CTA và nav tách biệt, constraints MIN; không áp dụng Auto Layout cho shell định vị này. Nội dung bên trong vẫn dùng Auto Layout. Nav là instance của component dùng chung. Giỏ bỏ nút/tiền lặp thừa, phí vẫn là tạm tính tới checkout. Form Tiếp tục, review Đặt hàng/Đặt trước. Icon thêm giỏ có đúng route và trạng thái disabled, không dùng biểu tượng không bấm được.

8UserFlow editable được vẽ lại: circle Start/End, rectangle thao tác, diamond quyết định, nhánh có nhãn và loop; cùng centerline, không chữ BƯỚC/ĐIỀU KIỆN thừa. Giữ authintent/quantity/directcart/money/paid/reconcile/cancel invariants, không thêm API/DB/provider. Chế độ mặc định trong UI **Mobile + User Flow** giữ các node desktop nguyên ID, cập nhật mobile và Landing; full build vẫn có cho file mới.

## Kiểm thực

Mock cuối V41:214screens (212prototype+2review),8diagram,3pages,3287routes,35SKU;27925nodes/depth14. Kiểm mọi viewport/nav/action bands; nav ở tất cả101màn thường; shared navigation component,29purchase/primarybindings. Giá/số lượng/login/register/directBuy/quote-review/payment outcomes/admin/cancel và labels/containment đạt. UpgradeV40/39/38vàcácbảncũ, ảnh sửa tay, rollback/cancel và mobile-only giữdesktopIDs đạt. Log có injected failure/cancel chủ ý, cuối passed/exit0.

Playwright đã render9màn app và2sơ đồ từ SVG các lớp mock, không phải screenshot native Figma cuối. Ảnh output/figma-d115/browse-v41.png,checkout-v41.png,account-v41.png,user-flow-v41.png. Cart/checkout/chi tiết giữ tab+CTA không chồng nhau; sơ đồ dùng đúng shape và không che labels. Ảnh136originals/35PNG không đổi; node syntax/build/ZIP kiểm riêng. Không chạy lại Laravel/website tests cho thay đổi plugin, không gọi provider.

## Bước còn lại để hoàn tất native

Giải nén V41 → import manifest → chạy plugin trong file trên → giữ **Mobile + User Flow** → **Cập nhật thiết kế** → Present. Chỉ import không cập nhật canvas. Chủ dự án kiểm native sau Run; hoặc tiếp tục MCP khi quota được khôi phục. Trạng thái task in_progress cho tới khi áp dụng/kiểm phần native còn lại, không coi skipped/not_run là passed. Backend gate/release giữ nguyên.

## Bằng chứng D112 trước đây (lịch sử)

# Rà soát Figma offline — V40 / D112, 20/11/2026

Bản hiện hành là `output/figma/THUTHANH-FIGMA-V40.zip`. Sửa theo hai screenshot chủ dự án: bestseller dùng viewport cuộn và rail/thumb thay nút mũi tên; footer link28px/step32px, heading gần nội dung hơn. Website giữ native scrollbar, footerdesktop30px+gap2; mobileweb giữ44px vùng chạm.

Mobile thiết kế lại theo pattern commerce app: search appbar,6shortcut,2cột card292px,rankedrail/newrail/recommendedgrid,active tab rõ,chi tiết với stickyCTA,cartsummarygọn,checkoutprogress/reviewreadonly,ordercards/filters,accountshortcut. Không có footer/breadcrumbweb trên app; không thêm voucher/rating/sales/chat nghiệp vụ ngoài scope.

Source nghiệp vụ, giá/tồn/quyền,auth/quantity/directcart/quote-review vẫn giữ;8UserFlow và ảnh nguồn giữ. Preview9màn native ở output/playwright/d112-app-*.png; desktop rank/footer ở d112-plugin-*.png. Mock kiểm geometry/navigation/money/upgradeV39/manualphotos/rollback, browser kiểm webscroll/keyboard/footerresponsive; kết quả cuối ở STATUS/PROGRESS. Chưa kiểm canvas native thực của chủ dự án: import manifest V40 rồi Run/Present.

Tham khảo tổ chức app mua sắm từ [Shopee trên Google Play](https://play.google.com/store/apps/details?id=com.shopee.vn&hl=vi); không tải hoặc dùng lại logo/campaign/asset của Shopee. Brand xanh Thu Thanh và nghiệp vụ được giữ.

## Snapshot kiểm D110 — V39 (lịch sử)

Phạm vi: source plugin hiện có, kiểm cấu trúc/luồng bằng mock Plugin API và xem SVG preview trong browser. Không có file cloud được chỉ định, nên chưa kiểm canvas native của chủ dự án. Gói hiện hành `output/figma/THUTHANH-FIGMA-V39.zip`, [hướng dẫn import và Run](../thu-thanh-figma-clicon/HUONG-DAN.md).

| Phần | Đã đối chiếu / thay đổi |
| --- | --- |
| Newsletter website và prototype | Chỉ email + Đăng ký, bỏ checkbox/payload consent. API email-only, normalize, CSRF, throttle, unique/idempotent, không lộ subscriber; submit ghi thời điểm đăng ký đầu, không tự gửi campaign |
| Desktop | Header76/lề2%, 5cột; catalog25/trang; home10mới/15gợi ý không trùng IDs; full detail thay popup; giữ auth/số lượng, giỏ và Mua ngay riêng, quote/review/order/payment-init failure/manual refresh |
| Mobile chrome | 402×874, status bar/app bar/back/4tab/home indicator; không breadcrumb/footer/map/partner website; header và tab bar cố định khi cuộn |
| Mobile browsing | Search riêng,6shortcut danh mục với icon theo nhóm; catalog2cột; home có feed sản phẩm cuộn ngang, bỏ cặp banner biên tập desktop; không nén nguyên trang desktop |
| Landing page | Hai bản duyệt desktop/app và section User Flow phía dưới; nút User Flow trong panel đưa thẳng đến section |
| Sơ đồ hoạt động | 8diagram editable với nodes/điều kiện/mũi tên có nhãn; source tại `thu-thanh-figma-clicon/src/user-flows.json` |
| Nghiệp vụ | Mua ngay giữ giỏ; auth giữ ý định/số lượng; delivery5phút/pickup24giờ; server xác nhận paid, uncertain phải đối soát; hủy/trả tồn/gộp giỏ đúng một lần; quyền owner/admin |
| Bảo toàn khi chạy lại | Nhận diện managedV38; giữ ảnh thay tay/node ngoài plugin, rollback/cancel giữ bản cũ khi lỗi |

8sơ đồ gồm khám phá, tài khoản, giỏ/Mua ngay, giao hàng/VNPAY, nhận tại quầy, theo dõi/hủy đơn, quản trị và bản tin website. Chúng giải thích luồng; không tự gọi provider hoặc quyết định trạng thái tiền/tồn. Bảy nhóm prototype mỗi nền tảng vẫn giữ riêng.

Đây là mức khớp bố cục và các luồng đã kiểm, không phải xác nhận mọi pixel/dữ liệu động giống production. Prototype dùng 35SKU lấy mẫu; website có catalog137SKU tại lần kiểm local. Mobile là thiết kế app trong Figma, chưa phải ứng dụng native đã code/deploy.

Preview: `output/figma/preview/app-home.svg`, `app-catalog.svg`, `app-detail.svg`, `user-flow.svg`; ảnh đã xem tại `output/playwright/d110-*.png`. Biên bản test/log và checksum cuối trong STATUS/PROGRESS/HANDOFF. Sau import **V39**, phải **Run** rồi Present để canvas đổi.

Kiểm D110: backend NewsletterTest **5 tests / 33 assertions**; frontend **71 file / 16 tests / build22static+2dynamic**; E2E newsletter email-only/recovery **1/1** trên DBtest riêng. Browser 375/768/1440 không có checkbox, chỉ1input email, không tràn ngang. Plugin **214 màn duyệt/prototype,8sơđồ,3page,3285route**,35SKU; geometry/labels/auth/quantity/money/upgradeV38/manual images/rollback/cancel đạt. Log `.runtime/d110-figma-final.log`; lỗi inject trong rollback/cancel là ca kiểm có chủ ý, kết quả cuối passed. Không chạy lại fullbackend/fullE2E hoặc provider thật trong D110.
