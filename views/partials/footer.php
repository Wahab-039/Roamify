<footer class="site-footer mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-5">
                <h5 class="fw-bold">Roamify</h5>
                <p class="mb-0 text-white-50">A smart travel planning platform for discovering destinations, estimating budgets, and checking weather before the journey.</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-uppercase small">Explore</h6>
                <a href="<?= url('destinations.php') ?>">Destinations</a>
                <a href="<?= url('budget.php') ?>">Budget planner</a>
                <a href="<?= url('contact.php') ?>">Contact</a>
            </div>
            <div class="col-md-4">
                <h6 class="text-uppercase small">Semester Project Ready</h6>
                <p class="mb-0 text-white-50">Built with PHP 8, MySQL, Bootstrap 5, PDO, CSRF protection, secure sessions, and role-based admin access.</p>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if (!empty($useLeaflet)): ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php endif; ?>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
