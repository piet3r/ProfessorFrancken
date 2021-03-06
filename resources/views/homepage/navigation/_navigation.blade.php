<div class="navigation__menu nav">
    @foreach ($items as $item)
        <div class="navigation__hoverable">
            <a class="navigation__menu-item nav-link active text-nowrap {{ $item['class'] or '' }}" href="{{ $item['url'] }}">
                @if ($item['icon'] != '')
                    <img src="/icons/filled/{{ $item['icon'] }}.svg" alt="" style="height: 20px" class="mr-2">
                @endif
                {{ $item['title'] }}
            </a>

            <nav class="navigation__sub-menu nav justify-content-end" >
                <?php $active = '/' . Request::segment(1) == $item['url'] ? 'navigation__sub-menu-item--active' : ''; ?>
                @foreach ($item['subItems'] as $subItem)
                    <a class="navigation__sub-menu-item nav-link {{ $active }} text-nowrap" href="{{ $subItem['url'] }}">{{ $subItem['title'] }}</a>
                @endforeach
            </nav>
        </div>
    @endforeach
</div>
