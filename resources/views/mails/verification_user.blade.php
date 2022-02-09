Dear
@if(!is_null($data['user']))
{{ $data['user']->name }}
@else
Anonymous
@endif,
<br/>

Please click link below to verify your email :
<br/>
<a href="{{ $data['url'] }}">Verify Email</a>