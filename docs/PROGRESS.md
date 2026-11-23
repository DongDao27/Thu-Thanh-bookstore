# Tiến độ gần nhất

## 22/11/2026 — Rút gọn repository

Đã lưu snapshot private của lịch sử Git, index, config và 682 file làm việc trước khi dọn. Đồng bộ lại các bản sửa CI/ảnh/polling từ main23842a5; giữ nguyên logic thanh toán. Chuyển source thiết kế sang design/figma và workbook gốc sang data/catalog; cập nhật đường dẫn build/verifier. Bỏ checklist môn học, hướng dẫn hoàn thiện, chia commit và launcher PowerShell cũ. Viết lại README và checkpoint gọn.

Kiểm tra local đã đạt: 179 backend/2.180 assertions, 29 E2E, 16 frontend; build/check/import, 14 tài liệu/22 liên kết/OpenAPI, Figma V41 và nguồn ảnh đạt. Workbook di chuyển giữ nguyên byte. Không phát hiện secret/runtime trong các file chuẩn bị commit. Đã commit và push branch dựa trên main23842a5 của repo nhà sách. Source commit b2e04f2; cập nhật main bằng fast-forward sau khi hosted CI đạt. Bộ tài liệu kiểm chứng được giữ trong .runtime/repository-cleanup ngoài Git.

## Bằng chứng vận hành trước đợt dọn

Backend179tests/2180assertions, E2E29/29, frontend16/16/build đạt; main-domain72/72 và security7/7. SSH key-only và backup/restore mới đã kiểm. Chủ dự án chấp nhận Sandbox và yêu cầu không tiếp tục xử lý QueryDr99. Không có mutation thanh toán hoặc database trong đợt dọn repository.

## 22/11/2026 — Đối chiếu GitHub Project 4

Đã kiểm đủ 20 item thuộc repo nhà sách, đóng 13 issue kỹ thuật theo yêu cầu chủ dự án và giữ issue #2 đã đóng. Đồng bộ 14 mục vào Closed; 6 mục vào In progress. Các issue có ghi bằng chứng hiện hành, giữ riêng ghi chú lịch sử và không tự tick review của thành viên khác. Task tổng cập nhật checklist và giữ mở. Đọc lại API xác nhận cả 20 trạng thái khớp nhau.

Còn mở: #4 nghiệm thu canvas Figma; #12 QR tại quầy trên môi trường triển khai; #13 scheduler hết hạn đang tắt; #19 nghiên cứu người dùng; #20 slide/video và kiểm bản nộp; #21 task tổng. Vercel đã triển khai b287d3e thành công; curl tên miền chính/API trả 200. Không cần deploy lại chỉ vì đổi trạng thái Project. Không gọi QueryDr99.

## 23/11/2026 — Bắt đầu đối chiếu cách chấm

GRADING-AUDIT đang thực hiện trên clone GitHub và cluster PostgreSQL riêng không TCP. Đã tìm lại COURSE-BRIEF CSB35050 trong snapshot private. Chủ dự án xác nhận Figma thuộc môn khác, bản nộp chỉ cần GitHub và domain; không coi canvas/slide/video là blocker môn này. Chưa chốt điểm trước khi có kết quả clone/coverage/performance.

Kết quả GRADING-AUDIT: clone GitHub0067580 cài dependency/build được;179 backend/2180assertions,coverage91,37%,16 frontend/check71files và29E2E đạt;HTTPauth19,queue/scheduler testDB vàScramble đạt. Composer/npm không báo advisory. Sau cài dependencyPythonpsutil đầy đủ monitoringtest đạt; README hiện chưa hướng dẫn bước này. Đã đo Lighthouse mobile2lần53/55,LCP7,6/7,0s,CLS0,49,payload5599KiB. API đọc20/20HTTP200 ởconcurrency2,p95~0,685s; không phải kiểm tải lớn.

Phát hiện chưa sửa: trang chủ chờauth vàrequesttuần tự trước render; clone migrate/seed chỉ6danhmục/0sảnphẩm; APIguestthiếuAcceptJSONtrả500 vì route login không tồn tại (JSONAccepttrả401); thiếu bước monitoring/Python/testsetup vàảnhminhhoạ trongREADME. Chủ dự án xác nhận thầy đã chấp nhậnNext.js. Figma/video không phải blockerbảnnộp theoD120. Không sửaapp hoặcdeploy trongđợtaudit.

