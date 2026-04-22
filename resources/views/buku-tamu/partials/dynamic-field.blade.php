<div class="form-group" id="{{ $field->name }}_group">
    <label for="{{ $field->name }}" class="form-label">
        {{ $field->label }} {!! $field->is_required ? '<span style="color: #ef4444;">*</span>' : '' !!}
    </label>

    @if($field->type === 'select')
        <select id="{{ $field->name }}" name="{{ $field->name }}" class="form-input" {{ $field->is_required ? 'required' : '' }}>
            <option value="">{{ $field->placeholder ?: '-- Pilih --' }}</option>
            @if($field->options)
                @foreach($field->options as $value => $label)
                    <option value="{{ $value }}" {{ old($field->name) == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            @endif
        </select>
    @elseif($field->type === 'textarea')
        <textarea id="{{ $field->name }}" name="{{ $field->name }}" class="form-input" 
                  placeholder="{{ $field->placeholder }}" rows="3" {{ $field->is_required ? 'required' : '' }}>{{ old($field->name) }}</textarea>
    @elseif($field->type === 'radio')
        <div class="radio-group">
            @if($field->options)
                @foreach($field->options as $value => $label)
                    <label class="radio-label">
                        <input type="radio" name="{{ $field->name }}" value="{{ $value }}" {{ old($field->name) == $value ? 'checked' : '' }} {{ $field->is_required ? 'required' : '' }}>
                        {{ $label }}
                    </label>
                @endforeach
            @endif
        </div>
    @elseif($field->type === 'checkbox')
        <div class="checkbox-group">
            @if($field->options)
                @foreach($field->options as $value => $label)
                    <label class="checkbox-label">
                        <input type="checkbox" name="{{ $field->name }}[]" value="{{ $value }}" {{ is_array(old($field->name)) && in_array($value, old($field->name)) ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                @endforeach
            @endif
        </div>
    @elseif($field->type === 'number')
        <input type="number" id="{{ $field->name }}" name="{{ $field->name }}" class="form-input" 
               placeholder="{{ $field->placeholder }}" {{ $field->is_required ? 'required' : '' }}
               value="{{ old($field->name) }}">
    @else
        <input type="{{ $field->type }}" id="{{ $field->name }}" name="{{ $field->name }}" class="form-input" 
               placeholder="{{ $field->placeholder }}" {{ $field->is_required ? 'required' : '' }}
               value="{{ old($field->name) }}">
    @endif
</div>
