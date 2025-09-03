<?php

use App\Models\Message;
use App\Models\Pengguna;

beforeEach(function () {
    // Create test users
    $this->user1 = Pengguna::factory()->create();
    $this->user2 = Pengguna::factory()->create();

    // Get JWT token for authentication
    $this->token = auth('api')->login($this->user1);
});

test('conversations are sorted by unread messages first', function () {
    // Create user3 for additional conversation
    $user3 = Pengguna::factory()->create();

    // Create conversations with different unread statuses

    // Conversation 1: Old message, already read (should be last)
    Message::create([
        'id_pengirim' => $this->user2->id_pengguna,
        'id_penerima' => $this->user1->id_pengguna,
        'tema' => 'Old Read Message',
        'isi_pesan' => 'This is an old read message',
        'status_terbaca' => true,
        'waktu_kirim' => now()->subDays(2),
        'waktu_baca' => now()->subDays(2)->addHours(1),
    ]);

    // Conversation 2: Recent unread message (should be first)
    Message::create([
        'id_pengirim' => $user3->id_pengguna,
        'id_penerima' => $this->user1->id_pengguna,
        'tema' => 'Recent Unread',
        'isi_pesan' => 'This is a recent unread message',
        'status_terbaca' => false,
        'waktu_kirim' => now()->subHours(1),
    ]);

    // Conversation 3: Older unread message (should be second)
    $anotherUser = Pengguna::factory()->create();
    Message::create([
        'id_pengirim' => $anotherUser->id_pengguna,
        'id_penerima' => $this->user1->id_pengguna,
        'tema' => 'Older Unread',
        'isi_pesan' => 'This is an older unread message',
        'status_terbaca' => false,
        'waktu_kirim' => now()->subHours(3),
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson('/api/messages');

    $response->assertStatus(200);

    $conversations = $response->json('data');

    // Verify sorting: unread messages first, then by time
    expect($conversations)->toHaveCount(3);

    // First conversation should have unread count > 0 (recent unread)
    expect($conversations[0]['unread_count'])->toBeGreaterThan(0);
    expect($conversations[0]['partner']['id_pengguna'])->toBe($user3->id_pengguna);

    // Second conversation should have unread count > 0 (older unread)
    expect($conversations[1]['unread_count'])->toBeGreaterThan(0);
    expect($conversations[1]['partner']['id_pengguna'])->toBe($anotherUser->id_pengguna);

    // Third conversation should have unread count = 0 (read message)
    expect($conversations[2]['unread_count'])->toBe(0);
    expect($conversations[2]['partner']['id_pengguna'])->toBe($this->user2->id_pengguna);
});

test('can get conversations', function () {
    // Create some messages
    Message::create([
        'id_pengirim' => $this->user2->id_pengguna,
        'id_penerima' => $this->user1->id_pengguna,
        'tema' => 'Test Message',
        'isi_pesan' => 'Hello, this is a test message',
        'waktu_kirim' => now(),
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson('/api/messages');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'partner' => ['id_pengguna', 'nm_pengguna'],
                    'last_message' => [
                        'id_message',
                        'tema',
                        'isi_pesan',
                        'waktu_kirim',
                        'status_terbaca',
                        'is_from_me'
                    ],
                    'unread_count'
                ]
            ],
            'pagination'
        ]);
});

test('can send message', function () {
    $messageData = [
        'id_penerima' => $this->user2->id_pengguna,
        'tema' => 'Test Subject',
        'isi_pesan' => 'This is a test message content',
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/messages', $messageData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id_message',
                'pengirim',
                'penerima',
                'tema',
                'isi_pesan',
                'id_replay',
                'pesan_direply',
                'status_terbaca',
                'waktu_kirim',
                'waktu_baca'
            ]
        ]);

    expect('messages')->toBeInDatabase([
        'id_pengirim' => $this->user1->id_pengguna,
        'id_penerima' => $this->user2->id_pengguna,
        'tema' => 'Test Subject',
        'isi_pesan' => 'This is a test message content',
    ]);
});

