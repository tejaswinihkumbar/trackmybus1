<!--  Bus selection section  -->
<section class="py-5">
    <div class="container">
        <h3 class="text-center mb-4">Select a Bus to Track</h3>
        <div class="row">
            <?php foreach ($buses as $bus): ?>
                <a href="track_bus.php?bus_id=<?= htmlspecialchars($bus['id']) ?>" class="btn btn-outline-primary m-1">
                    <?= htmlspecialchars($bus['vehicle_number']) ?>
                </a>
            <?php endforeach; ?>

        </div>
    </div>
</section> 