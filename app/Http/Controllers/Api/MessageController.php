<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Get all conversations for current user
     */
    public function index(Request $request)
    {
        try {
            $userId = auth('api')->user()->id_pengguna;
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Get all messages for current user to build conversations
            $allMessages = Message::where(function ($query) use ($userId) {
                $query->where('id_pengirim', $userId)
                    ->where('status_hapus_pengirim', false);
            })->orWhere(function ($query) use ($userId) {
                $query->where('id_penerima', $userId)
                    ->where('status_hapus_penerima', false);
            })
                ->with(['pengirim', 'penerima'])
                ->orderBy('waktu_kirim', 'desc')
                ->get();

            // Group by conversation partner and get latest unread message time
            $groupedConversations = [];
            foreach ($allMessages as $message) {
                $partnerId = ($message->id_pengirim == $userId) ? $message->id_penerima : $message->id_pengirim;

                if (!isset($groupedConversations[$partnerId])) {
                    $partner = ($message->id_pengirim == $userId) ? $message->penerima : $message->pengirim;

                    // Get latest unread message time for this conversation
                    $latestUnreadMessage = Message::where('id_pengirim', $partnerId)
                        ->where('id_penerima', $userId)
                        ->where('status_terbaca', false)
                        ->where('status_hapus_penerima', false)
                        ->orderBy('waktu_kirim', 'desc')
                        ->first();

                    $unreadCount = Message::where('id_pengirim', $partnerId)
                        ->where('id_penerima', $userId)
                        ->where('status_terbaca', false)
                        ->where('status_hapus_penerima', false)
                        ->count();

                    $groupedConversations[$partnerId] = [
                        'partner' => [
                            'id_pengguna' => $partner->id_pengguna,
                            'nm_pengguna' => $partner->nm_pengguna,
                        ],
                        'last_message' => [
                            'id_message' => $message->id_message,
                            'tema' => $message->tema,
                            'isi_pesan' => $message->isi_pesan,
                            'waktu_kirim' => $message->waktu_kirim,
                            'status_terbaca' => $message->status_terbaca,
                            'is_from_me' => $message->id_pengirim == $userId
                        ],
                        'unread_count' => $unreadCount,
                        'latest_unread_time' => $latestUnreadMessage ? $latestUnreadMessage->waktu_kirim : null,
                        'last_activity_time' => $message->waktu_kirim
                    ];
                }
            }

            // Sort conversations: 
            // 1. Conversations with unread messages first (sorted by latest unread time)
            // 2. Then conversations without unread messages (sorted by last activity time)
            $sortedConversations = collect($groupedConversations)->sort(function ($a, $b) {
                // If both have unread messages, sort by latest unread time (desc)
                if ($a['unread_count'] > 0 && $b['unread_count'] > 0) {
                    return $b['latest_unread_time'] <=> $a['latest_unread_time'];
                }

                // If only one has unread messages, prioritize it
                if ($a['unread_count'] > 0 && $b['unread_count'] == 0) {
                    return -1; // $a comes first
                }
                if ($a['unread_count'] == 0 && $b['unread_count'] > 0) {
                    return 1; // $b comes first
                }

                // If both have no unread messages, sort by last activity time (desc)
                return $b['last_activity_time'] <=> $a['last_activity_time'];
            })->values();

            // Remove temporary sorting fields from response
            $finalConversations = $sortedConversations->map(function ($conversation) {
                unset($conversation['latest_unread_time']);
                unset($conversation['last_activity_time']);
                return $conversation;
            });

            // Apply pagination manually since we sorted after database query
            $total = $finalConversations->count();
            $offset = ($page - 1) * $perPage;
            $paginatedConversations = $finalConversations->slice($offset, $perPage)->values();

            return response()->json([
                'status' => 'success',
                'data' => $paginatedConversations,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get conversations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get messages with specific user
     */
    public function show(Request $request, $partnerId)
    {
        try {
            $userId = auth('api')->user()->id_pengguna;
            $perPage = $request->get('per_page', 20);
            $page = $request->get('page', 1);

            // Validate partner exists
            $partner = Pengguna::find($partnerId);
            if (!$partner) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Partner not found'
                ], 404);
            }

            // Get messages between users
            $messages = Message::betweenUsers($userId, $partnerId)
                ->where(function ($query) use ($userId) {
                    $query->where(function ($q) use ($userId) {
                        $q->where('id_pengirim', $userId)
                            ->where('status_hapus_pengirim', false);
                    })->orWhere(function ($q) use ($userId) {
                        $q->where('id_penerima', $userId)
                            ->where('status_hapus_penerima', false);
                    });
                })
                ->with(['pengirim', 'penerima', 'pesanDireply.pengirim'])
                ->orderBy('waktu_kirim', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            // Mark received messages as read
            Message::where('id_pengirim', $partnerId)
                ->where('id_penerima', $userId)
                ->where('status_terbaca', false)
                ->update([
                    'status_terbaca' => true,
                    'waktu_baca' => now()
                ]);

            $formattedMessages = $messages->map(function ($message) use ($userId) {
                return [
                    'id_message' => $message->id_message,
                    'pengirim' => [
                        'id_pengguna' => $message->pengirim->id_pengguna,
                        'nm_pengguna' => $message->pengirim->nm_pengguna,
                    ],
                    'penerima' => [
                        'id_pengguna' => $message->penerima->id_pengguna,
                        'nm_pengguna' => $message->penerima->nm_pengguna,
                    ],
                    'tema' => $message->tema,
                    'isi_pesan' => $message->isi_pesan,
                    'id_replay' => $message->id_replay,
                    'pesan_direply' => $message->pesanDireply ? [
                        'id_message' => $message->pesanDireply->id_message,
                        'isi_pesan' => $message->pesanDireply->isi_pesan,
                        'pengirim' => $message->pesanDireply->pengirim->nm_pengguna,
                        'waktu_kirim' => $message->pesanDireply->waktu_kirim
                    ] : null,
                    'status_terbaca' => $message->status_terbaca,
                    'waktu_kirim' => $message->waktu_kirim,
                    'waktu_baca' => $message->waktu_baca,
                    'is_from_me' => $message->id_pengirim == $userId
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $formattedMessages,
                'partner' => [
                    'id_pengguna' => $partner->id_pengguna,
                    'nm_pengguna' => $partner->nm_pengguna,
                ],
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                    'last_page' => $messages->lastPage()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send new message
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_penerima' => 'required|exists:pengguna,id_pengguna',
                'tema' => 'nullable|string|max:255',
                'isi_pesan' => 'required|string',
                'id_replay' => 'nullable|exists:messages,id_message'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = auth('api')->user()->id_pengguna;

            // Validate that user is not sending message to themselves
            if ($userId == $request->id_penerima) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot send message to yourself'
                ], 422);
            }

            // If replying, validate the original message exists and user has access to it
            if ($request->id_replay) {
                $originalMessage = Message::where('id_message', $request->id_replay)
                    ->where(function ($query) use ($userId) {
                        $query->where('id_pengirim', $userId)
                            ->orWhere('id_penerima', $userId);
                    })->first();

                if (!$originalMessage) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Original message not found or access denied'
                    ], 404);
                }
            }

            $message = Message::create([
                'id_pengirim' => $userId,
                'id_penerima' => $request->id_penerima,
                'tema' => $request->tema,
                'isi_pesan' => $request->isi_pesan,
                'id_replay' => $request->id_replay,
                'waktu_kirim' => now()
            ]);

            $message->load(['pengirim', 'penerima', 'pesanDireply.pengirim']);

            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully',
                'data' => [
                    'id_message' => $message->id_message,
                    'pengirim' => [
                        'id_pengguna' => $message->pengirim->id_pengguna,
                        'nm_pengguna' => $message->pengirim->nm_pengguna,
                    ],
                    'penerima' => [
                        'id_pengguna' => $message->penerima->id_pengguna,
                        'nm_pengguna' => $message->penerima->nm_pengguna,
                    ],
                    'tema' => $message->tema,
                    'isi_pesan' => $message->isi_pesan,
                    'id_replay' => $message->id_replay,
                    'pesan_direply' => $message->pesanDireply ? [
                        'id_message' => $message->pesanDireply->id_message,
                        'isi_pesan' => $message->pesanDireply->isi_pesan,
                        'pengirim' => $message->pesanDireply->pengirim->nm_pengguna,
                        'waktu_kirim' => $message->pesanDireply->waktu_kirim
                    ] : null,
                    'status_terbaca' => $message->status_terbaca,
                    'waktu_kirim' => $message->waktu_kirim,
                    'waktu_baca' => $message->waktu_baca
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Request $request, $messageId)
    {
        try {
            $userId = auth('api')->user()->id_pengguna;

            $message = Message::where('id_message', $messageId)
                ->where('id_penerima', $userId)
                ->first();

            if (!$message) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Message not found or you are not the recipient'
                ], 404);
            }

            $message->markAsRead();

            return response()->json([
                'status' => 'success',
                'message' => 'Message marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark message as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete message for current user
     */
    public function destroy($messageId)
    {
        try {
            $userId = auth('api')->user()->id_pengguna;

            $message = Message::where('id_message', $messageId)
                ->where(function ($query) use ($userId) {
                    $query->where('id_pengirim', $userId)
                        ->orWhere('id_penerima', $userId);
                })->first();

            if (!$message) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Message not found'
                ], 404);
            }

            // Soft delete based on user role
            if ($message->id_pengirim == $userId) {
                $message->deleteForSender();
            } else {
                $message->deleteForReceiver();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Message deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread messages count
     */
    public function unreadCount()
    {
        try {
            $userId = auth('api')->user()->id_pengguna;

            $count = Message::where('id_penerima', $userId)
                ->where('status_terbaca', false)
                ->where('status_hapus_penerima', false)
                ->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'unread_count' => $count
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get unread count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search messages
     */
    public function search(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:3',
                'per_page' => 'nullable|integer|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = auth('api')->user()->id_pengguna;
            $searchQuery = $request->query;
            $perPage = $request->get('per_page', 20);
            $page = $request->get('page', 1);

            $messages = Message::where(function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    $q->where('id_pengirim', $userId)
                        ->where('status_hapus_pengirim', false);
                })->orWhere(function ($q) use ($userId) {
                    $q->where('id_penerima', $userId)
                        ->where('status_hapus_penerima', false);
                });
            })
                ->where(function ($query) use ($searchQuery) {
                    $query->where('tema', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('isi_pesan', 'LIKE', "%{$searchQuery}%");
                })
                ->with(['pengirim', 'penerima'])
                ->orderBy('waktu_kirim', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $formattedMessages = $messages->map(function ($message) use ($userId) {
                return [
                    'id_message' => $message->id_message,
                    'pengirim' => [
                        'id_pengguna' => $message->pengirim->id_pengguna,
                        'nm_pengguna' => $message->pengirim->nm_pengguna,
                    ],
                    'penerima' => [
                        'id_pengguna' => $message->penerima->id_pengguna,
                        'nm_pengguna' => $message->penerima->nm_pengguna,
                    ],
                    'tema' => $message->tema,
                    'isi_pesan' => $message->isi_pesan,
                    'waktu_kirim' => $message->waktu_kirim,
                    'is_from_me' => $message->id_pengirim == $userId
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $formattedMessages,
                'search_query' => $searchQuery,
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                    'last_page' => $messages->lastPage()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to search messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
