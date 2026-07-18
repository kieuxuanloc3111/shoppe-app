# Nhật ký làm việc

Ghi mới thêm vào cuối. Mới nhất ở dưới cùng.

---

## 2026-07-18 — Dọn luồng route admin/client
- **Làm gì:** Phân tích project, phát hiện lộn xộn: có 2 client trùng (blade `Frontend/*`
  và React SPA), route admin/client trộn lẫn trong web.php, trùng tên route (`blog.index`),
  Redux chết, file rác.
- **Vì sao:** Trước khi nâng cấp phải làm sạch, chốt kiến trúc: admin = blade, client = React.
- **File đụng:** (chỉ phân tích, chưa sửa)

## 2026-07-18 — Phase 1: bù tính năng React còn thiếu
- **Làm gì:** Thêm forgot-password + search (search/advanced/filter-price) vào API
  (`Api/ProductController`, `Api/AuthController`) và React (`Search.js`, `ForgotPassword.js`,
  `ResetPassword.js`). Reset link email trỏ về React (`AppServiceProvider`). Wire ô search Header.
- **Vì sao:** Chuẩn bị xóa blade client — React phải phủ hết tính năng trước, không mất chức năng.
- **File đụng:** Api/ProductController, Api/AuthController, AppServiceProvider, .env,
  routes/api.php, frontend Search/ForgotPassword/ResetPassword/Header/Login, index.js

## 2026-07-18 — Phase 2: xóa blade client
- **Làm gì:** Xóa toàn bộ `Frontend/*` controllers, `views/frontend/*`, MemberMiddleware,
  HomeController, welcome/home.blade, auth/passwords+verify+register. web.php còn admin-only.
  Root `/` redirect: chưa login→login, admin→dashboard. `advancedSearch` thêm paginate(6).
- **Vì sao:** Chốt kiến trúc admin=blade / client=React. Bỏ client trùng.
- **File đụng:** routes/web.php (viết lại), bootstrap/app.php (bỏ alias member), nhiều file bị xóa

## 2026-07-18 — Admin không được quên mật khẩu
- **Làm gì:** Guard `forgotPassword` chỉ gửi reset link cho member (level 0). Email admin →
  im lặng, cùng 1 message (chống dò email). Admin reset bị tắt trong Auth::routes.
- **Vì sao:** Thỏa thuận admin=blade riêng, không dùng chung luồng reset với client React.
- **File đụng:** Api/AuthController

## 2026-07-18 — Sửa migration khớp DB thật
- **Làm gì:** Dump schema DB `shoppe`, so với migration. Gộp cột thêm-tay vào create:
  users (phone varchar20, address varchar500, level int, remember_token), products
  (sale/status → boolean). Xóa 4 file add_* thừa. Không chạy migrate.
- **Vì sao:** Migration lệch DB do sửa cột bằng tay; làm sạch để migrate:fresh tái tạo đúng.
- **File đụng:** create_users, create_products migrations; xóa add_level/remember/status/blogs-timestamps

## 2026-07-18 — Chốt thiết kế marketplace + tạo ai-workspace
- **Làm gì:** Reframe project thành sàn nhiều người bán (Shopee/Lazada). Thiết kế backend
  toàn diện (ERD, escrow, bộ máy hoa hồng theo phí Shopee VN thật, 5 phase) → design doc artifact.
  Chốt 4 quyết định: gộp tài khoản, escrow, phân loại theo loại SP, VNPay. Tạo ai-workspace.
- **Vì sao:** Định hướng nâng cấp toàn diện trước khi code.
- **File đụng:** ai-workspace/* (mới)

## 2026-07-18 — Chốt quyết định #3: có cả category + variant
- **Làm gì:** Làm rõ #3. Sàn có CẢ: category (danh mục: quần áo/đồ ăn) VÀ variant
  (size/màu trong 1 sản phẩm, giá+tồn kho ở variant). Bỏ flag ⚠️ trong ROADMAP.
- **Vì sao:** Sàn bán quần áo/giày → 1 sản phẩm nhiều size/màu, cần bảng variant.
- **File đụng:** ai-workspace/ROADMAP.md

## 2026-07-18 — Lên plan P0 (vai trò & catalog)
- **Làm gì:** Chia P0 thành 8 task (T1-T8) có test riêng. Chốt 4 giả định: chỉ migration
  không seed, variant đủ dùng (options+variants JSON), bỏ cột rác, email verify để P3.
  3/4 "lỗi chặn" dời sang P1 (sống trong checkout). Export plan ra file.
- **Vì sao:** Muốn chắc trước khi code.
- **File đụng:** ai-workspace/plans/p0-role-catalog-2026-07-18.md (mới)
