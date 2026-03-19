<?php require_once __DIR__ . '/includes/bootstrap.php'; $pageTitle = 'MedNarrate | Contact'; include __DIR__ . '/includes/head.php'; ?>
<?php include __DIR__ . '/includes/public_header.php'; ?>
<div class="container section">
    <h1>Contact</h1>
    <div class="grid-2">
        <article class="card info-card">
            <form method="post" action="#">
                <div class="form-group"><label for="name">Name</label><input id="name" name="name" type="text" required></div>
                <div class="form-group"><label for="email">Email</label><input id="email" name="email" type="email" required></div>
                <div class="form-group"><label for="agency">Agency</label><input id="agency" name="agency" type="text"></div>
                <div class="form-group"><label for="message">Message</label><textarea id="message" name="message" required></textarea></div>
                <button class="btn btn-primary" type="submit">Send Message</button>
            </form>
        </article>
        <article class="card info-card">
            <h3>Support</h3>
            <p>Email: <a href="mailto:support@mednarrate.net">support@mednarrate.net</a></p>
            <h3 style="margin-top:12px;">Business Inquiries</h3>
            <p>Email: <a href="mailto:contact@mednarrate.net">contact@mednarrate.net</a></p>
        </article>
    </div>
</div>
<?php include __DIR__ . '/includes/public_footer.php'; ?>
