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
 * Message bubbles.
 *
 * @param list<array<string,mixed>> $messages
 */
function render_messages(array $messages): string
{
    $html = '';
    foreach ($messages as $message) {
        $agent = ($message['role'] ?? '') === 'agent';
        $at = (int) ($message['at'] ?? 0);
        $html .= '<div class="bubble' . ($agent ? ' agent' : '') . '">'
            . '<span class="role">' . ($agent ? 'Agent' : 'Visitor') . '</span>'
            . '<div>' . e((string) ($message['text'] ?? '')) . '</div>'
            . '<time datetime="' . e(date('c', $at)) . '">' . e(date('M j, H:i', $at)) . '</time>'
            . '</div>';
    }
    return $html;
}
