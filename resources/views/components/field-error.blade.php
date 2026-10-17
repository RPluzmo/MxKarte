@props(['field'])

@php($messages = $errors->get($field))

@if (str_ends_with($field, '.*'))
    @php($prefix = substr($field, 0, -1))
    @php($messages = collect($errors->getMessages())->filter(fn ($items, $key) => str_starts_with($key, $prefix))->flatten()->all())
@endif

@foreach ($messages as $message)
    <span class="field-error" id="error-{{ str_replace(['.', '*'], '-', $field) }}" role="alert">{{ $message }}</span>
@endforeach