Đọc cấu hình live có chọn lọc quaSSH: debugfalse,monitoringtrue,Telescopefalse,cache/queue database. Không in secrets. Monitoring đã bật trênVPS; thiếu hướng dẫn demo clone không đồng nghĩa thiếu implementation.

## 23/11/2026 — GRADING-FIX bắt đầu

Chủ dự án yêu cầu cải thiện bài theo rubric. Tiêu chí: sửa API401, demo seed an toàn, giảm LCP/CLS/tải ảnh, README cài/test/monitoring và ảnh minh họa; kiểm trước xuất bản. Không đổi nghiệp vụ thanh toán, QueryDr99 hoặc bật scheduler thật.

GRADING-FIX source: APIguest401 mọiAccept có HTTPregression; explicitDemoCatalogSeeder24sảnphẩm/ảnh với env/databaseguard và testkhôngghiđè; bannerServerComponent, fetchsongsong, cropSVGoptimizer/lazy và mainminheight giữCLS0; READMEcài/test/monitoring/rubric và2ảnhdữliệugiả. Local181/2193,coverage91,37%,HTTP22,frontend16/check/build đạt;29E2Ecũ và2regressionhomepage đạt. Candidate đang chuẩn bị CI/freshclone/deploy; không đánh dấu hoàn thành trước kiểm remote.

PR26 đã merge (main cd2c20f, cây source giống candidatec9bd205 đã kiểm). Hosted backend/frontend và30E2E push/PR đều đạt. Clone mới từGitHub đã càiComposer/npm/Python, migrate/seed24sảnphẩm/24ảnh/0user,181test/2193assertions,coverage91,37%,HTTP22,ops/frontend/build vàrepositorycheck đạt. Website frontend đã triển khai; Lighthousemobile2lần92/94,bestpractices100,CLS0,LCP3,4/3,0s,payload1470/1386KiB.

Chưa hoàn tất GRADING-FIX: backendc9bd205 đã chuẩn bị, shared.env/storage giữ nguyên vàpreflight đạt, nhưngcurrentVPSvẫn23842a5. Chờ operator chạy sudo bash /srv/thu-thanh/private-transfer/grading-fix/activate.sh đểswitch/reloadOPcache/worker;scriptcórollback. Chưa gọi QueryDr, seed hay migration trênVPS. Sau kích hoạt cần kiểm APIguest401/readiness/dịch vụ rồi cập nhậtcheckpointdone vàđóngissue25.


Kích hoạt backend lần đầu không đạt và đã rollback về23842a5; nginx/PHP-FPM/worker active, /up200. Kiểm tra thấy bootstrap/cache/*.php thuộcdeploy:deploy660 vàthưmụccache mấtsetgid, khiếnwww-data không đọcđược. Đã sửa scriptprivateactivate.sh đểchuẩnhóa quyềncache, bootcheckaswww-data trướcswitch, chờreadiness vàghi bước lỗi. bash-n vàremote--check đạt; chờoperator chạy lạisudo, chưa đánh dấu livebackendpass.


GRADING-FIX hoàn tất: operator kích hoạt c9bd205 thành công sau khi sửa quyền cache. Đã kiểm lại bằng SSH: current trỏ c9bd205; Nginx, PHP-FPM và worker active; cache deploy:www-data 0660. Deployment configuration checks đều PASS. Kiểm HTTP bên ngoài 11/11 đạt: homepage, /up, categories/products 200; www 308 về apex; admin operations 401 cho */*, text/html và application/json qua cả API lẫn frontend proxy. Bộ kiểm ban đầu kỳ vọng urllib tự theo 308 nên báo một lỗi; đã kiểm rõ Location và sửa tiêu chí đúng cho redirect, không sửa website. Không gọi QueryDr hoặc thay dữ liệu thật. Đóng issue #25 và chuyển item tương ứng sang Closed sau khi xác minh.


## 23/11/2026 — SECURITY-NEWSLETTER bắt đầu

Theo yêu cầu chủ dự án: cài skill security, kiểm source/API/ports/secret, sửa finding xác nhận; bổ sung email xác nhận đăng ký bản tin qua Resend. Tiêu chí ở TASKS. Không gọi QueryDr hoặc bật scheduler tài chính. Kiểm SSH ban đầu chỉ public listen22/80/443; PostgreSQL loopback. Phát hiện source release đang cho nhóm www-data quyền ghi cần thu hẹp; chưa áp dụng thay đổi trước test.


