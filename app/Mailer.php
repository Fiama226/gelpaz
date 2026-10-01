<?php
declare(strict_types=1);

namespace App;

use RuntimeException;

/**
 * Envoi d'e-mails : SMTP (TLS/SSL, AUTH LOGIN), fonction mail() de PHP, ou journal (tests).
 */
final class Mailer
{
    private string $lastError = '';

    public function error(): string
    {
        return $this->lastError;
    }

    /**
     * @param string|array $to Destinataire(s)
     */
    public function send(string|array $to, string $subject, string $html, ?string $replyTo = null, ?string $replyName = null): bool
    {
        $cfg = (array) config('mail');
        $to = array_values(array_filter((array) $to, static fn($a) => filter_var($a, FILTER_VALIDATE_EMAIL)));
        if (!$to) {
            $this->lastError = 'Aucun destinataire valide.';
            return false;
        }
        $fromEmail = (string) ($cfg['from_email'] ?: 'no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $fromName = (string) ($cfg['from_name'] ?: 'GELPAZ IMMO');
        $boundary = 'gz_' . bin2hex(random_bytes(12));
        $text = trim(html_entity_decode(strip_tags(preg_replace(['#<br\s*/?>#i', '#</(p|div|tr|h[1-6]|li)>#i'], "\n", $html) ?? ''), ENT_QUOTES, 'UTF-8'));
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        $headers = [
            'Date' => date('r'),
            'From' => $this->address($fromEmail, $fromName),
            'Message-ID' => '<' . bin2hex(random_bytes(10)) . '@' . (explode('@', $fromEmail)[1] ?? 'gelpaz.com') . '>',
            'MIME-Version' => '1.0',
            'Content-Type' => 'multipart/alternative; boundary="' . $boundary . '"',
            'X-Mailer' => 'GELPAZ-IMMO',
        ];
        if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $headers['Reply-To'] = $this->address($replyTo, (string) $replyName);
        }
        $body = "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($text))
            . "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($html))
            . "--$boundary--\r\n";
        $encodedSubject = $this->encodeHeader($subject);

        try {
            return match ($cfg['driver'] ?? 'mail') {
                'smtp' => $this->sendSmtp($cfg, $fromEmail, $to, $encodedSubject, $headers, $body),
                'log' => $this->log($to, $subject, $headers, $text),
                default => $this->sendMail($to, $encodedSubject, $headers, $body, $fromEmail),
            };
        } catch (\Throwable $e) {
            $this->lastError = $e->getMessage();
            error_log('[GELPAZ Mailer] ' . $e->getMessage());
            return false;
        }
    }

    private function address(string $email, string $name = ''): string
    {
        $email = str_replace(["\r", "\n"], '', $email);
        return $name !== '' ? $this->encodeHeader($name) . ' <' . $email . '>' : $email;
    }

    private function encodeHeader(string $value): string
    {
        $value = str_replace(["\r", "\n"], ' ', $value);
        return preg_match('/[^\x20-\x7E]/', $value) ? '=?UTF-8?B?' . base64_encode($value) . '?=' : $value;
    }

    private function sendMail(array $to, string $subject, array $headers, string $body, string $from): bool
    {
        $h = '';
        foreach ($headers as $k => $v) {
            if ($k !== 'Date') {
                $h .= "$k: $v\r\n";
            }
        }
        $ok = @mail(implode(', ', $to), $subject, $body, rtrim($h), '-f' . $from);
        if (!$ok) {
            $this->lastError = 'La fonction mail() a échoué (vérifiez la configuration de votre hébergement).';
        }
        return $ok;
    }

    private function log(array $to, string $subject, array $headers, string $text): bool
    {
        $line = str_repeat('=', 70) . "\n" . date('Y-m-d H:i:s') . "\nTo: " . implode(', ', $to) . "\nSubject: $subject\n"
            . 'Reply-To: ' . ($headers['Reply-To'] ?? '-') . "\n\n" . $text . "\n";
        @file_put_contents(ROOT . '/storage/logs/mail.log', $line, FILE_APPEND | LOCK_EX);
        return true;
    }

