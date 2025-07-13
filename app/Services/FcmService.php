<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive; // We don't use Drive, but Client needs a dummy Service class
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected string $projectId;
    protected array $credentials;
    protected Client $client;

    public function __construct()
    {
        // Load credentials from the secure JSON file
        $credentialsPath = storage_path('app/'.env('FIREBASE_CREDENTIALS', 'firebase_credentials.json'));

        if (!file_exists($credentialsPath)) {
            throw new \Exception("Firebase credentials file not found at: {$credentialsPath}");
        }

        $this->credentials = json_decode(file_get_contents($credentialsPath), true);

        if (!isset($this->credentials['project_id'], $this->credentials['private_key'], $this->credentials['client_email'])) {
            throw new \Exception("Invalid Firebase credentials file. Missing project_id, private_key, or client_email.");
        }

        $this->projectId = $this->credentials['project_id'];

        // Initialize Google Client for authentication
        $this->client = new Client();
        $this->client->setAuthConfig($credentialsPath);
        // Set the scope for Firebase Cloud Messaging (FCM)
        $this->client->setScopes(['https://www.googleapis.com/auth/firebase.messaging']);
        // The Google Client internally handles JWT generation and access token fetching/refreshing
    }

    protected function getAccessToken(): string
    {
        // Trigger the client to get an access token.
        // It will use the service account credentials and scopes.
        // This method also handles token refreshing automatically.
        $this->client->fetchAccessTokenWithAssertion();
        return $this->client->getAccessToken()['access_token'];
    }

    /**
     * Send an FCM notification message.
     *
     * @param string $to A device token or a topic name.
     * @param string $type 'token' or 'topic'.
     * @param string $title The title of the notification.
     * @param string $body The body of the notification.
     * @param array $data Optional custom data payload.
     * @return array Response from FCM API.
     * @throws \Exception If the FCM API call fails.
     */
    public function send(string $to, string $type, string $title, string $body, array $data = []): array
    {
        if (!in_array($type, ['token', 'topic'])) {
            throw new \InvalidArgumentException("Type must be 'token' or 'topic'.");
        }

        $accessToken = $this->getAccessToken();
        $fcmEndpoint = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $messagePayload = [
            'message' => [
                $type => $to, // 'token' or 'topic'
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($fcmEndpoint, $messagePayload);

            $response->throw(); // Throws an exception for 4xx or 5xx responses

            return $response->json();

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("FCM API Request Failed: " . $e->getMessage(), [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'payload' => $messagePayload,
            ]);
            throw new \Exception("Failed to send FCM message: " . $e->response->json('error.message', 'Unknown error'));
        } catch (\Exception $e) {
            Log::error("FCM Service Error: " . $e->getMessage(), [
                'exception' => $e,
                'payload' => $messagePayload,
            ]);
            throw new \Exception("An error occurred in FcmService: " . $e->getMessage());
        }
    }
}
