<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmation extends Notification
{
    use Queueable;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Formatta data e orario in modo pulito
        $dataFormattata = \Carbon\Carbon::parse($this->booking->date)->format('d/m/Y');
        $orarioFormattato = \Carbon\Carbon::parse($this->booking->timeSlot->time)->format('H:i');
        
        $message = (new MailMessage)
                    ->subject('⚽ Prenotazione Campo Confermata - Field Booking System')
                    ->greeting('Ciao ' . $this->booking->customer_name . '!')
                    ->line('**La tua prenotazione del campo è stata confermata!**')
                    ->line('')
                    ->line('## 📋 Dettagli Prenotazione')
                    ->line('📅 **Data:** ' . $dataFormattata)
                    ->line('🕒 **Orario:** ' . $orarioFormattato)
                    ->line('🏟️ **Campo:** ' . $this->booking->table->name)
                    ->line('👥 **Giocatori:** ' . $this->booking->guests_count)
                    ->line('📱 **Telefono:** ' . $this->booking->customer_phone);
        
        // Aggiungi note aggiuntive solo se presenti
        if (!empty($this->booking->special_requests)) {
            $message->line('')
                    ->line('📝 **Note Aggiuntive:**')
                    ->line($this->booking->special_requests);
        }
        
        return $message->line('')
                    ->line('## 📍 Dove trovarci')
                    ->line('**Centro Sportivo**')
                    ->line('📍 Via dello Sport 1, Napoli')
                    ->line('📞 081-123456')
                    ->line('')
                    ->line('---')
                    ->line('💡 **Ti consigliamo di arrivare 10 minuti prima per il check-in!**')
                    ->line('')
                    ->line('Grazie per averci scelto! Buona partita!')
                    ->salutation('A presto!')
                    ->salutation('**Team Field Booking**');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}