# Plan — P0: Nền tảng vai trò & catalog

**Ngày:** 2026-07-18
**Phase:** P0 (xem `ROADMAP.md`)
**Mục tiêu:** Có shop, sản phẩm chuẩn sàn (category + variant + ảnh), giỏ hàng server,
phân quyền vai trò, vá lỗi IDOR. Nền cho P1 (đơn hàng & tiền).

---

## Phạm vi

**Trong P0:** vai trò user, shops, danh mục cây + hoa hồng, products viết lại,
variants + options, ảnh sản phẩm, giỏ hàng server-side, vá IDOR, API Resources + `/api/v1`.

**KHÔNG trong P0 (đẩy sang P1):** 3/4 "lỗi chặn" sống trong checkout/orders, chưa vá được
khi chưa có đơn hàng:
- Giá tính từ DB (checkout) → P1
- Nền tảng đơn thật → P1
- Trừ tồn kho atomic khi đặt → P1 (*P0 chỉ tạo cột `stock`*)
- IDOR sửa profile → **vá ngay trong P0** (T7)

---

## Decisions & Approvals

| # | Câu hỏi | Lựa chọn đưa ra | Người dùng chọn |
|---|---------|-----------------|-----------------|
| A | DB đang có data, xử lý sao? | (1) migrate:fresh + seeder / (2) data-migration | **Chỉ migration, KHÔNG seed.** Data rác bỏ thoải mái, seed sau. Giữ migration làm lịch sử cho deploy. |
| B | Độ sâu variant? | (1) options + variants JSON / (2) option/value tables đầy đủ | **Đủ dùng cho sản phẩm sàn phổ biến, không quá sâu.** Giao mình quyết → options(name+values) + variants(sku/price/stock/option_values JSON). |
| C | Cột rác trong products? | bỏ / giữ | **Bỏ thoải mái** (`company`, đổi `detail`→`description`). |
| D | Email verify? | thêm cột giờ, ép ở P3 | **OK.** |

**Hệ quả:** T9 (seeder) bỏ khỏi P0. Cách migration: sửa create cũ (users/products/categories)
về shape cuối + thêm create mới (shops/options/variants/images/carts). `migrate:fresh` tái tạo.

**Phê duyệt:** ⏳ chờ duyệt task list ở cuối file trước khi code.

---

## Task breakdown

### T1 — Vai trò & trạng thái user
- **Migration** (sửa `create_users`): `level`(int)→`role`(enum admin/user, default user),
  +`status`(enum active/banned, default active), +`email_verified_at` (nullable).
- **User model:** cast `role`; `isAdmin()`, `isSeller()` (có shop active); `$hidden` +`remember_token`.
- **Middleware:** `role:admin` (thay logic AdminMiddleware theo role), `seller` (user có shop active);
  cập nhật alias `bootstrap/app.php`. Route admin blade dùng `role:admin`.
- **Test:** seed tay 1 admin + 1 user → user vào `/admin/*` = 403, admin = 200.

### T2 — Shops + mở gian hàng
- **Migration (mới):** `shops`(id, user_id unique, name, slug unique, logo nullable,
  description nullable, status enum pending/active/suspended default active, rating_avg default 0,
  followers_count default 0, timestamps).
- **Shop model** + quan hệ: `User hasOne Shop`, `Shop belongsTo User`, `Shop hasMany Products`.
- **Api/ShopController:** `POST /api/v1/shops` (mở shop, gán user hiện tại),
  `GET /api/v1/shops/{slug}` (công khai), `PUT /api/v1/seller/shop` (seller sửa shop mình).
- **Test:** user gọi mở shop → có shop, `isSeller()` true; GET shop công khai trả data.

### T3 — Danh mục (cây + hoa hồng)
- **Migration (sửa `create_categories`):** +`parent_id`(nullable, self FK), +`slug` unique,
  +`commission_rate`(decimal, % hoa hồng ngành hàng).
- **Category model:** `parent()`, `children()` đệ quy.
- **Api:** `GET /api/v1/categories` trả cây; admin chỉnh commission_rate (route admin sẵn có).
- **Test:** danh mục lồng cha-con trả đúng cây; set/đọc commission_rate.

### T4 — Products viết lại + ảnh
- **Migration (sửa `create_products`):** `user_id`→`shop_id`; +`slug` unique, +`sold_count`,
  +`view_count`, +`rating_avg`, +`softDeletes`; **bỏ** `image`(json), `company`, `sale`,
  `sale_price`, `price`, `status`(bit) → chuyển giá/kho xuống variant; `detail`→`description`.
  Product giữ: shop_id, category_id, brand_id, name, slug, description, is_active, sold/view/rating.
- **Migration (mới):** `product_images`(id, product_id FK, url, sort_order, timestamps).
- **Products model:** quan hệ `shop/category/brand/variants/images/options`; auto-gen slug;
  bỏ cast image; accessor `price_min`/`price_max` từ variants.
- **Test:** product kèm ảnh + variants load; slug tự sinh.

### T5 — Variants + options
- **Migration (mới):** `product_options`(id, product_id FK, name VD "Màu"/"Size", values json,
  timestamps); `product_variants`(id, product_id FK, sku nullable, price decimal, stock int default 0,
  option_values json VD {"Màu":"Đỏ","Size":"L"}, timestamps).
- **Models:** ProductOption, ProductVariant + quan hệ về Product.
- **Test:** product 2 option (Màu/Size) sinh nhiều variant; mỗi variant giá/kho riêng;
  price_min/max đúng.

### T6 — Giỏ hàng server
- **Migration (mới):** `carts`(id, user_id FK, timestamps); `cart_items`(id, cart_id FK,
  variant_id FK, qty, timestamps, unique[cart_id,variant_id]).
- **Api/CartController** (viết lại từ bản localStorage cũ): `GET/POST/PUT/DELETE /api/v1/cart` —
  **giá lấy từ DB qua variant**, chặn `qty > stock`, gộp item trùng.
- **Test:** thêm giỏ dùng giá DB (không nhận giá client); qty vượt kho bị từ chối.

### T7 — Vá IDOR (bảo mật) — làm sớm
- **MemberController@updateProfile:** bỏ tham số `{id}`, dùng `auth()->id()`; sửa route
  `/user/update/{id}` → `/api/v1/profile`. Kiểm mọi endpoint member lấy id từ URL.
- Sửa import `Intervention\Image` v2→v3 nếu lỗi (`MemberController`).
- **Test:** user A gọi update không thể chạm profile user B.

### T8 — API Resources + versioning
- Prefix toàn bộ API client dưới `/api/v1`.
- **Resource:** UserResource, ShopResource, ProductResource (kèm variants/images),
  CategoryResource, CartResource — ẩn field nhạy cảm, envelope thống nhất.
- Refactor response API hiện có (product, auth, blog...) qua Resource.
- **Test:** shape response nhất quán; không rò `password`/`remember_token`.

---

## Thứ tự & phụ thuộc
```
T1 → T7 → T2 → T3 → T4 → T5 → T6 → T8
```
- T7 làm sớm (rẻ, bảo mật, độc lập).
- T3 độc lập, chèn linh hoạt.
- T5 cần T4; T6 cần T5; T8 sau khi model xong.
- Không có phụ thuộc vòng.

## Không làm trong P0
Seeder/factory (seed sau) · checkout/orders/tiền (P1) · payment (P2) · review/voucher (P3) · Redis/search (P4).
