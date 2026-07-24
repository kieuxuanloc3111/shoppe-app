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

## 2026-07-18 — P0/T3: danh mục cây + hoa hồng
- **Làm gì:** Migration categories +`parent_id`(self FK nullOnDelete) +`slug` unique
  +`commission_rate`(decimal 5,2 default 0). Category model: parent/children/childrenRecursive
  (đệ quy dựng cây), slug tự sinh khi tạo (booted creating), cast commission decimal:2.
  Api/CategoryController@index GET /api/categories trả cây. Admin CategoryController store/update
  nhận parent_id + commission_rate (coerce null→0). Blade create/edit: dropdown cha + ô hoa hồng.
- **Vì sao:** Danh mục 2 tầng (quần áo/đồ ăn...) + hoa hồng theo ngành cho bộ máy phí (P1).
- **Verify:** test cây đệ quy + slug tự tăng + commission — pass 3/3, đã xóa.
- **File đụng:** create_categories migration, Category, Api/CategoryController, Admin/CategoryController,
  category create+edit blade, routes/api.php

## 2026-07-18 — P0/T4 (gộp T5): products viết lại + variants + options + ảnh
- **Làm gì:** Rewrite toàn bộ module product sang marketplace.
  - Migration: đổi shops → 2026_01_27 (chạy trước products để FK). products: user_id→shop_id,
    +slug/description/is_active/sold_count/view_count/rating_avg/softDeletes; BỎ price/sale/
    sale_price/company/image/status (giá+kho dời xuống variant). Thêm bảng product_images,
    product_options, product_variants.
  - Models: Products (rewrite: SoftDeletes, slug auto, appends price_min/price_max từ variants,
    quan hệ shop/category/brand/images/variants/options — FK chỉ định 'product_id' vì tên model
    'Products' số nhiều làm Eloquent đoán sai). ProductImage/Variant/Option mới. Shop.products().
  - Api/ProductController rewrite hết: product/detail/search/advancedSearch/filterPrice (lọc giá
    qua whereHas variants), myProduct/getProduct/addProduct/updateProduct/deleteProduct
    (shop-scoped, transaction, variants+options+ảnh; soft delete). productCart tạm dùng price_min
    (đổi theo variant ở T6).
  - Routes: nhóm quản lý sản phẩm bọc middleware 'seller'.
- **Vì sao:** T4 bỏ price khỏi products bắt buộc có variant → gộp T5. Sàn cần 1 SP nhiều phân loại.
- **Verify:** test tạo SP có variant (price_min/max), non-seller 403, ownership 403, list khoảng giá
  — pass 4/4, đã xóa. (Fix dọc đường: FK products_id→product_id.)
- **File đụng:** 4 migration (shops rename + products rewrite + images/options/variants),
  Products/ProductImage/ProductVariant/ProductOption/Shop models, Api/ProductController, routes/api.php
- **Nợ (admin-track):** admin blade product index/edit hiển thị thiếu (field cũ) → rewrite sau P0.

## 2026-07-18 — P0/T6: giỏ hàng server-side
- **Làm gì:** Bảng `carts` (1/user) + `cart_items` (variant_id, qty, unique[cart,variant]).
  Models Cart/CartItem (CartItem.variant FK chỉ định 'variant_id'). Api/CartController:
  GET/POST(add)/PUT(update)/DELETE(remove) /api/cart. Giá LUÔN từ DB (endpoint chỉ nhận
  variant_id+qty, bỏ qua giá client), chặn qty>stock (422), gộp variant trùng, ownership
  item (chống IDOR). Route trong nhóm auth:sanctum.
- **Vì sao:** Giỏ cũ chỉ localStorage + tin giá client (lỗ price-tampering). Server-side + variant.
- **Verify:** test giá-từ-DB, bỏ-giá-client, chặn-tồn-kho, gộp-trùng, ownership — pass 5/5, đã xóa.
- **File đụng:** migration carts, Cart, CartItem, Api/CartController, routes/api.php
- **Ghi chú:** endpoint cũ /product/cart (productCart) vẫn còn cho frontend cũ — thay hẳn khi dọn frontend.

