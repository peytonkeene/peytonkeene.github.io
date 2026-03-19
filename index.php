<?php require_once __DIR__ . '/includes/bootstrap.php'; $pageTitle = 'MedNarrate | Home'; include __DIR__ . '/includes/head.php'; ?>
<?php include __DIR__ . '/includes/public_header.php'; ?>
<div class="container">
    <section class="hero">
        <div>
            <h1>Faster EMS Narratives. Better Documentation.</h1>
            <p>MedNarrate helps EMS providers create structured, compliant narratives faster with consistent documentation workflows built for real patient care reporting.</p>
            <div class="cta-row">
                <a href="/login.php" class="btn btn-primary">Login</a>
                <a href="/solutions.php" class="btn btn-secondary">View Solutions</a>
            </div>
        </div>
        <article class="card module-card">
            <h3>Clinical Workflow Modules</h3>
            <ul class="module-list">
                <li>Narrative Generator</li>
                <li>Medical Necessity Builder</li>
                <li>QA Support</li>
                <li>Report Tools</li>
            </ul>
        </article>
    </section>

    <section class="section">
        <div class="grid-4">
            <article class="card info-card"><h3>Speed Documentation</h3><p>Reduce report writing time with guided EMS-first structure and less backtracking.</p></article>
            <article class="card info-card"><h3>Structured Narratives</h3><p>Maintain clinical consistency with workflow-oriented prompts and organized sections.</p></article>
            <article class="card info-card"><h3>Consistent Reporting</h3><p>Improve chart quality through reliable formatting and repeatable documentation patterns.</p></article>
            <article class="card info-card"><h3>Agency Quality Improvement</h3><p>Support QA initiatives with cleaner narratives ready for review, billing, and compliance.</p></article>
        </div>
    </section>
</div>
<?php include __DIR__ . '/includes/public_footer.php'; ?>
