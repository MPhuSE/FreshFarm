# TÀI LIỆU ĐẶC TẢ KỸ THUẬT RESTful API - FRESH FARM (NÔNG SẢN XANH)

> **Phiên bản API**: `v1`  
> **Backend Framework**: Laravel 11.x (PHP 8.2+)  
> **Database & Caching**: MySQL 8.0 + Redis  
> **Authentication**: Laravel Sanctum (Bearer Token)  
> **Cổng thanh toán**: VNPay Gateway (Sandbox & Production)  
> **Ngày cập nhật**: Tháng 09/2026  

---

## MỤC LỤC

1. [Tổng quan hệ thống & Kiến trúc](#1-tổng-quan-hệ-thống--kiến-trúc)
2. [Quy chuẩn giao tiếp API (Conventions)](#2-quy-chuẩn-giao-tiếp-api-conventions)
   - 2.1 [Base URL & Môi trường](#21-base-url--môi-trường)
   - 2.2 [Tiêu chuẩn Headers](#22-tiêu-chuẩn-headers)
   - 2.3 [Cấu trúc Response Envelope chuẩn](#23-cấu-trúc-response-envelope-chuẩn)
   - 2.4 [Mã trạng thái HTTP (HTTP Status Codes)](#24-mã-trạng-thái-http-http-status-codes)
   - 2.5 [Danh mục mã lỗi nghiệp vụ (Business Error Codes)](#25-danh-mục-mã-lỗi-nghiệp-vụ-business-error-codes)
3. [Xác thực & Ma trận phân quyền (Authentication & Authorization)](#3-xác-thực--ma-trận-phân-quyền-authentication--authorization)
4. [Đặc tả chi tiết Endpoints theo Module](#4-đặc-tả-chi-tiết-endpoints-theo-module)
   - [Module 1: Xác thực & Hồ sơ cá nhân (Auth & User Profile)](#module-1-xác-thực--hồ-sơ-cá-nhân-auth--user-profile)
   - [Module 2: Danh mục & Sản phẩm công khai (Public Catalog)](#module-2-danh-mục--sản-phẩm-công-khai-public-catalog)
   - [Module 3: Giỏ hàng (Shopping Cart)](#module-3-giỏ-hàng-shopping-cart)
   - [Module 4: Xem trước & Đặt hàng (Checkout & Orders)](#module-4-xem-trước--đặt-hàng-checkout--orders)
   - [Module 5: Cổng thanh toán trực tuyến VNPay (Payment Gateway)](#module-5-cổng-thanh-toán-trực-tuyến-vnpay-payment-gateway)
   - [Module 6: Đánh giá sản phẩm (Product Reviews)](#module-6-đánh-giá-sản-phẩm-product-reviews)
   - [Module 7: Quản trị viên (Admin Management)](#module-7-quản-trị-viên-admin-management)
   - [Module 8: Đo hiệu năng & Benchmark (Redis vs Database)](#module-8-đo-hiệu-năng--benchmark-redis-vs-database)
5. [Quy tắc nghiệp vụ & Luồng xử lý cốt lõi](#5-quy-tắc-nghiệp-vụ--luồng-xử-lý-cốt-lõi)
   - 5.1 [Luồng đặt hàng & Khóa tồn kho (Inventory Pessimistic Locking)](#51-luồng-đặt-hàng--khóa-tồn-kho-inventory-pessimistic-locking)
   - 5.2 [Ma trận vòng đời trạng thái đơn hàng (Order State Transition)](#52-ma-trận-vòng-đời-trạng-thái-đơn-hàng-order-state-transition)
   - 5.3 [Quy trình thanh toán VNPay & Hủy đơn tự động quá hạn](#53-quy-trình-thanh-toán-vnpay--hủy-đơn-tự-động-quá-hạn)
   - 5.4 [Chiến lược Caching Redis cho Catalog](#54-chiến-lược-caching-redis-cho-catalog)
6. [Hướng dẫn kiểm thử nhanh (cURL Quickstart)](#6-hướng-dẫn-kiểm-thử-nhanh-curl-quickstart)

---

## 1. TỔNG QUAN HỆ THỐNG & KIẾN TRÚC

Fresh Farm (Nông Sản Xanh) là hệ thống thương mại điện tử chuyên cung cấp nông sản sạch trực tiếp từ nông trại đến người tiêu dùng. Hệ thống backend xây dựng trên kiến trúc **Service-Oriented Clean Architecture** bằng Laravel 11, tuân thủ các nguyên tắc:
- **Thin Controller, Fat Service**: Controller chỉ đóng vai trò nhận request, xác thực Form Request, chuyển tiếp cho tầng Service và phản hồi JSON qua `ApiResponse` trait.
- **Data Consistency & Concurrency**: Bảo toàn dữ liệu tồn kho bằng Transaction và Database Pessimistic Locking (`lockForUpdate`).
- **High Performance**: Ứng dụng Redis Cache cho danh mục, sản phẩm công khai với cơ chế invalidation chủ động; tăng tốc truy vấn gấp nhiều lần so với truy vấn MySQL thông thường.
- **Strict Response Envelope**: Mọi phản hồi API đều đồng nhất theo một định dạng duy nhất, giúp đội ngũ Frontend (Web/Mobile) dễ dàng bắt lỗi và bóc tách dữ liệu.

---

## 2. QUY CHUẨN GIAO TIẾP API (CONVENTIONS)

### 2.1 Base URL & Môi trường

| Môi trường | Base URL | Ghi chú |
| :--- | :--- | :--- |
| **Local Development** | `http://localhost:8000/api/v1` | Môi trường phát triển nội bộ |
| **Staging / Test** | `https://staging-api.freshfarm.vn/api/v1` | Tích hợp kiểm thử giữa FE & BE |
| **Production** | `https://api.freshfarm.vn/api/v1` | Triển khai chính thức |

*(Lưu ý: Route Benchmark kiểm tra độ trễ nằm tại: `GET /v1/benchmark/redis-vs-db`)*

### 2.2 Tiêu chuẩn Headers

Mọi request từ phía Client gửi đến máy chủ cần có các Header tiêu chuẩn sau:

```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer <token>      # Bắt buộc đối với các endpoint yêu cầu đăng nhập
Idempotency-Key: <uuid-v4>         # Khuyến nghị/bắt buộc khi POST đặt hàng để chống trùng lặp
X-Request-ID: <trace-uuid>         # Tùy chọn, dùng để trace log qua hệ thống Sentry/Logs
```

### 2.3 Cấu trúc Response Envelope chuẩn

Hệ thống sử dụng trait `App\Traits\ApiResponse` để chuẩn hóa toàn bộ dữ liệu trả về theo 3 mẫu format:

#### 1. Thành công đơn lẻ (Single Object)
```json
{
  "success": true,
  "message": "Lấy chi tiết sản phẩm thành công.",
  "data": {
    "id": 1,
    "name": "Cam Sành Hàm Yên",
    "price": 35000
  },
  "meta": null,
  "errors": null
}
```

#### 2. Thành công danh sách có phân trang (Paginated List)
```json
{
  "success": true,
  "message": "Lấy danh sách và tìm kiếm sản phẩm thành công.",
  "data": [
    {
      "id": 1,
      "sku": "CAM-HY-01",
      "name": "Cam Sành Hàm Yên",
      "price": 35000
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 12,
    "total": 38,
    "last_page": 4,
    "from": 1,
    "to": 12
  },
  "errors": null,
  "links": {
    "first": "http://localhost:8000/api/v1/products?page=1",
    "last": "http://localhost:8000/api/v1/products?page=4",
    "prev": null,
    "next": "http://localhost:8000/api/v1/products?page=2"
  }
}
```

#### 3. Phản hồi Lỗi (Error Envelope)
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ.",
  "data": null,
  "error_code": "VALIDATION_ERROR",
  "errors": {
    "email": [
      "Email này đã được sử dụng trên hệ thống."
    ]
  },
  "trace_id": "req_65f8a7e089abc"
}
```

### 2.4 Mã trạng thái HTTP (HTTP Status Codes)

| Mã HTTP | Tên chuẩn | Trường hợp sử dụng trong Fresh Farm |
| :---: | :--- | :--- |
| **200** | `OK` | Truy vấn (GET), Cập nhật thành công (PUT/PATCH), Xóa logic (DELETE) |
| **201** | `Created` | Tạo mới thành công (Đăng ký, Thêm giỏ hàng, Đặt hàng, Upload ảnh) |
| **400** | `Bad Request` | Tham số không đúng logic, giỏ hàng trống khi checkout |
| **401** | `Unauthorized` | Chưa đăng nhập, token không hợp lệ hoặc đã hết hạn |
| **403** | `Forbidden` | Không có quyền (Ví dụ: khách hàng truy cập API admin; tài khoản bị khóa) |
| **404** | `Not Found` | Không tìm thấy tài nguyên (Sản phẩm, Đơn hàng, Địa chỉ không tồn tại) |
| **409** | `Conflict` | Xung đột nghiệp vụ (Hết hàng tồn kho, Chuyển trạng thái sai quy trình, v.v.) |
| **413** | `Payload Too Large`| File tải lên vượt quá giới hạn dung lượng (Ảnh > 5MB) |
| **415** | `Unsupported Media`| Định dạng file không được hỗ trợ |
| **422** | `Unprocessable Entity`| Lỗi xác thực dữ liệu đầu vào (Validation Form Request) |
| **429** | `Too Many Requests`| Bị chặn bởi cơ chế Rate Limiting (Quá số lần đăng nhập, spam đặt hàng) |
| **500** | `Internal Error` | Lỗi máy chủ không mong muốn |

### 2.5 Danh mục mã lỗi nghiệp vụ (Business Error Codes)

| `error_code` | HTTP Status | Giải thích chi tiết |
| :--- | :---: | :--- |
| `VALIDATION_ERROR` | 422 | Dữ liệu gửi lên sai định dạng hoặc thiếu các trường bắt buộc |
| `UNAUTHENTICATED` | 401 | Thiếu Bearer Token hoặc token không hợp lệ |
| `FORBIDDEN` | 403 | Tài khoản không có vai trò hoặc quyền thực thi thao tác |
| `INVALID_CREDENTIALS` | 401 | Email hoặc mật khẩu không chính xác |
| `ACCOUNT_LOCKED` | 403 | Tài khoản của người dùng đã bị quản trị viên khóa (`status = locked`) |
| `TOO_MANY_ATTEMPTS` | 429 | Thử đăng nhập sai quá nhiều lần liên tiếp |
| `PRODUCT_NOT_FOUND` | 404 | Không tìm thấy sản phẩm theo ID hoặc Slug |
| `OUT_OF_STOCK` | 409 | Sản phẩm trong kho đã hết hàng hoặc không đủ số lượng yêu cầu |
| `INSUFFICIENT_STOCK` | 409 | Số lượng yêu cầu vượt quá tồn kho khả dụng hiện tại |
| `CART_ITEM_NOT_FOUND` | 404 | Không tìm thấy sản phẩm trong giỏ của người dùng |
| `CART_CHANGED` | 400 / 409 | Giỏ hàng hoặc giá sản phẩm đã thay đổi trong quá trình thanh toán |
| `INVALID_COUPON` | 422 | Mã giảm giá không tồn tại, hết hạn hoặc không thỏa mãn điều kiện |
| `ORDER_NOT_FOUND` | 404 | Đơn hàng không tồn tại hoặc không thuộc quyền sở hữu của user |
| `INVALID_ORDER_TRANSITION`| 409 | Cố gắng chuyển đơn hàng sang trạng thái trái quy tắc nghiệp vụ |
| `INVALID_PAYMENT_STATE` | 409 | Cố cập nhật trạng thái thanh toán trái quy chuẩn |
| `CANNOT_LOCK_SELF` | 409 | Quản trị viên không được phép tự khóa tài khoản của chính mình |
| `CANNOT_CHANGE_SELF_ROLE`| 409 | Quản trị viên không được phép tự hạ hoặc thay đổi vai trò của mình |
| `FILE_TOO_LARGE` | 413 | Dung lượng file upload vượt quá giới hạn hệ thống |

---

## 3. XÁC THỰC & MA TRẬN PHÂN QUYỀN (AUTHENTICATION & AUTHORIZATION)

### 3.1 Cơ chế xác thực
- Hệ thống áp dụng **Laravel Sanctum**. Khi khách hàng hoặc nhân viên đăng nhập thành công qua `/api/v1/auth/login`, máy chủ cấp chuỗi `token` dạng PlainTextToken.
- Mọi request tiếp theo (yêu cầu xác thực) phải kèm header:  
  `Authorization: Bearer <token>`
- Khi đăng xuất qua `/api/v1/auth/logout`, token hiện tại sẽ bị thu hồi và xóa khỏi cơ sở dữ liệu (`personal_access_tokens`).

### 3.2 Ma trận phân quyền (RBAC Matrix)

| Nhóm API Endpoints | Khách (Guest) | Khách hàng (Customer) | Quản trị viên (Admin) |
| :--- | :---: | :---: | :---: |
| **Đăng ký, Đăng nhập, Xem Catalog & Sản phẩm** | ✅ Có | ✅ Có | ✅ Có |
| **Quản lý Giỏ hàng (Xem, Thêm, Sửa, Xóa item)** | ❌ Không | ✅ Có | ❌ Không |
| **Xem trước (Preview) & Đặt hàng (Place Order)** | ❌ Không | ✅ Có | ❌ Không |
| **Xem lịch sử đơn & Tự hủy đơn hàng của mình** | ❌ Không | ✅ Có | ❌ Không |
| **Tạo liên kết thanh toán VNPay** | ❌ Không | ✅ Có | ❌ Không |
| **Sổ địa chỉ & Cập nhật Profile cá nhân** | ❌ Không | ✅ Có | ✅ Có |
| **Quản lý Sản phẩm, Kho, Upload ảnh (Admin)** | ❌ Không | ❌ Không | ✅ Có (Middleware: `EnsureUserIsAdmin`) |
| **Quản lý trạng thái đơn hàng & Thanh toán (Admin)** | ❌ Không | ❌ Không | ✅ Có (Middleware: `EnsureUserIsAdmin`) |
| **Khóa tài khoản & Phân quyền User (Admin)** | ❌ Không | ❌ Không | ✅ Có (Middleware: `EnsureUserIsAdmin`) |
| **Báo cáo thống kê KPI tổng quan (Admin)** | ❌ Không | ❌ Không | ✅ Có (Middleware: `EnsureUserIsAdmin`) |

---

## 4. ĐẶC TẢ CHI TIẾT ENDPOINTS THEO MODULE

---

### MODULE 1: XÁC THỰC & HỒ SƠ CÁ NHÂN (AUTH & USER PROFILE)

#### 1.1 Đăng ký tài khoản
- **Method**: `POST`
- **Path**: `/api/v1/auth/register`
- **Auth**: Không (Public)
- **Middleware**: `throttle:auth` (tối đa 10 lần/phút)
- **Request Body**:
```json
{
  "name": "Nguyễn Văn An",
  "email": "nguyenvanan@example.com",
  "phone": "0987654321",
  "password": "Password@123",
  "password_confirmation": "Password@123"
}
```
- **Phản hồi thành công (201 Created)**:
```json
{
  "success": true,
  "message": "Đăng ký tài khoản thành công.",
  "data": {
    "user": {
      "id": 15,
      "name": "Nguyễn Văn An",
      "email": "nguyenvanan@example.com",
      "phone": "0987654321",
      "role": "customer",
      "status": "active",
      "created_at": "2026-09-15T15:00:00.000000Z"
    },
    "token": "15|9qwe8d7as6d5as4d...",
    "token_type": "Bearer"
  },
  "meta": null,
  "errors": null
}
```
- **Lỗi thường gặp**:
  - `422 Unprocessable Entity`: `VALIDATION_ERROR` (Email đã tồn tại, mật khẩu không khớp hoặc ít hơn 8 ký tự).

---

#### 1.2 Đăng nhập
- **Method**: `POST`
- **Path**: `/api/v1/auth/login`
- **Auth**: Không (Public)
- **Middleware**: `throttle:auth`
- **Request Body**:
```json
{
  "email": "nguyenvanan@example.com",
  "password": "Password@123"
}
```
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "message": "Đăng nhập thành công.",
  "data": {
    "user": {
      "id": 15,
      "name": "Nguyễn Văn An",
      "email": "nguyenvanan@example.com",
      "phone": "0987654321",
      "role": "customer",
      "status": "active"
    },
    "token": "16|k1j2h3k4j5h6g7...",
    "token_type": "Bearer"
  },
  "meta": null,
  "errors": null
}
```
- **Lỗi thường gặp**:
  - `401 Unauthorized`: `INVALID_CREDENTIALS` ("Tài khoản hoặc mật khẩu không chính xác.")
  - `403 Forbidden`: `ACCOUNT_LOCKED` ("Tài khoản của bạn đã bị khóa.")
  - `429 Too Many Requests`: `TOO_MANY_ATTEMPTS` (Vượt quá giới hạn thử sai).

---

#### 1.3 Đăng xuất
- **Method**: `POST`
- **Path**: `/api/v1/auth/logout`
- **Auth**: Bearer Token (`auth:sanctum`)
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "message": "Đăng xuất thành công.",
  "data": null,
  "meta": null,
  "errors": null
}
```

---

#### 1.4 Lấy thông tin tài khoản hiện tại
- **Method**: `GET`
- **Path**: `/api/v1/me`
- **Auth**: Bearer Token (`auth:sanctum`)
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "message": "Lấy thông tin tài khoản thành công.",
  "data": {
    "id": 15,
    "name": "Nguyễn Văn An",
    "email": "nguyenvanan@example.com",
    "phone": "0987654321",
    "role": "customer",
    "status": "active",
    "created_at": "2026-09-15T15:00:00.000000Z"
  },
  "meta": null,
  "errors": null
}
```

---

#### 1.5 Cập nhật thông tin cá nhân
- **Method**: `PUT`
- **Path**: `/api/v1/user/profile`
- **Auth**: Bearer Token (`auth:sanctum`)
- **Request Body**:
```json
{
  "name": "Nguyễn Văn An (VIP)",
  "phone": "0912345678"
}
```
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "message": "Cập nhật thông tin cá nhân thành công.",
  "data": {
    "id": 15,
    "name": "Nguyễn Văn An (VIP)",
    "email": "nguyenvanan@example.com",
    "phone": "0912345678",
    "role": "customer",
    "status": "active"
  }
}
```

---

#### 1.6 Quản lý Sổ địa chỉ người dùng
Tất cả endpoint yêu cầu header `Authorization: Bearer <token>`.

- **`GET /api/v1/user/addresses`**: Lấy danh sách địa chỉ nhận hàng của người dùng hiện tại (sắp xếp ưu tiên địa chỉ mặc định `is_default = true`).
- **`POST /api/v1/user/addresses`**: Thêm địa chỉ mới.
  - Request Body:
    ```json
    {
      "recipient_name": "Nguyễn Văn An",
      "phone": "0987654321",
      "address": "Số 123 Đường Cầu Giấy, Phường Quan Hoa, Quận Cầu Giấy, Hà Nội",
      "is_default": true
    }
    ```
  - *Lưu ý*: Nếu `is_default: true`, hệ thống tự động reset các địa chỉ cũ khác thành `is_default: false`.
- **`PUT /api/v1/user/addresses/{address_id}`**: Cập nhật địa chỉ (Chỉ sửa được địa chỉ của chính mình).
- **`DELETE /api/v1/user/addresses/{address_id}`**: Xóa địa chỉ.

---

### MODULE 2: DANH MỤC & SẢN PHẨM CÔNG KHAI (PUBLIC CATALOG)

#### 2.1 Danh sách danh mục công khai
- **Method**: `GET`
- **Path**: `/api/v1/categories`
- **Auth**: Không (Public)
- **Cơ chế tối ưu**: Dữ liệu được cache trên Redis (`catalog:categories:active`) với TTL 60 phút. Tự động xóa cache khi Admin sửa danh mục.
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "message": "Lấy danh sách danh mục công khai thành công.",
  "data": [
    {
      "id": 1,
      "name": "Rau củ hữu cơ",
      "slug": "rau-cu-huu-co",
      "status": "active"
    },
    {
      "id": 2,
      "name": "Trái cây nhiệt đới",
      "slug": "trai-cay-nhiet-doi",
      "status": "active"
    }
  ],
  "meta": null,
  "errors": null
}
```

---

#### 2.2 Danh sách sản phẩm & Tìm kiếm, Lọc, Phân trang
- **Method**: `GET`
- **Path**: `/api/v1/products`
- **Auth**: Không (Public)
- **Query Parameters**:
  - `page` (int, default: 1): Trang hiện tại
  - `limit` (int, default: 12): Số sản phẩm trên mỗi trang
  - `category_id` (int, optional): Lọc theo ID danh mục
  - `search` (string, optional): Từ khóa tìm kiếm theo tên hoặc SKU
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "message": "Lấy danh sách và tìm kiếm sản phẩm thành công.",
  "data": [
    {
      "id": 101,
      "category": {
        "id": 2,
        "name": "Trái cây nhiệt đới",
        "slug": "trai-cay-nhiet-doi",
        "status": "active"
      },
      "sku": "CAM-VINH-01",
      "name": "Cam Sành Vinh Hữu Cơ",
      "slug": "cam-sanh-vinh-huu-co",
      "unit": "kg",
      "price": 45000,
      "origin": "Nghệ An",
      "available_quantity": 85,
      "status": "active",
      "primary_image_url": "http://localhost:8000/storage/products/cam-vinh.webp"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 12,
    "total": 1,
    "last_page": 1,
    "from": 1,
    "to": 1
  },
  "links": {
    "first": "http://localhost:8000/api/v1/products?page=1",
    "last": "http://localhost:8000/api/v1/products?page=1",
    "prev": null,
    "next": null
  }
}
```

---

#### 2.3 Chi tiết sản phẩm
- **Method**: `GET`
- **Path**: `/api/v1/products/{slug}`
- **Auth**: Không (Public)
- **Path Parameter**: `slug` (string, ví dụ: `cam-sanh-vinh-huu-co`)
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "message": "Lấy chi tiết sản phẩm thành công.",
  "data": {
    "id": 101,
    "category": {
      "id": 2,
      "name": "Trái cây nhiệt đới",
      "slug": "trai-cay-nhiet-doi",
      "status": "active"
    },
    "sku": "CAM-VINH-01",
    "name": "Cam Sành Vinh Hữu Cơ",
    "slug": "cam-sanh-vinh-huu-co",
    "unit": "kg",
    "price": 45000,
    "origin": "Nghệ An",
    "available_quantity": 85,
    "status": "active",
    "primary_image_url": "http://localhost:8000/storage/products/cam-vinh.webp",
    "description_html": "<p>Cam sành hữu cơ chuẩn VietGAP mọng nước, vị ngọt thanh tự nhiên.</p>",
    "images": [
      {
        "id": 1,
        "image_url": "http://localhost:8000/storage/products/cam-vinh-1.webp",
        "alt_text": "Ảnh chính cam sành",
        "sort_order": 0,
        "is_primary": true
      },
      {
        "id": 2,
        "image_url": "http://localhost:8000/storage/products/cam-vinh-2.webp",
        "alt_text": "Ảnh bổ cam mọng nước",
        "sort_order": 1,
        "is_primary": false
      }
    ]
  },
  "meta": null,
  "errors": null
}
```
- **Lỗi thường gặp**:
  - `404 Not Found`: Không tìm thấy sản phẩm có slug tương ứng.

---

### MODULE 3: GIỎ HÀNG (SHOPPING CART)

Tất cả endpoint giỏ hàng yêu cầu đăng nhập: `Authorization: Bearer <token>`.

#### 3.1 Xem giỏ hàng hiện tại
- **Method**: `GET`
- **Path**: `/api/v1/cart`
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "message": "Lấy giỏ hàng hiện tại thành công.",
  "data": {
    "id": 8,
    "items": [
      {
        "id": 24,
        "product": {
          "id": 101,
          "category": {
            "id": 2,
            "name": "Trái cây nhiệt đới",
            "slug": "trai-cay-nhiet-doi"
          },
          "sku": "CAM-VINH-01",
          "name": "Cam Sành Vinh Hữu Cơ",
          "slug": "cam-sanh-vinh-huu-co",
          "unit": "kg",
          "price": 45000,
          "origin": "Nghệ An",
          "available_quantity": 85,
          "status": "active",
          "primary_image_url": "/storage/products/cam-vinh.webp"
        },
        "quantity": 2,
        "unit_price": 45000,
        "line_total": 90000
      }
    ],
    "summary": {
      "subtotal": 90000,
      "discount": 0,
      "shipping_fee": 30000,
      "grand_total": 120000
    }
  },
  "meta": null,
  "errors": null
}
```

---

#### 3.2 Thêm sản phẩm vào giỏ
- **Method**: `POST`
- **Path**: `/api/v1/cart/items`
- **Middleware**: `throttle:cart`
- **Request Body**:
```json
{
  "product_id": 101,
  "quantity": 3
}
```
- **Phản hồi thành công (201 Created)**: Trả về cấu trúc giỏ hàng mới cập nhật (tương tự GET `/api/v1/cart`).
- **Lỗi nghiệp vụ**:
  - `409 Conflict`: `OUT_OF_STOCK` (Sản phẩm hết hàng hoặc số lượng yêu cầu vượt quá tồn kho khả dụng).

---

#### 3.3 Cập nhật số lượng sản phẩm trong giỏ
- **Method**: `PATCH`
- **Path**: `/api/v1/cart/items/{id}`
- **Path Parameter**: `id` - ID của bản ghi CartItem (ví dụ: `24`)
- **Request Body**:
```json
{
  "quantity": 5
}
```
- **Phản hồi thành công (200 OK)**: Trả về giỏ hàng kèm tổng tiền mới.

---

#### 3.4 Xóa sản phẩm khỏi giỏ hàng
- **Method**: `DELETE`
- **Path**: `/api/v1/cart/items/{id}`
- **Path Parameter**: `id` - ID bản ghi CartItem
- **Phản hồi thành công (200 OK)**: Trả về giỏ hàng đã loại bỏ item đó.

---

### MODULE 4: XEM TRƯỚC & ĐẶT HÀNG (CHECKOUT & ORDERS)

Tất cả endpoint yêu cầu header `Authorization: Bearer <token>`.

#### 4.1 Xem trước thông tin thanh toán (Checkout Preview)
- **Method**: `POST`
- **Path**: `/api/v1/checkout/preview`
- **Mục đích**: Tính toán lại tạm tính, phí ship, áp dụng mã giảm giá và đối chiếu số tiền tổng cộng trước khi khách bấm nút "Đặt hàng".
- **Request Body**:
```json
{
  "address_id": 4,
  "payment_method": "cod",
  "coupon_code": "XANH10"
}
```
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "message": "Xem trước thành công.",
  "data": {
    "items": [
      {
        "id": 24,
        "product": {
          "id": 101,
          "name": "Cam Sành Vinh Hữu Cơ",
          "unit": "kg",
          "price": 45000
        },
        "quantity": 2,
        "unit_price": 45000,
        "line_total": 90000
      }
    ],
    "address": {
      "id": 4,
      "recipient_name": "Nguyễn Văn An",
      "phone": "0987654321",
      "full_address": "Số 123 Đường Cầu Giấy, Phường Quan Hoa, Cầu Giấy, Hà Nội"
    },
    "subtotal": 90000,
    "discount": 9000,
    "shipping_fee": 30000,
    "grand_total": 111000,
    "coupon": {
      "code": "XANH10",
      "discount": 9000
    }
  },
  "meta": null,
  "errors": null
}
```
- **Lỗi thường gặp**:
  - `400 Bad Request`: Giỏ hàng đang trống.
  - `404 Not Found`: Không tìm thấy địa chỉ hoặc địa chỉ không thuộc về user.
  - `422 Unprocessable Entity`: `INVALID_COUPON` ("Mã giảm giá không hợp lệ.").

---

#### 4.2 Tạo đơn hàng (Place Order)
- **Method**: `POST`
- **Path**: `/api/v1/orders`
- **Headers**:
  - `Authorization: Bearer <token>`
  - `Idempotency-Key: <uuid-v4>` (Chống lặp đơn khi bấm nhiều lần)
- **Middleware**: `throttle:checkout`
- **Request Body**:
```json
{
  "address_id": 4,
  "payment_method": "cod",
  "coupon_code": "XANH10",
  "note": "Giao vào giờ hành chính giúp tôi",
  "idempotency_key": "41d3345c-8731-4ca6-8b8d-22c899b5a100"
}
```
*Ghi chú `payment_method`:*
- `"cod"`: Thanh toán khi nhận hàng.
- `"vnpay"`: Thanh toán qua cổng VNPay. Khi chọn vnpay, hệ thống sẽ tự động lên lịch hủy đơn sau 10 phút nếu khách không thanh toán.

- **Phản hồi thành công (201 Created)**:
```json
{
  "success": true,
  "message": "Tạo đơn hàng thành công.",
  "data": {
    "id": 45,
    "order_code": "NSX-20260915-4891",
    "status": "pending",
    "payment_status": "unpaid",
    "payment_method": "cod",
    "subtotal": 90000,
    "discount": 9000,
    "shipping_fee": 30000,
    "grand_total": 111000,
    "created_at": "2026-09-15T15:30:00+07:00"
  },
  "meta": null,
  "errors": null
}
```
- **Cơ chế xử lý ngầm tại máy chủ**:
  1. Mở DB Transaction.
  2. Thực hiện `Inventory::lockForUpdate()` trên từng sản phẩm. Nếu không đủ tồn kho, ném lỗi `409 Conflict` (`OUT_OF_STOCK`).
  3. Trừ số lượng tồn kho `quantity_on_hand -= quantity`.
  4. Lưu Order & OrderItems với thông tin snapshot (tên, giá tại thời điểm mua).
  5. Xóa sạch các mặt hàng trong giỏ hàng hiện tại của khách.
  6. Commit Transaction. Nếu lỗi, Rollback toàn bộ.

---

#### 4.3 Danh sách đơn hàng của khách
- **Method**: `GET`
- **Path**: `/api/v1/orders`
- **Query Parameters**:
  - `status` (string, optional): Lọc theo trạng thái (`pending`, `confirmed`, `shipping`, `delivered`, `cancelled`)
  - `page` (int, default: 1)
  - `per_page` (int, default: 20)
- **Phản hồi thành công (200 OK)**: Danh sách đơn hàng có phân trang.

---

#### 4.4 Chi tiết đơn hàng của khách
- **Method**: `GET`
- **Path**: `/api/v1/orders/{order_code}`
- **Path Parameter**: `order_code` (ví dụ: `NSX-20260915-4891`)
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "message": "Chi tiết đơn của khách thành công.",
  "data": {
    "id": 45,
    "order_code": "NSX-20260915-4891",
    "user_id": 15,
    "status": "pending",
    "payment_status": "unpaid",
    "payment_method": "cod",
    "recipient_name": "Nguyễn Văn An",
    "phone": "0987654321",
    "shipping_address": "Số 123 Đường Cầu Giấy, Phường Quan Hoa, Cầu Giấy, Hà Nội",
    "subtotal": 90000,
    "discount_amount": 9000,
    "shipping_fee": 30000,
    "grand_total": 111000,
    "note": "Giao vào giờ hành chính giúp tôi",
    "created_at": "2026-09-15T15:30:00.000000Z",
    "items": [
      {
        "id": 89,
        "order_id": 45,
        "product_id": 101,
        "product_name": "Cam Sành Vinh Hữu Cơ",
        "unit": "kg",
        "unit_price": 45000,
        "quantity": 2,
        "line_total": 90000
      }
    ]
  },
  "meta": null,
  "errors": null
}
```

---

#### 4.5 Khách tự hủy đơn hàng
- **Method**: `POST`
- **Path**: `/api/v1/orders/{order_code}/cancel`
- **Điều kiện**: Đơn hàng phải thuộc về user đang đăng nhập và có trạng thái là `pending` hoặc `confirmed`.
- **Request Body** (tùy chọn):
```json
{
  "reason": "Tôi muốn đổi địa chỉ giao hàng khác"
}
```
- **Phản hồi thành công (200 OK)**: Đơn chuyển sang `cancelled`.
- **Lỗi nghiệp vụ**:
  - `409 Conflict`: `INVALID_ORDER_TRANSITION` ("Trạng thái đơn không cho phép hủy." do đơn đã bước vào khâu `shipping` hoặc `delivered`).

---

### MODULE 5: CỔNG THANH TOÁN TRỰC TUYẾN VNPAY (PAYMENT GATEWAY)

#### 5.1 Tạo URL thanh toán VNPay
- **Method**: `POST`
- **Path**: `/api/v1/payment/vnpay/{order_code}`
- **Auth**: Bearer Token (`auth:sanctum`)
- **Path Parameter**: `order_code` (Mã đơn hàng cần thanh toán)
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "payment_url": "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=11100000&vnp_Command=pay&vnp_CreateDate=20260915153000&vnp_CurrCode=VND&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+NSX-20260915-4891&vnp_OrderType=billpayment&vnp_ReturnUrl=http%3A%2F%2Flocalhost%3A3000%2Fcheckout%2Fvnpay-result&vnp_TmnCode=FRESHFARM&vnp_TxnRef=NSX-20260915-4891&vnp_Version=2.1.0&vnp_SecureHash=a9b8c7d6..."
}
```
- **Frontend Action**: FE chuyển hướng trình duyệt của khách hàng đến đường dẫn `payment_url` này để thực hiện thanh toán trên cổng VNPay.
- **Lỗi thường gặp**:
  - `400 Bad Request`: "Đơn hàng đã được thanh toán."

---

#### 5.2 VNPay Webhook (Server-to-Server IPN)
- **Method**: `GET`
- **Path**: `/api/v1/payment/vnpay/ipn`
- **Auth**: Không (Máy chủ VNPay tự động gọi đến)
- **Mục đích**: VNPay bắn thông báo kết quả giao dịch sang Backend. Backend tiến hành kiểm tra chữ ký Hash bảo mật (`vnp_SecureHash`), nếu hợp lệ thì tự động chuyển `payment_status` của đơn hàng thành `paid`.
- **Response trả về cho VNPay**:
```json
{
  "RspCode": "00",
  "Message": "Confirm Success"
}
```

---

### MODULE 6: ĐÁNH GIÁ SẢN PHẨM (PRODUCT REVIEWS)

#### 6.1 Xem danh sách đánh giá sản phẩm (Public)
- **Method**: `GET`
- **Path**: `/api/v1/products/{id}/reviews`
- **Auth**: Không (Public)
- **Mô tả**: Trả về các đánh giá đã được kiểm duyệt (`status = approved`) của sản phẩm.

#### 6.2 Gửi đánh giá sản phẩm sau mua hàng
- **Method**: `POST`
- **Path**: `/api/v1/reviews`
- **Auth**: Bearer Token (`auth:sanctum`)
- **Request Body**:
```json
{
  "order_code": "NSX-20260915-4891",
  "product_id": 101,
  "rating": 5,
  "comment": "Cam rất tươi ngon, nhiều nước, giao nhanh đúng hẹn!"
}
```
- **Quy tắc nghiệp vụ**:
  - Khách chỉ được đánh giá khi đơn hàng đã chuyển sang trạng thái `delivered`.
  - Mỗi sản phẩm trong một đơn hàng chỉ được đánh giá 1 lần (tránh spam).

---

### MODULE 7: QUẢN TRỊ VIÊN (ADMIN MANAGEMENT)

> **Yêu cầu bảo mật**: Tất cả các route bắt đầu bằng `/api/v1/admin/` đều bắt buộc qua 2 lớp Middleware:
> 1. `auth:sanctum` (Phải có Bearer Token)
> 2. `App\Http\Middleware\EnsureUserIsAdmin` (User phải có vai trò `admin` hoặc `staff`).

---

#### 7.1 Quản lý Sản phẩm (Admin Catalog)

- **`GET /api/v1/admin/products`**: Xem danh sách toàn bộ sản phẩm (kèm cả sản phẩm ẩn/inactive).
  - Params: `page`, `limit`, `search`.
- **`POST /api/v1/admin/products`**: Tạo mới sản phẩm.
  - Request Body:
    ```json
    {
      "name": "Bưởi Da Xanh Bến Tre",
      "category_id": 2,
      "sku": "BUOI-DX-01",
      "unit": "quả",
      "price": 65000,
      "origin": "Bến Tre",
      "stock": 50,
      "description": "Bưởi da xanh tép hồng ngọt đậm tự nhiên."
    }
    ```
- **`GET /api/v1/admin/products/{id}`**: Lấy chi tiết sản phẩm theo ID.
- **`PATCH /api/v1/admin/products/{id}`**: Cập nhật thông tin sản phẩm.
- **`DELETE /api/v1/admin/products/{id}`**: Xóa/Ẩn sản phẩm.

---

#### 7.2 Quản lý Thư viện ảnh sản phẩm (Images)

- **`POST /api/v1/admin/products/{id}/images`**: Upload nhiều ảnh cho sản phẩm.
  - `Content-Type: multipart/form-data`
  - Body:
    - `images[]`: Danh sách các file ảnh (jpg, png, webp, max 5MB).
    - `alt_text[]`: Text mô tả ảnh (tùy chọn).
- **`PATCH /api/v1/admin/products/{id}/images/reorder`**: Sắp xếp lại thứ tự ưu tiên hiển thị của các ảnh.
  - Body:
    ```json
    {
      "images": [
        { "id": 12, "sort_order": 0 },
        { "id": 15, "sort_order": 1 }
      ]
    }
    ```

---

#### 7.3 Quản lý Đơn hàng (Admin Orders)

- **`GET /api/v1/admin/orders`**: Xem danh sách tất cả đơn hàng trong hệ thống.
  - Query Params:
    - `status` (`pending`, `confirmed`, `shipping`, `delivered`, `cancelled`, `returned`)
    - `payment_status` (`unpaid`, `paid`, `refunded`)
    - `page`, `per_page`
- **`GET /api/v1/admin/orders/{order_code}`**: Xem chi tiết đơn hàng cho quản trị viên.
- **`PATCH /api/v1/admin/orders/{id}/status`**: Cập nhật trạng thái đơn hàng.
  - Request Body:
    ```json
    {
      "status": "confirmed",
      "note": "Đã gọi điện xác nhận đơn với khách hàng"
    }
    ```
  - **Logic Hoàn kho**: Nếu Admin đổi trạng thái thành `cancelled`, hệ thống tự động chạy Transaction lặp qua toàn bộ sản phẩm trong đơn và hoàn trả lại số lượng vào kho (`quantity_on_hand += quantity`).
- **`PATCH /api/v1/admin/orders/{id}/payment-status`**: Cập nhật trạng thái thanh toán.
  - Request Body:
    ```json
    {
      "payment_status": "paid",
      "transaction_ref": "VCB-8829103"
    }
    ```

---

#### 7.4 Quản lý Người dùng & Phân quyền (Users & Roles)

- **`PATCH /api/v1/admin/users/{user_id}/status`**: Khóa hoặc Mở khóa tài khoản người dùng.
  - Request Body:
    ```json
    {
      "status": "locked"
    }
    ```
  - *Quy tắc bảo mật*: Admin **không thể tự khóa tài khoản của chính mình** (Sẽ trả về `409 Conflict` - `CANNOT_LOCK_SELF`).
- **`PATCH /api/v1/admin/users/{user_id}/role`**: Đổi quyền người dùng (`customer`, `staff`, `admin`).
  - Request Body:
    ```json
    {
      "role": "staff"
    }
    ```
  - *Quy tắc bảo mật*: Admin **không thể tự hạ quyền của chính mình** (Trả về `409 Conflict` - `CANNOT_CHANGE_SELF_ROLE`).

---

#### 7.5 Báo cáo tổng quan (Reports)

- **`GET /api/v1/admin/reports/summary`**: Lấy số liệu thống kê KPI nhanh của sàn.
- **Phản hồi thành công (200 OK)**:
```json
{
  "success": true,
  "message": "Admin - báo cáo tổng hợp thành công.",
  "data": {
    "total_users": 150,
    "total_orders": 320,
    "total_revenue": 156400000
  }
}
```

---

### MODULE 8: ĐO HIỆU NĂNG & BENCHMARK (REDIS VS DATABASE)

- **Method**: `GET`
- **Path**: `/v1/benchmark/redis-vs-db`
- **Auth**: Không (Public Benchmark)
- **Mục đích**: Thực hiện 100 truy vấn liên tục trực tiếp vào MySQL Database và so sánh đối chiếu với 100 lượt truy vấn qua Redis Cache để đo lường độ trễ (latency) và hệ số tăng tốc.
- **Phản hồi thành công (200 OK)**:
```json
{
  "iterations": 100,
  "results": {
    "database_time_ms": 48.25,
    "redis_time_ms": 5.12,
    "difference_ms": 43.13,
    "speedup_factor": "9.42x"
  },
  "message": "Benchmark completed successfully."
}
```

---

## 5. QUY TẮC NGHIỆP VỤ & LUỒNG XỬ LÝ CỐT LÕI

### 5.1 Luồng đặt hàng & Khóa tồn kho (Inventory Pessimistic Locking)

Nhằm ngăn chặn hiện tượng **Overselling** (Bán quá số lượng tồn kho khi nhiều khách cùng đặt một mặt hàng tại cùng một thời điểm), hàm `placeOrder()` trong `CheckoutService` áp dụng quy trình:

```
[Khách gửi request Đặt hàng]
           │
           ▼
   DB::beginTransaction()
           │
           ▼
Lặp qua từng item trong đơn
           │
           ▼
Inventory::where('product_id', $id)->lockForUpdate()->first()
           │
           ├──── [quantity_on_hand < quantity] ──► Rollback & Trả về 409 OUT_OF_STOCK
           │
           ▼
Trừ tồn kho: quantity_on_hand -= quantity
Tạo bản ghi Order & OrderItem
Xóa CartItem của user
           │
           ▼
     DB::commit()
           │
           ▼
[Đặt hàng thành công: Trả về HTTP 201 Created]
```

---

### 5.2 Ma trận vòng đời trạng thái đơn hàng (Order State Transition)

Trạng thái đơn hàng chỉ được phép di chuyển theo một chiều logic được kiểm soát chặt chẽ:

```
       ┌───────────┐
       │  Pending  │◄── (Mới tạo, chờ xác nhận)
       └─────┬─────┘
             │
     ┌───────┴────────┐
     ▼                ▼
┌───────────┐   ┌───────────┐
│ Confirmed │   │ Cancelled │◄── (Hủy đơn & Hoàn kho)
└─────┬─────┘   └───────────┘
      │
      ▼
┌───────────┐
│ Shipping  │
└─────┬─────┘
      │
      ▼
┌───────────┐
│ Delivered │
└─────┬─────┘
      │
      ▼
┌───────────┐
│ Returned  │ (Khách trả hàng / hoàn tiền)
└───────────┘
```

- **Quy tắc cho Khách hàng**: Khách chỉ được tự hủy đơn khi đơn ở trạng thái `pending` hoặc `confirmed`.
- **Quy tắc cho Quản trị viên**: Khi Admin chuyển đơn sang `cancelled`, toàn bộ tồn kho đã trừ trước đó sẽ được tự động hoàn trả lại cho sản phẩm tương ứng.

---

### 5.3 Quy trình thanh toán VNPay & Hủy đơn tự động quá hạn

1. Khách tạo đơn hàng với `payment_method = 'vnpay'`.
2. Máy chủ đưa Job `CancelUnpaidOrderJob` vào hàng đợi (Queue) với độ trễ (delay) **10 phút**.
3. Khách gọi API `/payment/vnpay/{order_code}` để nhận `payment_url` và tiến hành quét mã QR/thẻ ngân hàng.
4. **Nếu khách thanh toán thành công trong 10 phút**: Cổng VNPay gửi tín hiệu IPN đến `/api/v1/payment/vnpay/ipn`. Backend kiểm tra chữ ký và cập nhật `payment_status = 'paid'`. Khi Job 10 phút thức giấc, nhận thấy đơn đã `paid` nên bỏ qua.
5. **Nếu khách không thanh toán sau 10 phút**: `CancelUnpaidOrderJob` thực thi, kiểm tra thấy đơn vẫn `unpaid` -> Đơn tự động bị chuyển sang `cancelled` và hoàn lại số lượng tồn kho.

---

### 5.4 Chiến lược Caching Redis cho Catalog

- **Mô hình**: Cache-Aside Pattern.
- **Dữ liệu áp dụng**:
  - `catalog:categories:active` (Danh mục đang hoạt động)
  - `catalog:products:hot` (Sản phẩm nổi bật)
- **Thời gian sống (TTL)**: 3600 giây (60 phút).
- **Cơ chế Invalidation**: Khi Quản trị viên tạo, sửa hoặc xóa bất kỳ danh mục hay sản phẩm nào trong trang Admin, hệ thống tự động phát tín hiệu xóa key cache liên quan trên Redis để đảm bảo tính nhất quán dữ liệu.

---

## 6. HƯỚNG DẪN KIỂM THỬ NHANH (CURL QUICKSTART)

### Bước 1: Đăng nhập lấy Token
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"customer@freshfarm.vn","password":"password"}'
```
*Sao chép chuỗi `data.token` từ response để dùng cho các bước tiếp theo.*

### Bước 2: Xem chi tiết giỏ hàng
```bash
curl -X GET http://localhost:8000/api/v1/cart \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Accept: application/json"
```

### Bước 3: Thêm sản phẩm vào giỏ
```bash
curl -X POST http://localhost:8000/api/v1/cart/items \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1, "quantity": 2}'
```

### Bước 4: Xem trước kết quả đặt hàng (Checkout Preview)
```bash
curl -X POST http://localhost:8000/api/v1/checkout/preview \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"address_id": 1, "payment_method": "cod", "coupon_code": "XANH10"}'
```

### Bước 5: Đặt hàng chính thức
```bash
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -H "Idempotency-Key: 9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d" \
  -d '{
    "address_id": 1,
    "payment_method": "cod",
    "coupon_code": "XANH10",
    "note": "Giao sáng mai giúp mình"
  }'
```

---

*Tài liệu được biên soạn và bảo trì bởi Đội ngũ Kỹ thuật Backend Fresh Farm.*
