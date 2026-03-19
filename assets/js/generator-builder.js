(function () {
    var form = document.getElementById('generator-builder-form');
    if (!form) return;

    var sectionBuilder = document.getElementById('section-builder');
    var addSectionBtn = document.getElementById('add-section-btn');
    var summary = document.getElementById('structure-summary');
    var structureInput = document.getElementById('structure_json');
    var initial = Array.isArray(window.generatorBuilderInitial) ? window.generatorBuilderInitial : [];

    function fieldTypesOptions(selected) {
        var types = [
            'text', 'textarea', 'select', 'checkbox', 'radio', 'toggle_section', 'hidden',
            'chip_selector', 'multi_select_treatments', 'skin_condition_group', 'yes_no_toggle',
            'modal_trigger_block', 'admin_note', 'output_helper_text'
        ];

        return types.map(function (type) {
            var isSelected = type === selected ? ' selected' : '';
            return '<option value="' + type + '"' + isSelected + '>' + type + '</option>';
        }).join('');
    }

    function createFieldBlock(sectionIndex, fieldData) {
        fieldData = fieldData || {};
        var field = document.createElement('div');
        field.className = 'field-builder-row';
        field.innerHTML = '' +
            '<div class="grid-2">' +
                '<div class="form-group"><label>Field Name</label><input data-key="field_name" value="' + (fieldData.field_name || '') + '" placeholder="patient_position"></div>' +
                '<div class="form-group"><label>Field Label</label><input data-key="field_label" value="' + (fieldData.field_label || '') + '" placeholder="Patient Position"></div>' +
            '</div>' +
            '<div class="grid-2">' +
                '<div class="form-group"><label>Field Type</label><select data-key="field_type">' + fieldTypesOptions(fieldData.field_type || 'text') + '</select></div>' +
                '<div class="form-group"><label>Options (one per line)</label><textarea data-key="options_text" placeholder="Option A\nOption B">' + ((fieldData.options || []).join('\n')) + '</textarea></div>' +
            '</div>' +
            '<div class="grid-2">' +
                '<div class="form-group"><label>Placeholder</label><input data-key="placeholder_text" value="' + (fieldData.placeholder_text || '') + '"></div>' +
                '<div class="form-group"><label>Help Text</label><input data-key="help_text" value="' + (fieldData.help_text || '') + '"></div>' +
            '</div>' +
            '<div class="row-inline">' +
                '<label><input type="checkbox" data-key="is_required" ' + (fieldData.is_required ? 'checked' : '') + '> Required</label>' +
                '<label><input type="checkbox" data-key="is_active" ' + (fieldData.is_active === 0 ? '' : 'checked') + '> Active</label>' +
                '<button type="button" class="btn btn-outline btn-sm remove-field-btn">Remove Field</button>' +
            '</div>';

        field.querySelector('.remove-field-btn').addEventListener('click', function () {
            field.remove();
            syncStructure();
        });

        Array.prototype.forEach.call(field.querySelectorAll('input,textarea,select'), function (el) {
            el.addEventListener('input', syncStructure);
            el.addEventListener('change', syncStructure);
        });

        return field;
    }

    function createSectionBlock(sectionData) {
        sectionData = sectionData || {};
        var section = document.createElement('article');
        section.className = 'section-builder-card';

        section.innerHTML = '' +
            '<div class="section-header-row">' +
                '<h4>Section</h4>' +
                '<div>' +
                    '<button type="button" class="btn btn-secondary btn-sm add-field-btn">Add Field</button> ' +
                    '<button type="button" class="btn btn-outline btn-sm remove-section-btn">Remove Section</button>' +
                '</div>' +
            '</div>' +
            '<div class="grid-2">' +
                '<div class="form-group"><label>Section Name</label><input data-section-key="section_name" value="' + (sectionData.section_name || '') + '" placeholder="Dispatch"></div>' +
                '<div class="form-group"><label>Section Description</label><input data-section-key="section_description" value="' + (sectionData.section_description || '') + '" placeholder="Dispatch notes and coding"></div>' +
            '</div>' +
            '<div class="row-inline">' +
                '<label><input type="checkbox" data-section-key="is_toggleable" ' + (sectionData.is_toggleable ? 'checked' : '') + '> Toggleable Section</label>' +
                '<label><input type="checkbox" data-section-key="default_open" ' + (sectionData.default_open ? 'checked' : '') + '> Open by Default</label>' +
            '</div>' +
            '<div class="field-list"></div>';

        var fieldList = section.querySelector('.field-list');
        var fields = Array.isArray(sectionData.fields) ? sectionData.fields : [];
        fields.forEach(function (fieldData) {
            fieldList.appendChild(createFieldBlock(0, fieldData));
        });

        section.querySelector('.add-field-btn').addEventListener('click', function () {
            fieldList.appendChild(createFieldBlock(0));
            syncStructure();
        });

        section.querySelector('.remove-section-btn').addEventListener('click', function () {
            section.remove();
            syncStructure();
        });

        Array.prototype.forEach.call(section.querySelectorAll('input,textarea,select'), function (el) {
            el.addEventListener('input', syncStructure);
            el.addEventListener('change', syncStructure);
        });

        return section;
    }

    function syncStructure() {
        var sections = [];
        Array.prototype.forEach.call(sectionBuilder.querySelectorAll('.section-builder-card'), function (sectionEl, sectionIdx) {
            var section = {
                section_name: sectionEl.querySelector('[data-section-key="section_name"]').value.trim(),
                section_description: sectionEl.querySelector('[data-section-key="section_description"]').value.trim(),
                section_order: sectionIdx + 1,
                is_toggleable: sectionEl.querySelector('[data-section-key="is_toggleable"]').checked ? 1 : 0,
                default_open: sectionEl.querySelector('[data-section-key="default_open"]').checked ? 1 : 0,
                fields: []
            };

            Array.prototype.forEach.call(sectionEl.querySelectorAll('.field-builder-row'), function (fieldEl, fieldIdx) {
                var optionsText = fieldEl.querySelector('[data-key="options_text"]').value;
                section.fields.push({
                    field_name: fieldEl.querySelector('[data-key="field_name"]').value.trim(),
                    field_label: fieldEl.querySelector('[data-key="field_label"]').value.trim(),
                    field_type: fieldEl.querySelector('[data-key="field_type"]').value,
                    placeholder_text: fieldEl.querySelector('[data-key="placeholder_text"]').value.trim(),
                    help_text: fieldEl.querySelector('[data-key="help_text"]').value.trim(),
                    is_required: fieldEl.querySelector('[data-key="is_required"]').checked ? 1 : 0,
                    is_active: fieldEl.querySelector('[data-key="is_active"]').checked ? 1 : 0,
                    field_order: fieldIdx + 1,
                    options: optionsText.split(/\n/).map(function (s) { return s.trim(); }).filter(Boolean)
                });
            });

            sections.push(section);
        });

        structureInput.value = JSON.stringify(sections);
        renderSummary(sections);
    }

    function renderSummary(sections) {
        if (!sections.length) {
            summary.innerHTML = '<p class="muted">No sections configured yet.</p>';
            return;
        }

        var html = sections.map(function (section) {
            var fieldsHtml = section.fields.map(function (field) {
                return '<li><code>{{' + (field.field_name || 'field_name') + '}}</code> · ' + (field.field_type || 'text') + '</li>';
            }).join('');

            return '<div class="summary-block">' +
                '<h4>' + (section.section_name || 'Untitled Section') + '</h4>' +
                '<ul>' + (fieldsHtml || '<li>No fields</li>') + '</ul>' +
            '</div>';
        }).join('');

        summary.innerHTML = html;
    }

    addSectionBtn.addEventListener('click', function () {
        sectionBuilder.appendChild(createSectionBlock());
        syncStructure();
    });

    if (!initial.length) {
        sectionBuilder.appendChild(createSectionBlock());
    } else {
        initial.forEach(function (sectionData) {
            sectionBuilder.appendChild(createSectionBlock(sectionData));
        });
    }

    form.addEventListener('submit', syncStructure);
    syncStructure();
})();