SECURITY-NEWSLETTER: PR #28 đã merge main f149bc1, source candidate625a042 đã chuẩn bị trên VPS, current vẫn c9bd205. Hosted CI185tests/2221assertions,coverage91,43%,HTTP22,ops/frontend và30E2E đạt; SupabasePreview skipped. Script activation đã kiểm cú pháp/preflight; default-deny Nginx đạt trên testports18080/18443, fullroot80/443 chưa chạy. Còn operator sudo kích hoạt, kiểm lại worker UID/quyền source/defaultIP và thư xác nhận live; issue27/Project giữ In progress. Không gọi QueryDr hoặc gửi bulkmail.


SECURITY-NEWSLETTER live activation: current625a042, nginx/PHP-FPM/worker active; workerUserwww-data/NoNewPrivileges/ProtectHome/ProtectSystemstrict; source app group-write0. HTTP live6/6 đạt, rawIP404 không version, TLS rawIP từ chối. Bước probe đầu lỗi quyền đọc file kết quả (ownerwww-data,deploy không thuộcgroup), PHP standalone handler trả0 khiến script inpassed sai. Đã sửa privateprobe sang databasecache và catchThrowable, rootactivation yêu cầuJSONPASS; kiểm lạiPASS workerPID55457/UID33, invalidinputexit1. Không nới quyền dữ liệu.

Theo địa chỉ chủ dự án cung cấp, đã submit một đăng ký bản tin qua HTTP/CSRF thật:202, tạo1 encryptedjob, sau đópending0/failed0. Chờ chủ dự án xác nhận inbox; chưa đóngissue27 hoặc gọi đây là delivered vào inbox. Không gửi lại thư trùng.


SECURITY-NEWSLETTER hoàn tất: chủ dự án xác nhận đã nhận email xác nhận đăng ký bản tin. Source/hostedCI/livehạ tầng vàdeliveryinbox đã kiểm; đóngissue27 vàchuyểnProjectitemClosed. Không cần operator chạy lại activation. Đã sửa lỗi riêng ởprivateprobe, không dùng thông báo passed sai trước đó làm bằng chứng.


## 23/11/2026 — Chốt GitHub Project và bản nộp

Chủ dự án yêu cầu đóng tất cả. Đã đối chiếu22item,6cònIn progress (#4,#12,#13,#19,#20,#21). Website/API200,repoPUBLIC vàCI Backend/Frontend/E2E của main3b3caa0 đạt. Chuẩn bị cập nhật checklist hiện hành theoD122, giữ nguyên lịch sử riêng; task không chứng minh được không được gọi đã nghiệm thu production.


SUBMISSION-CLOSE hoàn tất: đọc lại API xác nhận22/22issuesClosed và22/22itemsClosed,Project4closed.18issuecompleted,4not_planned (#4Figma,#12QRquầylive,#13schedulerlive,#19nghiêncứungườidùng) có lýdo/phạmvicụthể. Checklist hiện hành đãtick các bước đối chiếu/chốt; các mục lịch sử chưa kiểm đổi thành ghi chú, không tick giả review/humanstudy/providerlive. GitHubPUBLIC,website/API200,CI main3b3caa0 đạt; đủchốt nộp GitHub/domain trong phạmviSandbox đã chấp nhận. Không thay sourceapp hoặcdeploy lại.


## 23/11/2026 — Biên tập README

Theo yêu cầu chủ dự án, sửa README theo hướng tài liệu sản phẩm/kỹ thuật; bỏ nhận xét chấm điểm và rubric. Giữ các bước clone/setup/demo/test đã kiểm và mô tả VNPAY Sandbox chính xác. Không thay source ứng dụng, không cần deploy thủ công.


README-POLISH hoàn tất: README giới thiệu sản phẩm, có ảnh storefront/admin, bảng công nghệ/kiến trúc, hướng dẫn local6bước, test/coverage vàAPI/monitoring, liên kết tài liệu và thành viên. Đã bỏ toàn bộrubric/nhận xét chấm điểm và ghi chú thầy chấp nhậnstack. VNPAY được mô tảSandbox trung tính; bản tin đưa vào tính năng/email. Đối chiếu command với source, repository14docs/26links vàdiffcheck đạt. Không sửaapp/database/deploy; Project4giữclosed.
