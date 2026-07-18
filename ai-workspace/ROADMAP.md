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
- [ ] `users.level` → `users.role` (admin/user) + status + email verify
- [ ] Bảng `shops` + onboarding người bán; middleware `role` / `seller`
- [ ] `products`: `user_id`→`shop_id`, +slug, +soft delete, +sold_count
- [ ] `product_variants` (giá + tồn kho) + `product_images` (bỏ json 1 ô)
- [ ] Danh mục cây (`parent_id`) + `commission_rate` theo ngành hàng
- [ ] Giỏ hàng server-side (`carts` / `cart_items`)
- [ ] **Vá 4 lỗi chặn:** giá tính từ DB · IDOR update profile · nền tảng đơn thật · tồn kho
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

## Không làm (thừa ở quy mô 10k)
microservices · sharding · read-replica · kubernetes · event-sourcing · GraphQL · kafka

---

## Đã xong trước khi vào P0 (dọn dẹp nền)
- Xóa blade client (giữ admin blade + React SPA + API).
- web.php admin-only, root `/` redirect login/dashboard.
- Thêm forgot-password + search cho React/API.
- Sửa migration khớp DB thật.
Chi tiết: `logs/history.md`.
