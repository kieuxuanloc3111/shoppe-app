# ROADMAP — Nâng cấp Shoppe thành sàn TMĐT

Mục tiêu: từ project "bài tập sinh viên" → sàn nhiều người bán (mô hình Shopee/Lazada),
chạy được cho ≥10.000 khách. Backend + DB trước, frontend sau.

Thiết kế tổng thể: xem design doc (artifact) — ERD, dòng tiền ký quỹ, bộ máy hoa hồng.

---

## Quyết định đã chốt

1. **Tài khoản gộp** — 1 tài khoản vừa mua vừa bán. Mở `shop` thì thành người bán,
   vẫn mua của shop khác. (mô hình Shopee)
2. **Escrow** — người mua trả tiền cho sàn, sàn giữ, giao xong nhả cho người bán
   sau khi trừ hoa hồng + phí.
3. **Có CẢ hai tầng phân loại:**
   - **Category** (danh mục toàn sàn): quần áo, đồ ăn, điện thoại... — cây `parent_id`.
   - **Variant** (biến thể trong 1 sản phẩm): size/màu — VD "Áo thun" có Trắng/S, Đen/L.
     **Giá + tồn kho nằm ở variant**, không ở sản phẩm.
4. **Thanh toán: VNPay** trước (sandbox), bám sát cách sàn thật làm.

## Vai trò
- **Admin** = chủ sàn: duyệt shop, chỉnh phí/hoa hồng, duyệt rút tiền, doanh thu.
- **Người mua** = mặc định mọi tài khoản.
- **Người bán** = tài khoản có shop active.

---

## Phase

### P0 — Nền tảng vai trò & catalog  ☐
Sau phase này: có shop, sản phẩm đúng chuẩn, giỏ server, hết 4 lỗi chặn.
- [x] `users.level` → `users.role` (admin/user) + status + email verify  *(T1 xong)*
- [x] Bảng `shops` + onboarding người bán; middleware `role` / `seller`  *(T1+T2 xong)*
- [x] `products`: `user_id`→`shop_id`, +slug, +soft delete, +sold_count  *(T4 xong)*
- [x] `product_variants` (giá + tồn kho) + `product_images` (bỏ json 1 ô)  *(T5 gộp vào T4)*
- [x] Danh mục cây (`parent_id`) + `commission_rate` theo ngành hàng  *(T3 xong)*
- [ ] Giỏ hàng server-side (`carts` / `cart_items`)
- [~] **Vá 4 lỗi chặn:** ~~IDOR update profile~~ (T7 xong: +chống leo quyền, +ẩn password);
      giá tính từ DB · nền tảng đơn thật · tồn kho → dời P1 (sống trong checkout/orders)
- [ ] API Resources (envelope thống nhất, ẩn field nhạy cảm)

### P1 — Đơn hàng & lõi tiền  ☐
- [ ] `orders` / `shop_orders` / `order_items` — tách đơn theo shop
- [ ] Trừ tồn kho atomic trong transaction; state machine vòng đời đơn
- [ ] `seller_wallets` + `wallet_ledger` (sổ cái bất biến)
- [ ] Bộ máy hoa hồng/phí cấu hình được + `shop_order_fees`

### P2 — Thanh toán ký quỹ  ☐
- [ ] Tích hợp VNPay (redirect + verify callback/IPN chữ ký)
- [ ] Escrow: giữ pending → nhả available khi hoàn tất
- [ ] Hoàn tiền + luồng payout + admin duyệt

### P3 — Tính năng sàn  ☐
- [ ] Đánh giá sản phẩm (verified purchase) → cập nhật rating
- [ ] Voucher sàn + shop; wishlist & follow shop server-side
- [ ] Thông báo; tranh chấp/hoàn hàng cơ bản

### P4 — Scale 10k & vận hành  ☐
- [ ] Redis (cache + session + queue)
- [ ] Index DB + eager load diệt N+1; Scout + Meilisearch
- [ ] Queue/Horizon; ảnh lên S3 + resize + CDN
- [ ] Test (Pest) cho tiền/đơn/thanh toán; Sentry + health check

## Track song song: Admin blade (UI vận hành)

Admin = blade (giữ). Mỗi phase backend xong → thêm màn admin tương ứng NGAY SAU
(admin cần vận hành sớm, không dồn cuối). Hiện admin mới có CRUD cơ bản
(product/user/category/brand/blog/country/history) + dashboard rỗng.

Cần thêm khi backend tới:
- [ ] (sau P0) Quản lý shop: duyệt pending→active; danh mục + `commission_rate`
- [ ] (sau P0) **Rewrite quản lý sản phẩm admin** theo shop+variant — hiện index/edit
      hiển thị thiếu (giá dời xuống variant, ảnh sang bảng riêng). Admin nên chỉ xem/ẩn,
      không sửa giá (seller tự quản)
- [ ] (sau P1) Xem tất cả đơn / shop_orders; bảng phí `shop_order_fees`
- [ ] (sau P2) Duyệt rút tiền (payout); đối soát ví
- [ ] (sau P3) Xử lý tranh chấp/hoàn tiền; duyệt review
- [ ] Dashboard doanh thu sàn thật (thay dashboard rỗng); quản lý seller
- Lưu ý: admin KHÔNG tạo user (user tự đăng ký ở client) — đúng thực tế sàn.

## Không làm (thừa ở quy mô 10k)
microservices · sharding · read-replica · kubernetes · event-sourcing · GraphQL · kafka

---

## Đã xong trước khi vào P0 (dọn dẹp nền)
- Xóa blade client (giữ admin blade + React SPA + API).
- web.php admin-only, root `/` redirect login/dashboard.
- Thêm forgot-password + search cho React/API.
- Sửa migration khớp DB thật.
Chi tiết: `logs/history.md`.
