<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerFxtw5gd\appDevDebugProjectContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerFxtw5gd/appDevDebugProjectContainer.php') {
    touch(__DIR__.'/ContainerFxtw5gd.legacy');

    return;
}

if (!\class_exists(appDevDebugProjectContainer::class, false)) {
    \class_alias(\ContainerFxtw5gd\appDevDebugProjectContainer::class, appDevDebugProjectContainer::class, false);
}

return new \ContainerFxtw5gd\appDevDebugProjectContainer([
    'container.build_hash' => 'Fxtw5gd',
    'container.build_id' => '08682d73',
    'container.build_time' => 1581367166,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerFxtw5gd');
