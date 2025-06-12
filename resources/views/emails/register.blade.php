<x-mail::message>
<!-- # Introduction

The body of your message.



Thanks,<br>
{{ config('app.name') }} -->

Berikut Merupakan Link Untuk Verifikasi Email
<x-mail::button :url="$link">
Verifikasi
</x-mail::button>

</x-mail::message>
