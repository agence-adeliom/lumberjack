<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualConflictsReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([__DIR__ . '/packages']);
    $mbConfig->defaultBranch('1.x');

    $mbConfig->dataToRemove([
        'require-dev' => [
            # remove these to merge of packages' composer.json
            'phpunit/phpunit' => '*',
        ],
        'minimum-stability' => 'dev',
        'prefer-stable' => true,
    ]);

    $mbConfig->dataToAppend([
        'require-dev' => [
            'nunomaduro/phpinsights' => '^2.4',
        ]
    ]);

    $mbConfig->workers([
        // release workers - in order to execute
        UpdateReplaceReleaseWorker::class,
        //SetCurrentMutualConflictsReleaseWorker::class,
        //SetCurrentMutualDependenciesReleaseWorker::class,
        TagVersionReleaseWorker::class,
        PushTagReleaseWorker::class,
        //SetNextMutualDependenciesReleaseWorker::class,
        UpdateBranchAliasReleaseWorker::class,
        Mono\PushNextDevReleaseWorker::class,
    ]);
};
