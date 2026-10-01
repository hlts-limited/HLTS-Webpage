<?php
/**
 * Plain-text email.
 *
 * Drivers (config "mail.driver"):
 *   smtp  send through a real mailbox; best for delivery (recommended live)
 *   mail  PHP mail(); works on most cPanel hosts but often lands in spam
 *   log   write to storage/logs/mail.log; used for local preview
 */

function send_mail(string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    $cfg = config('mail');
    $clean = fn (string $v) => trim(str_replace(["\r", "\n"], '', $v));

    $to = $clean($to);
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    if ($replyTo !== null && !filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $replyTo = null;
    }

    $from = $clean($cfg['from']);
    $fromName = $clean($cfg['from_name']);
    $encodedSubject = '=?UTF-8?B?' . base64_encode($clean($subject)) . '?=';
    $encodedName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';

    $headers = [
        'From' => "$encodedName <$from>",
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=UTF-8',
        'Content-Transfer-Encoding' => 'base64',
        'X-Mailer' => 'HLTS website',
    ];
    if ($replyTo) {
        $headers['Reply-To'] = $clean($replyTo);
    }

    $encodedBody = chunk_split(base64_encode($body));

    switch ($cfg['driver']) {
        case 'log':
            $entry = sprintf("---- %s\nTo: %s\nSubject: %s\nReply-To: %s\n\n%s\n\n", now(), $to, $subject, $replyTo ?? '-', $body);
            return @file_put_contents(STORAGE_DIR . '/logs/mail.log', $entry, FILE_APPEND | LOCK_EX) !== false;

        case 'smtp':
            try {
                return smtp_send($cfg, $from, $to, $encodedSubject, $headers, $encodedBody);
            } catch (Throwable $e) {
                log_event('SMTP_FAILED', $e->getMessage());
                return false;
            }

        default:
            $headerLines = [];
            foreach ($headers as $name => $value) {
                $headerLines[] = "$name: $value";
            }
            return mail($to, $encodedSubject, $encodedBody, implode("\r\n", $headerLines), '-f' . $from);
    }
}

function smtp_send(array $cfg, string $from, string $to, string $subject, array $headers, string $body): bool
{
    $secure = $cfg['smtp_secure'] === 'ssl' ? 'ssl://' : 'tcp://';
    $socket = @stream_socket_client($secure . $cfg['smtp_host'] . ':' . (int) $cfg['smtp_port'], $errno, $errstr, 15);
    if (!$socket) {
        throw new RuntimeException("Connect failed: $errstr ($errno)");
    }
    stream_set_timeout($socket, 15);

    $read = function () use ($socket): string {
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (strlen($line) < 4 || $line[3] === ' ') {
                break;
            }
        }
        return $response;
    };
    $command = function (string $cmd, array $expect) use ($socket, $read): string {
        fwrite($socket, $cmd . "\r\n");
        $response = $read();
        if (!in_array((int) substr($response, 0, 3), $expect, true)) {
            throw new RuntimeException('SMTP error after "' . strtok($cmd, ' ') . '": ' . trim($response));
        }
        return $response;
    };

    $read();
    $host = parse_url((string) config('site_url'), PHP_URL_HOST) ?: 'localhost';
    $command("EHLO $host", [250]);

    if ($cfg['smtp_secure'] === 'tls') {
        $command('STARTTLS', [220]);
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('STARTTLS failed');
        }
        $command("EHLO $host", [250]);
    }

    if ($cfg['smtp_user'] !== '') {
        $command('AUTH LOGIN', [334]);
        $command(base64_encode($cfg['smtp_user']), [334]);
        $command(base64_encode($cfg['smtp_pass']), [235]);
    }

    $command("MAIL FROM:<$from>", [250]);
    $command("RCPT TO:<$to>", [250, 251]);
    $command('DATA', [354]);

    $message = 'Date: ' . date('r') . "\n";
    $message .= "To: <$to>\n";
    $message .= "Subject: $subject\n";
    $message .= 'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $host . ">\n";
    foreach ($headers as $name => $value) {
        $message .= "$name: $value\n";
    }
    $message .= "\n" . $body;
    // Normalise line endings, escape lines that start with a dot, then use CRLF.
    $message = str_replace(["\r\n", "\r"], "\n", $message);
    $message = str_replace("\n.", "\n..", $message);
    $message = str_replace("\n", "\r\n", $message);

    fwrite($socket, $message . "\r\n.\r\n");
    $response = $read();
    if ((int) substr($response, 0, 3) !== 250) {
        throw new RuntimeException('Message rejected: ' . trim($response));
    }

    fwrite($socket, "QUIT\r\n");
    fclose($socket);
    return true;
}
