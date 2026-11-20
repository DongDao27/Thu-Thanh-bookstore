# Bàn giao hiện hành — D115, 21/11/2026

Task F00-MOBILE-V41 in_progress vì native acceptance còn chờ. [Review](FIGMA-REVIEW.md) phân biệt rõ native/offline. FileFigma H6ixSJmRWBtwI9B1CDhEBi/mobilepage7:25879 đã patch thành công101màn thường,24CTA/5productbars/29bindings;5overlaysgiữ. Sau đó Figma Starter hết lượtMCP,không thể QA ảnh cuối hoặc cập nhật8UserFlow trong canvas. Đừng nói toàn bộFigma đã xong.

SourceV41 hoàn thiện ở D:/ThuThanh-review-dong/thu-thanh-figma-clicon,khôngrestoreplugin bịxóa ở workspacegốc. ZIP bàn giao output/figma/THUTHANH-FIGMA-V41.zip;giải nén/importmanifest/chạy plugin **Mobile+UserFlow** mặc định rồi **Cập nhật thiết kế**. Chế độ này giữ nguyên desktopIDs,update mobile/Landing và8flow chuẩn. User cần Present để nghiệm thu native hoặc tiếp tụcMCP khiquotareset. Chỉ import không đổi canvas.

Kiểm V41 mock214screens/8flow/3287routes/27925nodes/depth14;101normalmobile navalways,CTA722/nav794;money/auth/quantity/directcart/payment/cancel/upgradeV40/manualphotos/rollback/cancel/mobileonly đạt. Preview9app+2flow ở output/figma-d115 đãxem,khôngphảinativefinal. Evidence .runtime/d115-evidence.json/log d115-figma-final.log. No backend/business/schema/provider/deploy/merge;gatesexternally_blocked/not_started giữ. PR22 được quan sát OPEN/ready (đã đổi khỏi draft ngoài phiên);giữ nguyên trạng thái,không merge.388fileThiện khôngchạm. Checkpoint cập nhật một người,giữ nguyên originalGitindex/branch/remotePayment và mọi userdeletions/privateDB/keys/media.

## Bàn giao D114 (lịch sử)

