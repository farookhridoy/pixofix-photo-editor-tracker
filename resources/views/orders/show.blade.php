<div x-data="{ progress: {{ $order->progress }} }">
    <div class="progress-bar">
        <div :style="`width: ${progress}%`"></div>
    </div>

    @foreach($order->files as $file)
        <div class="file-item {{ $file->status }}">
            {{ $file->path }}
            <span class="status">{{ $file->status }}</span>
        </div>
    @endforeach
</div>

<script>
    window.Echo.channel('order-progress')
        .listen('FileCompleted', (data) => {
            if (data.order_id === {{ $order->id }}) {
                window.dispatchEvent(new CustomEvent('progress-update', {
                    detail: {progress: data.progress}
                }));
            }
        });
</script>
