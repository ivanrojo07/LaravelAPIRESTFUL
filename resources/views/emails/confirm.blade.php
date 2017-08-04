@component('mail::message')
# Hola {{$user->name}}

Has cambiado tu correo electrónico. Por favor verificala usando el siguiente boton:

@component('mail::button', ['url' => route('verify', $user->verification_token) ])
Button Text
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent