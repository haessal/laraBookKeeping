<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ImportingCompleted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The URL of the import source.
     *
     * @var string
     */
    public $sourceUrl;

    /**
     * The status of the importing.
     *
     * @var int
     */
    public $status;

    /**
     * The error message of the importing.
     *
     * @var string|null
     */
    public $errorMessage;

    /**
     * The result of the importing.
     *
     * @var string
     */
    public $result;

    /**
     * Create a new message instance.
     *
     * @param  string  $sourceUrl
     * @param  int  $status
     * @param  string|null  $errorMessage
     * @param  string  $result
     */
    public function __construct($sourceUrl, $status, $errorMessage, $result)
    {
        $this->sourceUrl = $sourceUrl;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        $this->result = $result;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'BookKeeping: Importing books has been completed',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.importing',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->result, 'result.json')
                    ->withMime('application/json'),
        ];
    }
}