Task B00-DONG-PR hoàn tất xuất bản theo yêu cầu. **[PR#22](https://github.com/DongDao27/Thu-Thanh-bookstore/pull/22) đang OPEN/draft**, base main, branch feat/dong-storefront-figma-docs,authorDongDao27. Chưa merge source hoặc deploy. Backend/release gates vẫn externally_blocked/not_started; tiếp B07/B00-PG-LIVE và Thiện PR backend.

Chủ repo chấp thuận README theo cấu trúc Daily Vocabulary làm nền. Main chỉ README, commit e37d579b519321b496d7ecb323a9b63bf78b5588. Tổng phần Đông300file:1README nền+299filePR (D1=258,D2=12,D3=29);sourcecommits9554286/3671bb8/078eba5. Commit docscheckpoint sau PR ghi bằng chứng thật;HEAD cuối tra .runtime/d114-evidence.json/GitHub,không giả hash tự tham chiếu. Remote owner/path/main README-only đã kiểm;0file T1Thiện được commit. GitHub connector tạoPR thiếu quyền403;CLI authenticatedDongDao27 đã mởPR thành công.

Guide [Thiện](PHAN-CONG-VA-COMMIT.md) chứa đúng388file backend/PG/deploy và lệnh clone riêng→copy/stage đúngCSV→commit→push→PRmain/reviewerDongDao27. File local `output/handoff/THIEN-BACKEND-SOURCE.zip` gồm388source+CSV+guide,khôngsecret/data/runtime,CRC vàsecretcheck đạt;SHA256 cuối ở file.zip.sha256 và evidence. Thiện dùng tài khoản thật;backend PR để Đông review trước,không commit thay Thiện. Sau backend merge,cập nhật main vào branch Đông,chạy đủCI rồi Thiện review PR22. Không tắt check để merge.

Kiểm D114:71JS/import,16frontendtests,build22static+2dynamic,plugin214screens/3130routes/8diagrams,136original/35losslessPNG vànpm audit0 đạt. Manifest688file vàguide4PowerShellblocks đạt;docs check_pack21required/59tasks/56API. Hosted Frontend sourcecommitpassed;browser/E2E failed tại composer install do thư mục backend còn thiếu (log44773323251),không gọi passed hoặc release-ready. Supabase preview tích hợp ngoài đangskipped,không phải kiểm stack/projectđạt. Backend/E2Efull lịch sử vẫnD107/D106,chưa chạy lại cho task này. COURSE-BRIEF đầu vào giữ nguyên,trailingwhitespace của tài liệu nhận không sửa;diff-check phần còn lại đạt với CRLF được nhận đúng.

Workspace gốc D:/ThuThanh-WebDesign vẫn dirty,branch feat/d87-nextjs-figma-padding,HEADfeb16bd,originPayment,index không stage;publication ở D:/ThuThanh-review-dong với remote đúngrepo. Không push workspace gốc. Giữ DB/env/APPKEY/media/private/runtime như D112 bên dưới. Lệnh tiếp theo cho Thiện nằm trong guide;chưa biết username để autoassign reviewer nên chưa tự gán tài khoản.

## Bàn giao D113 — lịch sử, thứ tự xuất bản đã thay bằng D114

Task `B00-TEAM-COMMIT-GUIDE` hoàn tất local: [hướng dẫn Đông/Thiện](PHAN-CONG-VA-COMMIT.md), [danh mục 688 file](COMMIT-FILES.csv), công cụ `scripts/prepare-team-commits.py`. Thiện T1:388 file backend/PG/operations; Đông D1:258 frontend, D2:12 Figma, D3:30 docs/tooling. Một chủ trì mỗi file; tỷ lệ trách nhiệm60/40 không phải số file hoặc tác giả lịch sử.

GitHub đọc ngày20/11: Thu-Thanh-bookstore public,size0,branches=[]; local origin vẫn Payment. Repo rỗng cần chủ repo quyết định ngoại lệ commit nền README trước khi tạo PR; chưa tự thực hiện hoặc ngầm cho phép ngoại lệ. Source xuất sang clone riêng, Thiện PR backend→integration/Đông review; Đông PR frontend/Figma/docs→integration/Thiện review; cuối cùng PR integration→main với đủ CI và review. Không đổi remote/identity/index/branch gốc, không push/PR/merge/deploy hoặc thay database/provider.

Kiểm thực D113: manifest verify688file không thiếu/trùng;10block PowerShell parse; fixture export/dry-run và guards main/wrong-origin/dirty/stale-manifest/private-dump đạt. Fixture Git commits chỉ trong `.runtime/d113-fixtures/`, dùng danh tính giả `Test Fixture` cho kiểm thử; không phải commit dự án hoặc lên GitHub. `python scripts/check_pack.py` đạt21required/58tasks/56API/71links; diff-check đạt sau bỏ dòng trắng dư ở cuối DECISIONS. App tests không chạy lại vì không đổi ứng dụng. Evidence `.runtime/d113-evidence.json`. Lệnh tiếp theo chỉ đọc: `python scripts/prepare-team-commits.py verify`; người dùng theo guide để quyết định nền, collaborator và thực hiện PR. Gate backend/release vẫn externally_blocked/not_started; next B07/B00-PG-LIVE. Giữ toàn bộ dữ liệu private như phần D112 dưới đây.

## Bàn giao giao diện D112 còn hiệu lực

Task `F00-APP-POLISH` hoàn tất local. Chủ dự án yêu cầu thanh cuộn bestseller, footer gọn và mobile giống cách tổ chức app mua sắm chuyên nghiệp. [Review Figma](FIGMA-REVIEW.md), [hướng dẫn import](../thu-thanh-figma-clicon/HUONG-DAN.md). Backend gate vẫn `externally_blocked`, release `not_started`; tiếp theo B07/B00-PG-LIVE.

## Thay đổi

- Plugin **V40** bỏ hai nút nhảy lên/xuống của bestseller. Desktop dùng viewport cuộn dọc và rail/thumb; mobile dùng rail cuộn ngang. Trong Figma, rail/thumb minh họa trạng thái đầu, nội dung cuộn native bằng con lăn/trackpad/vuốt; không phải DOM scrollbar tương tác đầy đủ. Website giữ scrollbar native thực.
- Footer plugin: link cao28px, bước dòng32px, cách heading38px. Website: link desktop30px + gap2px; mobile giữ vùng chạm ít nhất44px.
- Thiết kế lại9 màn mobile chính: search trong app bar,6shortcut danh mục,card2cột cao292px,rail bán chạy/mới và grid gợi ý; back theo ngữ cảnh và tab đang chọn rõ. Chi tiết có quantity và CTA cố định; giỏ/tổng tiền gọn; checkout có bước Thông tin/Xác nhận với review chỉ đọc; đơn hàng dạng card/filter; tài khoản có shortcut.
- Giữ nhận diện Thu Thanh, không chép asset/campaign Shopee hoặc mở thêm nghiệp vụ. Auth/số lượng/Mua ngay giữ giỏ/quote-review/giá/tồn/trạng thái thanh toán, newsletter email-only và8UserFlow giữ nguyên.

## Kiểm thực

Plugin mock cuối đạt214screens (212prototype +2reviews),8diagram,3page,3130routes,35SKU;27731nodes/depth13. Geometry/nativeAutoLayout/scrollbar/CTA cố định/review chỉ đọc/money/quantity/auth/routes/upgradeV39/ảnh sửa tay/rollback/cancel đạt. Log có injected failures chủ ý; kết quả cuối passed/exit0.

Frontend:71file syntax/import,16tests và production build22static+2dynamic đạt. Asset136originals/35losslessPNG đạt. Browser kiểm cuộn wheel và chọn hạng bằng ArrowDown; footer375/768/1440 không tràn ngang. Đã xem9SVG preview native và desktop rank/footer; chưa kiểm canvas Figma native. Web/API/proxy đều HTTP200. Fullbackend177/2170 và coverage91,35%/fullE2E29/29 vẫn là D107/D106, không chạy lại cho task CSS/plugin.

Evidence: `.runtime/d112-evidence.json`, `d112-figma-final.log` (UTF16), `d112-browser-final.log`, `d112-word-render.json`; ảnh `output/playwright/d112-*.png`.

## Artifact và thao tác tiếp theo

- `output/figma/THUTHANH-FIGMA-V40.zip`, SHA256 `da2bc5de92196fec1ca92d3f578fe525d54c1c9eab7d9717df277b3e7fdf6669`. Giải nén, import manifest V40 rồi Run/Cập nhật thiết kế; Present để kiểm canvas thật. Chỉ import không thay canvas.
- File Word hướng dẫn hoàn thiện đã đổi tham chiếu sangV40 sau khi kiểm hash bảo vệ sửa đổi của người dùng; mở/export bằng Word16trang đạt. SHA256 hiện tại `489988e231b1f25d52a261ccf1bd2248164565c4a8563c5ea893726231f17843`; hash D111 trong PROGRESS là snapshot trước cập nhật D112.

Đã mở lại PostgreSQL55432/API8000/web3000 để kiểm UI; không bật worker/scheduler/provider thật. Giữ các dịch vụ chính chạy cho người dùng; logs launcher `.runtime/d112-frontend.stdout.log` và `d112-backend.stdout.log`. Sau reboot: `python scripts/postgres-test-local.py`, `powershell -ExecutionPolicy Bypass -File scripts/start-backend.ps1`; terminal khác `powershell -ExecutionPolicy Bypass -File scripts/start-frontend.ps1`. Không chạy PHPUnit và E2E đồng thời.

## Dữ liệu và nghiệm thu còn lại

Giữ env/APP_KEY, PostgreSQL main/test/Sandbox/restore DB riêng, cluster/tools/password/settings private, uploads/workbook/ảnh gốc và PG dumps/media `.runtime/d106-cutover`, Sandbox dump `.runtime/d107-sandbox`. Không key:generate/migrate:fresh/reset hoặc restore đè. MySQL/backups/cụm EM3 trong workspace đã xóa D107; source EM3 ở repo khác không sửa. Metadata catalog từng bị chặn thuộc scope khác, xem CLEANUP/STATUS.

Còn VPS/Vercel/domain/TLS/AOP/firewall/cookie/cache/client-IP/worker/timer, Resend/Pusher thật, VNPAY QueryDr/expiry/reconcile hai đơn lịch sử, backup offsite+restore, CI remote/load và owner native Figma acceptance. Không sửa Laravel/schema/business data, không gọi provider/mail/payment hoặc commit/push/deploy trong task này. Branch `feat/d87-nextjs-figma-padding`, HEAD `feb16bd` là commit cũ; git dirty thực tế, last_verified_commit=null.
