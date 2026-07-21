# [Tên cụm] — VD: Sản phẩm

> File mô tả nghiệp vụ, để người ngoài đọc hiểu. Viết SAU khi code xong cụm này.
> Khi nghiệp vụ đổi: SỬA ĐÈ nội dung cũ, không thêm changelog (lịch sử ở logs/history.md).
> Xóa dòng hướng dẫn này khi viết file thật.

## Cụm này làm gì
Một đoạn ngắn: cụm này phục vụ nghiệp vụ gì, cho vai trò nào (mua / bán / admin).

## Luồng nghiệp vụ
Kể từng bước, ngôn ngữ đời thường:
1. Người bán đăng sản phẩm → ...
2. ...

## Bảng DB dùng
| Bảng | Vai trò | Cột quan trọng |
|------|---------|----------------|
| `products` | ... | ... |

## Controller & Endpoint
| Method | Endpoint | Controller@action | Quyền | Việc |
|--------|----------|-------------------|-------|------|
| GET | `/api/v1/products` | ProductController@index | công khai | liệt kê |

## Quy tắc / ràng buộc quan trọng
- VD: giá luôn tính từ DB, không tin client.
- VD: trừ tồn kho atomic trong transaction.

## Liên quan cụm khác
- Nối với cụm [Đơn hàng] qua ...