test('can reply to message', function () {
    // Create original message
    $originalMessage = Message::create([
        'id_pengirim' => $this->user2->id_pengguna,
        'id_penerima' => $this->user1->id_pengguna,
        'tema' => 'Original Message',
        'isi_pesan' => 'This is the original message',
        'waktu_kirim' => now(),
    ]);

    $replyData = [
        'id_penerima' => $this->user2->id_pengguna,
        'tema' => 'Re: Original Message',
        'isi_pesan' => 'This is a reply to your message',
        'id_replay' => $originalMessage->id_message,
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/messages', $replyData);

    $response->assertStatus(201);

    expect('messages')->toBeInDatabase([
        'id_pengirim' => $this->user1->id_pengguna,
        'id_penerima' => $this->user2->id_pengguna,
        'id_replay' => $originalMessage->id_message,
    ]);
});

test('cannot send message to self', function () {
    $messageData = [
        'id_penerima' => $this->user1->id_pengguna, // Same as sender
        'tema' => 'Test Subject',
        'isi_pesan' => 'This should fail',
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/messages', $messageData);

    $response->assertStatus(422)
        ->assertJson([
            'status' => 'error',
            'message' => 'Cannot send message to yourself'
        ]);
});

test('can get messages with specific user', function () {
    // Create some messages between users
    Message::create([
        'id_pengirim' => $this->user1->id_pengguna,
        'id_penerima' => $this->user2->id_pengguna,
        'tema' => 'Message 1',
        'isi_pesan' => 'Hello from user 1',
        'waktu_kirim' => now()->subHours(2),
    ]);

    Message::create([
        'id_pengirim' => $this->user2->id_pengguna,
        'id_penerima' => $this->user1->id_pengguna,
        'tema' => 'Message 2',
        'isi_pesan' => 'Hello from user 2',
        'waktu_kirim' => now()->subHours(1),
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson('/api/messages/' . $this->user2->id_pengguna);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id_message',
                    'pengirim',
                    'penerima',
                    'tema',
                    'isi_pesan',
                    'id_replay',
                    'pesan_direply',
                    'status_terbaca',
                    'waktu_kirim',
                    'waktu_baca',
                    'is_from_me'
                ]
            ],
            'partner',
            'pagination'
        ]);
});

test('can mark message as read', function () {
    $message = Message::create([
        'id_pengirim' => $this->user2->id_pengguna,
        'id_penerima' => $this->user1->id_pengguna,
        'tema' => 'Unread Message',
        'isi_pesan' => 'This message should be marked as read',
        'status_terbaca' => false,
        'waktu_kirim' => now(),
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->patchJson('/api/messages/' . $message->id_message . '/read');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Message marked as read'
        ]);

    expect('messages')->toBeInDatabase([
        'id_message' => $message->id_message,
        'status_terbaca' => true,
    ]);
});

test('can delete message', function () {
    $message = Message::create([
        'id_pengirim' => $this->user1->id_pengguna,
        'id_penerima' => $this->user2->id_pengguna,
        'tema' => 'Message to Delete',
        'isi_pesan' => 'This message will be deleted',
        'waktu_kirim' => now(),
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->deleteJson('/api/messages/' . $message->id_message);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Message deleted successfully'
        ]);

    expect('messages')->toBeInDatabase([
        'id_message' => $message->id_message,
        'status_hapus_pengirim' => true,
    ]);
});

test('can get unread count', function () {
    // Create some unread messages
    Message::create([
        'id_pengirim' => $this->user2->id_pengguna,
        'id_penerima' => $this->user1->id_pengguna,
        'tema' => 'Unread 1',
        'isi_pesan' => 'Unread message 1',
        'status_terbaca' => false,
        'waktu_kirim' => now(),
    ]);

    Message::create([
        'id_pengirim' => $this->user2->id_pengguna,
        'id_penerima' => $this->user1->id_pengguna,
        'tema' => 'Unread 2',
        'isi_pesan' => 'Unread message 2',
        'status_terbaca' => false,
        'waktu_kirim' => now(),
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson('/api/messages/unread-count');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data' => [
                'unread_count' => 2
            ]
        ]);
});

test('can search messages', function () {
    // Create some messages to search
    Message::create([
        'id_pengirim' => $this->user1->id_pengguna,
        'id_penerima' => $this->user2->id_pengguna,
        'tema' => 'Project Discussion',
        'isi_pesan' => 'Let\'s discuss about the project deadline',
        'waktu_kirim' => now(),
    ]);

    Message::create([
        'id_pengirim' => $this->user2->id_pengguna,
        'id_penerima' => $this->user1->id_pengguna,
        'tema' => 'Meeting Schedule',
        'isi_pesan' => 'Project meeting will be tomorrow',
        'waktu_kirim' => now(),
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson('/api/messages/search?query=project');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id_message',
                    'pengirim',
                    'penerima',
                    'tema',
                    'isi_pesan',
                    'waktu_kirim',
                    'is_from_me'
                ]
            ],
            'search_query',
            'pagination'
        ]);
});

test('requires authentication', function () {
    $response = $this->getJson('/api/messages');

    $response->assertStatus(401);
});

test('validates message input', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/messages', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['id_penerima', 'isi_pesan']);
});
