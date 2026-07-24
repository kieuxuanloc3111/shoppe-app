# Plan — P2: Thanh toán ký quỹ (VNPay + COD, escrow, payout)

**Ngày:** 2026-07-18
**Phase:** P2 (xem `ROADMAP.md`)
**Tiền đề:** P1 xong (đơn, phí, ví+sổ cái, state machine).
**Mục tiêu:** Tiền thật chạy. 2 kiểu thanh toán giống Shopee — trả liền (VNPay) & trả khi nhận
(COD). Cả hai đi qua sàn (escrow): giữ → giao xong nhả ví seller. Hoàn tiền + rút tiền (payout).

---

## Hai kiểu thanh toán (đều escrow — tiền qua sàn)

Khác nhau CHỈ ở thời điểm "paid":

| | VNPay (trả liền) | COD (trả khi nhận) |
|---|---|---|
| Lúc `paid` | Ngay khi TT online (trước giao) | Khi shipper/seller xác nhận **đã giao + thu tiền** |
| Được ship khi | Đã `paid` | Ship ngay (chưa paid) |
| Giữ tiền (hold→pending) | lúc callback paid | lúc `delivered` |
| Nhả (pending→available) | lúc `completed` (buyer nhận) | lúc `completed` |

→ Cùng 1 lõi: **sự kiện `paid` → tính phí + hold(pending); `completed` → release(available).**
Chỉ nguồn kích `paid` khác nhau.

*(Chưa có hệ shipper riêng → seller bấm "đã giao" đóng vai shipper xác nhận. Ghi chú, nâng sau.)*

---

## Phạm vi
**Trong P2:** payments table, VNPay (URL + verify callback/IPN), COD paid-on-delivery,
escrow hold/release hợp nhất, refund, payout + admin duyệt. State machine thêm `delivered`.

**KHÔNG trong P2:** refund API VNPay tự động (admin tay), hệ shipper/logistics thật,
MoMo/ZaloPay, đối soát ngân hàng, admin blade payout UI (admin-track).

---

## Decisions & Approvals

| # | Câu hỏi | Giả định (chờ duyệt) |
|---|---------|----------------------|
| A | VNPay creds | User cấp TMN_CODE + HASH_SECRET sandbox; .env placeholder; test dùng secret giả. |
| B | Hoàn tiền | Đảo bút toán nội bộ + mark refunded; refund API VNPay = admin tay/sau. |
| C | COD | **First-class**, escrow như VNPay. `paid` lúc seller xác nhận `delivered` (thay shipper). |
| D | Chặn giao | VNPay phải paid mới ship; COD ship ngay, paid lúc delivered. |
| E | Escrow | `paid` → phí + seller_earning vào `pending`; `completed` → pending→available. |

**Phê duyệt:** ⏳ chờ duyệt task list cuối file.

---

## Task breakdown

### T1 — payments table + tạo URL VNPay
- Migration MỚI `payments`(order_id, gateway, amount, gateway_txn_id nullable,
  status[pending/success/failed], raw json nullable, timestamps).
- `.env` + `config/vnpay.php`: TMN_CODE, HASH_SECRET, URL sandbox, RETURN_URL, IPN_URL.
- `VnpayService`: build payment URL (sort params + `vnp_SecureHash` HMAC-SHA512).
- Endpoint buyer: `POST /orders/{order}/pay` → order vnpay chưa paid → trả URL redirect.
- **Test:** URL có secure hash; verify hash roundtrip đúng/sai.

### T2 — Callback/IPN VNPay → paid
- `GET /payment/vnpay/return` (buyer về) + `GET /payment/vnpay/ipn` (server, nguồn sự thật).
- Verify chữ ký + `vnp_ResponseCode==00` → gọi `markOrderPaid()` (dùng chung, xem T3). Ghi payments.
- **Idempotent** (IPN bắn nhiều lần không xử lý lại).
- **Test:** chữ ký hợp lệ + code 00 → paid; sai chữ ký từ chối; bắn lại không nhân đôi.

### T3 — Escrow lõi: markOrderPaid() + hold
- `WalletService.hold()` (cộng `pending`), `release()` (pending→available).
- Hàm dùng chung `markOrderPaid(order)`: set payment_status=paid; mỗi shop_order →
  FeeCalculator + hold(seller_earning vào pending). Idempotent (đã paid thì bỏ qua).
- Sửa `ship`: VNPay order chưa paid → 422 (chặn giao); COD → cho ship.
- **Test:** markOrderPaid → wallet.pending = Σ seller_earning + fee ghi; ship VNPay chưa paid → 422.

### T4 — delivered + release; COD paid-on-delivery
- State machine thêm `deliver` (shipping→delivered, seller):
  - COD → gọi `markOrderPaid` (thu tiền khi giao) = hold pending.
  - VNPay → chỉ chuyển delivered (đã paid từ trước).
- `received` (buyer): delivered→completed → `release` (pending→available). Thay logic P1
  (P1 credit thẳng available) bằng escrow release.
- **Test:** COD deliver → paid+pending; completed → available. VNPay paid→deliver→completed → available.

### T5 — Hoàn tiền (refund)
- Hủy/hoàn đơn đã paid (chưa completed): đảo `pending`, `order.payment_status=refunded`,
  hoàn kho nếu chưa ship. Ghi ledger `refund`. (Đơn chưa paid: hủy như P1, chỉ hoàn kho.)
- **Test:** refund đơn paid → pending về 0 + ledger refund + status refunded.

### T6 — Payout (rút tiền) + admin duyệt
- Migration `payouts`(shop_id, amount, status[requested/approved/paid/rejected], bank_info, ts).
- Seller: `POST /seller/payouts` (amount ≤ available) → requested.
- Admin (API/logic; blade = admin-track): duyệt → paid → `debitPayout` (available−, ledger).
- **Test:** amount > available → 422; duyệt → available giảm + ledger `payout`.

---

## Thứ tự & phụ thuộc
```
T1 → T2 → T3 → T4 → T5 → T6
```
T3 là lõi escrow (T2 và T4 đều gọi `markOrderPaid`). T4 sửa release (thay logic tiền P1).
T6 độc lập (chỉ cần ví). Không vòng.

## Không làm P2
Refund API VNPay tự động · hệ shipper thật · MoMo/ZaloPay · đối soát ngân hàng · admin blade payout UI.
