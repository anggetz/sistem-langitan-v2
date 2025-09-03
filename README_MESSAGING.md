# Modul API Messaging

Modul API messaging yang lengkap untuk sistem komunikasi antar pengguna dengan fitur-fitur canggih.

## ✨ Fitur Utama

### 🔐 Authentication & Authorization

-   **JWT Authentication**: Menggunakan JWT token untuk autentikasi API
-   **User Validation**: Validasi akses pengguna untuk setiap operasi
-   **Role-based Access**: Akses berdasarkan peran pengguna

### 💬 Core Messaging Features

-   **Send Messages**: Kirim pesan antar pengguna
-   **Reply System**: Sistem reply dengan referensi pesan asli
-   **Subject/Theme**: Setiap pesan dapat memiliki tema/subjek
-   **Message History**: Riwayat lengkap percakapan

### 📖 Read Status Management

-   **Read Status**: Status terbaca/belum terbaca
-   **Auto Mark Read**: Otomatis tandai sebagai terbaca saat membuka percakapan
-   **Read Timestamp**: Waktu kapan pesan dibaca
-   **Unread Counter**: Hitung jumlah pesan belum terbaca

### 🗑️ Soft Delete System

-   **User-specific Delete**: Setiap user bisa hapus pesan dari sisi mereka
-   **Separate Flags**: Flag terpisah untuk pengirim dan penerima
-   **Data Preservation**: Data tidak dihapus permanen

### 🔍 Advanced Search

-   **Content Search**: Pencarian berdasarkan isi pesan dan tema
-   **Pagination Support**: Mendukung paginasi untuk hasil pencarian
-   **Minimum Query Length**: Validasi minimal 3 karakter untuk pencarian

### 👥 Conversation Management

-   **Conversation Grouping**: Kelompokkan pesan berdasarkan partner
-   **Last Message Preview**: Preview pesan terakhir di setiap percakapan
-   **Unread Count per Conversation**: Jumlah pesan belum terbaca per percakapan

## 📁 Struktur File

```
app/
├── Models/
│   └── Message.php                    # Model utama untuk messages
├── Http/
│   ├── Controllers/Api/
│   │   └── MessageController.php      # Controller API messaging
│   ├── Requests/
│   │   └── StoreMessageRequest.php    # Validation request untuk store
│   └── Resources/
│       └── MessageResource.php        # API resource untuk response formatting
├── Services/                          # (Optional untuk logic bisnis kompleks)
└── Traits/                           # (Menggunakan Blameable trait)

database/
├── migrations/
│   └── 2025_08_26_105212_create_messages_table.php
└── seeders/
    └── MessageSeeder.php             # Sample data untuk testing

tests/
└── Feature/
    └── MessageApiTest.php            # Unit test untuk API

docs/
└── API_MESSAGING.md                  # Dokumentasi lengkap API

routes/
└── api.php                          # Route definitions
```

## 🗄️ Database Schema

### Table: `messages`

```sql
- id_message (PK, Auto Increment)
- id_pengirim (FK to pengguna.id_pengguna)
- id_penerima (FK to pengguna.id_pengguna)
- tema (VARCHAR 255, Nullable)
- isi_pesan (TEXT)
- id_replay (FK to messages.id_message, Nullable)
- status_terbaca (BOOLEAN, Default: false)
- waktu_kirim (TIMESTAMP, Default: CURRENT_TIMESTAMP)
- waktu_baca (TIMESTAMP, Nullable)
- status_hapus_pengirim (BOOLEAN, Default: false)
- status_hapus_penerima (BOOLEAN, Default: false)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Indexes untuk Performance

-   `(id_pengirim, id_penerima)` - Untuk query percakapan
-   `(id_penerima, status_terbaca)` - Untuk unread messages
-   `waktu_kirim` - Untuk sorting chronological

## 🛣️ API Endpoints

| Method   | Endpoint                         | Description                     |
| -------- | -------------------------------- | ------------------------------- |
| `GET`    | `/api/messages`                  | Get all conversations           |
| `GET`    | `/api/messages/{partnerId}`      | Get messages with specific user |
| `POST`   | `/api/messages`                  | Send new message                |
| `PATCH`  | `/api/messages/{messageId}/read` | Mark message as read            |
| `DELETE` | `/api/messages/{messageId}`      | Delete message (soft delete)    |
| `GET`    | `/api/messages/unread-count`     | Get unread messages count       |
| `GET`    | `/api/messages/search`           | Search messages                 |

## 🔧 Installation & Setup

### 1. Run Migration

```bash
php artisan migrate
```

### 2. (Optional) Seed Sample Data

```bash
php artisan db:seed --class=MessageSeeder
```

### 3. Test API

```bash
php artisan test tests/Feature/MessageApiTest.php
```

## 📝 Usage Examples

### Kirim Pesan Baru

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

### Get Conversation

```bash
curl -X GET "http://your-domain/api/messages/2?per_page=20&page=1" \
  -H "Authorization: Bearer your-jwt-token"
