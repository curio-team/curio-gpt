@props([
'model' => 'enabled',
'onLabel' => __('Enabled'),
'offLabel' => __('Disabled'),
])

<label {{
       $attributes->merge(['class' => 'flex items-center gap-3 cursor-pointer']) }}>
    <button type="button"
            @click="{{ $model }} = !{{ $model }}"
            class="relative inline-flex h-5 w-9 shrink-0 items-center rounded-full transition-colors"
            :class="{{ $model }} ? 'bg-black dark:bg-white' : 'bg-black/20 dark:bg-white/20'"
            :aria-checked="{{ $model }}"
            role="switch">
        <span class="inline-block h-4 w-4 transform rounded-full bg-white dark:bg-black transition-transform"
              :class="{{ $model }} ? 'translate-x-4.5' : 'translate-x-0.5'"></span>
    </button>
    <span class="text-sm text-black dark:text-white"
          x-text="{{ $model }} ? '{{ $onLabel }}' : '{{ $offLabel }}'"></span>
</label>