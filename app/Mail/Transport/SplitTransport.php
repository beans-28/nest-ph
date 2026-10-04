<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;

/**
 * Sends each email through one of two mailers depending on who it's for.
 *
 * If every recipient is on the "real" list, the email goes through the real
 * mailer (Gmail) and lands in their actual inbox. Everything else goes to the
 * sandbox mailer (Mailtrap), so dummy accounts never get real emails.
 * Putting "*" in the list sends everything for real.
 */
class SplitTransport implements TransportInterface
{
    /** @param  string[]  $realRecipients  lowercase email addresses, or ["*"] */
    public function __construct(
        private TransportInterface $real,
        private TransportInterface $sandbox,
        private array $realRecipients,
    ) {}

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        $envelope ??= Envelope::create($message);

        return $this->goesToRealInbox($envelope)
            ? $this->real->send($message, $envelope)
            : $this->sandbox->send($message, $envelope);
    }

    private function goesToRealInbox(Envelope $envelope): bool
    {
        if (in_array('*', $this->realRecipients, true)) {
            return true;
        }

        foreach ($envelope->getRecipients() as $address) {
            if (! in_array(strtolower($address->getAddress()), $this->realRecipients, true)) {
                return false;
            }
        }

        return true;
    }

    public function __toString(): string
    {
        return 'split('.$this->real.', '.$this->sandbox.')';
    }
}
