<?php
/** @var array $formData */
/** @var array $agencies */
/** @var bool $isEdit */
/** @var string $actionUrl */
?>
<form method="post" action="<?php echo htmlspecialchars($actionUrl); ?>" id="generator-builder-form">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?php echo (int)$formData['id']; ?>">
    <?php endif; ?>

    <div class="builder-layout">
        <div class="builder-main">
            <article class="card builder-card">
                <h3>Generator Basics</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="name">Generator Name</label>
                        <input id="name" name="name" required value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" placeholder="e.g., ALS Patient Care Narrative">
                    </div>
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input id="slug" name="slug" value="<?php echo htmlspecialchars($formData['slug'] ?? ''); ?>" placeholder="e.g., als-patient-care-narrative">
                        <small class="help-text">Optional. Leave blank to auto-generate from the name.</small>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="is_active">Status</label>
                        <select id="is_active" name="is_active">
                            <option value="1" <?php echo (int)($formData['is_active'] ?? 1) === 1 ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo (int)($formData['is_active'] ?? 1) === 0 ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Briefly describe this workflow."><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="agency_id">Assigned Agency</label>
                        <select id="agency_id" name="agency_id" <?php echo is_superadmin() ? '' : 'disabled'; ?>>
                            <?php foreach ($agencies as $agency): ?>
                                <option value="<?php echo (int)$agency['id']; ?>" <?php echo (int)$agency['id'] === (int)($formData['agency_id'] ?? current_user_agency_id()) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($agency['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!is_superadmin()): ?>
                            <input type="hidden" name="agency_id" value="<?php echo (int)current_user_agency_id(); ?>">
                            <small class="help-text">Admins can only assign generators to their own agency.</small>
                        <?php endif; ?>
                    </div>
                </div>
            </article>

            <article class="card builder-card">
                <div class="section-header-row">
                    <h3>Sections & Fields</h3>
                    <button type="button" class="btn btn-secondary btn-sm" id="add-section-btn">Add Section</button>
                </div>
                <div id="section-builder"></div>
            </article>

            <article class="card builder-card">
                <h3>Narrative Template</h3>
                <p class="muted">Use placeholders like <code>{{chief_complaint}}</code> and <code>{{treatment_list}}</code>.</p>
                <textarea name="template_content" id="template_content" class="template-editor" placeholder="DISPATCH: {{dispatch_text}}\n\nARRIVAL: The patient was found {{patient_position}}...\n\nTREATMENT: {{treatment_list}}."><?php echo htmlspecialchars($formData['template_content'] ?? ''); ?></textarea>
            </article>

            <div class="builder-actions">
                <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Update Generator' : 'Save Generator'; ?></button>
                <a class="btn btn-outline" href="/admin/generator-list.php">Cancel</a>
            </div>
        </div>

        <aside class="builder-sidebar">
            <article class="card builder-card sticky-card">
                <h3>Live Structure Summary</h3>
                <div id="structure-summary" class="summary-list"></div>
            </article>
        </aside>
    </div>

    <input type="hidden" id="structure_json" name="structure_json" value="">
</form>

<script>
window.generatorBuilderInitial = <?php echo json_encode($formData['structure'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
</script>
<script src="/assets/js/generator-builder.js"></script>
