@props([
    'key',
    'name',
    'label',
    'title',
    'search',
    'button',
    'empty',
    'options',
    'selected' => null,
    'none' => null,
    'noneHint' => 'Nav attēla',
    'showImage' => true,
])

@php($selectedOption = collect($options)->firstWhere('name', $selected))

<div class="field">
    <span>{{ $label }}</span>
    <input id="{{ $key }}-value" type="hidden" name="{{ $name }}" value="{{ $selected }}">
    <div class="choice-control">
        <div class="choice-selected">
            @if ($showImage)
                <img
                    id="{{ $key }}-image"
                    src="{{ $selectedOption['image_url'] ?? '' }}"
                    alt=""
                    @if (empty($selectedOption['image_url'])) hidden @endif
                >
            @endif
            <output id="{{ $key }}-label">{{ $selected ?: ($none ?? 'Izvēlieties') }}</output>
        </div>
        <button id="open-{{ $key }}-picker" class="button-secondary" type="button">{{ $button }}</button>
    </div>
</div>

@push('dialogs')
    <dialog id="{{ $key }}-picker-dialog" class="choice-dialog" data-choice-dialog="{{ $key }}" aria-labelledby="{{ $key }}-picker-title">
        <div class="choice-dialog-header">
            <h2 id="{{ $key }}-picker-title">{{ $title }}</h2>
            <form method="dialog">
                <button class="button-secondary" type="submit">Aizvērt</button>
            </form>
        </div>

        <label for="{{ $key }}-search">{{ $search }}</label>
        <input id="{{ $key }}-search" class="choice-search" type="search">

        <div class="choice-grid">
            @if ($none)
                <button class="choice-option" type="button" data-choice-option data-choice-value="" data-choice-name="{{ $none }}" data-choice-image="" aria-pressed="{{ $selected ? 'false' : 'true' }}">
                    <span class="choice-option-placeholder">{{ $noneHint }}</span>
                    <span class="choice-option-label">{{ $none }}</span>
                </button>
            @endif

            @foreach ($options as $option)
                <button
                    class="choice-option"
                    type="button"
                    data-choice-option
                    data-choice-value="{{ $option['name'] }}"
                    data-choice-name="{{ $option['name'] }}"
                    data-choice-image="{{ $option['image_url'] ?? '' }}"
                    aria-pressed="{{ $selected === $option['name'] ? 'true' : 'false' }}"
                >
                    @if ($option['image_url'])
                        <img src="{{ $option['image_url'] }}" alt="{{ $option['alt'] }}" loading="lazy">
                    @else
                        <span class="choice-option-placeholder">{{ $option['hint'] }}</span>
                    @endif
                    <span class="choice-option-label">{{ $option['name'] }}</span>
                </button>
            @endforeach
        </div>

        <p id="{{ $key }}-search-empty" hidden>{{ $empty }}</p>
    </dialog>
@endpush
