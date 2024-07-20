<div class="list-feed list-feed-solid m-2">
    @foreach ($history as $item)
        @php
            $borderClass = $item['status'] == 'closed' ? 'border-success' : 'border-warning';
            $opacityClass = $item['status'] == 'open' ? 'opacity-50' : '';
            $statusClass = trim("$borderClass $opacityClass");
        @endphp
        <div class="list-feed-item {{ $statusClass }}">
            <div class="text-muted fs-sm fw-semibold">{{ $item['date'] ?? '' }}</div>
            <div class="fw-semibold">{!! $item['title'] !!}</div>
            <small>{!! $item['text'] !!}
                <span class="fw-semibold">{{ $item['status'] == 'closed' ? $item['user'] : '' }}</span>
            </small>
        </div>
    @endforeach
</div>
