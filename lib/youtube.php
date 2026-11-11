<?php
/**
 * The HLTS YouTube channel on the website. Reads the public feed YouTube publishes for every
 * channel (no API key or Google account needed), keeps it for an hour in storage/cache, and keeps
 * showing the last good list if YouTube can't be reached. The feed holds the latest 15 videos.
 */

const YOUTUBE_CACHE_SECONDS = 3600;
const YOUTUBE_RETRY_SECONDS = 600;

/** The latest videos: [['id', 'title', 'published', 'description', 'thumb', 'url'], ...]. */
function youtube_videos(int $limit = 6): array
{
    $channel = (string) config('youtube_channel_id', '');
    if (!preg_match('/^UC[\w-]{22}$/', $channel)) {
        return [];
    }
    $file = STORAGE_DIR . '/cache/youtube-' . $channel . '.json';
    $cached = is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
    $cached = is_array($cached) ? $cached : null;
    if ($cached && ($cached['at'] ?? 0) > time() - YOUTUBE_CACHE_SECONDS) {
        return array_slice($cached['videos'] ?? [], 0, $limit);
    }

    $videos = youtube_fetch($channel);
    $at = time();
    if ($videos === null) {
        // YouTube unreachable: keep the last good list and try again in about 10 minutes.
        $videos = $cached['videos'] ?? [];
        $at = time() - YOUTUBE_CACHE_SECONDS + YOUTUBE_RETRY_SECONDS;
    }
    if (!is_dir(dirname($file))) {
        @mkdir(dirname($file), 0755, true);
    }
    @file_put_contents($file, json_encode(['at' => $at, 'videos' => $videos]), LOCK_EX);
    return array_slice($videos, 0, $limit);
}

/** The channel feed as a list of videos, or null if it couldn't be read. */
function youtube_fetch(string $channel): ?array
{
    $ch = curl_init('https://www.youtube.com/feeds/videos.xml?channel_id=' . rawurlencode($channel));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_USERAGENT => 'HLTS-Website/1.0',
    ]);
    $xml = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    if (!is_string($xml) || $status !== 200) {
        return null;
    }
    $feed = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA);
    if (!$feed) {
        return null;
    }
    $videos = [];
    foreach ($feed->entry as $entry) {
        $id = (string) $entry->children('http://www.youtube.com/xml/schemas/2015')->videoId;
        if (!preg_match('/^[\w-]{11}$/', $id)) {
            continue;
        }
        $media = $entry->children('http://search.yahoo.com/mrss/')->group;
        $videos[] = [
            'id' => $id,
            'title' => mb_substr(trim((string) $entry->title), 0, 200),
            'published' => substr((string) $entry->published, 0, 10),
            'description' => mb_substr(trim((string) ($media ? $media->description : '')), 0, 300),
            'thumb' => youtube_thumb($id),
            'url' => 'https://www.youtube.com/watch?v=' . $id,
        ];
    }
    return $videos;
}

/** The video ID from any YouTube link (watch, youtu.be, shorts, embed, live), or null. */
function youtube_id(string $url): ?string
{
    $url = trim($url);
    if (preg_match('~^(?:https?://)?(?:www\.|m\.)?(?:youtube\.com/(?:watch\?(?:.*&)?v=|shorts/|embed/|live/)|youtu\.be/)([\w-]{11})(?:[?&#/].*)?$~i', $url, $m)) {
        return $m[1];
    }
    return null;
}

function youtube_thumb(string $id): string
{
    return 'https://i.ytimg.com/vi/' . rawurlencode($id) . '/hqdefault.jpg';
}

/**
 * A YouTube video shown as its thumbnail with a play button. The player (YouTube's privacy-enhanced
 * version) only loads when someone presses play, so pages stay light and nobody is tracked first.
 */
function youtube_player(string $id, string $title): string
{
    return '<div class="yt-player">'
        . '<button class="yt-player__button" type="button" data-youtube="' . h($id) . '" data-title="' . h($title) . '" aria-label="Play video: ' . h($title) . '">'
        . '<img src="' . h(youtube_thumb($id)) . '" alt="" loading="lazy">'
        . '<span class="yt-player__play" aria-hidden="true">' . icon('play-fill') . '</span>'
        . '</button></div>';
}
