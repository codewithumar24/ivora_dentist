<div class="head">
    <table class="grid">
        <tr>
            <td style="width: 55%;">
                <div class="brand">Ivora Dental Studio</div>
                <div class="brand-sub">Quiet, careful dentistry</div>
            </td>
            <td class="right clinic">
                {{ \App\Models\Setting::get('address') }}<br>
                {{ \App\Models\Setting::get('phone') }} · {{ \App\Models\Setting::get('email') }}
            </td>
        </tr>
    </table>
</div>
