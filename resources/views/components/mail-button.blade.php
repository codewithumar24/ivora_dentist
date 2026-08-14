@props(['url', 'label'])
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:24px 0;">
    <tr>
        <td style="background:#10403B;border-radius:999px;">
            <a href="{{ $url }}" style="display:inline-block;padding:13px 28px;color:#F4F7F5;text-decoration:none;font-size:14px;font-weight:500;">{{ $label }}</a>
        </td>
    </tr>
</table>
