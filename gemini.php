<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function askGemini($message)
{
    $apiKey = getenv("GEMINI_API_KEY");

if (empty($apiKey)) {
    die("❌ GEMINI_API_KEY not found");
}

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

    $systemPrompt = <<<'PROMPT'
You are MindCare AI, a compassionate mental wellness companion.

Your role is to emotionally support users like a trusted friend while maintaining healthy boundaries.

Personality:
- Warm, calm, kind and encouraging.
- Sound natural and human.
- Never mention that you are an AI, chatbot or language model.
- Speak like someone who genuinely cares.

Response Style:
- Keep every response between 30 and 70 words.
- Use simple and conversational language.
- Avoid long explanations.
- Do not use bullet points or numbered lists.
- Always acknowledge the user's feelings first.
- Give one practical suggestion.
- End with one short follow-up question if appropriate.
- Use at most one emoji naturally.

Language Rules:
- Reply in the same language as the user.
- If the user writes in Hindi, reply in Hindi.
- If the user writes in Hinglish, reply in Hinglish.
- If the user writes in English, reply in English.
- Never translate unless the user asks.

Safety:
- If a user mentions self-harm or suicide, respond with empathy, encourage them to contact someone they trust or local emergency services immediately, and avoid encouraging harmful actions.
PROMPT;

    $data = [
        "systemInstruction" => [
            "parts" => [
                [
                    "text" => $systemPrompt
                ]
            ]
        ],
        "contents" => [
            [
                "parts" => [
                    [
                        "text" => $message
                    ]
                ]
            ]
        ]
    ];

    $options = [
        "http" => [
            "header" => "Content-Type: application/json",
            "method" => "POST",
            "content" => json_encode($data),
            "ignore_errors" => true
        ]
    ];
    $context = stream_context_create($options);

$result = file_get_contents($url, false, $context);

if ($result === false) {

    $error = error_get_last();

    return [
        "error" => [
            "message" => $error['message'] ?? "Unknown HTTP error"
        ]
    ];
}

$response = json_decode($result, true);

if (json_last_error() !== JSON_ERROR_NONE) {

    return [
        "error" => [
            "message" => "Invalid JSON: " . json_last_error_msg(),
            "raw" => $result
        ]
    ];
}

return $response;

file_put_contents(__DIR__ . "/http_headers.txt", print_r($http_response_header ?? [], true));
file_put_contents(__DIR__ . "/gemini_raw.txt", $result === false ? "FALSE" : $result);



if ($result === false) {
    return [
        "candidates" => [
            [
                "content" => [
                    "parts" => [
                        [
                            "text" => "I'm sorry, something went wrong. Please try again."
                        ]
                    ]
                ]
            ]
        ]
    ];
}

file_put_contents(
    __DIR__ . "/gemini_log.txt",
    $result
);

return json_decode($result, true);
}


function askGeminiSummary($conversation)
{
    $prompt = "
Summarize the following therapy conversation.

Return exactly in this format:

Overall Mood:
Risk Level:
Summary:
Recommendations:

Conversation:

$conversation
";

    return askGemini($prompt);
}