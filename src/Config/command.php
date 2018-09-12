<?php

return [
    /*FolderPath*/
    'ServicePath' => '/Services',
    'EntityPath' => '/Entities',
    'RepositoryPath' => '/Repositories',
    'ModelPath' => '/Models',

    /*Base*/
    'BaseFile' => [
        'Service' => 'BaseService.php',
        'Repository' => 'BaseRepository.php',
        'Model' => 'BaseModel.php',
    ],
    'Stubs' => [
        'BaseService' => 'stubs/Base/BaseService.stub',
        'BaseRepository' => 'stubs/Base/BaseRepository.stub',
        'BaseModel' => 'stubs/Base/BaseModel.stub'
    ]
];