```

## 🧪 Testing

Modul ini dilengkapi dengan comprehensive test cases:

-   ✅ Authentication testing
-   ✅ Message sending validation
-   ✅ Reply functionality
-   ✅ Read status management
-   ✅ Soft delete functionality
-   ✅ Search functionality
-   ✅ Pagination testing
-   ✅ **Priority-based sorting verification**
-   ✅ **Unread message prioritization**
-   ✅ Error handling
-   ✅ Search functionality
-   ✅ Pagination testing
-   ✅ Error handling

## 🎯 Business Logic Features

### 1. Smart Conversation Threading

-   Otomatis kelompokkan pesan berdasarkan partner
-   Tampilkan preview pesan terakhir
-   Hitung unread messages per conversation
-   **Priority-Based Sorting**: Percakapan dengan pesan unread diprioritaskan
-   **Smart Ordering**: Unread messages diurutkan berdasarkan waktu pesan unread terakhir
-   **Activity-Based Fallback**: Percakapan tanpa unread diurutkan berdasarkan aktivitas terakhir

### 2. Reply Chain Management

-   Validasi akses untuk reply (hanya bisa reply pesan yang melibatkan user)
-   Tampilkan context pesan yang direply
-   Support nested reply (reply of reply)

### 3. Privacy & Security

-   User hanya bisa melihat pesan yang mereka terlibat
-   Soft delete per user (tidak mengganggu partner)
-   Validasi tidak bisa kirim pesan ke diri sendiri

### 4. Performance Optimization

-   Database indexing untuk query cepat
-   Pagination untuk handling data besar
-   Efficient query dengan proper relationships

## 🔍 Advanced Features

### 1. Search Capabilities

-   Search berdasarkan tema dan isi pesan
-   Support partial matching dengan LIKE query
-   Minimal 3 karakter untuk performance

### 2. Status Management

-   Real-time read status
-   Timestamp tracking untuk waktu baca
-   Bulk mark as read saat buka conversation

### 3. Data Integrity

-   Foreign key constraints
-   Validation rules untuk semua input
-   Error handling yang comprehensive

## 📚 Documentation

Dokumentasi lengkap tersedia di:

-   **API Documentation**: `docs/API_MESSAGING.md`
-   **Code Comments**: Inline documentation di setiap method
-   **Test Cases**: Comprehensive test coverage

## 🚀 Future Enhancements

Fitur yang bisa dikembangkan selanjutnya:

-   **File Attachments**: Dukungan untuk lampiran file
-   **Message Reactions**: Emoji reactions
-   **Group Messaging**: Pesan grup
-   **Message Encryption**: Enkripsi end-to-end
-   **Push Notifications**: Notifikasi real-time
-   **Message Templates**: Template pesan yang sering digunakan
-   **Message Scheduling**: Jadwal kirim pesan
-   **Message Analytics**: Statistik penggunaan messaging

## 💡 Best Practices Implemented

1. **RESTful API Design**: Mengikuti standar REST API
2. **Proper HTTP Status Codes**: Response code yang sesuai
3. **Input Validation**: Validasi komprehensif untuk semua input
4. **Error Handling**: Error handling yang user-friendly
5. **Database Optimization**: Index dan query optimization
6. **Security**: JWT authentication dan authorization
7. **Code Documentation**: Komentar dan dokumentasi yang lengkap
8. **Testing**: Unit test yang comprehensive
9. **Modular Design**: Struktur kode yang modular dan maintainable
10. **Performance**: Pagination dan efficient queries

---

**Developed with ❤️ using Laravel Framework**
