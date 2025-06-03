@component('mail::message')
# Invoice Transaksi

@component('mail::button', ['url' => '#'])
Unduh Invoice PDF (lihat lampiran)
@endcomponent

Terima kasih telah bertransaksi bersama kami.

Salam hangat,  
{{ config('app.name') }}
@endcomponent
