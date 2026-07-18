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

## 2026-07-18 — P0/T1: vai trò & trạng thái user
- **Làm gì:** Đổi `users.level`(int) → `role`(enum admin/user) + `status`(active/banned) +
  `email_verified_at`. Sửa mọi chỗ dùng level tận gốc: User model (fillable, isAdmin(),
  hidden remember_token, bỏ duplicate Notifiable), AdminMiddleware, web.php redirect,
  LoginController, Api/AuthController (register/login/forgot), Admin/UserController
  (validate/update/destroy), blade user edit+index. Bỏ cast password=>hashed (tránh double-hash).
- **Vì sao:** Nền phân quyền marketplace; role rõ nghĩa hơn level; gộp tài khoản.
- **File đụng:** create_users migration, User, AdminMiddleware, web.php, LoginController,
  Api/AuthController, Admin/UserController, admin/user/edit+index.blade
- **Quy tắc mới:** lệnh chạy thật do người dùng chạy, AI chỉ chạy để tự test/debug (ghi ở CLAUDE.md).

## 2026-07-18 — Thêm track admin blade vào ROADMAP
- **Làm gì:** Làm rõ: client CÓ tự đăng ký (React Register + /api/register); admin KHÔNG
  tạo user (đúng thực tế). Admin blade là track song song — mỗi phase backend xong thêm
  màn admin tương ứng (duyệt shop, cấu hình phí, đơn, payout, dispute, doanh thu).
- **Vì sao:** User hỏi sao 5 phase không nhắc nâng cấp admin. Ghi để không quên.
- **File đụng:** ai-workspace/ROADMAP.md

## 2026-07-18 — P0/T2: shops + mở gian hàng
- **Làm gì:** Bảng `shops` (user_id unique, name, slug unique, logo, description,
  status pending/active/suspended [dev mặc định active], rating_avg, followers). Shop model
  (belongsTo User). User: thêm `shop()` hasOne + `isSeller()` (user + shop active).
  SellerMiddleware + alias `seller`. Api/ShopController: POST /shops (mở, chặn nếu đã có),
  GET /shops/{slug} (công khai), PUT /seller/shop (seller sửa, slug giữ nguyên).
- **Vì sao:** Nền người bán marketplace. 1 tài khoản 1 shop, vẫn mua được (gộp).
- **File đụng:** migration create_shops, Shop, User, SellerMiddleware, bootstrap/app.php,
  Api/ShopController, routes/api.php
- **Còn treo:** T7 (vá IDOR) chưa làm — nhảy T2 theo yêu cầu. Route mới để /api (chưa /v1),
  T8 sẽ gom hết sang /api/v1.

## 2026-07-18 — P0/T2: test tự động + fix 2 bug
- **Làm gì:** Viết `tests/Feature/ShopTest.php` (6 case: mở shop, chặn 2 shop, slug tự tăng,
  xem công khai, seller sửa giữ slug, non-seller 403). Chạy pass 6/6. Bắt 2 bug:
  (1) response shop `status:null` vì model chưa refresh sau create → thêm `$shop->refresh()`.
  (2) UserFactory không set role/status → instance null → thêm role/status vào factory.
- **Vì sao:** User không quen test curl → dùng test tự động (`php artisan test`), sqlite riêng.
- **File đụng:** tests/Feature/ShopTest.php (mới), Api/ShopController, database/factories/UserFactory
- **Thêm:** Xóa test mẫu Laravel (ExampleTest Feature+Unit) — nó giả định `/` trả 200,
  nhưng ta đã đổi `/` thành redirect (302). Test suite giờ chỉ còn ShopTest, 6/6 pass.

## 2026-07-18 — P0/T7: vá bảo mật updateProfile (3 lỗ)
- **Làm gì:** `MemberController@updateProfile`: (1) bỏ dùng `$id` URL, dùng `auth()->user()`
  → chống IDOR; (2) thay `$request->all()` bằng validate whitelist → chống leo quyền
  (gửi role=admin không ăn); (3) `Auth` chỉ trả field an toàn, bỏ password. Xóa import
  Intervention Image v2 (facade) thừa. Route giữ nguyên `/user/update/{id}` (không phá frontend),
  id bị bỏ qua. Token vẫn trả (frontend cần) — đánh dấu ponytail bỏ khi dọn frontend.
- **Vì sao:** 3 lỗ bảo mật thật: chiếm tài khoản, leo quyền admin, rò password hash.
- **Verify:** test 3 case (IDOR, leo quyền, email trùng) pass 3/3 → đã xóa test.
- **File đụng:** Api/MemberController
- **Quy tắc mới:** test xong pass thì xóa (CLAUDE.md #7).

