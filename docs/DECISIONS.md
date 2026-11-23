# Quyết định hiện hành

- **D29:** giao tận nơi mặc định 25.000đ, nhận tại cửa hàng 0đ; tổng tiền do backend tính và kiểm lại khi tạo đơn.
- **D34:** cho phép frontend trên các API đã kiểm chứng; không đồng nghĩa nghiệm thu tiền thật hoặc khả năng chịu tải lớn.
- **D57/D58/D99:** nhận diện xanh–trắng, font tự host có giấy phép; bố cục và nguồn ảnh giữ theo source đã được kiểm.
- **D68:** hủy đơn hợp lệ gộp món trở lại giỏ đúng một lần; transaction và idempotency giữ nguyên.
- **D98:** Next.js dùng JavaScript/JSX, không đổi lại TypeScript.
- **D105:** PostgreSQL 17; Eloquent/migration quản lý schema và dữ liệu. Không quay lại MySQL.
- **D110:** đăng ký bản tin chỉ nhận email, không thêm checkbox bắt buộc.
- **D115:** thiết kế mobile có điều hướng Trang chủ/Danh mục/Giỏ hàng/Tài khoản; source Figma giữ trong design/figma.
- **D116:** polling là phương án hiện hành, không bắt buộc Pusher.
- **D117:** tên miền chính nhasachthuthanh.id.vn và API api.nhasachthuthanh.id.vn; giữ APP_KEY/database và VNPAY Sandbox.
- **D118:** SSH deploy dùng key, tắt root/password; backup VPS và bản sao private trên laptop đã kiểm phục hồi. Laptop phải có mạng/phiên hoạt động để tự kéo backup.
- **D119 (22/11/2026):** chủ dự án chấp nhận luồng thanh toán Sandbox thành công và yêu cầu bỏ qua việc điều tra QueryDr 99. Không truy vấn lại, không sửa adapter hoặc trạng thái đơn, không tự bật scheduler tài chính. Dọn tài liệu chia commit/hướng dẫn hoàn thiện, hoàn thiện README, đưa thiết kế/catalog về đúng thư mục và đổi origin sang repo nhà sách. Giữ source chức năng, tests, giấy phép, dữ liệu và backup riêng. Commit đầy đủ trên lịch sử main của repo hiện tại; không force push.

Lịch sử tài liệu trước khi rút gọn được giữ trong snapshot private ngoài Git. Quy tắc nghiệp vụ chi tiết vẫn ở DOMAIN-RULES, API và DATABASE; không đổi hợp đồng khi dọn repository.

- **D120 (23/11/2026):** chủ dự án xác nhận Figma thuộc môn khác, bản nộp môn web chỉ gồm link GitHub và domain. Đánh giá lại theo rubric CSB35050 và khả năng clone/cài/chạy, ưu tiên security/performance. Không yêu cầu canvas Figma hoặc slide/video để đóng phạm vi môn web; không xóa thiết kế của môn khác. Ngoại lệ Next.js vẫn theo D34/D98; không tuyên bố đã dùng Blade/Livewire. D119 về thanh toán giữ nguyên.

D120 bổ sung: chủ dự án xác nhận giảng viên đã chấp nhận Next.js thay Blade/Livewire. Đánh giá theo xác nhận này, không đổi stack hoặc tự dựng bằng chứng phê duyệt.

- **D121 (23/11/2026):** chủ dự án yêu cầu rà soát và cải thiện bảo mật cùng email xác nhận đăng ký bản tin. Đăng ký mới gửi email “Bạn đã đăng ký nhận bản tin của Nhà sách Thu Thanh” qua mailer/queue hiện có sau commit; đăng ký trùng giữ response202 và không gửi lại. Đây là xác nhận giao dịch đăng ký, không tự gửi campaign hoặc gửi hàng loạt cho danh sách cũ. Thay quy định cũ trong API về không gửi email xác nhận; D110 và D119 giữ nguyên.
