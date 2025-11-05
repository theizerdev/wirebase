@if($layoutType === 'horizontal')
    <x-layouts.horizontal :title="$title ?? config('app.name')">
        {{ $slot }}
    </x-layouts.horizontal>
@else
    <x-layouts.admin :title="$title ?? config('app.name')">
        {{ $slot }}
    </x-layouts.admin>
@endif