## 2026-07-18 — Seeder admin + đổi cách migration
- **Làm gì:** DatabaseSeeder chỉ seed 1 admin (admin@test.com/123456, updateOrCreate idempotent),
  bỏ CategorySeeder/BrandSeeder khỏi run (data rác "truyện tranh" + CategorySeeder dùng raw insert
  → không sinh slug → vỡ). Giờ `migrate:fresh --seed` = reset + có admin, khỏi tinker tay.
- **Vì sao:** User ngại tạo admin lại mỗi lần fresh. Lỗi migrate: do đã đổi tên+sửa create cũ
  (shops rename, products/categories sửa) → plain migrate xung đột "shops already exists".
- **Quyết định:** từ P1 trở đi ưu tiên MIGRATION MỚI (alter) thay vì sửa create cũ → chỉ cần
  `php artisan migrate`, giữ data, khỏi fresh (đã ghi CLAUDE.md #8).
- **File đụng:** database/seeders/DatabaseSeeder.php

## 2026-07-18 — P0/T8: API Resources + /api/v1 → P0 HOÀN TẤT
- **Làm gì:** apiPrefix 'api/v1' (bootstrap/app.php) → mọi route API giờ /api/v1/*. Tạo Resource
  UserResource (ẩn password/remember_token/timestamp), ShopResource, CategoryResource (children
  đệ quy), ProductResource (price_min/max, images/variants/options whenLoaded). Áp: Auth
  register/login (UserResource), Product product()/detail(), Shop store/show/update, Category index.
  Search/paginate/seller-CRUD giữ raw (đã có price_min qua $appends; giữ pagination meta) —
  có thể áp Resource dần.
- **Vì sao:** Output curate nhất quán + versioning production. (Field nhạy cảm đã ẩn từ T1 qua $hidden.)
- **Verify:** test /v1 áp dụng (/api trần 404), login không lộ password, product resource gọn
  (có price_min, bỏ created_at) — pass 3/3, đã xóa.
- **File đụng:** bootstrap/app.php, 4 Resource mới, Api Auth/Product/Shop/Category controllers.
- **⚠️ FRONTEND VỠ:** React gọi /api/... → giờ /api/v1/... → 404. Cần đổi base URL khi dọn frontend
  (đằng nào React cũng phải rewrite cho schema marketplace: shop/variant).

## ✅ P0 HOÀN TẤT (T1–T8)
Vai trò (role) · shops + seller · danh mục cây + hoa hồng · products+variants+options+ảnh ·
giỏ server · vá IDOR+leo-quyền · API Resources + /api/v1 · seeder admin.
Tiếp theo: P1 (orders/shop_orders/order_items + ví + bộ máy phí). Nhớ: P1 dùng migration MỚI (alter),
chỉ `php artisan migrate`, khỏi fresh.

## 2026-07-18 — Ghi định hướng P5 (realtime) + P6 (AI) vào ROADMAP
- **Làm gì:** Thêm P5 (chat/thông báo realtime — Reverb) và P6 (AI: recommendation, semantic
  search, seller content-gen, fraud...) vào ROADMAP dưới mục "Định hướng tương lai — CHƯA làm".
- **Vì sao:** User hỏi sao roadmap không có AI/realtime. Chúng là lớp trên nền commerce + cần
  traffic/data → đánh dấu định hướng, làm sau P1–P4. Không làm giờ (YAGNI).
- **File đụng:** ai-workspace/ROADMAP.md

## 2026-07-18 — Lên plan P1 (đơn hàng & lõi tiền)
- **Làm gì:** Chia P1 thành 6 task (schema đơn+tiền → checkout → phí → ví → state machine → query).
  Chốt giả định: VNPay=P2 (P1 mark-paid tay), trừ kho lúc đặt, escrow, ship=0. Vá 3 bug P0 còn lại
  ở T2 (giá từ DB, đơn thật, kho atomic). Export file plan.
- **Vì sao:** Plan trước khi code (như P0).
- **File đụng:** ai-workspace/plans/p1-orders-money-2026-07-18.md (mới)

## 2026-07-18 — P1/T1: schema đơn + tiền (migrations + models)
- **Làm gì:** 2 migration MỚI (bảng mới hết → chỉ `migrate`): orders/shop_orders/order_items;
  fee_settings/shop_order_fees/seller_wallets/wallet_ledger. 7 model + quan hệ. order_items snapshot
  name+unit_price; product_id/variant_id nullable nullOnDelete (giữ lịch sử đơn khi SP/variant bị
  xóa/sửa). FeeSetting::current() (firstOrCreate 1 dòng). FK chỉ định rõ ('product_id','variant_id',
  'buyer_id') do model số nhiều/khác tên.
- **Vì sao:** Nền dữ liệu đơn + escrow + hoa hồng cho P1.
- **Verify:** test order→shop_order→item, ví+ledger+fee, FeeSetting::current — pass 3/3, đã xóa.
  (Fix: tạo lại tests/Unit/.gitkeep — phpunit cần thư mục.)
- **File đụng:** 2 migration (orders, wallet+fee), 7 model (Order/ShopOrder/OrderItem/FeeSetting/
  ShopOrderFee/SellerWallet/WalletLedger).
- **Nợ:** T4 updateProduct hard-delete variants → order_items.variant_id thành null (snapshot vẫn
  giữ). Sau nên soft-delete/không-xóa variant đã có đơn.

## 2026-07-18 — P1/T2: checkout (đặt hàng) — VÁ 3 BUG P0 CÒN LẠI
- **Làm gì:** Rewrite Api/CheckoutController. Từ giỏ server → trong DB transaction: khóa variant
  (lockForUpdate) → kiểm+trừ kho atomic (chống oversell) → tạo order + tách shop_orders theo shop
  + order_items (snapshot name+giá). **Giá lấy TỪ DB** (bỏ giá client). grand_total tính server.
  Dọn giỏ. payment_status=pending (VNPay ở P2). Bỏ History/Mail cũ.
- **Vì sao:** 3 bug P0 sống trong checkout: giá client (price-tampering), đơn giả (chỉ History),
  không trừ kho. Giờ vá hết.
- **Verify:** test tách-đơn-đa-shop + trừ kho, giá-từ-DB, oversell→422+rollback (kho không trừ,
  đơn không tạo), giỏ trống→400 — pass 4/4, đã xóa.
- **File đụng:** Api/CheckoutController (route /api/v1/checkout sẵn có).

## 2026-07-18 — P1/T3: bộ máy phí (FeeCalculator)
- **Làm gì:** Service `App\Services\FeeCalculator` bóc phí 1 shop_order: commission theo
  category.commission_rate từng item + payment/tech (% trên subtotal) + infra (cố định) →
  platform_total, seller_earning → ghi shop_order_fees (updateOrCreate). FeeSetting::current()
  default Shopee VN (payment 5%, tech 5%, infra 3000đ). Admin-edit fee_settings = admin-track (skip).
- **Vì sao:** Tính hoa hồng/phí sàn cho escrow (T5 gọi khi đơn completed).
- **Verify:** test khớp ví dụ design doc (đơn 500k, hoa hồng 4% → seller 427k), gọi lại không
  tạo trùng — pass 2/2, đã xóa.
- **File đụng:** app/Services/FeeCalculator.php (mới), app/Models/FeeSetting.php.

## 2026-07-18 — P1/T4: ví + sổ cái (WalletService)
- **Làm gì:** Service `App\Services\WalletService`: apply(shop,type,amount,ref) cộng/trừ
  `available` + ghi 1 dòng wallet_ledger (balance_after), khóa ví lockForUpdate chống race.
  Tiện: creditSale (cộng khi completed), debitPayout (trừ khi rút). `pending` để dành escrow P2.
- **Vì sao:** Số dư seller = sổ cái bút toán bất biến, đối soát được.
- **Verify:** cộng dồn + balance_after đúng, rút trừ available (bút toán âm) — pass 2/2, đã xóa.
- **File đụng:** app/Services/WalletService.php (mới).

## 2026-07-18 — P1/T5: vòng đời đơn (state machine)
- **Làm gì:** Api/OrderController: seller confirm (pending→confirmed), ship (confirmed→shipping);
  buyer received (shipping→completed → FeeCalculator + WalletService.creditSale, trong transaction),
  cancel (pending/confirmed → cancelled + hoàn kho). Guard: seller chỉ đơn shop mình, buyer chỉ đơn
  mình; transition sai → 422. Routes: seller trong nhóm 'seller', buyer trong auth:sanctum.
- **Vì sao:** Nối T2+T3+T4 — hoàn tất đơn thì bóc phí + cộng ví seller (escrow release); hủy hoàn kho.
- **Verify:** confirm/ship, transition sai 422, seller khác 403, received→phí+ví (seller_earning
  427k vào available), hủy hoàn kho, không hủy sau ship — pass 6/6, đã xóa.
- **File đụng:** Api/OrderController (mới), routes/api.php.

## 2026-07-18 — P1/T6: endpoint xem/quản đơn + Resources → P1 HOÀN TẤT
- **Làm gì:** OrderController thêm myOrders (buyer, đơn mình), show (buyer, chi tiết, ownership),
  sellerOrders (seller, shop_order của shop mình). Resources: OrderResource, ShopOrderResource
  (items + fee + receiver whenLoaded), OrderItemResource. Routes GET /orders, /orders/{order},
  /seller/orders.
- **Vì sao:** Buyer/seller cần xem đơn; output curate nhất quán.
- **Verify:** buyer chỉ thấy đơn mình + resource shape, buyer không xem đơn người khác (403),
  seller chỉ thấy đơn shop mình — pass 3/3, đã xóa.
- **File đụng:** Api/OrderController, 3 Resource mới, routes/api.php.

## ✅ P1 HOÀN TẤT (T1–T6)
Schema đơn+tiền · checkout (giá DB, trừ kho atomic, tách shop) · FeeCalculator · WalletService+ledger ·
state machine (confirm/ship/received/cancel, completed→phí+ví, hủy→hoàn kho) · endpoint xem đơn.
Vá xong cả 4 lỗi chặn P0. Tiếp: P2 (VNPay + escrow + payout).

## 2026-07-18 — Lên plan P2 (thanh toán ký quỹ)
- **Làm gì:** Chia P2 thành 6 task (payments+VNPay URL → callback/IPN → escrow hold → release+COD
  → refund → payout). Chốt giả định A-E (VNPay creds user cấp, refund nội bộ, COD đơn giản, chặn
  ship khi chưa paid, escrow pending→available). Export file plan.
- **Vì sao:** Plan trước khi code.
- **File đụng:** ai-workspace/plans/p2-payment-escrow-2026-07-18.md (mới)
- **Sửa plan:** User yêu cầu 2 kiểu TT như Shopee. COD thành first-class (không còn "đơn giản"):
  COD tiền vẫn qua sàn (escrow), `paid` lúc seller xác nhận `delivered` (thay shipper). Hợp nhất
  money model: sự kiện paid → phí+hold(pending); completed → release(available). State machine
  thêm bước `delivered`. T3 = lõi markOrderPaid dùng chung cho VNPay(callback) + COD(delivered).

## 2026-07-18 — P2/T1: payments + VnpayService (tạo URL)
- **Làm gì:** Migration MỚI `payments`(order_id, gateway, amount, gateway_txn_id, status, raw json).
  Payment model. config/vnpay.php + .env (VNP_TMN_CODE/HASH_SECRET/URL/RETURN_URL placeholder).
  VnpayService: createPaymentUrl (sort param + HMAC-SHA512) + validSignature (verify callback).
  Api/PaymentController@pay: buyer đơn vnpay chưa paid → tạo payment(pending) + trả pay_url.
  Route POST /orders/{order}/pay.
- **Vì sao:** Nền thanh toán online VNPay.
- **Verify:** URL có chữ ký + verify roundtrip đúng, chữ ký sai từ chối, pay ra URL, COD 422,
  đơn người khác 403 — pass 5/5, đã xóa.
- **File đụng:** migration payments, Payment, config/vnpay.php, .env, VnpayService, PaymentController,
  routes/api.php.
- **Cần user:** đăng ký VNPay sandbox điền VNP_TMN_CODE + VNP_HASH_SECRET để chạy thật.

