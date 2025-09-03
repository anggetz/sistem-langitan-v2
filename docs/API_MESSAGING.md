# API Messaging Documentation

Sistem API Messaging untuk komunikasi antar pengguna dengan fitur reply, status terbaca, dan tema pesan.

## Authentication

Semua endpoint memerlukan authentication dengan JWT token melalui header:

```
Authorization: Bearer {token}
```

## Endpoints

### 1. Get All Conversations

**GET** `/api/messages`

Mendapatkan daftar semua percakapan pengguna.

**Sorting Logic:**

1. Percakapan dengan pesan unread muncul terlebih dahulu, diurutkan berdasarkan waktu pesan unread terakhir (terbaru dulu)
2. Percakapan tanpa pesan unread muncul setelahnya, diurutkan berdasarkan waktu aktivitas terakhir (terbaru dulu)

**Parameters:**

-   `per_page` (optional): Jumlah data per halaman (default: 10)
-   `page` (optional): Nomor halaman (default: 1)

**Response:**

```json
{
    "status": "success",
    "data": [
        {
            "partner": {
                "id_pengguna": 2,
                "nm_pengguna": "John Doe"
            },
            "last_message": {
                "id_message": 15,
                "tema": "Diskusi Tugas",
                "isi_pesan": "Baik, terima kasih atas informasinya",
                "waktu_kirim": "2025-08-26T14:30:00.000000Z",
                "status_terbaca": true,
                "is_from_me": false
            },
            "unread_count": 2
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 5,
        "last_page": 1
    }
}
```

### 2. Get Messages with Specific User

**GET** `/api/messages/{partnerId}`

Mendapatkan pesan-pesan dengan pengguna tertentu.

**Parameters:**

-   `partnerId`: ID pengguna partner
-   `per_page` (optional): Jumlah data per halaman (default: 20)
-   `page` (optional): Nomor halaman (default: 1)

**Response:**

```json
{
    "status": "success",
    "data": [
        {
            "id_message": 15,
            "pengirim": {
                "id_pengguna": 2,
                "nm_pengguna": "John Doe"
            },
            "penerima": {
                "id_pengguna": 1,
                "nm_pengguna": "Jane Smith"
            },
            "tema": "Diskusi Tugas",
            "isi_pesan": "Baik, terima kasih atas informasinya",
            "id_replay": 14,
            "pesan_direply": {
                "id_message": 14,
                "isi_pesan": "Jangan lupa deadline tugasnya besok ya",
                "pengirim": "Jane Smith",
                "waktu_kirim": "2025-08-26T13:30:00.000000Z"
            },
            "status_terbaca": true,
            "waktu_kirim": "2025-08-26T14:30:00.000000Z",
            "waktu_baca": "2025-08-26T14:35:00.000000Z",
            "is_from_me": false
        }
    ],
    "partner": {
        "id_pengguna": 2,
        "nm_pengguna": "John Doe"
    },
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 25,
        "last_page": 2
    }
}
```

### 3. Send New Message

**POST** `/api/messages`

Mengirim pesan baru.

**Request Body:**

```json
{
    "id_penerima": 2,
    "tema": "Diskusi Tugas",
    "isi_pesan": "Halo, saya mau bertanya tentang tugas yang kemarin",
    "id_replay": null
}
```

**Validation Rules:**

-   `id_penerima`: required, harus ada di tabel pengguna, tidak boleh sama dengan pengirim
-   `tema`: optional, maksimal 255 karakter
-   `isi_pesan`: required, maksimal 5000 karakter
-   `id_replay`: optional, harus ada di tabel messages

**Response:**

```json
{
    "status": "success",
    "message": "Message sent successfully",
    "data": {
        "id_message": 16,
        "pengirim": {
            "id_pengguna": 1,
            "nm_pengguna": "Jane Smith"
        },
        "penerima": {
            "id_pengguna": 2,
            "nm_pengguna": "John Doe"
        },
        "tema": "Diskusi Tugas",
        "isi_pesan": "Halo, saya mau bertanya tentang tugas yang kemarin",
        "id_replay": null,
        "pesan_direply": null,
        "status_terbaca": false,
        "waktu_kirim": "2025-08-26T15:00:00.000000Z",
        "waktu_baca": null
    }
}
```

### 4. Reply to Message

**POST** `/api/messages`

Mengirim reply ke pesan tertentu.

**Request Body:**

```json
{
    "id_penerima": 1,
    "tema": "Re: Diskusi Tugas",
    "isi_pesan": "Tugasnya tentang analisis sistem informasi, deadline besok jam 23:59",
    "id_replay": 16
}
```

### 5. Mark Message as Read

**PATCH** `/api/messages/{messageId}/read`

Menandai pesan sebagai sudah dibaca.

**Response:**

```json
{
    "status": "success",
    "message": "Message marked as read"
}
```

### 6. Delete Message

**DELETE** `/api/messages/{messageId}`

Menghapus pesan (soft delete).

**Response:**

```json
{
    "status": "success",
    "message": "Message deleted successfully"
}
```

### 7. Get Unread Messages Count

**GET** `/api/messages/unread-count`

Mendapatkan jumlah pesan yang belum dibaca.

