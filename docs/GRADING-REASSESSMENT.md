# Đánh giá sau sửa — 26/11/2026

Đối chiếu bản chấm thử `Advanced_Nhom5_ThuThanhBookstore.pdf`:8,25/10, chấm commit8190455 lúc10:37 ngày26/11. SHA256 tài liệu: `46353759166a900ea8f8501caf8a9bf0a87433d7721075f4fc122aab83b6f218`.

Đây là **tự đánh giá kỹ thuật**, không thay điểm giảng viên. Source ứng viên nằm trên nhánh `fix/grading-feedback-20261126`, issue[#29](https://github.com/DongDao27/Thu-Thanh-bookstore/issues/29). Chỉ nhánh chính tại hạn nộp mới được giảng viên tính; chưa coi PR chưa merge là bài nộp đã cập nhật. Production/scheduler chưa được kích hoạt trong đợt này.

## Điểm dự kiến theo cùng rubric

| Phần | Bản chấm thử | Ứng viên sau sửa | Căn cứ |
|---|---:|---:|---|
| Task1 — Git/workflow | 0,625/1 | 0,750/1 | Đã thêm description repo; commit theo phạm vi, issue29 và PR nối issue. Giữ trừ0,125 commit lớn lịch sử và0,125 workflow/review lịch sử; không viết lại Git hay tự nhận review người khác. |
| Task2 — Database | 1/1 | 1/1 | Không đổi schema; giữ migration/seed và regression tests. |
| Task3 — UI/chức năng | 1,875/3 | 2,500/3 | Tailwind thực sự điều khiển responsive grid/toolbar; có React component tests và coverage toàn source Next.js >70% lines/statements. Giữ3(f) một phần vì hai video provider thật chưa có link. |
| Task4 — REST API | 2/2 | 2/2 | API/schema/tests đồng bộ trường catalog; full regression đạt. |
| Task5 — Logging/monitoring | 1/1 | 1/1 | Giữ bằng chứng trước, bổ sung alert lỗi IPN qua cơ chế notification/cooldown đã có test. Không coi scheduler live đang chạy. |
| Task6 — Performance | 1,75/2 | 2/2 | Intent prefetch và static/ISR một giờ được khai báo, test/build xác nhận; cache catalog40 sản phẩm và giảm invalidation. |
| **Tổng** | **8,25/10** | **9,25/10** | Dự kiến khi source và bằng chứng CI tương ứng vào main. |

Bốn khoảng trống được xử lý có tổng1điểm:3(g)+0,5;6(b)+0,25;1(a)+0,125;3(a)+0,125. Không cộng tự động0,25 cho lịch sử Git. Sau khi hai video thực sự chứng minh VNPAY Sandbox và email Resend,3(f) có thể thêm0,5, tương ứng **9,75/10** theo cách chấm này. Giảng viên quyết định mức cuối cùng.

## Thay đổi có thể kiểm tra

- [Vitest config](../frontend/vitest.config.js), [component tests](../frontend/tests/unit/components.test.jsx), [prefetch tests](../frontend/tests/unit/intent-link.test.jsx); [coverage runner](../scripts/frontend-coverage.mjs) tính cả file không chạy, không chọn riêng vài component để nâng tỷ lệ.
- [Frontend workflow](../.github/workflows/frontend.yml) lưu unit report; [E2E workflow](../.github/workflows/e2e.yml) build production instrument riêng, đo Chromium và yêu cầu lines/statements >70%, lưu reportHTML/LCOV/JSON.
- [IntentLink](../frontend/src/components/intent-link.jsx) prefetch theo hover/focus/touch, một lần mỗi href đến khi invalidation, bỏ qua Save-Data/2G; dùng ở card và bestseller.
- [Trang chủ](../frontend/src/app/page.jsx) và [chính sách](../frontend/src/app/policies/page.jsx) force-static/revalidate3600. Giá/tồn/tài khoản vẫn đọc API, không prerender dữ liệu riêng.
- [Catalog](../frontend/src/app/products/page.jsx), [feed](../frontend/src/components/home-storefront.jsx) dùng Tailwind grid/breakpoints; CSS legacy nằm trong layer riêng, bỏ layout cũ tương ứng. Giữ nhận diện, ảnh và nghiệp vụ.
- Backend cache catalog40 công khai trong snapshot20giây; giữ new12 và recommendation private. Observer chỉ xóa cache Order/PaymentReceipt khi trường liên quan aggregate thay đổi; lỗiIPN tạo cảnh báo đã lọc. [Regression tests](../backend/tests/Feature/GradingFeedbackTest.php), [OpenAPI](openapi.json) và [hợp đồng API](API.md) đồng bộ. Commit backend:24771a1.
- Description GitHub đã đọc lại có nội dung; [PR template](../.github/PULL_REQUEST_TEMPLATE.md) và [PR policy](../.github/workflows/pr-policy.yml) yêu cầu title chuẩn và Closes#issue. Checkbox review chỉ được tick sau review thật.

## Kiểm chứng và phạm vi

Local: Ubuntu22.04, Node24.22.3, PHP8.4.28, PostgreSQL17.12. Dùng cluster socket riêng tại `.runtime/grading-fix/fresh-socket`, database `thu_thanh_test`, storage E2E riêng; không seed/migrate/query provider trên production. Log private ở `.runtime/grading-feedback`; bản tóm tắt có thể chia sẻ ở `docs/evidence/grading-feedback`.

- `php artisan test --compact`:188passed/2.312assertions,24,50s.
- `npm run test:unit`:15passed; `npm run test:api`:16passed.
- `npm run build`:passed; bảng route cho thấy `/` và `/policies` static, revalidate1giờ.
- `FRONTEND_COVERAGE=1 node scripts/e2e.mjs`:30passed trên build production; 1kịch bản responsive bổ sung375/768/1024/1440passed; đã xem ảnh375/1440 và sửa trùng tên utilitycontainer, kiểm lại lề trang đạt.
- Coverage Chromium cuối, toàn65file: **lines75,80%(1275/1682), statements73,12%(1347/1842)**. Functions65,80%,branches68,68%; không tuyên bố hai chỉ số này đạt70%.
- Backend statement coverage mới:91,62%(1782/1945); Pint vànpm audit0vulnerabilities đạt.
- [Tóm tắt có thể tái kiểm](evidence/grading-feedback/verification.json), [coverage từng file](evidence/grading-feedback/frontend-coverage-summary.json), [hash source](evidence/grading-feedback/source-manifest.json). Hosted CI/PR hiện chờ xuất bản nhánh, cập nhật sau readback thực tế. Các số185tests/91,43%backendcoverage hay Lighthouse92/94 ngày23/11 là lịch sử, không giả làm lần đo mới.

Lần đo dev đầu tiên có29passed/1failed vì Fast Refresh tải lại admin, đóng modal khi biên dịch route trả về. Đã chuyển coverage sang production build; kịch bản QR chạy đạt, không sửa logic thanh toán để bỏ qua lỗi test. Coverage unit JSX transform và browser có bản đồ statement khác nhau nên lưu riêng, không cộng hai mẫu số; ngưỡng dùng browser trên toàn source. Function/branch coverage có báo cáo riêng và không được tuyên bố >70% nếu chưa đạt.

## Phần còn lại cần chủ dự án/production

Hai video do chủ dự án tự quay: [checklist và nơi bổ sung link](DEMO-EVIDENCE.md). Chưa có link thì3(f) không full. Không có yêu cầu dựng video giả hoặc thay testfixture bằng provider thật.

TheoD119/D123, không tự bật scheduler tài chính, gọi lại QueryDr99 hay triển khai production. IP thật qua Vercel/Cloudflare và rate-limit hai client chưa được nghiệm thu; cache lock503, tìm kiếm mô tả/đơn trên tập dữ liệu lớn, Redis/shared storage/onOneServer còn giới hạn. Xem [danh sách kỹ thuật có phân biệt đã sửa/chưa kiểm](SECURITY-PERFORMANCE.md). Các mục này không bị PDF trừ điểm nhưng cần trình bày trung thực khi vấn đáp.
