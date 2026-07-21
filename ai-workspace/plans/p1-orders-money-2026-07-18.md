# Plan — P1: Đơn hàng & lõi tiền

**Ngày:** 2026-07-18
**Phase:** P1 (xem `ROADMAP.md`)
**Tiền đề:** P0 xong (shop, product+variant, giỏ server, role).
**Mục tiêu:** Từ giỏ → đơn hàng thật (tách theo shop), trừ kho an toàn, bộ máy hoa hồng,
ví người bán + sổ cái. Đây cũng là nơi vá 3/4 "lỗi chặn" còn lại của P0.

---

## Phạm vi

**Trong P1:** schema đơn + tiền, đặt hàng (checkout), trừ kho atomic, bộ máy phí,
ví + sổ cái, vòng đời đơn (state machine), endpoint xem/quản đơn.

**KHÔNG trong P1:**
- Cổng thanh toán thật (VNPay) → **P2**. P1 để đơn ở `payment_status=pending`;
  test đánh dấu paid tay để chạy tiếp luồng.
- Hết-hạn giữ kho, tính phí ship thật, COD nuance → sau (ghi chú, không làm).

**Vá lỗi P0 (làm ở đây):** giá tính từ DB lúc checkout · nền tảng đơn thật · trừ kho atomic.

---

## Decisions & Approvals

| # | Câu hỏi | Giả định (chờ duyệt) |
|---|---------|----------------------|
| A | Thanh toán | VNPay = P2. P1 đơn `pending`, test mark-paid tay. |
| B | Trừ kho | Trừ lúc đặt (reserve) trong transaction; hủy → hoàn kho. Chưa làm expiry. |
| C | Mô hình tiền | Escrow: sàn giữ → completed thì trừ hoa hồng + cộng ví seller. COD sau. |
| D | Phí ship | P1 = 0 (phẳng). Tính ship thật sau. |

**Phê duyệt:** ⏳ chờ duyệt task list cuối file trước khi code.

---

## Schema mới (tất cả bảng MỚI → chỉ `php artisan migrate`, KHỎI fresh)

- `orders`(buyer_id, receiver_name/phone/address snapshot, grand_total, payment_status
  [pending/paid/failed], payment_method, placed_at)
- `shop_orders`(order_id, shop_id, subtotal, shipping_fee, status
  [pending/confirmed/shipping/delivered/completed/cancelled])
- `order_items`(shop_order_id, product_id, variant_id, name + unit_price **snapshot**, qty, line_total)
- `fee_settings`(1 dòng: payment_fee_rate, tech_fee_rate, infra_fee_amount) — admin chỉnh
- `shop_order_fees`(shop_order_id, commission, payment_fee, tech_fee, infra_fee,
  platform_total, seller_earning)
- `seller_wallets`(shop_id, available, pending)
- `wallet_ledger`(shop_id, type, amount, ref_type, ref_id, balance_after)

---

## Task breakdown

### T1 — Schema đơn + tiền (migrations + models)
- Migration 7 bảng trên (bảng mới hết).
- Models: Order, ShopOrder, OrderItem, FeeSetting, ShopOrderFee, SellerWallet, WalletLedger
  + quan hệ (Order hasMany ShopOrder; ShopOrder hasMany OrderItem, belongsTo Shop; …).
- **Test:** tạo order lồng shop_order + item chạy; quan hệ load được.

### T2 — Đặt hàng (checkout)
- `Api/CheckoutController` viết lại: input = danh sách item từ giỏ (variant_id + qty) hoặc
  lấy thẳng giỏ server của user.
- Trong **DB transaction**:
  - Lấy variant từ DB, **giá = DB** (bỏ giá client → vá bug).
  - Kiểm tồn kho; **trừ kho atomic** (khóa dòng, chống oversell).
  - Tạo `order` + tách `shop_orders` theo shop + `order_items` (snapshot name/price).
  - Tính subtotal mỗi shop + grand_total server-side.
  - Xóa item đã đặt khỏi giỏ.
- `payment_status = pending` (chờ P2). Trả order.
- **Test:** đặt hàng nhiều shop → tách đúng; kho giảm đúng; giá từ DB (gửi giá lạ bị bỏ);
  đặt vượt kho → 422, không tạo đơn (rollback).

### T3 — Bộ máy phí
- `fee_settings` seed 1 dòng mặc định (theo Shopee VN: payment 5%, tech 5%, infra 3.000đ).
- `FeeCalculator` service: input shop_order → tính commission (theo `category.commission_rate`
  từng item) + payment + tech + infra → `platform_total`, `seller_earning` → ghi `shop_order_fees`.
- Admin chỉnh fee_settings (route admin — blade để admin-track, P1 chỉ cần API/logic).
- **Test:** đơn 500k, commission 4% → seller_earning đúng công thức (khớp ví dụ design doc).

### T4 — Ví + sổ cái
- `SellerWallet` (get-or-create theo shop).
- `WalletService`: `credit()/debit()` — mỗi lần ghi 1 dòng `wallet_ledger` (type, amount,
  ref, balance_after). Số dư = sổ cái (bút toán bất biến).
- **Test:** credit → available tăng + có dòng ledger balance_after đúng; nhiều bút toán cộng dồn đúng.

### T5 — Vòng đời đơn (state machine)
- Chuyển trạng thái `shop_order`:
  - Seller: confirmed → shipping → delivered.
  - Buyer: completed (xác nhận nhận) ; cancel (khi chưa ship) → **hoàn kho**.
- Trên **completed**: gọi FeeCalculator (T3) + WalletService.credit seller_earning (T4).
- Guard: chỉ seller của shop thao tác đơn shop mình; chỉ buyer của order thao tác đơn mình.
- **Test:** completed → seller_earning vào ví + ledger; cancel → kho hoàn lại; sai người → 403.

### T6 — Endpoint xem/quản đơn + Resources
- Buyer: `GET /orders` (của mình), `GET /orders/{id}`, `POST /orders/{shopOrder}/received`,
  `POST /orders/{shopOrder}/cancel`.
- Seller: `GET /seller/orders`, `POST /seller/orders/{id}/confirm|ship`.
- `OrderResource` / `ShopOrderResource` (envelope nhất quán).
- **Test:** buyer chỉ thấy đơn mình; seller chỉ thấy đơn shop mình; shape resource đúng.

---

## Thứ tự & phụ thuộc
```
T1 → T2 → T3 → T4 → T5 → T6
```
- T2 cần T1. T3, T4 cần T1 (độc lập nhau, làm tuần tự). T5 cần T2+T3+T4 (completion nối phí+ví).
  T6 cần T5. Không phụ thuộc vòng.

## Không làm trong P1
Cổng VNPay (P2) · escrow funding thật (P2) · hết-hạn giữ kho · ship thật · COD accounting · dispute (P3).
