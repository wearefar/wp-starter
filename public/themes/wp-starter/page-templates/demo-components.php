<?php

$components = [
    //
];

get_header();

?>
<div class="mt-18 lg:mt-24">
    <?php foreach ($components as $component) : ?>
        <div>
            <div class="px-4 py-1 font-bold text-sm text-center uppercase bg-black text-white"><?= $component['label'] ?? $component['name'] ?></div>
            <?php component($component['name'], $component['args'] ?? []); ?>
        </div>
    <?php endforeach; ?>
</div>
<?php

get_footer();
