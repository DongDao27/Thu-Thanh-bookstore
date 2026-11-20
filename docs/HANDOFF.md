# Bàn giao hiện hành — D114, 20/11/2026

Task B00-DONG-PR in_progress. Chủ dự án yêu cầu thực hiện commit/PR phần Đông, để Thiện tự commit phần còn lại. 300file Đông đã export byte-match sang D:/ThuThanh-review-dong; guide PHAN-CONG-VA-COMMIT chỉ giữ hướng dẫn Thiện388file T1. PR Đông dự kiến draft/main, Thiện backend PR/main riêng để Đông review trước; không merge trong task hiện tại. Không thực hiện provider/deploy.

Chủ repo đã chấp thuận README theo cấu trúc repo Daily Vocabulary làm nền rồi dùng PR. Đã sửa README và push duy nhất baseline README `e37d579b519321b496d7ecb323a9b63bf78b5588` vào main. D1 frontend9554286/D2Figma3671bb8 đã commit local;D3docs và pushfeature/open draftPR còn thực hiện. Lệnh tiếp theo: hoàn tấtD3, kiểm tree300file (1README nền+299filePR),push feature và tạo draftPR,đọc lại remote files/commits. Không merge hoặc commit388fileThiện. Evidence .runtime/d114-evidence.json.

Kiểm D114:71JS/import,16frontendtests,build22static+2dynamic,plugin214screens/3130routes/8flow và136original/35PNG passed. Đã kiểm không secret env thật trong300file trước export; source env/keys/DB/private/media/originalGit giữ. Git original vẫn feat/d87-nextjs-figma-padding,HEADfeb16bd,originPayment,index không stage. Gate backend externally_blocked/release not_started,nextB07/B00-PG-LIVE sau publication. Chi tiết bảo toàn dữ liệu trong D112 bên dưới.

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
