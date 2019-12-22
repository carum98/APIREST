@component('mail::message')
    # Hola {{ $user->name }}

    Grasias por crear una cuenta. Por favor verificala usando el siguiente enlace

    @component('mail::button', ['url' => route( 'verify', $user->verification_token )])
        Confirmar mi cuenta
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
