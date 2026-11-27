# Đánh giá sau sửa — cập nhật 27/11/2026

Đối chiếu bản chấm thử `Advanced_Nhom5_ThuThanhBookstore.pdf`: 8,25/10, chấm commit 8190455 lúc 10:37 ngày 26/11. SHA256 tài liệu: `46353759166a900ea8f8501caf8a9bf0a87433d7721075f4fc122aab83b6f218`.

Đây là **tự đánh giá kỹ thuật**, không thay điểm giảng viên. Source đã được CI kiểm tại commit7aa6a3b; [PR #30](https://github.com/DongDao27/Thu-Thanh-bookstore/pull/30) được chuẩn bị để review. Source ứng viên nằm trên nhánh `fix/grading-feedback-20261126`, issue [#29](https://github.com/DongDao27/Thu-Thanh-bookstore/issues/29). Chỉ nhánh chính tại hạn nộp mới được giảng viên tính; chưa coi PR chưa merge là bài nộp đã cập nhật. Production/scheduler chưa được kích hoạt trong đợt này.

## Điểm dự kiến theo cùng rubric

| Phần | Bản chấm thử | Ứng viên sau sửa | Căn cứ |
|---|---:|---:|---|
| Task1 — Git/workflow | 0,625/1 | 0,750/1 | Đã thêm description repo; commit theo phạm vi, issue29 và PR nối issue. Giữ trừ 0,125 commit lớn lịch sử và 0,125 workflow/review lịch sử; không viết lại Git hay tự nhận review người khác. |
| Task2 — Database | 1/1 | 1/1 | Không đổi schema; giữ migration/seed và regression tests. |
| Task3 — UI/chức năng | 1,875/3 | 3,000/3 | Tailwind thực sự điều khiển responsive grid/toolbar; có React component tests và coverage toàn source Next.js >70% lines/statements. Mục 3(f) đạt nhờ video VNPAY Sandbox và hai video thư đến hộp thư thật. |
| Task4 — REST API | 2/2 | 2/2 | API/schema/tests đồng bộ trường catalog; full regression đạt. |
| Task5 — Logging/monitoring | 1/1 | 1/1 | Giữ bằng chứng trước, bổ sung alert lỗi IPN qua cơ chế notification/cooldown đã có test. Không coi scheduler live đang chạy. |
| Task6 — Performance | 1,75/2 | 2/2 | Intent prefetch và static/ISR một giờ được khai báo, test/build xác nhận; cache catalog 40 sản phẩm và giảm invalidation. |
| **Tổng** | **8,25/10** | **9,75/10** | Dự kiến khi source và bằng chứng CI tương ứng vào main. |

Bản tự chấm ngày 26/11 đạt 9,25/10. Ba clip bổ sung ngày 27/11 đủ căn cứ cộng 0,5 điểm cho mục 3(f) theo tiêu chí chấp nhận video thực tế. Hai khoản trừ 0,125 điểm về lịch sử commit lớn và workflow/review vẫn được giữ. Giảng viên quyết định điểm chính thức. [Ba video minh chứng](DEMO-EVIDENCE.md#video-minh-chứng-dịch-vụ-ngoài).

## Thay đổi có thể kiểm tra

- [Vitest config](../frontend/vitest.config.js), [component tests](../frontend/tests/unit/components.test.jsx), [prefetch tests](../frontend/tests/unit/intent-link.test.jsx); [coverage runner](../scripts/frontend-coverage.mjs) tính cả file không chạy, không chọn riêng vài component để nâng tỷ lệ.
- [Frontend workflow](../.github/workflows/frontend.yml) lưu unit report; [E2E workflow](../.github/workflows/e2e.yml) build production instrument riêng, đo Chromium và yêu cầu lines/statements >70%, lưu reportHTML/LCOV/JSON.
- [IntentLink](../frontend/src/components/intent-link.jsx) prefetch theo hover/focus/touch, một lần mỗi href đến khi invalidation, bỏ qua Save-Data/2G; dùng ở card và bestseller.
- [Trang chủ](../frontend/src/app/page.jsx) và [chính sách](../frontend/src/app/policies/page.jsx) force-static/revalidate 3600. Giá/tồn/tài khoản vẫn đọc API, không prerender dữ liệu riêng.
- [Catalog](../frontend/src/app/products/page.jsx), [feed](../frontend/src/components/home-storefront.jsx) dùng Tailwind grid/breakpoints; CSS legacy nằm trong layer riêng, bỏ layout cũ tương ứng. Giữ nhận diện, ảnh và nghiệp vụ.
- Backend cache catalog 40 công khai trong snapshot 20 giây; giữ new 12 và recommendation private. Observer chỉ xóa cache Order/PaymentReceipt khi trường liên quan aggregate thay đổi; lỗi IPN tạo cảnh báo đã lọc. [Regression tests](../backend/tests/Feature/GradingFeedbackTest.php), [OpenAPI](openapi.json) và [hợp đồng API](API.md) đồng bộ. Commit backend:24771a1.
- Description GitHub đã đọc lại có nội dung; [PR template](../.github/PULL_REQUEST_TEMPLATE.md) và [PR policy](../.github/workflows/pr-policy.yml) yêu cầu title chuẩn và Closes#issue. Checkbox review chỉ được tick sau review thật.

## Kiểm chứng và phạm vi

Local: Ubuntu 22.04, Node 24.22.3, PHP 8.4.28, PostgreSQL 17.12. Dùng cluster socket riêng tại `.runtime/grading-fix/fresh-socket`, database `thu_thanh_test`, storage E2E riêng; không seed/migrate/query provider trên production. Log private ở `.runtime/grading-feedback`; bản tóm tắt có thể chia sẻ ở `docs/evidence/grading-feedback`.

- `php artisan test --compact`:188 passed / 2.312 assertions; lần coverage sau patch 27,90s.
- `npm run test:unit`:15 passed; `npm run test:api`:16 passed.
- `npm run build`:passed; bảng route cho thấy `/` và `/policies` static, revalidate1giờ.
- `FRONTEND_COVERAGE=1 node scripts/e2e.mjs`:30 passed trên build production; 1 kịch bản responsive bổ sung 375/768/1024/1440 passed; đã xem ảnh 375/1440 và sửa trùng tên utility container, kiểm lại lề trang đạt.
- Coverage Chromium cuối, toàn bộ 65 file: **lines75,80%(1275/1682), statements73,12%(1347/1842)**. Functions 65,80%, branches 68,68%; không tuyên bố hai chỉ số này đạt70%.
- Backend statement coverage mới:91,62% (1782/1945); Pint và npm audit 0 vulnerabilities đạt.
- [Tóm tắt có thể tái kiểm](evidence/grading-feedback/verification.json), [coverage từng file](evidence/grading-feedback/frontend-coverage-summary.json), [hash source](evidence/grading-feedback/source-manifest.json). Hosted CI của7aa6a3b: [Backend](https://github.com/DongDao27/Thu-Thanh-bookstore/actions/runs/45813979212), [Frontend](https://github.com/DongDao27/Thu-Thanh-bookstore/actions/runs/45813979271), [E2E](https://github.com/DongDao27/Thu-Thanh-bookstore/actions/runs/45813979120) và [PR policy](https://github.com/DongDao27/Thu-Thanh-bookstore/actions/runs/45813988922) đều passed. **31/31 E2E, coverage toàn source76,27% lines (1283/1682),73,56% statements (1355/1842)**; function66,60%,branch70,03%. [Snapshot hosted](evidence/grading-feedback/hosted-browser-summary.json); số local75,80%/73,12% phía trên được giữ đúng môi trường. Supabase Preview skipped, không tínhpassed. Các số185tests/91,43%backendcoverage hay Lighthouse 92/94 ngày 23/11 là lịch sử, không giả làm lần đo mới.

Lần đo dev đầu tiên có29 passed / 1 failed vì Fast Refresh tải lại admin, đóng modal khi biên dịch route trả về. Đã chuyển coverage sang production build; kịch bản QR chạy đạt, không sửa logic thanh toán để bỏ qua lỗi test. Coverage unit JSX transform và browser có bản đồ statement khác nhau nên lưu riêng, không cộng hai mẫu số; ngưỡng dùng browser trên toàn source. Function/branch coverage có báo cáo riêng và không được tuyên bố >70% nếu chưa đạt.

## Phần còn lại cần chủ dự án/production

Ba clip minh chứng đã được kiểm nội dung và công khai trên nhánh PR. Chúng ghi lại kết quả chạy trên website hiện hành. Kết quả CI của ứng viên được dẫn ở phần kiểm chứng.

TheoD119/D123, không tự bật scheduler tài chính, gọi lại QueryDr 99 hay triển khai production. IP thật qua Vercel/Cloudflare và rate-limit hai client chưa được nghiệm thu; cache lock 503, tìm kiếm mô tả/đơn trên tập dữ liệu lớn, Redis/shared storage/onOneServer còn giới hạn. Xem [danh sách kỹ thuật có phân biệt đã sửa/chưa kiểm](SECURITY-PERFORMANCE.md). Các mục này không bị PDF trừ điểm nhưng cần trình bày trung thực khi vấn đáp.

## Bản vá dependency phát hiện trong CI

Backend CI đầu tiên trên aeedd94 không đạt bước `composer audit`: 5 advisory mới ngày 25/11/2026 ảnh hưởng Symfony http-kernel/mailer/routing. Không bỏ qua audit. Đã cập nhật Symfony từ 7.4.22–23 lên 7.4.24 và các patch phụ thuộc tương thích; Laravel vẫn 12.73.0, PHP vẫn 8.4. Composer validate/platform checks và audit sau sửa không còn advisory. Full regression sau patch đã đạt 188 tests / 2.312 assertions; coverage local 91,72%. Backend CI trên7aa6a3b cũng đã đạt, coverage91,62%, HTTP22 vàops smoke đạt, audit sạch. Frontend,31E2E vàPRpolicy của cùng commit đều đạt.

Tham chiếu triển khai: [Next.js prefetch](https://nextjs.org/docs/app/guides/prefetching), [Tailwind phát hiện/exclude utility](https://tailwindcss.com/docs/detecting-classes-in-source-files), [Vitest coverage](https://vitest.dev/guide/coverage.html). Các kết luận đạt/chưa đạt ở trên dựa trên source/test/report của dự án, không dựa riêng vào hướng dẫn framework.
