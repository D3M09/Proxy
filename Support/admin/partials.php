<?php

declare(strict_types=1);

/**
 * HTML fragments shared by admin/index.php and admin/api.php so the live
 * refresh renders exactly what a full page load renders.
 */

/**
 * Sidebar rows.
 *
 * @param list<array<string,mixed>> $rows
 */
function render_list_items(array $rows, ?string $selectedId = null): string
{
    if ($rows === []) {
        return '<p class="empty">No conversations yet.</p>';
    }

    $html = '';
    foreach ($rows as $row) {
        $id = (string) $row['id'];
        $status = (string) $row['status'];
        $classes = ['conv'];
        if ($id === $selectedId) {
            $classes[] = 'active';
        }
        if ($status === 'closed') {
            $classes[] = 'closed';
        }

        $html .= '<a class="' . implode(' ', $classes) . '" href="?id=' . e($id) . '" data-id="' . e($id) . '">'
            . '<span class="conv-top">'
            . '<span class="conv-name">' . e((string) $row['name'] !== '' ? (string) $row['name'] : 'Anonymous') . '</span>'
            . '<span class="conv-time">' . e(time_ago((int) $row['updated_at'])) . '</span>'
            . '</span>'
            . '<span class="conv-preview">' . e((string) $row['preview']) . '</span>'
            . '<span class="conv-meta">'
            . '<span class="tag ' . e($status) . '">' . e($status === 'open' ? 'Open' : 'Closed') . '</span>'
            . '<span class="tag">' . (int) $row['message_count'] . ' msg</span>'
            . ((int) $row['agent_unread'] > 0
                ? '<span class="tag count">' . (int) $row['agent_unread'] . ' new</span>'
                : '')
            . '</span>'
            . '</a>';
    }

    return $html;
}

/**
 * Message bubbles, including any attachment.
 *
 * @param list<array<string,mixed>> $messages
 */
function render_messages(array $messages, string $chatId = '', int $offset = 0): string
{
    $html = '';
    foreach (array_values($messages) as $i => $message) {
        $agent = ($message['role'] ?? '') === 'agent';
        $at = (int) ($message['at'] ?? 0);
        $text = (string) ($message['text'] ?? '');
        $file = is_array($message['file'] ?? null) ? $message['file'] : null;

        // data-index is the absolute message index so the console can place
        // the Seen marker from the presence feed (incremental polls pass $offset).
        $html .= '<div class="bubble' . ($agent ? ' agent' : '') . '" data-index="' . ($offset + $i) . '">'
            . '<span class="role">' . ($agent ? 'Agent' : 'Visitor') . '</span>';

        if ($file !== null) {
            $html .= render_attachment($chatId, $file);
        }
        if ($text !== '') {
            $html .= '<div>' . e($text) . '</div>';
        }

        $html .= '<time datetime="' . e(date('c', $at)) . '">' . e(date('M j, H:i', $at)) . '</time>'
            . '</div>';
    }
    return $html;
}

/**
 * An image or video block. media.php is one directory up from the console.
 *
 * @param array<string,mixed> $file
 */
function render_attachment(string $chatId, array $file): string
{
    $token = (string) ($file['token'] ?? '');
    if (preg_match('/^[a-f0-9]{32}$/', $token) !== 1) {
        return '';
    }
    if (preg_match('/^[a-f0-9]{32}$/', $chatId) !== 1) {
        return '';
    }

    $url = '../media.php?id=' . rawurlencode($chatId) . '&f=' . rawurlencode($token);
    $mime = (string) ($file['mime'] ?? '');
    $name = (string) ($file['name'] ?? '');
    $label = $name !== '' ? $name : 'attachment';
    $meta = e($label) . ' · ' . e(format_bytes((int) ($file['size'] ?? 0)));

    if (media_is_video($mime)) {
        return '<div class="media"><video class="media-video" controls preload="metadata" src="' . e($url) . '"></video>'
            . '<a class="media-name" href="' . e($url) . '" target="_blank" rel="noreferrer noopener">' . $meta . '</a></div>';
    }

    return '<div class="media"><a href="' . e($url) . '" target="_blank" rel="noreferrer noopener">'
        . '<img class="media-img" src="' . e($url) . '" alt="' . e($label) . '" loading="lazy"></a>'
        . '<span class="media-name">' . $meta . '</span></div>';
}
