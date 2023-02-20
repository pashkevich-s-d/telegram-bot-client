<?php

namespace Pashkevichsd\TelegramBotClient\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class TelegramBotClient
{
    private const API = 'https://api.telegram.org/bot';
    private const SEND_MESSAGE = 'sendMessage';
    private const GET_UPDATES = 'getUpdates';
    private const SEND_DOCUMENT = 'sendDocument';

    private string $token;
    private HttpClientInterface $httpClient;

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->httpClient = HttpClient::create();
    }

    public function getUpdates(): array
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            $this->getUri(self::GET_UPDATES)
        );

        return $response->toArray();
    }

    public function sendMessage(
        int $chatId,
        string $text
    ): array {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            $this->getUri(self::SEND_MESSAGE) . '?chat_id=' . $chatId . '&text=' . $text
        );

        return $response->toArray();
    }

    public function sendDocument(string $chatId, string $filePath)
    {
        $formFields = [
            'chat_id' => $chatId,
            'document' => DataPart::fromPath($filePath),
        ];
        $formData = new FormDataPart($formFields);

        $response = $this->httpClient->request(
            Request::METHOD_POST,
            $this->getUri(self::SEND_DOCUMENT),
            [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ]
        );

        return $response->toArray();
    }

    private function getUri(string $telegramMethod): string
    {
        return self::API . $this->token . '/' . $telegramMethod;
    }
}
