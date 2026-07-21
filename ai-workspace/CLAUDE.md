# AI Workspace — Quy tắc làm việc

Thư mục này là bộ nhớ & tài liệu cho quá trình nâng cấp project Shoppe (sàn TMĐT).
Đọc file này trước khi làm bất cứ việc gì trong project.

## Cấu trúc

```
ai-workspace/
├── CLAUDE.md          ← file này. Quy tắc.
├── ROADMAP.md         ← các phase cần làm + trạng thái + quyết định đã chốt.
├── logs/
│   └── history.md     ← nhật ký MỌI lần làm việc. Chỉ ghi lịch sử ở đây.
└── modules/           ← mô tả nghiệp vụ từng cụm (sản phẩm, đơn hàng, ví...).
                         Viết SAU khi code cụm đó xong.
```

## Quy tắc

1. **Nhật ký chỉ ghi vào `logs/history.md`.** Mỗi lần làm xong một việc (sửa code,
   thêm tính năng, quyết định) → thêm 1 mục vào cuối file. Không rải log ở nơi khác.
   Format mỗi mục:
   ```
   ## YYYY-MM-DD — <tên việc>
   - **Làm gì:** ...
   - **Vì sao:** ...
   - **File đụng:** ...
   ```

2. **File trong `modules/` là mô tả TRẠNG THÁI HIỆN TẠI, ghi đè.**
   Khi nghiệp vụ đổi → sửa thẳng nội dung cũ, viết lại cho đúng hiện tại.
   KHÔNG thêm dòng kiểu "đã sửa / đã update / changelog" làm file phình dài.
   Lịch sử thay đổi thuộc về `logs/history.md`, không thuộc module file.

3. **Module file phải để người ngoài đọc hiểu nghiệp vụ.** Gồm tối thiểu:
   luồng nghiệp vụ, tên bảng DB dùng, tên controller/endpoint, cách hoạt động.
   Viết tiếng Việt, không giả định người đọc biết code.

4. **ROADMAP.md là nguồn sự thật về phạm vi.** Làm xong phase/mục nào → tick ở đó.
   Quyết định lớn ghi ở phần "Quyết định đã chốt".

5. **Không tự ý mở rộng phạm vi.** Chỉ làm đúng phase đang mở. Nghi ngờ thì hỏi.

6. **Lệnh chạy thật (migrate, seed, npm, artisan, git...) → NGƯỜI DÙNG chạy, không phải AI.**
   AI chỉ viết code + hướng dẫn lệnh để người dùng tự chạy.
   Ngoại lệ: AI được chạy lệnh để TỰ KIỂM TRA / debug / lint khi đang code (VD `php -l`,
   đọc schema, chạy test để fix lỗi). Không chạy lệnh làm đổi trạng thái project của người dùng.

7. **Test là công cụ dùng-một-lần.** Viết test verify task → chạy `php artisan test` cho pass
   → XÓA file test. Không giữ lại (repo nhẹ). AI tự chạy test để verify trước khi xóa.

8. **Migration:** nếu SỬA/ĐỔI TÊN create-migration đã chạy → người dùng phải `migrate:fresh`
   (xóa data, chạy lại từ đầu). Chỉ `migrate` khi THÊM migration mới hoàn toàn.
   AI phải ghi rõ "fresh" hay "migrate" trong mỗi hướng dẫn test.
