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

### P0 — Nền tảng vai trò & catalog  ✅ XONG (2026-07-18)
Sau phase này: có shop, sản phẩm đúng chuẩn, giỏ server, hết 4 lỗi chặn.
- [x] `users.level` → `users.role` (admin/user) + status + email verify  *(T1 xong)*
- [x] Bảng `shops` + onboarding người bán; middleware `role` / `seller`  *(T1+T2 xong)*
- [x] `products`: `user_id`→`shop_id`, +slug, +soft delete, +sold_count  *(T4 xong)*
- [x] `product_variants` (giá + tồn kho) + `product_images` (bỏ json 1 ô)  *(T5 gộp vào T4)*
- [x] Danh mục cây (`parent_id`) + `commission_rate` theo ngành hàng  *(T3 xong)*
- [x] Giỏ hàng server-side (`carts` / `cart_items`)  *(T6 xong)*
- [x] **Vá 4 lỗi chặn:** ~~IDOR (P0/T7)~~ · ~~giá từ DB · đơn thật · trừ kho atomic (P1/T2)~~ — XONG hết
- [x] API Resources + `/api/v1` (envelope curate, ẩn field nhạy cảm)  *(T8 xong)*

### P1 — Đơn hàng & lõi tiền  ☐
- [x] `orders` / `shop_orders` / `order_items` — tách đơn theo shop  *(T1 schema + T2 checkout xong)*
- [x] Trừ tồn kho atomic trong transaction (T2); state machine vòng đời đơn (T5)
- [x] `seller_wallets` + `wallet_ledger` (sổ cái bất biến)  *(T4: WalletService xong)*
- [x] Bộ máy hoa hồng/phí cấu hình được + `shop_order_fees`  *(T3: FeeCalculator xong; admin-edit fee = admin-track)*

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

---

## Định hướng tương lai (CHƯA làm — chỉ đánh dấu)

Cả hai là LỚP giá trị trên nền commerce đã chạy + có traffic/data. Không làm khi chưa có đơn/tiền/người dùng thật (YAGNI). Thêm sau khi P1–P4 xong.

### P5 — Realtime (sau P4)
Tech: Laravel **Reverb** (WebSocket built-in) hoặc Pusher. 10k user gánh dư.

| Tính năng | Giá trị |
|-----------|---------|
| Chat mua ↔ bán (như Shopee Chat) | cao — gần bắt buộc |
| Thông báo realtime (đơn mới, đã giao, tin nhắn) | cao |
| Cập nhật tồn kho/giá live | thấp |
| Flash sale countdown / live sell | nâng cao, sau |

### P6 — AI (muộn hơn, chọn lọc)
Cần data hành vi + hệ chạy thật trước. Không theo hype — chỉ làm cái đẩy được metric.

| AI | Giá trị | Điều kiện |
|----|---------|-----------|
| Gợi ý sản phẩm (recommendation) | cao (tăng bán) | cần data mua/xem |
| Semantic search | vừa | nâng cấp Meilisearch (P4) |
| Seller: tự sinh mô tả/tiêu đề SP (LLM) | vừa-cao | làm sớm được |
| Tóm tắt / lọc review giả | vừa | khi nhiều review |
| Chatbot hỗ trợ | vừa | sau |
| Chống gian lận (fraud) | cao | khi có tiền thật chạy |

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
