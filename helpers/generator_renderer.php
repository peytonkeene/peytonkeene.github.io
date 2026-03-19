<?php

function render_dynamic_field(array $field): string
{
    $name = htmlspecialchars($field['field_slug']);
    $label = htmlspecialchars($field['field_label']);
    $placeholder = htmlspecialchars($field['placeholder_text'] ?? '');
    $help = htmlspecialchars($field['help_text'] ?? '');
    $required = !empty($field['is_required']) ? 'required' : '';
    $type = $field['field_type'];
    $options = $field['options'] ?? [];

    ob_start();
    echo '<div class="builder-field">';
    if (!in_array($type, ['hidden', 'admin_note', 'output_helper_text'], true)) {
        echo '<label for="f_' . $name . '">' . $label . '</label>';
    }

    if ($type === 'textarea') {
        echo '<textarea id="f_' . $name . '" name="' . $name . '" placeholder="' . $placeholder . '" ' . $required . '></textarea>';
    } elseif ($type === 'select') {
        echo '<select id="f_' . $name . '" name="' . $name . '" ' . $required . '>';
        echo '<option value="">Select...</option>';
        foreach ($options as $option) {
            $o = htmlspecialchars($option);
            echo '<option value="' . $o . '">' . $o . '</option>';
        }
        echo '</select>';
    } elseif (in_array($type, ['radio', 'chip_selector', 'skin_condition_group', 'yes_no_toggle'], true)) {
        echo '<div class="chip-row">';
        foreach ($options as $index => $option) {
            $o = htmlspecialchars($option);
            $id = 'f_' . $name . '_' . $index;
            $inputType = in_array($type, ['yes_no_toggle', 'radio'], true) ? 'radio' : 'checkbox';
            echo '<label class="chip-option" for="' . $id . '"><input id="' . $id . '" type="' . $inputType . '" name="' . $name . ($inputType === 'checkbox' ? '[]' : '') . '" value="' . $o . '"> <span>' . $o . '</span></label>';
        }
        echo '</div>';
    } elseif (in_array($type, ['checkbox', 'multi_select_treatments'], true)) {
        echo '<div class="chip-row">';
        foreach ($options as $index => $option) {
            $o = htmlspecialchars($option);
            $id = 'f_' . $name . '_' . $index;
            echo '<label class="chip-option" for="' . $id . '"><input id="' . $id . '" type="checkbox" name="' . $name . '[]" value="' . $o . '"> <span>' . $o . '</span></label>';
        }
        echo '</div>';
    } elseif ($type === 'toggle_section') {
        echo '<label class="switch"><input type="checkbox" name="' . $name . '" value="1"><span>Enable section details</span></label>';
    } elseif ($type === 'modal_trigger_block') {
        echo '<button type="button" class="btn btn-secondary btn-sm">Open Detail Block</button>';
        echo '<textarea name="' . $name . '" placeholder="Capture detail from modal or notes..."></textarea>';
    } elseif ($type === 'admin_note' || $type === 'output_helper_text') {
        echo '<p class="helper-note">' . ($help !== '' ? $help : $label) . '</p>';
        echo '<input type="hidden" name="' . $name . '" value="">';
    } elseif ($type === 'hidden') {
        echo '<input type="hidden" name="' . $name . '" value="">';
    } else {
        echo '<input id="f_' . $name . '" name="' . $name . '" type="text" placeholder="' . $placeholder . '" ' . $required . '>';
    }

    if ($help !== '' && !in_array($type, ['admin_note', 'output_helper_text'], true)) {
        echo '<small class="help-text">' . $help . '</small>';
    }
    echo '</div>';

    return (string)ob_get_clean();
}

function build_narrative_from_template(string $template, array $input): string
{
    return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_\-]+)\s*\}\}/', static function ($matches) use ($input) {
        $key = $matches[1];
        if (!array_key_exists($key, $input)) {
            return '';
        }

        $value = $input[$key];
        if (is_array($value)) {
            return implode(', ', array_filter(array_map('trim', $value)));
        }

        return trim((string)$value);
    }, $template) ?? '';
}