    private function sendSmtp(array $cfg, string $from, array $to, string $subject, array $headers, string $body): bool
    {
        $host = (string) $cfg['host'];
        $port = (int) ($cfg['port'] ?: 587);
        $enc = strtolower((string) ($cfg['encryption'] ?? ''));
        $remote = ($enc === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
        $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'SNI_enabled' => true]]);
        $fp = @stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) {
            throw new RuntimeException("Connexion SMTP impossible ($host:$port) : $errstr");
        }
        stream_set_timeout($fp, 20);
        $read = static function () use ($fp): string {
            $data = '';
            while (($line = fgets($fp, 515)) !== false) {
                $data .= $line;
                if (isset($line[3]) && $line[3] === ' ') {
                    break;
                }
            }
            return $data;
        };
        $cmd = static function (string $command, array $expect) use ($fp, $read): string {
            if ($command !== '') {
                fwrite($fp, $command . "\r\n");
            }
            $resp = $read();
            $code = (int) substr($resp, 0, 3);
            if (!in_array($code, $expect, true)) {
                $shown = str_starts_with($command, 'AUTH') || strlen($command) > 60 ? '[commande]' : $command;
                throw new RuntimeException("Erreur SMTP sur « $shown » : " . trim($resp));
            }
            return $resp;
        };
        $ehloHost = preg_replace('/[^a-z0-9.\-]/i', '', (string) ($_SERVER['SERVER_NAME'] ?? 'localhost')) ?: 'localhost';
        $cmd('', [220]);
        $cmd('EHLO ' . $ehloHost, [250]);
        if ($enc === 'tls') {
            $cmd('STARTTLS', [220]);
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) {
                throw new RuntimeException('Impossible d’activer le chiffrement TLS.');
            }
            $cmd('EHLO ' . $ehloHost, [250]);
        }
        if (!empty($cfg['username'])) {
            $cmd('AUTH LOGIN', [334]);
            $cmd(base64_encode((string) $cfg['username']), [334]);
            $cmd(base64_encode((string) $cfg['password']), [235]);
        }
        $cmd('MAIL FROM:<' . $from . '>', [250]);
        foreach ($to as $rcpt) {
            $cmd('RCPT TO:<' . $rcpt . '>', [250, 251]);
        }
        $cmd('DATA', [354]);
        $h = 'To: ' . implode(', ', $to) . "\r\nSubject: $subject\r\n";
        foreach ($headers as $k => $v) {
            $h .= "$k: $v\r\n";
        }
        $data = $h . "\r\n" . $body;
        $data = preg_replace('/^\./m', '..', str_replace(["\r\n", "\r"], "\n", $data)) ?? $data;
        $data = str_replace("\n", "\r\n", $data);
        fwrite($fp, $data . "\r\n.\r\n");
        $cmd('', [250]);
        fwrite($fp, "QUIT\r\n");
        fclose($fp);
        return true;
    }

    /** Gabarit HTML aux couleurs de GELPAZ pour les notifications. */
    public static function template(string $title, array $rows, string $intro = '', string $message = ''): string
    {
        $tr = '';
        foreach ($rows as $label => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $tr .= '<tr><td style="padding:10px 14px;border-bottom:1px solid #E2E8F0;color:#5B6576;font-size:13px;width:38%;vertical-align:top">'
                . htmlspecialchars((string) $label) . '</td><td style="padding:10px 14px;border-bottom:1px solid #E2E8F0;color:#0B1B33;font-size:14px;font-weight:600">'
                . nl2br(htmlspecialchars((string) $value)) . '</td></tr>';
        }
        $msg = $message !== '' ? '<div style="margin-top:20px;padding:18px;background:#F3F7FC;border-left:4px solid #C9A24A;border-radius:8px;color:#0B1B33;font-size:14px;line-height:1.6">'
            . nl2br(htmlspecialchars($message)) . '</div>' : '';
        $site = htmlspecialchars((string) setting('site_name', 'GELPAZ IMMO'));
        return '<!doctype html><html lang="fr"><body style="margin:0;background:#EEF3F9;font-family:Arial,Helvetica,sans-serif">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px"><tr><td align="center">'
            . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border-radius:14px;overflow:hidden">'
            . '<tr><td style="background:#0A1B33;padding:22px 28px"><span style="color:#fff;font-size:22px;font-weight:800;letter-spacing:2px">GELPAZ</span>'
            . '<span style="color:#E6C77A;font-size:12px;letter-spacing:3px;margin-left:8px">IMMO-SA</span></td></tr>'
            . '<tr><td style="height:4px;background:#1C80F0"></td></tr>'
            . '<tr><td style="padding:28px">'
            . '<h1 style="margin:0 0 10px;color:#0B1B33;font-size:20px">' . htmlspecialchars($title) . '</h1>'
            . ($intro !== '' ? '<p style="margin:0 0 18px;color:#5B6576;font-size:14px;line-height:1.6">' . $intro . '</p>' : '')
            . ($tr !== '' ? '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E2E8F0;border-radius:10px;border-collapse:separate;overflow:hidden">' . $tr . '</table>' : '')
            . $msg . '</td></tr>'
            . '<tr><td style="padding:18px 28px;background:#F3F7FC;color:#5B6576;font-size:12px;line-height:1.6">'
            . $site . ' · ' . htmlspecialchars((string) setting('address')) . '<br>' . htmlspecialchars((string) setting('phone')) . ' · '
            . htmlspecialchars((string) setting('email')) . '</td></tr></table></td></tr></table></body></html>';
    }
}