**Response:**

```json
{
    "status": "success",
    "data": {
        "unread_count": 5
    }
}
```

### 8. Search Messages

**GET** `/api/messages/search`

Mencari pesan berdasarkan tema atau isi pesan.

**Parameters:**

-   `query`: kata kunci pencarian (minimal 3 karakter)
-   `per_page` (optional): Jumlah data per halaman (default: 20)
-   `page` (optional): Nomor halaman (default: 1)

**Response:**

```json
{
    "status": "success",
    "data": [
        {
            "id_message": 15,
            "pengirim": {
                "id_pengguna": 2,
                "nm_pengguna": "John Doe"
            },
            "penerima": {
                "id_pengguna": 1,
                "nm_pengguna": "Jane Smith"
            },
            "tema": "Diskusi Tugas",
            "isi_pesan": "Baik, terima kasih atas informasinya",
            "waktu_kirim": "2025-08-26T14:30:00.000000Z",
            "is_from_me": false
        }
    ],
    "search_query": "tugas",
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 3,
        "last_page": 1
    }
}
```

## Error Responses

### Validation Error (422)

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "id_penerima": ["Penerima pesan harus diisi."],
        "isi_pesan": ["Isi pesan harus diisi."]
    }
}
```

### Not Found (404)

```json
{
    "status": "error",
    "message": "Message not found"
}
```

### Unauthorized (401)

```json
{
    "message": "Unauthenticated",
    "status": false,
    "data": null
}
```

### Server Error (500)

```json
{
    "status": "error",
    "message": "Failed to send message",
    "error": "Database connection error"
}
```

## Features

### 1. Tema Pesan

Setiap pesan dapat memiliki tema/subjek yang memudahkan kategorisasi.

### 2. Reply System

-   Pesan dapat di-reply dengan menggunakan `id_replay`
-   Pesan reply akan menampilkan informasi pesan asli yang direply
-   Validasi akses: hanya bisa reply pesan yang melibatkan user tersebut

### 3. Status Terbaca

-   Otomatis menandai pesan sebagai terbaca ketika membuka percakapan
-   Tracking waktu baca
-   Counter jumlah pesan belum dibaca

### 4. Soft Delete

-   Pesan tidak dihapus permanen
-   Setiap user bisa hapus pesan dari sisi mereka
-   Flag terpisah untuk pengirim dan penerima

### 5. Search Functionality

-   Pencarian berdasarkan tema dan isi pesan
-   Minimal 3 karakter
-   Mendukung paginasi

### 6. Conversation Grouping

-   Mengelompokkan pesan berdasarkan partner percakapan
-   Menampilkan pesan terakhir dan jumlah unread
-   **Smart Sorting**: Percakapan dengan pesan unread diprioritaskan dan diurutkan berdasarkan waktu pesan unread terakhir
-   Percakapan tanpa unread message diurutkan berdasarkan waktu aktivitas terakhir

### 7. Priority-Based Sorting

-   **Unread First**: Percakapan dengan pesan belum dibaca selalu muncul di atas
-   **Latest Unread Priority**: Pesan unread terbaru menentukan urutan di antara percakapan dengan unread
-   **Activity-Based**: Percakapan tanpa unread diurutkan berdasarkan aktivitas terakhir
-   **Real-time Updates**: Urutan berubah dinamis saat ada pesan baru atau status baca berubah

## Database Schema

### Table: messages

```sql
- id_message (Primary Key)
- id_pengirim (Foreign Key to pengguna.id_pengguna)
- id_penerima (Foreign Key to pengguna.id_pengguna)
- tema (varchar 255, nullable)
- isi_pesan (text)
- id_replay (Foreign Key to messages.id_message, nullable)
- status_terbaca (boolean, default false)
- waktu_kirim (timestamp, default current)
- waktu_baca (timestamp, nullable)
- status_hapus_pengirim (boolean, default false)
- status_hapus_penerima (boolean, default false)
- created_at (timestamp)
- updated_at (timestamp)
```

## Usage Examples

### Mengirim Pesan Baru

```bash
curl -X POST http://your-domain/api/messages \
  -H "Authorization: Bearer your-jwt-token" \
  -H "Content-Type: application/json" \
  -d '{
    "id_penerima": 2,
    "tema": "Diskusi Project",
    "isi_pesan": "Halo, bisa diskusi tentang project kita?"
  }'
```

### Reply Pesan

```bash
curl -X POST http://your-domain/api/messages \
  -H "Authorization: Bearer your-jwt-token" \
  -H "Content-Type: application/json" \
  -d '{
    "id_penerima": 1,
    "tema": "Re: Diskusi Project",
    "isi_pesan": "Bisa, bagaimana kalau kita meeting besok?",
    "id_replay": 16
  }'
```

### Mendapatkan Pesan dengan User Tertentu

```bash
curl -X GET "http://your-domain/api/messages/2?per_page=20&page=1" \
  -H "Authorization: Bearer your-jwt-token"
```

### Mencari Pesan

```bash
curl -X GET "http://your-domain/api/messages/search?query=project&per_page=10" \
  -H "Authorization: Bearer your-jwt-token"
```
