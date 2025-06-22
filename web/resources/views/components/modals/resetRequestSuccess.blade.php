@php
use App\Helpers\TranslationHelper as T;
@endphp
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">{{ T::translate('Reset Password Request', 'Magrequest na I-reset ang Password')}}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            {{ T::translate('A password reset link has been sent to you email. Please check you email.', 'Ang link sa pag-reset ng password ay ipinadala sa iyong email. Mangyaring i-check ang iyong email')}}
        </div>
        <div class="modal-footer">
            <a href="{{ route('login') }}">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara')}}</button> </a>
        </div>
        </div>
    </div>
</div>